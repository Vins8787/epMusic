<?php

namespace App\Controller;

use App\Entity\Audio;
use App\Entity\Strumenti;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Costanti;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class StrumentiController extends AbstractController {

    #[Route('/strumenti', name: 'listaStrumenti')]
    public function listaStrumenti(ManagerRegistry $doctrine, Request $request): Response {
        $em = $doctrine->getManager();
        $params = $request->query->all();

        $getStrumenti = $em->getRepository(Strumenti::class)->findStrumenti($params);

        //dump($getStrumenti);

        return $this->render('impostazioni/lista-strumenti.html.twig', [
            "sidebarActive" => "strumenti",
            "profileMenu" => "strumenti",
            "SEO_title" => "Strumenti",
            "listaStrumenti" => $getStrumenti,
            "filtri" => $params
        ]);
    }


    #[Route('/strumenti/{id}', name: 'strumentiNewUpdate', methods: ['POST'])]
    public function strumentiNewUpdate(Request $request, $id, ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();
        $data = $request->request->all();

        if ($id == "new") {
            $strumenti = new Strumenti();
        } else {
            $strumenti = $em->getRepository(Strumenti::class)->findOneBy(array('id' => $id));
        }

        if (isset($data['codice'])) { $strumenti->setCodice($data['codice']); }
        if (isset($data['nome'])) { $strumenti->setNome($data['nome']); }
        $strumenti->setDataModifica(new \DateTime());

        $em->persist($strumenti);
        $em->flush(); //esegue

        if ($id == "new") {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_CREATE_TITLE, "text" => str_replace("{elemento}", $strumenti->getNome(), Costanti::TOAST_CREATE_TEXT)]);
        } else {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_UPDATE_TITLE, "text" => str_replace("{elemento}", $strumenti->getNome(), Costanti::TOAST_UPDATE_TEXT)]);
        }

        return $this->redirect($this->generateUrl('schedaStrumenti', ['id' => $strumenti->getId()]));
    }


    #[Route('/strumenti/{id}', name: 'schedaStrumenti', methods: ['GET'])]
    public function schedaStrumenti($id, ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();

        $strumenti = $em->getRepository(Strumenti::class)->findOneBy(array('id' => $id));
        if ($strumenti == null && $id != "new") {
            return $this->redirect($this->generateUrl('listaStrumenti'));
        }
        //dump($strumenti);

        return $this->render('impostazioni/scheda-strumenti.html.twig', [
            "sidebarActive" => "strumenti",
            "profileMenu" => "strumenti",
            "SEO_title" => "Strumenti " . ($strumenti != null ? $strumenti->getNome() : "nuovo"),
            "strumenti" => $strumenti
        ]);
    }

    #[Route('/delete/strumenti/{id}', name: 'deleteStrumenti', methods: ['POST'])]
    public function deleteStrumenti($id, ManagerRegistry $doctrine) {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Strumenti::class);
        $getElemento = $repository->findOneBy(["id"=> $id]);

        //controllo se esistono audio con questi generi (nel caso blocco)
        $checkStrumentiAudio = $em->getRepository(Audio::class)->findBy([]);
        foreach ($checkStrumentiAudio as $item => $value) {
            foreach ($value->getAudioStrumenti() as $i => $v) {
                if ($v->getId() == $id) {
                    $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_ERRORE_DELETE_TITLE, "text" => str_replace("{elemento}", $getElemento->getNome(), Costanti::TOAST_ERRORE_DELETE2_TEXT)]);
                    return $this->redirect($this->generateUrl('listaStrumenti'));
                }
            }
        }

        $em->remove($getElemento);
        $em->flush();

        $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_DELETE_TITLE, "text" => str_replace("{elemento}", $getElemento->getNome(), Costanti::TOAST_DELETE_TEXT)]);
        return $this->redirect($this->generateUrl('listaStrumenti'));
    }


}
