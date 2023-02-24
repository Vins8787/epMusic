<?php

namespace App\Controller;

use App\Entity\Audio;
use App\Entity\Log;
use App\Entity\Statistiche;
use App\Entity\Utenti;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use App\Entity\Costanti;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class StatisticheController extends AbstractController {

    // ####################################################################################
    // ###################################### AUDIO ######################################
    // ####################################################################################

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/statisticheAudio', name: 'listaStatisticheAudio')]
    public function listaStatisticheAudio(ManagerRegistry $doctrine, Request $request): Response {
        $em = $doctrine->getManager();
        $params = $request->query->all();

        $getAudio = $em->getRepository(Audio::class)->findAudio($params);

        $statisticheAudio =  $em->getRepository(Log::class)->findBy(["tipo" => "view-audio"]);
        $statisticheAudioArray = [];
        if ($statisticheAudio != null) {
            foreach ($statisticheAudio as $item => $value) {
                $statisticheAudioArray[$value->getIdAudio()][] = $value;
            }
        }

        $statisticheAudioRichieste =  $em->getRepository(Log::class)->findBy(["tipo" => "richiesta-audio"]);
        $statisticheAudioRichiesteArray = [];
        if ($statisticheAudioRichieste != null) {
            foreach ($statisticheAudioRichieste as $item => $value) {
                $statisticheAudioRichiesteArray[$value->getIdAudio()][] = $value;
            }
        }

        if (isset($params['ord']) && ($params['ord'] == 'ascoltiAsc' || $params['ord'] == 'ascoltiDesc') ) {
            foreach ($getAudio as $item => $value) {
                if (isset($statisticheAudioArray[$value->getId()])) {
                    $value->ascolti = count($statisticheAudioArray[$value->getId()]);
                } else {
                    $value->ascolti = 0;
                }
            }
            if ($params['ord'] == 'ascoltiAsc') {
                usort($getAudio, function($a, $b) {
                    return $a->ascolti <=> $b->ascolti; //asc
                });
            }
            if ($params['ord'] == 'ascoltiDesc') {
                usort($getAudio, function($a, $b) {
                    return $b->ascolti <=> $a->ascolti; //desc
                });
            }
        }

        if (isset($params['ord']) && ($params['ord'] == 'richiesteAsc' || $params['ord'] == 'richiesteDesc')) {
            foreach ($getAudio as $item => $value) {
                if (isset($statisticheAudioRichiesteArray[$value->getId()])) {
                    $value->richieste = count($statisticheAudioRichiesteArray[$value->getId()]);
                } else {
                    $value->richieste = 0;
                }
            }
            if ($params['ord'] == 'richiesteAsc') {
                usort($getAudio, function($a, $b) {
                    return $a->richieste <=> $b->richieste; //asc
                });
            }
            if ($params['ord'] == 'richiesteDesc') {
                usort($getAudio, function($a, $b) {
                    return $b->richieste <=> $a->richieste; //desc
                });
            }
        }

        return $this->render('statistiche/lista-statistiche-audio.html.twig', [
            "sidebarActive" => "statisticheAudio",
            "profileMenu" => "statisticheAudio",
            "SEO_title" => "Statistiche audio",
            "listaAudio" => $getAudio,
            "statisticheAudio" => $statisticheAudioArray,
            "statisticheAudioRichieste" => $statisticheAudioRichiesteArray,
            "filtri" => $params
        ]);
    }

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/statisticheAudio/{id}', name: 'vistaStatisticheAudio', methods: ['GET'])]
    public function vistaStatisticheAudio(ManagerRegistry $doctrine, Request $request, $id): Response {
        $em = $doctrine->getManager();
        $params = $request->query->all();

        $getAudio = $em->getRepository(Audio::class)->findOneBy(["id" => $id]);

        $getUtenti = $em->getRepository(Utenti::class)->findBy([]);
        $arrayUtenti = [];
        if ($getUtenti != null) {
            foreach ($getUtenti as $item => $value) {
                $arrayUtenti[$value->getId()] = $value;
            }
        }

        $statisticheAudio =  $em->getRepository(Log::class)->findBy(["idAudio" => $id, "tipo" => "view-audio"]);
        $statisticheAudioArray = [];
        if ($statisticheAudio != null) {
            foreach ($statisticheAudio as $item => $value) {
                $statisticheAudioArray[$value->getIdUtente()][] = $value;
            }
        }

        $statisticheAudioRichieste =  $em->getRepository(Log::class)->findBy(["idAudio" => $id, "tipo" => "richiesta-audio"]);
        $statisticheAudioRichiesteArray = [];
        if ($statisticheAudioRichieste != null) {
            foreach ($statisticheAudioRichieste as $item => $value) {
                $statisticheAudioRichiesteArray[$value->getIdUtente()][] = $value;
            }
        }



        if (isset($params['ord']) && ($params['ord'] == 'ascoltiAsc' || $params['ord'] == 'ascoltiDesc') ) {
            foreach ($statisticheAudioArray as $item => $value) {
                if (!isset($statisticheAudioArray[$item]['ascolti'])) {
                    $statisticheAudioArray[$item]['ascolti'] = 0;
                }
                if (!isset($statisticheAudioArray[$item]['richieste'])) {
                    $statisticheAudioArray[$item]['richieste'] = 0;
                }
                $statisticheAudioArray[$item]['ascolti'] = isset($statisticheAudioArray[$item]) ? $statisticheAudioArray[$item]['ascolti'] + count($statisticheAudioArray[$item]) : 0;
                $statisticheAudioArray[$item]['richieste'] = isset($statisticheAudioRichiesteArray[$item]) ? $statisticheAudioArray[$item]['richieste'] + count($statisticheAudioRichiesteArray[$item]) : 0;
            }
            if ($params['ord'] == 'ascoltiAsc') {
                uasort($statisticheAudioArray, function($a, $b) {
                    return $a['ascolti'] <=> $b['ascolti']; //asc
                });
            }
            if ($params['ord'] == 'ascoltiDesc') {
                uasort($statisticheAudioArray, function($a, $b) {
                    return $b['ascolti'] <=> $a['ascolti']; //desc
                });
            }

            foreach ($statisticheAudioArray as $item => $value) {
                unset($statisticheAudioArray[$item]['ascolti']);
                unset($statisticheAudioArray[$item]['richieste']);
            }
        }

        if (isset($params['ord']) && ($params['ord'] == 'richiesteAsc' || $params['ord'] == 'richiesteDesc') ) {
            foreach ($statisticheAudioArray as $item => $value) {
                if (!isset($statisticheAudioArray[$item]['ascolti'])) {
                    $statisticheAudioArray[$item]['ascolti'] = 0;
                }
                if (!isset($statisticheAudioArray[$item]['richieste'])) {
                    $statisticheAudioArray[$item]['richieste'] = 0;
                }
                $statisticheAudioArray[$item]['ascolti'] = isset($statisticheAudioArray[$item]) ? $statisticheAudioArray[$item]['ascolti'] + count($statisticheAudioArray[$item]) : 0;
                $statisticheAudioArray[$item]['richieste'] = isset($statisticheAudioRichiesteArray[$item]) ? $statisticheAudioArray[$item]['richieste'] + count($statisticheAudioRichiesteArray[$item]) : 0;
            }
            if ($params['ord'] == 'richiesteAsc') {
                uasort($statisticheAudioArray, function($a, $b) {
                    return $a['richieste'] <=> $b['richieste']; //asc
                });
            }
            if ($params['ord'] == 'richiesteDesc') {
                uasort($statisticheAudioArray, function($a, $b) {
                    return $b['richieste'] <=> $a['richieste']; //desc
                });
            }
            foreach ($statisticheAudioArray as $item => $value) {
                unset($statisticheAudioArray[$item]['ascolti']);
                unset($statisticheAudioArray[$item]['richieste']);
            }
        }
        
        //dump($statisticheAudioArray);

        return $this->render('statistiche/vista-statistiche-audio.html.twig', [
            "sidebarActive" => "statisticheAudio",
            "profileMenu" => "statisticheAudio",
            "SEO_title" => "Statistiche audio",
            "audio" => $getAudio,
            "statisticheAudio" => $statisticheAudioArray,
            "statisticheAudioRichieste" => $statisticheAudioRichiesteArray,
            "arrayUtenti" => $arrayUtenti,
            "filtri" => $params
        ]);
    }


    // ####################################################################################
    // ###################################### UTENTI ######################################
    // ####################################################################################

    #[IsGranted('ROLE_ADMIN')]
    #[Route('/statisticheUtenti', name: 'listaStatisticheUtenti')]
    public function listaStatisticheUtenti(ManagerRegistry $doctrine, Request $request): Response {
        $em = $doctrine->getManager();
        $params = $request->query->all();

        $getUtenti = $em->getRepository(Utenti::class)->findUtenti($params);

        $statisticheUtenti =  $em->getRepository(Log::class)->findBy(["tipo" => "view-audio"]);
        $statisticheUtentiArray = [];
        if ($statisticheUtenti != null) {
            foreach ($statisticheUtenti as $item => $value) {
                $statisticheUtentiArray[$value->getIdUtente()][] = $value;
            }
        }
        $statisticheUtentiRichiesta =  $em->getRepository(Log::class)->findBy(["tipo" => "richiesta-audio"]);
        $statisticheUtentiRichiesteArray = [];
        if ($statisticheUtentiRichiesta != null) {
            foreach ($statisticheUtentiRichiesta as $item => $value) {
                $statisticheUtentiRichiesteArray[$value->getIdUtente()][] = $value;
            }
        }


        if (isset($params['ord']) && ($params['ord'] == 'ascoltiAsc' || $params['ord'] == 'ascoltiDesc') ) {
            foreach ($getUtenti as $item => $value) {
                if (isset($statisticheUtentiArray[$value->getId()])) {
                    $value->ascolti = count($statisticheUtentiArray[$value->getId()]);
                } else {
                    $value->ascolti = 0;
                }
            }
            if ($params['ord'] == 'ascoltiAsc') {
                usort($getUtenti, function($a, $b) {
                    return $a->ascolti <=> $b->ascolti; //asc
                });
            }
            if ($params['ord'] == 'ascoltiDesc') {
                usort($getUtenti, function($a, $b) {
                    return $b->ascolti <=> $a->ascolti; //desc
                });
            }
        }

        if (isset($params['ord']) && ($params['ord'] == 'richiesteAsc' || $params['ord'] == 'richiesteDesc')) {
            foreach ($getUtenti as $item => $value) {
                if (isset($statisticheUtentiRichiesteArray[$value->getId()])) {
                    $value->richieste = count($statisticheUtentiRichiesteArray[$value->getId()]);
                } else {
                    $value->richieste = 0;
                }
            }
            if ($params['ord'] == 'richiesteAsc') {
                usort($getUtenti, function($a, $b) {
                    return $a->richieste <=> $b->richieste; //asc
                });
            }
            if ($params['ord'] == 'richiesteDesc') {
                usort($getUtenti, function($a, $b) {
                    return $b->richieste <=> $a->richieste; //desc
                });
            }
        }






        return $this->render('statistiche/lista-statistiche-utenti.html.twig', [
            "sidebarActive" => "statisticheUtenti",
            "profileMenu" => "statisticheUtenti",
            "SEO_title" => "Statistiche utenti",
            "listaUtenti" => $getUtenti,
            "statisticheUtenti" => $statisticheUtentiArray,
            "statisticheUtentiRichieste" => $statisticheUtentiRichiesteArray,
            "filtri" => $params
        ]);
    }


    #[IsGranted('ROLE_ADMIN')]
    #[Route('/statisticheUtenti/{id}', name: 'vistaStatisticheUtenti', methods: ['GET'])]
    public function vistaStatisticheUtenti(ManagerRegistry $doctrine, Request $request, $id): Response {
        $em = $doctrine->getManager();
        $params = $request->query->all();

        $getUtente = $em->getRepository(Utenti::class)->findOneBy(["id" => $id]);

        $getAudio = $em->getRepository(Audio::class)->findBy([]);
        $arrayAudio = [];
        if ($getAudio != null) {
            foreach ($getAudio as $item => $value) {
                $arrayAudio[$value->getId()] = $value;
            }
        }

        $statisticheUtenti =  $em->getRepository(Log::class)->findBy(["idUtente" => $id, "tipo" => "view-audio"]);
        $statisticheUtentiArray = [];
        if ($statisticheUtenti != null) {
            foreach ($statisticheUtenti as $item => $value) {
                $statisticheUtentiArray[$value->getIdAudio()][] = $value;
            }
        }

        $statisticheUtentiRichieste =  $em->getRepository(Log::class)->findBy(["idUtente" => $id, "tipo" => "richiesta-audio"]);
        $statisticheUtentiRichiesteArray = [];
        if ($statisticheUtentiRichieste != null) {
            foreach ($statisticheUtentiRichieste as $item => $value) {
                $statisticheUtentiRichiesteArray[$value->getIdAudio()][] = $value;
            }
        }



        if (isset($params['ord']) && ($params['ord'] == 'ascoltiAsc' || $params['ord'] == 'ascoltiDesc') ) {
            foreach ($statisticheUtentiArray as $item => $value) {
                if (!isset($statisticheUtentiArray[$item]['ascolti'])) {
                    $statisticheUtentiArray[$item]['ascolti'] = 0;
                }
                if (!isset($statisticheUtentiArray[$item]['richieste'])) {
                    $statisticheUtentiArray[$item]['richieste'] = 0;
                }
                $statisticheUtentiArray[$item]['ascolti'] = isset($statisticheUtentiArray[$item]) ? $statisticheUtentiArray[$item]['ascolti'] + count($statisticheUtentiArray[$item]) : 0;
                $statisticheUtentiArray[$item]['richieste'] = isset($statisticheUtentiRichiesteArray[$item]) ? $statisticheUtentiArray[$item]['richieste'] + count($statisticheUtentiRichiesteArray[$item]) : 0;
            }
            if ($params['ord'] == 'ascoltiAsc') {
                uasort($statisticheUtentiArray, function($a, $b) {
                    return $a['ascolti'] <=> $b['ascolti']; //asc
                });
            }
            if ($params['ord'] == 'ascoltiDesc') {
                uasort($statisticheUtentiArray, function($a, $b) {
                    return $b['ascolti'] <=> $a['ascolti']; //desc
                });
            }

            foreach ($statisticheUtentiArray as $item => $value) {
                unset($statisticheUtentiArray[$item]['ascolti']);
                unset($statisticheUtentiArray[$item]['richieste']);
            }
        }

        
        if (isset($params['ord']) && ($params['ord'] == 'richiesteAsc' || $params['ord'] == 'richiesteDesc') ) {
            foreach ($statisticheUtentiArray as $item => $value) {
                if (!isset($statisticheUtentiArray[$item]['ascolti'])) {
                    $statisticheUtentiArray[$item]['ascolti'] = 0;
                }
                if (!isset($statisticheUtentiArray[$item]['richieste'])) {
                    $statisticheUtentiArray[$item]['richieste'] = 0;
                }
                $statisticheUtentiArray[$item]['ascolti'] = isset($statisticheUtentiArray[$item]) ? $statisticheUtentiArray[$item]['ascolti'] + count($statisticheUtentiArray[$item]) : 0;
                $statisticheUtentiArray[$item]['richieste'] = isset($statisticheUtentiRichiesteArray[$item]) ? $statisticheUtentiArray[$item]['richieste'] + count($statisticheUtentiRichiesteArray[$item]) : 0;
            }
            if ($params['ord'] == 'richiesteAsc') {
                uasort($statisticheUtentiArray, function($a, $b) {
                    return $a['richieste'] <=> $b['richieste']; //asc
                });
            }
            if ($params['ord'] == 'richiesteDesc') {
                uasort($statisticheUtentiArray, function($a, $b) {
                    return $b['richieste'] <=> $a['richieste']; //desc
                });
            }

            foreach ($statisticheUtentiArray as $item => $value) {
                unset($statisticheUtentiArray[$item]['ascolti']);
                unset($statisticheUtentiArray[$item]['richieste']);
            }
        }





        return $this->render('statistiche/vista-statistiche-utenti.html.twig', [
            "sidebarActive" => "statisticheUtenti",
            "profileMenu" => "statisticheUtenti",
            "SEO_title" => "Statistiche utente",
            "utente" => $getUtente,
            "statisticheUtenti" => $statisticheUtentiArray,
            "statisticheUtentiRichieste" => $statisticheUtentiRichiesteArray,
            "arrayAudio" => $arrayAudio,
            "filtri" => $params
        ]);
    }
}
