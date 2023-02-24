<?php

namespace App\Controller;

use App\Entity\Audio;
use App\Entity\Feeling;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Costanti;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class FeelingController extends AbstractController {

    
    #[Route('/feeling', name: 'listaFeeling')]
    public function listaFeeling(ManagerRegistry $doctrine, Request $request): Response {
        $em = $doctrine->getManager();
        $params = $request->query->all();

        $getFeeling = $em->getRepository(Feeling::class)->findFeeling($params);

        //dump($getFeeling);

        return $this->render('impostazioni/lista-feeling.html.twig', [
            "sidebarActive" => "feeling",
            "profileMenu" => "feeling",
            "SEO_title" => "Feeling",
            "listaFeeling" => $getFeeling,
            "filtri" => $params
        ]);
    }


    #[Route('/feeling/{id}', name: 'feelingNewUpdate', methods: ['POST'])]
    public function feelingNewUpdate(Request $request, $id, ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();
        $data = $request->request->all();

        if ($id == "new") {
            $feeling = new Feeling();
        } else {
            $feeling = $em->getRepository(Feeling::class)->findOneBy(array('id' => $id));
        }

        if (isset($data['codice'])) { $feeling->setCodice($data['codice']); }
        if (isset($data['nome'])) { $feeling->setNome($data['nome']); }
        $feeling->setDataModifica(new \DateTime());

        $em->persist($feeling);
        $em->flush(); //esegue

        if ($id == "new") {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_CREATE_TITLE, "text" => str_replace("{elemento}", $feeling->getNome(), Costanti::TOAST_CREATE_TEXT)]);
        } else {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_UPDATE_TITLE, "text" => str_replace("{elemento}", $feeling->getNome(), Costanti::TOAST_UPDATE_TEXT)]);
        }

        return $this->redirect($this->generateUrl('schedaFeeling', ['id' => $feeling->getId()]));
    }


    #[Route('/feeling/{id}', name: 'schedaFeeling', methods: ['GET'])]
    public function schedaFeeling($id, ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();

        $feeling = $em->getRepository(Feeling::class)->findOneBy(array('id' => $id));
        if ($feeling == null && $id != "new") {
            return $this->redirect($this->generateUrl('listaFeeling'));
        }
        //dump($feeling);

        return $this->render('impostazioni/scheda-feeling.html.twig', [
            "sidebarActive" => "feeling",
            "profileMenu" => "feeling",
            "SEO_title" => "Feeling " . ($feeling != null ? $feeling->getNome() : "nuovo"),
            "feeling" => $feeling
        ]);
    }

    #[Route('/delete/feeling/{id}', name: 'deleteFeeling', methods: ['POST'])]
    public function deleteFeeling($id, ManagerRegistry $doctrine) {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Feeling::class);
        $getElemento = $repository->findOneBy(["id"=> $id]);

        //controllo se esistono audio con questi generi (nel caso blocco)
        $checkFeelingAudio = $em->getRepository(Audio::class)->findBy([]);
        foreach ($checkFeelingAudio as $item => $value) {
            foreach ($value->getAudioFeeling() as $i => $v) {
                if ($v->getId() == $id) {
                    $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_ERRORE_DELETE_TITLE, "text" => str_replace("{elemento}", $getElemento->getNome(), Costanti::TOAST_ERRORE_DELETE2_TEXT)]);
                    return $this->redirect($this->generateUrl('listaFeeling'));
                }
            }
        }

        $em->remove($getElemento);
        $em->flush();

        $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_DELETE_TITLE, "text" => str_replace("{elemento}", $getElemento->getNome(), Costanti::TOAST_DELETE_TEXT)]);
        return $this->redirect($this->generateUrl('listaFeeling'));
    }


}
