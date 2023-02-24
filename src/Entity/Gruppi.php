<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;


/**
 * Group
 * @ORM\Entity
 * @ORM\Table(name="`gruppi`")
 *
 */
class Gruppi {
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="title", type="string", length=255)
     * @Assert\NotBlank
     */
    private $title;

    /**
     * @ORM\Column(name="description", type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(name="role",  type="json")
     */
    private $role = [];

    /**
     * @ORM\Column(name="isActive", type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(name="createdOn", type="datetime")
     */
    private $createdOn;


    // ##########################################################################################
    // #################################### RELAZIONI ###########################################
    // ##########################################################################################



    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Utenti", mappedBy="gruppo")
     * @ORM\JoinColumn(name="gruppo_id", referencedColumnName="id", nullable=true)
     */
    private $utenteGruppo;


    public function __construct() {
        $this->title = "";
        $this->description = "";
        $this->isActive = false;
        $this->createdOn = new \DateTime();

        $this->utenteGruppo = new ArrayCollection();
    }


    // ##########################################################################################
    // #################################### GETTER AND SETTER ###################################
    // ##########################################################################################

    /*
 *  methods for RoleInterface
 *  @return string|null A string representation of the role, or null
 */
    public function getRole() {
        $result = $this->role;
        return ($result);
    }

    public function setRole($role) {
        $this->role= $role;
        return $this;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * @param string $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     * @return \Datetime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @param \Datetime $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     * @return mixed
     */
    public function getUtenteGruppo()
    {
        return $this->utenteGruppo;
    }

    /**
     * @param mixed $utenteGruppo
     */
    public function setUtenteGruppo($utenteGruppo)
    {
        $this->utenteGruppo = $utenteGruppo;
    }


//    public function getRoles()
//    {
//        $roles = $this->roles;
//        // give everyone ROLE_USER!
//        if (!in_array('ROLE_USER', $roles)) {
//            $roles[] = 'ROLE_USER';
//        }
//
//        return $roles;
//    }





}
