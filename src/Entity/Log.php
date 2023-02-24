<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Log
 * @ORM\Table(name="log")
 * @ORM\Entity()
 */
class Log {
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(name="idUtente", type="integer", nullable=true)
     */
    private $idUtente;

    /**
     * @ORM\Column(name="idAudio", type="integer", nullable=true)
     */
    private $idAudio;

    /**
     * @ORM\Column(name="tipo", type="string", length=255, nullable=true)
     */
    private $tipo;

    /**
     * @ORM\Column(name="dataCreazione", type="datetime")
     */
    private $dataCreazione;


    public function __construct() {
        $this->tipo = "";
        $this->dataCreazione = new \DateTime();
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
    public function getIdUtente()
    {
        return $this->idUtente;
    }

    /**
     * @param mixed $idUtente
     */
    public function setIdUtente($idUtente) {
        $this->idUtente = $idUtente;
    }

    /**
     * @return mixed
     */
    public function getIdAudio()
    {
        return $this->idAudio;
    }

    /**
     * @param mixed $idAudio
     */
    public function setIdAudio($idAudio) {
        $this->idAudio = $idAudio;
    }

    /**
     * @return string
     */
    public function getTipo() {
        return $this->tipo;
    }

    /**
     * @param string $tipo
     */
    public function setTipo(string $tipo) {
        $this->tipo = $tipo;
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
