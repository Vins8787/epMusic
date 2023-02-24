<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Table(name="utenti")
 * @ORM\Entity(repositoryClass="App\Repository\UtentiRepository")
 */
class Utenti implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="nome", type="string", length=255)
     * @Assert\NotBlank
     */
    private $nome;

    /**
     * @ORM\Column(name="cognome", type="string", length=255)
     * @Assert\NotBlank
     */
    private $cognome;

    /**
     * @ORM\Column(name="userIdentifier", type="string", length=255, unique=true)
     */
    private $userIdentifier;

    /**
     * @ORM\Column(name="password", type="string", length=512)
     */
    private $password;

    /**
     * @ORM\Column(name="is_active", type="integer", nullable=true)
     */
    private $isActive;

    /**
     * @ORM\Column(name="codeActivation", type="string", length=255, nullable=true)
     */
    private $codeActivation;

    /**
     * @ORM\Column(name="dataCreazione", type="datetime")
     */
    private $dataCreazione;

    /**
     * @ORM\Column(name="dataModifica", type="datetime")
     */
    private $dataModifica;

    /**
     * @ORM\Column(name="company", type="string", length=255)
     */
    private $company;

    /**
     * @ORM\Column(name="ruolo", type="string", length=255)
     * @Assert\NotBlank
     */
    private $ruolo;


    // ##########################################################################################
    // #################################### RELAZIONI ###########################################
    // ##########################################################################################

    /**
     * @var int Something else
     * @ORM\ManyToOne(targetEntity="App\Entity\Gruppi", inversedBy="utenteGruppo", cascade={"persist"})
     */
    private $gruppo;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Comuni", inversedBy="utente", cascade={"persist"})
     */
    private $comune;



    public function __construct() {
        $this->nome = "";
        $this->cognome = "";
        $this->password = "";
        $this->isActive = 0;
        $this->codeActivation = "";
        $this->dataCreazione = new \DateTime();
        $this->dataModifica = new \DateTime();
        $this->company = "";
        $this->ruolo = "";

        $this->roles = array('ROLE_USER');
    }

    public function getId()
    {
        return $this->id;
    }
    public function setId($id) {
        $this->id = $id;
        return $this;
    }
    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }
    public function getUsername()
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier($userIdentifier)
    {
        $this->userIdentifier = $userIdentifier;
    }


    public function getSalt()
    {
        return null;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        if ($password != "") {
            $this->password = $password;
        }
    }
    public function getRoles(): array {
        $gruppo = $this->getGruppo();
        return $gruppo->getRole();
    }
    public function eraseCredentials() {
    }

    /**
     * @return string
     */
    public function getNome() {
        return $this->nome;
    }

    /**
     * @param string $nome
     */
    public function setNome(string $nome) {
        $this->nome = $nome;
    }

    /**
     * @return int
     */
    public function getGruppo() {
        return $this->gruppo;
    }

    /**
     * @param int $gruppo
     */
    public function setGruppo($gruppo) {
        $this->gruppo = $gruppo;
    }

    /**
     * @return mixed
     */
    public function getComune()
    {
        return $this->comune;
    }

    /**
     * @param mixed $comune
     */
    public function setComune($comune) {
        $this->comune = $comune;
    }

    /**
     * @return int
     */
    public function getIsActive(): int
    {
        return $this->isActive;
    }

    /**
     * @param int $isActive
     */
    public function setIsActive(int $isActive) {
        $this->isActive = $isActive;
    }

    /**
     * @return string
     */
    public function getCodeActivation() {
        return $this->codeActivation;
    }

    /**
     * @param string $codeActivation
     */
    public function setCodeActivation($codeActivation) {
        $this->codeActivation = $codeActivation;
    }

    /**
     * @return \DateTime
     */
    public function getDataCreazione() {
        return $this->dataCreazione;
    }

    /**
     * @param \DateTime $dataCreazione
     */
    public function setDataCreazione($dataCreazione) {
        $this->dataCreazione = $dataCreazione;
    }

    /**
     * @return \DateTime
     */
    public function getDataModifica() {
        return $this->dataModifica;
    }

    /**
     * @param \DateTime $dataModifica
     */
    public function setDataModifica($dataModifica)  {
        $this->dataModifica = $dataModifica;
    }

    /**
     * @return string
     */
    public function getCognome() {
        return $this->cognome;
    }

    /**
     * @param string $cognome
     */
    public function setCognome($cognome) {
        $this->cognome = $cognome;
    }

    /**
     * @return string
     */
    public function getCompany() {
        return $this->company;
    }

    /**
     * @param string $company
     */
    public function setCompany($company) {
        $this->company = $company;
    }

    /**
     * @return string
     */
    public function getRuolo() {
        return $this->ruolo;
    }

    /**
     * @param string $ruolo
     */
    public function setRuolo($ruolo) {
        $this->ruolo = $ruolo;
    }



}
