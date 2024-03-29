<?php

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ErrorException;
use Symfony\Component\Validator\Constraints as Assert;
use App\DBAL\Types\EtatEnumType;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 * @ORM\HasLifecycleCallbacks()
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
     * @var datetime
     * @ORM\Column(type="time")
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
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant")
     */
    private $participants;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GroupePrive", inversedBy="sorties")
     */
    private $groupePrive;

    /**
     * Sortie constructor.
     */
    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }


    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id): void
    {
        $this->id = $id;
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
     * @param Sortie $sortie
     */
    public function inscriptionCloturee(Sortie $sortie)
    {
            $this->getEtat()->setLibelle(EtatEnumType::CLOTUREE);
    }


    /**
     * @return Collection|Participant[]
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    /**
     * @param Participant $participant
     * @return $this
     * @throws ErrorException
     */
    public function addParticipant(Participant $participant): self
    {
        if($this->getEtat()->getLibelle() == EtatEnumType::OUVERTE && $this->getParticipants()->count() < $this->getNbInscriptionMax()){
            if (!$this->participants->contains($participant)) {
                $this->participants[] = $participant;
            } else {
                throw new ErrorException('Ce candidat est déjà inscrit !');
            }
        } else {
            throw new ErrorException('il n\'est pas\\plus possible de s\'inscrire !');
        }
        return $this;
    }

    /**
     * @param Participant $participant
     * @return $this
     * @throws ErrorException
     */
    public function removeParticipant(Participant $participant): self
    {
        if(($this->getEtat()->getLibelle() == EtatEnumType::OUVERTE || ($this->getEtat()->getLibelle() == EtatEnumType::CLOTUREE) && $this->getDateLimiteInscription() > (new \DateTime())) ){
            if ($this->participants->contains($participant)) {
                $this->participants->removeElement($participant);
            } else {
                throw new ErrorException('Ce candidat n\'est pas inscrit !');
            }
        } else {
            throw new ErrorException('il n\'est plus possible de ce désinscrire !');
        }
        return $this;
    }


    /**
     * @return Participant|null
     */
    public function getOrganisateur(): ?Participant
    {
        return $this->organisateur;
    }

    /**
     * @param Participant|null $organisateur
     * @return $this
     */
    public function setOrganisateur(?Participant $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Etat|null
     */
    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    /**
     * @param Etat|null $etat
     * @return $this
     */
    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * @return Site|null
     */
    public function getSite(): ?Site
    {
        return $this->site;
    }

    /**
     * @param Site|null $site
     * @return $this
     */
    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    /**
     * @return Lieu|null
     */
    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    /**
     * @param Lieu|null $lieu
     * @return $this
     */
    public function setLieu(Lieu $lieu): self
    {

        $this->lieu = $lieu;

        return $this;
    }

    /**
     * @return GroupePrive|null
     */
    public function getGroupePrive(): ?GroupePrive
    {
        return $this->groupePrive;
    }

    /**
     * @param GroupePrive|null $groupePrive
     * @return $this
     */
    public function setGroupePrive(?GroupePrive $groupePrive): self
    {
        $this->groupePrive = $groupePrive;

        return $this;
    }

    public function updateEtat(Etat $etat): self
    {
        $this->setEtat($etat);
    }

}