<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Comuni
 * @ORM\Table(name="comuni")
 * @ORM\Entity
 */
class Comuni {
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="CodISTATCom", type="string", length=3, nullable=true)
     */
    private $codistatcom;

    /**
     * @ORM\Column(name="Comune", type="string", length=50, nullable=true)
     */
    private $comune;

    /**
     * @ORM\Column(name="CodISTATPro", type="string", length=3, nullable=true)
     */
    private $codistatpro;

    /**
     * @ORM\Column(name="Provincia", type="string", length=25, nullable=true)
     */
    private $provincia;

    /**
     * @ORM\Column(name="CodISTATReg", type="string", length=2, nullable=true)
     */
    private $codistatreg;

    /**
     * @ORM\Column(name="Regione", type="string", length=25, nullable=true)
     */
    private $regione;


    public function __construct() {

        $this->utente = new \Doctrine\Common\Collections\ArrayCollection();
    }

    // ##########################################################################################
    // #################################### RELAZIONI ###########################################
    // ##########################################################################################


    /**
     * @Groups({"readComuniItem"})
     * @ORM\OneToMany(targetEntity="App\Entity\Utenti", mappedBy="comune")
     * @ORM\JoinColumn(name="utente_id", referencedColumnName="id")
     */
    private $utente;


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
    public function getCodistatcom()
    {
        return $this->codistatcom;
    }

    /**
     * @param mixed $codistatcom
     */
    public function setCodistatcom($codistatcom)
    {
        $this->codistatcom = $codistatcom;
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
    public function setComune($comune)
    {
        $this->comune = $comune;
    }

    /**
     * @return mixed
     */
    public function getCodistatpro()
    {
        return $this->codistatpro;
    }

    /**
     * @param mixed $codistatpro
     */
    public function setCodistatpro($codistatpro)
    {
        $this->codistatpro = $codistatpro;
    }

    /**
     * @return mixed
     */
    public function getProvincia()
    {
        return $this->provincia;
    }

    /**
     * @param mixed $provincia
     */
    public function setProvincia($provincia)
    {
        $this->provincia = $provincia;
    }

    /**
     * @return mixed
     */
    public function getCodistatreg()
    {
        return $this->codistatreg;
    }

    /**
     * @param mixed $codistatreg
     */
    public function setCodistatreg($codistatreg)
    {
        $this->codistatreg = $codistatreg;
    }

    /**
     * @return mixed
     */
    public function getRegione()
    {
        return $this->regione;
    }

    /**
     * @param mixed $regione
     */
    public function setRegione($regione)
    {
        $this->regione = $regione;
    }

    /**
     * @return mixed
     */
    public function getUtente()
    {
        return $this->utente;
    }

    /**
     * @param mixed $utente
     */
    public function setUtente($utente)
    {
        $this->utente = $utente;
    }




}
