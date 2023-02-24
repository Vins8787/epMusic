<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 *
 * @ORM\Entity
 * @ORM\Table(name="files")
 *
 */
class Files {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(name="path", type="string", length=255)
     */
    private $path;

    /**
     * @ORM\Column(name="stato", type="integer")
     */
    private $stato;

    /**
     * @ORM\Column(name="ord", type="integer")
     */
    private $ord;

    /**
     * @ORM\Column(name="size", type="string", length=255)
     */
    private $size;

    /**
     * @ORM\Column(name="mimeType", type="string", length=255)
     */
    private $mimeType;

    /**
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @ORM\Column(name="dataCreazione", type="datetime")
     */
    private $dataCreazione;

    /**
     * @ORM\Column(name="dataModifica", type="datetime")
     */
    private $dataModifica;

    /**
     * @ORM\Column(name="idAppartamento", type="integer")
     */
    private $idAppartamento;



    public function __construct() {
        $this->name = "";
        $this->path = "";
        $this->size = "";
        $this->mimeType = "";
        $this->type = "";
        $this->dataCreazione = new \DateTime();
        $this->dataModifica = new \DateTime();
        $this->stato = 1;
        $this->ord = 0;
        $this->idAppartamento = 0;
    }


    // ##########################################################################################
    // #################################### GETTER AND SETTER ###################################
    // ##########################################################################################

    /**
     * Get id.
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * @return mixed
     */
    public function getStato()
    {
        return $this->stato;
    }

    /**
     * @param mixed $stato
     */
    public function setStato($stato)
    {
        $this->stato = $stato;
    }

    /**
     * @return mixed
     */
    public function getOrd()
    {
        return $this->ord;
    }

    /**
     * @param mixed $ord
     */
    public function setOrd($ord)
    {
        $this->ord = $ord;
    }

    /**
     * @return mixed
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * @param mixed $size
     */
    public function setSize($size)
    {
        $this->size = $size;
    }

    /**
     * @return mixed
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param mixed $mimeType
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getDataCreazione()
    {
        return $this->dataCreazione;
    }

    /**
     * @param mixed $dataCreazione
     */
    public function setDataCreazione($dataCreazione)
    {
        $this->dataCreazione = $dataCreazione;
    }

    /**
     * @return mixed
     */
    public function getDataModifica()
    {
        return $this->dataModifica;
    }

    /**
     * @param mixed $dataModifica
     */
    public function setDataModifica($dataModifica)
    {
        $this->dataModifica = $dataModifica;
    }

    /**
     * @return mixed
     */
    public function getIdAppartamento()
    {
        return $this->idAppartamento;
    }

    /**
     * @param mixed $idAppartamento
     */
    public function setIdAppartamento($idAppartamento)
    {
        $this->idAppartamento = $idAppartamento;
    }


}
