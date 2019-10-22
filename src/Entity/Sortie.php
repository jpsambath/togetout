<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=50)
     * @Assert\Length(max="50")
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $dateHeureDebut;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $duree;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank()
     */
    private $dateLimiteInscription;

    /**
     * @var integer
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbInscriptionMax;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Length(max="255")
     */
    private $infosSortie;

    /**
     * @var Etat
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat")
     */
    private $etat;

    /**
     * @var Participant
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant", inversedBy="sortieCreer")
     */
    private $organisateur;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant")
     */
    private $inscrit;



    /**
     * @var Lieu
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu")
     */
    private $lieu;

    /**
     * @var Site
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     */
    private $site;

    /**
     * Sortie constructor.
     */
    public function __construct()
    {
        $this->inscrit = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getNom(): string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     */
    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    /**
     * @return DateTime
     */
    public function getDateHeureDebut(): DateTime
    {
        return $this->dateHeureDebut;
    }

    /**
     * @param DateTime $dateHeureDebut
     */
    public function setDateHeureDebut(DateTime $dateHeureDebut): void
    {
        $this->dateHeureDebut = $dateHeureDebut;
    }

    /**
     * @return DateTime
     */
    public function getDuree(): DateTime
    {
        return $this->duree;
    }

    /**
     * @param DateTime $duree
     */
    public function setDuree(DateTime $duree): void
    {
        $this->duree = $duree;
    }

    /**
     * @return DateTime
     */
    public function getDateLimiteInscription(): DateTime
    {
        return $this->dateLimiteInscription;
    }

    /**
     * @param DateTime $dateLimiteInscription
     */
    public function setDateLimiteInscription(DateTime $dateLimiteInscription): void
    {
        $this->dateLimiteInscription = $dateLimiteInscription;
    }

    /**
     * @return int
     */
    public function getNbInscriptionMax(): int
    {
        return $this->nbInscriptionMax;
    }

    /**
     * @param int $nbInscriptionMax
     */
    public function setNbInscriptionMax(int $nbInscriptionMax): void
    {
        $this->nbInscriptionMax = $nbInscriptionMax;
    }

    /**
     * @return string
     */
    public function getInfosSortie(): string
    {
        return $this->infosSortie;
    }

    /**
     * @param string $infosSortie
     */
    public function setInfosSortie(string $infosSortie): void
    {
        $this->infosSortie = $infosSortie;
    }

    /**
     * @return Etat
     */
    public function getEtat(): Etat
    {
        return $this->etat;
    }

    /**
     * @param Etat $etat
     */
    public function setEtat(Etat $etat): void
    {
        $this->etat = $etat;
    }

    /**
     * @return Participant
     */
    public function getOrganisateur(): Participant
    {
        return $this->organisateur;
    }

    /**
     * @param Participant $organisateur
     */
    public function setOrganisateur(Participant $organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return ArrayCollection
     */
    public function getInscrit(): ArrayCollection
    {
        return $this->inscrit;
    }

    /**
     * @param ArrayCollection $inscrit
     */
    public function setInscrit(ArrayCollection $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return Lieu
     */
    public function getLieu(): Lieu
    {
        return $this->lieu;
    }

    /**
     * @param Lieu $lieu
     */
    public function setLieu(Lieu $lieu): void
    {
        $this->lieu = $lieu;
    }

    /**
     * @return Site
     */
    public function getSite(): Site
    {
        return $this->site;
    }

    /**
     * @param Site $site
     */
    public function setSite(Site $site): void
    {
        $this->site = $site;
    }


}