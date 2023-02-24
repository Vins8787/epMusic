<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Comuni
 * @ORM\Table(name="strumenti")
 * @ORM\Entity(repositoryClass="App\Repository\StrumentiRepository")
 */
class Strumenti {
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="codice", type="string", length=50, nullable=true)
     */
    private $codice;

    /**
     * @ORM\Column(name="nome", type="string", length=255, nullable=true)
     */
    private $nome;


    /**
     * @ORM\Column(name="dataModifica", type="datetime")
     */
    private $dataModifica;

    /**
     * @ORM\Column(name="dataCreazione", type="datetime")
     */
    private $dataCreazione;


    public function __construct() {
        $this->dataCreazione = new \DateTime();
        $this->dataModifica = new \DateTime();
    }

    // ##########################################################################################
    // #################################### RELAZIONI ###########################################
    // ##########################################################################################



    // ##########################################################################################
    // #################################### GETTER AND SETTER ###################################
    // ##########################################################################################


    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCodice()
    {
        return $this->codice;
    }

    /**
     * @param mixed $codice
     */
    public function setCodice($codice) {
        $this->codice = $codice;
    }

    /**
     * @return mixed
     */
    public function getNome()
    {
        return $this->nome;
    }

    /**
     * @param mixed $nome
     */
    public function setNome($nome) {
        $this->nome = $nome;
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
    public function setDataModifica($dataModifica) {
        $this->dataModifica = $dataModifica;
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




}
