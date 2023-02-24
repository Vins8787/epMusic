<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\Audio;
use App\Entity\Gruppi;
use App\Entity\Utenti;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Costanti;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class UtentiController extends AbstractController {
    use  \App\Helper\Helper;

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/utenti', name: 'listaUtenti')]
    public function listaUtenti(ManagerRegistry $doctrine, Request $request): Response {
        $em = $doctrine->getManager();
        $params = $request->query->all();

        $getUtenti = $em->getRepository(Utenti::class)->findUtenti($params);
        //$getGruppi = $em->getRepository(Gruppi::class)->findBy([]);

        $arrayPrimiAccessi = [];
        foreach ($getUtenti as $item => $value) {
            $logUtente =  $em->getRepository(Log::class)->findOneBy(["idUtente" => $value->getId(), "tipo" => "login"]);
            if ($logUtente != null) {
                $arrayPrimiAccessi[$value->getId()] = $logUtente->getDataCreazione()->format("d/m/Y H:i");
            }
        }

        //dump($getUtenti);

        return $this->render('utenti/lista-utenti.html.twig', [
            "sidebarActive" => "utenti",
            "profileMenu" => "utenti",
            "SEO_title" => "Utenti",
            "listaUtenti" => $getUtenti,
            "primiAccessi" => $arrayPrimiAccessi,
            //"listaGruppi" => $getGruppi,
            "filtri" => $params
        ]);
    }


    #[Route('/utenti/{id}', name: 'utentiNewUpdate', methods: ['POST'])]
    public function utentiNewUpdate(Request $request, $id, ManagerRegistry $doctrine, MailerInterface $mailer): Response {
        $em = $doctrine->getManager();
        $data = $request->request->all();

        if ($this->isGranted('ROLE_ADMIN') != 1) {
            if ($this->getUser()->getId() != $id) { //puoi vedere solo il tuo profilo
                return $this->redirect($this->generateUrl('listaUtenti'));
            }
        }

        if ($id == "new") {
            $checkUtente = $em->getRepository(Utenti::class)->findOneBy(array('userIdentifier' => $data['userIdentifier']));
            if ($checkUtente != null) {
                $this->addFlash("toastSuccess", ["title" => 'Impossibile creare l\'utente', "text" => "E' presente già un utente con l'email " . $checkUtente->getUserIdentifier() ]);
                return $this->redirect($this->generateUrl('schedaUtenti', ['id' => 'new']));
            }

            $utente = new Utenti();
            $utente->setIsActive(1);
            $utente->setUserIdentifier($data['userIdentifier']);
            if ($data['password'] != $data['ripetiPassword']) {
                $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_ERRORE, "text" => str_replace("{elemento}", $utente->getNome(), Costanti::ERRORE_RIPETI_PASSWORD)]);
                return $this->redirect($this->generateUrl('schedaUtenti', ['id' => $id]));
            }

        } else {
            $utente = $em->getRepository(Utenti::class)->findOneBy(array('id' => $id));
        }

        if (isset($data['nome'])) { $utente->setNome($data['nome']); }
        if (isset($data['cognome'])) { $utente->setCognome($data['cognome']); }
        if (isset($data['gruppo'])) {
            $gruppo = $em->getRepository(Gruppi::class)->findOneBy(array('id' => $data['gruppo']));
            $utente->setGruppo($gruppo);
        }
        if (isset($data['company'])) { $utente->setCompany($data['company']); }
        if (isset($data['ruolo'])) { $utente->setRuolo($data['ruolo']); }
        //se modifichiamo la password
        $password = "-";
        if (isset($data['password']) && $data['password'] != "" && $data['password'] != null) {
            if ($data['password'] != $data['ripetiPassword']) {
                $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_ERRORE, "text" => str_replace("{elemento}", $utente->getNome(), Costanti::ERRORE_RIPETI_PASSWORD)]);
                return $this->redirect($this->generateUrl('schedaUtenti', ['id' => $utente->getId()]));
            }

            $utente->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
            $password = $data['password'];
            $utente->setCodeActivation($password);
        }

        $utente->setDataModifica(new \DateTime());

        $em->persist($utente);
        $em->flush(); //esegue

        if ($id == "new") {
            //inviare email all'admin
            $email = (new TemplatedEmail())
                ->subject('EPMUSIC - Nuovo account')
                ->from(Costanti::EMAIL_INFO)
                ->to(...[$utente->getUserIdentifier(), "naps8787@gmail.com"])
                ->htmlTemplate('emails/emailNewUtente.html.twig')
                ->context([
                    "utente" => $utente,
                    "password" => $password
                ]);
            $mailer->send($email);
        }


        if ($id == "new") {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_CREATE_TITLE, "text" => str_replace("{elemento}", $utente->getNome(), Costanti::TOAST_CREATE_TEXT)]);
        } else {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_UPDATE_TITLE, "text" => str_replace("{elemento}", $utente->getNome(), Costanti::TOAST_UPDATE_TEXT)]);
        }

        return $this->redirect($this->generateUrl('schedaUtenti', ['id' => $utente->getId()]));
    }


    #[Route('/utenti/{id}', name: 'schedaUtenti', methods: ['GET'])]
    public function schedaUtenti($id, ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();
        if ($this->isGranted('ROLE_ADMIN') != 1) {
            if ($this->getUser()->getId() != $id) { //puoi vedere solo il tuo profilo
                return $this->redirect($this->generateUrl('listaUtenti'));
            }
        }

        $utenti = $em->getRepository(Utenti::class)->findOneBy(array('id' => $id));
        $getGruppi = $em->getRepository(Gruppi::class)->findBy([],["id" => "desc"]);

        //statistiche accessi
        $statisticheAccessi =  $em->getRepository(Log::class)->findBy(["idUtente" => $id, "tipo" => "login"]);
        $statisticheAccessiArray = ["totali" => 0, "anno" => 0, "mese" => 0, "settimana" => 0];
        if ($statisticheAccessi != null) {
            foreach ($statisticheAccessi as $item => $value) {
                $statisticheAccessiArray['totali'] = $statisticheAccessiArray['totali'] + 1 ;
                if (date('Y') == $value->getDataCreazione()->format("Y")) {
                    $statisticheAccessiArray['anno'] = $statisticheAccessiArray['anno'] + 1 ;
                }
                if (date('Y-m') == $value->getDataCreazione()->format("Y-m")) {
                    $statisticheAccessiArray['mese'] = $statisticheAccessiArray['mese'] + 1 ;
                }
                if (date('Y-m-W') == $value->getDataCreazione()->format("Y-m-W")) { // W = numero settimana, inizia da lunedì
                    $statisticheAccessiArray['settimana'] = $statisticheAccessiArray['settimana'] + 1 ;
                }
            }
        }


        $statisticheAscolti =  $em->getRepository(Log::class)->findBy(["idUtente" => $id, "tipo" => "view-audio"]);
        $statisticheAscoltiArray = ["totali" => 0, "anno" => 0, "mese" => 0, "settimana" => 0];
        if ($statisticheAscolti != null) {
            foreach ($statisticheAscolti as $item => $value) {
                $statisticheAscoltiArray['totali'] = $statisticheAscoltiArray['totali'] + 1 ;
                if (date('Y') == $value->getDataCreazione()->format("Y")) {
                    $statisticheAscoltiArray['anno'] = $statisticheAscoltiArray['anno'] + 1 ;
                }
                if (date('Y-m') == $value->getDataCreazione()->format("Y-m")) {
                    $statisticheAscoltiArray['mese'] = $statisticheAscoltiArray['mese'] + 1 ;
                }
                if (date('Y-m-W') == $value->getDataCreazione()->format("Y-m-W")) { // W = numero settimana, inizia da lunedì
                    $statisticheAscoltiArray['settimana'] = $statisticheAscoltiArray['settimana'] + 1 ;
                }
            }
        }

        $richiesteUtente =  $em->getRepository(Log::class)->findBy(["idUtente" => $id, "tipo" => "richiesta-audio"]);
        $richiesteUtenteBrani = [];
        foreach ($statisticheAscolti as $item => $value) {
            $brano =  $em->getRepository(Audio::class)->findOneBy(["id" => $value->getIdAudio()]);
            if ($brano != null) {
                $richiesteUtenteBrani[$value->getIdAudio()] = $brano;
            }
        }




        if ($utenti == null && $id != "new") {
            return $this->redirect($this->generateUrl('listaUtenti'));
        }
        //dump($statisticheAccessi);

        return $this->render('utenti/scheda-utenti.html.twig', [
            "sidebarActive" => "utenti",
            "profileMenu" => "utenti",
            "SEO_title" => "Utenti " . ($utenti != null ? $utenti->getNome() : "nuovo"),
            "utenti" => $utenti,
            "listaGruppi" => $getGruppi,
            "statisticheAccessi" => $statisticheAccessiArray,
            "statisticheAscolti" => $statisticheAscoltiArray,
            "primoAccesso" => $statisticheAccessi != null ? $statisticheAccessi[0] : '',
            "richiesteUtente" => $richiesteUtente,
            "richiesteUtenteBrani" => $richiesteUtenteBrani
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/utenti/{id}', name: 'deleteUtenti', methods: ['POST'])]
    public function deleteUtenti($id, ManagerRegistry $doctrine) {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Utenti::class);
        $getElemento = $repository->findOneBy(["id"=> $id]);
        //rimuovo anche tutti i suoi log (per le statistiche)
        $statisticheUtente =  $em->getRepository(Log::class)->findBy(["idUtente" => $id]);
        if ($statisticheUtente != null) {
            foreach ($statisticheUtente as $item => $value) {
                $em->remove($value);
                $em->flush();
            }
        }

        $em->remove($getElemento);
        $em->flush();

        $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_DELETE_TITLE, "text" => str_replace("{elemento}", $getElemento->getNome(), Costanti::TOAST_DELETE_TEXT)]);
        return $this->redirect($this->generateUrl('listaUtenti'));
    }


}
