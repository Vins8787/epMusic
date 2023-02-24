<?php

namespace App\Controller;

use App\Entity\Log;
use App\Entity\Audio;
use App\Entity\Costanti;
use App\Entity\Feeling;
use App\Entity\Genere;
use App\Entity\Strumenti;
use App\Entity\Utenti;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class AudioController extends AbstractController {
    use  \App\Helper\Helper;

    public function __construct(RequestStack $requestStack) {
        $this->requestStack = $requestStack;
    }


    #[Route('/', name: 'home')]
    #[Route('/brani', name: 'listaAudio')]
    public function listaAudio(ManagerRegistry $doctrine, Request $request): Response {
        $em = $doctrine->getManager();
        $params = $request->query->all();
        $user = $this->getUser();

        $getAudio = $em->getRepository(Audio::class)->findAudio($params);
        $getGeneri = $em->getRepository(Genere::class)->findGenere([]);
        $getFeeling = $em->getRepository(Feeling::class)->findFeeling([]);
        $getStrumenti = $em->getRepository(Strumenti::class)->findStrumenti([]);


        $statisticheUtenti =  $em->getRepository(Log::class)->findBy(["idUtente" => $user->getId(), "tipo" => "view-audio"]);
        $statisticheUtentiArray = [];
        if ($statisticheUtenti != null) {
            foreach ($statisticheUtenti as $item => $value) {
                $statisticheUtentiArray[$value->getIdAudio()][] = $value;
            }
        }



        $statisticheUtentiRichieste =  $em->getRepository(Log::class)->findBy(["idUtente" => $user->getId(), "tipo" => "richiesta-audio"]);
        $statisticheUtentiRichiesteArray = [];
        if ($statisticheUtentiRichieste != null) {
            foreach ($statisticheUtentiRichieste as $item => $value) {
                $statisticheUtentiRichiesteArray[$value->getIdAudio()][] = $value;
            }
        }

        if (isset($params['stato']) && $params['stato'] != "") {
            if ($params['stato'] == "ascoltati") {
                foreach ($getAudio as $item => $value) {
                    if (!isset($statisticheUtentiArray[$value->getId()])) {
                        unset($getAudio[$item]);
                    }
                }
            }
            if ($params['stato'] == "richiesti") {
                foreach ($getAudio as $item => $value) {
                    if (!isset($statisticheUtentiRichiesteArray[$value->getId()])) {
                        unset($getAudio[$item]);
                    }
                }
            }
        }

        $arrayFilm = [];
        $getAudioAll = $em->getRepository(Audio::class)->findAudio([]);
        foreach ($getAudioAll as $item => $value) {
            if (!in_array($value->getFilmSerieNome(), $arrayFilm)) {
                $arrayFilm[] = $value->getFilmSerieNome();
            }
        }




        //dump($getAudio);

        return $this->render('audio/lista-audio.html.twig', [
            "sidebarActive" => "audio",
            "profileMenu" => "audio",
            "SEO_title" => "Audio",
            "listaAudio" => $getAudio,
            "statisticheUtenti" => $statisticheUtentiArray,
            "statisticheUtentiRichieste" => $statisticheUtentiRichiesteArray,
            "listaGenere" => $getGeneri,
            "listaFeeling" => $getFeeling,
            "listaStrumenti" => $getStrumenti,
            "listaSerieFilm" => $arrayFilm,
            "filtri" => $params
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/audio/{id}', name: 'audioNewUpdate', methods: ['POST'])]
    public function audioNewUpdate(Request $request, $id, ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();
        $data = $request->request->all();

        if ($id == "new") {
            $audio = new Audio();
        } else {
            $audio = $em->getRepository(Audio::class)->findOneBy(array('id' => $id));
        }

        if (isset($data['codice'])) { $audio->setCodice($data['codice']); }
        if (isset($data['rep'])) { $audio->setRep($data['rep']); }
        if (isset($data['titolo'])) { $audio->setTitolo($data['titolo']); }
        if (isset($data['autori'])) { $audio->setAutori($data['autori']); }
        if (isset($data['filmSerie'])) { $audio->setFilmSerie($data['filmSerie']); }
        if (isset($data['filmSerieNome'])) { $audio->setFilmSerieNome($data['filmSerieNome']); }
        if (isset($data['note'])) { $audio->setNote($data['note']); }
        if (isset($data['durata'])) { $audio->setDurata($data['durata']); }
        if (isset($data['editore'])) { $audio->setEditore($data['editore']); }
        if (isset($data['licenza'])) { $audio->setLicenza($data['licenza']); }
        if (isset($data['scf'])) { $audio->setScf($data['scf']); }
        if (isset($data['produttore'])) { $audio->setProduttore($data['produttore']); }
        if (isset($data['isrc'])) { $audio->setIsrc($data['isrc']); }
        if (isset($data['ean'])) { $audio->setEan($data['ean']); }
        if (isset($data['mastered'])) { $audio->setMastered($data['mastered']); }
        if (isset($data['stem'])) { $audio->setStem($data['stem']); }
        if (isset($data['versioni'])) { $audio->setVersioni($data['versioni']); }

        //gestione multiselect audioGenere
        foreach ($audio->getAudioGenere() as $item => $value) {
            $audio->removeAudioGenere($value);
        }
        if (isset($data['audioGenere'])) {
            foreach ($data['audioGenere'] as $item => $value) {
                $genere = $em->getRepository(Genere::class)->findOneBy(array('id' => $value));
                $audio->addAudioGenere($genere);
            }
        }
        //gestione multiselect audioFeeling
        foreach ($audio->getAudioFeeling() as $item => $value) {
            $audio->removeAudioFeeling($value);
        }
        if (isset($data['audioFeeling'])) {
            foreach ($data['audioFeeling'] as $item => $value) {
                $feeling = $em->getRepository(Feeling::class)->findOneBy(array('id' => $value));
                $audio->addAudioFeeling($feeling);
            }
        }
        //gestione multiselect audioStrumenti
        foreach ($audio->getAudioStrumenti() as $item => $value) {
            $audio->removeAudioStrumenti($value);
        }
        if (isset($data['audioStrumenti'])) {
            foreach ($data['audioStrumenti'] as $item => $value) {
                $strumenti = $em->getRepository(Strumenti::class)->findOneBy(array('id' => $value));
                $audio->addAudioStrumenti($strumenti);
            }
        }
        
        $audio->setDataModifica(new \DateTime());
        

        $em->persist($audio);
        $em->flush(); //esegue

        if ($id == "new") {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_CREATE_TITLE, "text" => str_replace("{elemento}", $audio->getTitolo(), Costanti::TOAST_CREATE_TEXT)]);
        } else {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_UPDATE_TITLE, "text" => str_replace("{elemento}", $audio->getTitolo(), Costanti::TOAST_UPDATE_TEXT)]);
        }

        return $this->redirect($this->generateUrl('schedaAudio', ['id' => $audio->getId()]));
    }

    #[Route('/audio/{id}', name: 'schedaAudio', methods: ['GET'])]
    public function schedaAudio($id, ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();

        //gli utenti ROLE_USER non possono creare gli audio
        if ($id == "new" && $this->isGranted('ROLE_USER') == 1) {
            return $this->redirect($this->generateUrl('listaAudio'));
        }

        $audio = $em->getRepository(Audio::class)->findOneBy(array('id' => $id));
        if ($audio == null && $id != "new") {
            return $this->redirect($this->generateUrl('listaAudio'));
        }

        //dump($audio);

        $getAudioGenere = $em->getRepository(Genere::class)->findBy([]);
        $getAudioFeeling = $em->getRepository(Feeling::class)->findBy([]);
        $getAudioStrumenti = $em->getRepository(Strumenti::class)->findBy([]);


        $statisticheAscolti =  $em->getRepository(Log::class)->findBy(["idAudio" => $id, "tipo" => "view-audio"]);
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

        $statisticheRichieste =  $em->getRepository(Log::class)->findBy(["idAudio" => $id, "tipo" => "richiesta-audio"]);
        $statisticheRichiesteArray = [];
        foreach ($statisticheRichieste as $item => $value) {
            $utente =  $em->getRepository(Utenti::class)->findOneBy(["id" => $value->getIdUtente()]);
            if ($utente != null) {
                $statisticheRichiesteArray[] =  array(
                    "utente" => $utente->getNome() . " " . $utente->getCognome(),
                    "email" => $utente->getUserIdentifier(),
                    "dataRichiesta" => $value->getDataCreazione()->format("d/m/Y H:i"),
                );
            }
        }



        $template = 'audio/scheda-audio.html.twig';
        if ($this->isGranted('ROLE_USER')) {
            $template = 'audio/vista-audio.html.twig';
        }

        //non contare se siamo admin
        if ($this->isGranted('ROLE_ADMIN') != 1) {
            $log = new Log();
            $log->setIdUtente($this->getUser()->getId());
            $log->setIdAudio($id);
            $log->setTipo("view-audio");
            $em->persist($log);
            $em->flush();
        }

        return $this->render($template, [
            "sidebarActive" => "audio",
            "profileMenu" => "audio",
            "SEO_title" => "Audio " . ($audio != null ? $audio->getTitolo() : "nuovo"),
            "audio" => $audio,
            "listaGenere" => $getAudioGenere,
            "listaFeeling" => $getAudioFeeling,
            "listaStrumenti" => $getAudioStrumenti,
            "statisticheAscolti" => $statisticheAscoltiArray,
            "statisticheRichieste" => $statisticheRichiesteArray
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/delete/audio/{id}', name: 'deleteAudio', methods: ['POST'])]
    public function deleteAudio($id, ManagerRegistry $doctrine) {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Audio::class);
        $getElemento = $repository->findOneBy(["id"=> $id]);

        $em->remove($getElemento);
        $em->flush();

        $statisticheAudio =  $em->getRepository(Log::class)->findBy(["idAudio" => $id]);
        if ($statisticheAudio != null) {
            foreach ($statisticheAudio as $item => $value) {
                $em->remove($value);
                $em->flush();
            }
        }


        $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_DELETE_TITLE, "text" => str_replace("{elemento}", $getElemento->getTitolo(), Costanti::TOAST_DELETE_TEXT)]);
        return $this->redirect($this->generateUrl('listaAudio'));
    }


    #[Route('/richiesta/audio/{id}', name: 'richiestaAudio', methods: ['POST'])]
    public function richiestaAudio($id, ManagerRegistry $doctrine, MailerInterface $mailer) {
        $em = $doctrine->getManager();
        $user = $this->getUser();

        $repository = $em->getRepository(Audio::class);
        $getElemento = $repository->findOneBy(["id"=> $id]);

        //inviare email
        $email = (new TemplatedEmail())
            ->subject('EPMUSIC - Nuova richiesta info brano: ' . $getElemento->getTitolo())
            ->from(Costanti::EMAIL_INFO)
            ->to(Costanti::EMAIL_INFO_RICHIESTA)
            ->htmlTemplate('emails/emailRichiestaAudio.html.twig')
            ->context(array(
                'utente' => $user,
                'audio' => $getElemento
            ));
        $mailer->send($email);

        $log = new Log();
        $log->setIdUtente($this->getUser()->getId());
        $log->setIdAudio($id);
        $log->setTipo("richiesta-audio");
        $em->persist($log);
        $em->flush();

        $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_RICHIESTA_TITLE, "text" => str_replace("{elemento}", $getElemento->getTitolo(), Costanti::TOAST_RICHIESTA_TEXT)]);
        return $this->redirect($this->generateUrl('schedaAudio', ['id' => $getElemento->getId()]));
    }




}
