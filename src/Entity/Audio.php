<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Comuni
 * @ORM\Table(name="audio")
 * @ORM\Entity(repositoryClass="App\Repository\AudioRepository")
 */
class Audio {
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
     * @ORM\Column(name="rep", type="string", length=255, nullable=true)
     */
    private $rep;

    /**
     * @ORM\Column(name="titolo", type="string", length=255, nullable=true)
     */
    private $titolo;

    /**
     * @ORM\Column(name="autori", type="string", length=255, nullable=true)
     */
    private $autori;

    /**
     * @ORM\Column(name="filmSerie", type="string", length=255, nullable=true)
     */
    private $filmSerie;

    /**
     * @ORM\Column(name="filmSerieNome", type="string", length=255, nullable=true)
     */
    private $filmSerieNome;

    /**
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @ORM\Column(name="durata", type="string", length=255, nullable=true)
     */
    private $durata;

    /**
     * @ORM\Column(name="editore", type="string", length=255, nullable=true)
     */
    private $editore;

    /**
     * @ORM\Column(name="licenza", type="integer", nullable=true)
     */
    private $licenza;

    /**
     * @ORM\Column(name="scf", type="integer", nullable=true)
     */
    private $scf;

    /**
     * @ORM\Column(name="produttore", type="string", length=255, nullable=true)
     */
    private $produttore;

    /**
     * @ORM\Column(name="isrc", type="string", length=255, nullable=true)
     */
    private $isrc;

    /**
     * @ORM\Column(name="ean", type="string", length=255, nullable=true)
     */
    private $ean;

    /**
     * @ORM\Column(name="mastered", type="string", length=255, nullable=true)
     */
    private $mastered;

    /**
     * @ORM\Column(name="stem", type="string", length=255, nullable=true)
     */
    private $stem;

    /**
     * @ORM\Column(name="versioni", type="integer", nullable=true)
     */
    private $versioni;

    /**
     * @ORM\Column(name="dataModifica", type="datetime")
     */
    private $dataModifica;

    /**
     * @ORM\Column(name="dataCreazione", type="datetime")
     */
    private $dataCreazione;


    public function __construct() {
        $this->codice = "";
        $this->rep = "";
        $this->titolo = "";
        $this->autori = "";
        $this->filmSerie = "";
        $this->filmSerieNome = "";
        $this->note = "";
        $this->durata = "";
        $this->editore = "";
        $this->licenza = "";
        $this->scf = "";
        $this->produttore = "";
        $this->isrc = "";
        $this->ean = "";
        $this->mastered = "";
        $this->stem = "";
        $this->versioni = "";
        $this->dataCreazione = new \DateTime();
        $this->dataModifica = new \DateTime();

        $this->audioGenere = new ArrayCollection();
        $this->audioFeeling = new ArrayCollection();
        $this->audioStrumenti = new ArrayCollection();
    }

    // ##########################################################################################
    // #################################### RELAZIONI ###########################################
    // ##########################################################################################

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Genere", cascade={"remove", "persist"})
     * @ORM\JoinTable(name="relAudioGenere",
     *      joinColumns={@ORM\JoinColumn(name="audio_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="genere_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $audioGenere;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Feeling", cascade={"remove", "persist"})
     * @ORM\JoinTable(name="relAudioFeeling",
     *      joinColumns={@ORM\JoinColumn(name="audio_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="feeling_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $audioFeeling;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Strumenti", cascade={"remove", "persist"})
     * @ORM\JoinTable(name="relAudioStrumenti",
     *      joinColumns={@ORM\JoinColumn(name="audio_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="strumenti_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     */
    private $audioStrumenti;



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
     * @return string
     */
    public function getCodice() {
        return $this->codice;
    }

    /**
     * @param string $codice
     */
    public function setCodice($codice) {
        $this->codice = $codice;
    }

    /**
     * @return string
     */
    public function getRep() {
        return $this->rep;
    }

    /**
     * @param string $rep
     */
    public function setRep($rep) {
        $this->rep = $rep;
    }

    /**
     * @return string
     */
    public function getTitolo() {
        return $this->titolo;
    }

    /**
     * @param string $titolo
     */
    public function setTitolo($titolo) {
        $this->titolo = $titolo;
    }

    /**
     * @return string
     */
    public function getAutori() {
        return $this->autori;
    }

    /**
     * @param string $autori
     */
    public function setAutori($autori) {
        $this->autori = $autori;
    }

    /**
     * @return string
     */
    public function getFilmSerie() {
        return $this->filmSerie;
    }

    /**
     * @param string $filmSerie
     */
    public function setFilmSerie($filmSerie) {
        $this->filmSerie = $filmSerie;
    }

    /**
     * @return string
     */
    public function getFilmSerieNome() {
        return $this->filmSerieNome;
    }

    /**
     * @param string $filmSerieNome
     */
    public function setFilmSerieNome($filmSerieNome) {
        $this->filmSerieNome = $filmSerieNome;
    }

    /**
     * @return string
     */
    public function getNote() {
        return $this->note;
    }

    /**
     * @param string $note
     */
    public function setNote($note) {
        $this->note = $note;
    }

    /**
     * @return string
     */
    public function getDurata() {
        return $this->durata;
    }

    /**
     * @param string $durata
     */
    public function setDurata($durata) {
        $this->durata = $durata;
    }

    /**
     * @return string
     */
    public function getEditore() {
        return $this->editore;
    }

    /**
     * @param string $editore
     */
    public function setEditore($editore) {
        $this->editore = $editore;
    }

    /**
     * @return string
     */
    public function getLicenza() {
        return $this->licenza;
    }

    /**
     * @param string $licenza
     */
    public function setLicenza($licenza) {
        $this->licenza = $licenza;
    }

    /**
     * @return string
     */
    public function getScf() {
        return $this->scf;
    }

    /**
     * @param string $scf
     */
    public function setScf($scf) {
        $this->scf = $scf;
    }

    /**
     * @return string
     */
    public function getProduttore() {
        return $this->produttore;
    }

    /**
     * @param string $produttore
     */
    public function setProduttore($produttore)  {
        $this->produttore = $produttore;
    }

    /**
     * @return string
     */
    public function getIsrc() {
        return $this->isrc;
    }

    /**
     * @param string $isrc
     */
    public function setIsrc(string $isrc) {
        $this->isrc = $isrc;
    }

    /**
     * @return string
     */
    public function getEan() {
        return $this->ean;
    }

    /**
     * @param string $ean
     */
    public function setEan(string $ean) {
        $this->ean = $ean;
    }

    /**
     * @return string
     */
    public function getMastered() {
        return $this->mastered;
    }

    /**
     * @param string $mastered
     */
    public function setMastered(string $mastered) {
        $this->mastered = $mastered;
    }

    /**
     * @return string
     */
    public function getStem(){
        return $this->stem;
    }

    /**
     * @param string $stem
     */
    public function setStem(string $stem) {
        $this->stem = $stem;
    }

    /**
     * @return string
     */
    public function getVersioni() {
        return $this->versioni;
    }

    /**
     * @param string $versioni
     */
    public function setVersioni($versioni) {
        $this->versioni = $versioni;
    }

    /**
     * @return mixed
     */
    public function getAudioGenere() {
        return $this->audioGenere;
    }

    /**
     * @param mixed $audioGenere
     */
    public function setAudioGenere($audioGenere) {
        $this->audioGenere = $audioGenere;
    }
    public function addAudioGenere(\App\Entity\Genere $genere) {
        $this->audioGenere[] = $genere;
        return $this;
    }
    public function removeAudioGenere(\App\Entity\Genere $genere){
        return $this->audioGenere->removeElement($genere);
    }

    /**
     * @return mixed
     */
    public function getAudioFeeling()
    {
        return $this->audioFeeling;
    }

    /**
     * @param mixed $audioFeeling
     */
    public function setAudioFeeling($audioFeeling) {
        $this->audioFeeling = $audioFeeling;
    }
    public function addAudioFeeling(\App\Entity\Feeling $feeling) {
        $this->audioFeeling[] = $feeling;
        return $this;
    }
    public function removeAudioFeeling(\App\Entity\Feeling $feeling){
        return $this->audioFeeling->removeElement($feeling);
    }

    /**
     * @return mixed
     */
    public function getAudioStrumenti()
    {
        return $this->audioStrumenti;
    }

    /**
     * @param mixed $audioStrumenti
     */
    public function setAudioStrumenti($audioStrumenti) {
        $this->audioStrumenti = $audioStrumenti;
    }
    public function addAudioStrumenti(\App\Entity\Strumenti $strumenti) {
        $this->audioStrumenti[] = $strumenti;
        return $this;
    }
    public function removeAudioStrumenti(\App\Entity\Strumenti $strumenti){
        return $this->audioStrumenti->removeElement($strumenti);
    }

    /**
     * @return \DateTime
     */
    public function getDataModifica()  {
        return $this->dataModifica;
    }

    /**
     * @param \DateTime $dataModifica
     */
    public function setDataModifica(\DateTime $dataModifica) {
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
