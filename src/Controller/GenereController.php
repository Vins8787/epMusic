<?php

namespace App\Controller;

use App\Entity\Audio;
use App\Entity\Genere;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Costanti;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class GenereController extends AbstractController {

    #[Route('/genere', name: 'listaGenere')]
    public function listaGenere(ManagerRegistry $doctrine, Request $request): Response {
        $em = $doctrine->getManager();
        $params = $request->query->all();

        $getGeneri = $em->getRepository(Genere::class)->findGenere($params);

        //dump($getGeneri);

        return $this->render('impostazioni/lista-genere.html.twig', [
            "sidebarActive" => "genere",
            "profileMenu" => "genere",
            "SEO_title" => "Generi",
            "listaGenere" => $getGeneri,
            "filtri" => $params
        ]);
    }


    #[Route('/genere/{id}', name: 'genereNewUpdate', methods: ['POST'])]
    public function genereNewUpdate(Request $request, $id, ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();
        $data = $request->request->all();

        if ($id == "new") {
            $genere = new Genere();
        } else {
            $genere = $em->getRepository(Genere::class)->findOneBy(array('id' => $id));
        }

        if (isset($data['codice'])) { $genere->setCodice($data['codice']); }
        if (isset($data['nome'])) { $genere->setNome($data['nome']); }
        $genere->setDataModifica(new \DateTime());

        $em->persist($genere);
        $em->flush(); //esegue

        if ($id == "new") {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_CREATE_TITLE, "text" => str_replace("{elemento}", $genere->getNome(), Costanti::TOAST_CREATE_TEXT)]);
        } else {
            $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_UPDATE_TITLE, "text" => str_replace("{elemento}", $genere->getNome(), Costanti::TOAST_UPDATE_TEXT)]);
        }

        return $this->redirect($this->generateUrl('schedaGenere', ['id' => $genere->getId()]));
    }


    #[Route('/genere/{id}', name: 'schedaGenere', methods: ['GET'])]
    public function schedaGenere($id, ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();

        $genere = $em->getRepository(Genere::class)->findOneBy(array('id' => $id));
        if ($genere == null && $id != "new") {
            return $this->redirect($this->generateUrl('listaGenere'));
        }
        //dump($genere);

        return $this->render('impostazioni/scheda-genere.html.twig', [
            "sidebarActive" => "genere",
            "profileMenu" => "genere",
            "SEO_title" => "Genere " . ($genere != null ? $genere->getNome() : "nuovo"),
            "genere" => $genere
        ]);
    }

    #[Route('/delete/genere/{id}', name: 'deleteGenere', methods: ['POST'])]
    public function deleteGenere($id, ManagerRegistry $doctrine) {
        $em = $doctrine->getManager();
        $repository = $em->getRepository(Genere::class);
        $getElemento = $repository->findOneBy(["id"=> $id]);

        //controllo se esistono audio con questi generi (nel caso blocco)
        $checkGenereAudio = $em->getRepository(Audio::class)->findBy([]);
        foreach ($checkGenereAudio as $item => $value) {
            foreach ($value->getAudioGenere() as $i => $v) {
                if ($v->getId() == $id) {
                    $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_ERRORE_DELETE_TITLE, "text" => str_replace("{elemento}", $getElemento->getNome(), Costanti::TOAST_ERRORE_DELETE2_TEXT)]);
                    return $this->redirect($this->generateUrl('listaGenere'));
                }
            }
        }

        $em->remove($getElemento);
        $em->flush();

        $this->addFlash("toastSuccess", ["title" => Costanti::TOAST_DELETE_TITLE, "text" => str_replace("{elemento}", $getElemento->getNome(), Costanti::TOAST_DELETE_TEXT)]);
        return $this->redirect($this->generateUrl('listaGenere'));
    }


}
