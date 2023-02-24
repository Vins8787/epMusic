<?php

namespace App\Controller;

use App\Entity\Audio;
use App\Entity\Feeling;
use App\Entity\Genere;
use App\Entity\Log;
use App\Entity\Servizi;
use App\Entity\ServiziLavori;
use App\Entity\Strumenti;
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

use App\Entity\Costanti;
use App\Entity\Utenti;
use App\Entity\Gruppi;


class DefaultController extends AbstractController {
    use  \App\Helper\Helper;

    public function __construct(RequestStack $requestStack) {
        $this->requestStack = $requestStack;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request,  MailerInterface $mailer, ManagerRegistry $doctrine) {
        $em = $doctrine->getManager();

        $_username = $request->request->get('_username');
        $_password = $request->request->get('_password');

        //se abbiamo inserito username e password
        if (isset($_username) && isset($_password) && $_username != "" && $_password != "") {
            //Cerchiamo l'username nel database
            $user = $em->getRepository(Utenti::class)->findOneBy(array('userIdentifier' => $_username));

            if (!$user) {
                $this->addFlash("noticeLogin", Costanti::ERRORE_LOGIN);
                return $this->redirect($this->generateUrl('login'));
            }
            if ($user->getIsActive() == 0) {
                $this->addFlash("noticeLogin", str_replace("<username>", $_username, Costanti::ACCOUNT_NO_CONFERMATO));
                return $this->redirect($this->generateUrl('login', array('_username' => $_username)));
            } else if ($user->getIsActive() == 2) {
                $this->addFlash("noticeLogin", str_replace("<username>", $_username, Costanti::ACCOUNT_RIFIUTATO));
                return $this->redirect($this->generateUrl('login', array('_username' => $_username)));
            }

            //confrontiamo le password
            if (!password_verify($_password, $user->getPassword())) {
                $this->addFlash("noticeLogin", Costanti::ERRORE_LOGIN);
                return $this->render('login.html.twig', array( 'data' => '' ));
            }

            // The password matches ! then proceed to set the user in session
            // The third parameter "main" can change according to the name of your firewall in security.yml
            $token = new UsernamePasswordToken($user, 'main', $user->getRoles());
            $this->container->get('security.token_storage')->setToken($token);
            // If the firewall name is not main, then the set value would be instead:
            // $this->get('session')->set('_security_XXXFIREWALLNAMEXXX', serialize($token));

            $session = $this->requestStack->getSession();
            $session->set('_security_main', serialize($token));

            $session->set('id', $user->getId());
            $session->set('groupId', $user->getGruppo()->getId());

            $log = new Log();
            $log->setIdUtente($user->getId());
            $log->setTipo("login");
            $em->persist($log);
            $em->flush();

            return $this->redirect($this->generateUrl('listaAudio'));
        }

        return $this->render('login.html.twig', [
            "SEO_title" => "Login"
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout()
    {
        // controller can be blank: it will never be called!
        throw new \Exception('Don\'t forget to activate logout in security.yaml');
    }



    /**
     * @Route("/recupera-password", name="recuperaPassword")
     */
    public function recuperaPassword(Request $request, MailerInterface $mailer, ManagerRegistry $doctrine) {
        $em = $doctrine->getManager();

        $recuperaPassword = $request->request->get('_username');
        $username = "";

        if (isset($recuperaPassword) && $recuperaPassword != "") {
            $repository = $em->getRepository(Utenti::class);
            $getUtente = $repository->findOneBy(array("userIdentifier" => $recuperaPassword));

            if ($getUtente == null) {
                $this->addFlash("noticeLogin", "La e-mail '" . $recuperaPassword . "' non esiste");
                return $this->redirect($this->generateUrl('recuperaPassword'));
            } else {

                if ($getUtente->getIsActive() == 0) {
                    $this->addFlash("noticeLogin", Costanti::ERRORE_REGISTRAZIONE_IN_APPROVAZIONE);
                    return $this->redirect($this->generateUrl('recuperaPassword'));
                } else if ($getUtente->getIsActive() == 2) {
                    $this->addFlash("noticeLogin", Costanti::ACCOUNT_NO_CONFERMATO);
                    return $this->redirect($this->generateUrl('recuperaPassword'));
                } else {
                    $newPassword = $this->randomPassword();
                    $getUtente->setPassword(password_hash($newPassword, PASSWORD_DEFAULT));

                    $em->persist($getUtente);
                    $em->flush();

                    //inviare email
                    $email = (new TemplatedEmail())
                        ->subject('EPMUSIC - Richiesta nuova password ' . $recuperaPassword)
                        ->from(Costanti::EMAIL_INFO)
                        ->to($recuperaPassword)
                        ->htmlTemplate('emails/emailRecuperaPassword.html.twig')
                        ->context(array(
                            'utente' => $getUtente,
                            'newPassword' => $newPassword
                        ));
                    $mailer->send($email);

                    $this->addFlash("successLogin", "E' stata inviata una e-mail con la nuova password a " . $recuperaPassword);
                    $username = $getUtente->getUserIdentifier();
                }
            }
        }

        return $this->render('recupera-password.html.twig', [
                "SEO_title" => "Recupera password",
                "username" => $username
            ]
        );
    }


    #[Route('/importCatalogo', name: 'importCatalogo')]
    public function importCatalogo(ManagerRegistry $doctrine): Response {
        $em = $doctrine->getManager();

        echo "stop sicura";
        exit;

        $stmt = $em->getConnection()->prepare('SELECT * FROM catalogo_edizioni where 1');
        $resultSet = $stmt->executeQuery();
        $resultSet = $resultSet->fetchAllAssociative();
        foreach ($resultSet as $item => $value) {
            $audio = new Audio();
            $audio->setCodice($value['CODE']);
            $audio->setRep($value['REP']);
            $audio->setTitolo($value['Titolo']);
            $audio->setAutori($value['Autore']);
            $audio->setFilmSerieNome($value['FilmSerie']);
            $audio->setFilmSerie($value['FS']);
            $audio->setNote($value['Note']);
            $audio->setDurata($value['Durata']);
            $audio->setEditore($value['Editore']);
            $audio->setLicenza($value['Licenza'] == "X" ? 1 : 0);
            $audio->setScf($value['SCF'] == "X" ? 1 : 0);
            $audio->setProduttore($value['Produttore']);
            $audio->setIsrc($value['ISRC']);
            $audio->setEan($value['EAN']);
            $audio->setMastered($value['Mastered']);
            $audio->setStem($value['Stem']);
            $audio->setVersioni($value['Versioni'] != "" ? $value['Versioni'] : 0);

            //generi
            $generi = str_replace(" ", "", $value['Genere']);
            $generi = explode(",", $generi);
            if (!empty($generi) && $generi[0] != "") {
                foreach ($generi as $i => $v) {
                    $gen = $em->getRepository(Genere::class)->findOneBy(array('codice' => $v));
                    if ($gen != null) {
                        $audio->addAudioGenere($gen);
                    }
                }
            }

            //feeling
            $feeling = str_replace(" ", "", $value['Feeling']);
            $feeling = explode(",", $feeling);
            if (!empty($feeling) && $feeling[0] != "") {
                foreach ($feeling as $i => $v) {
                    $gen = $em->getRepository(Feeling::class)->findOneBy(array('codice' => $v));
                    if ($gen != null) {
                        $audio->addAudioFeeling($gen);
                    }
                }
            }

            //strumenti
            $strumenti = str_replace(" ", "", $value['Strumenti']);
            $strumenti = explode(",", $strumenti);
            if (!empty($strumenti) && $strumenti[0] != "") {
                foreach ($strumenti as $i => $v) {
                    $gen = $em->getRepository(Strumenti::class)->findOneBy(array('codice' => $v));
                    if ($gen != null) {
                        $audio->addAudioStrumenti($gen);
                    }
                }
            }

            $em->persist($audio);
            $em->flush();
        }

        $getServizi = $em->getRepository(Audio::class)->findBy([]);
        if ($getServizi != null) {
        }
        //dump($resultSet);

        return $this->render('login.html.twig', [
            "SEO_title" => "Login"
        ]);
    }


}
