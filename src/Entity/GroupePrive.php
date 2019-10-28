<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupePriveRepository")
 */
class GroupePrive
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $avatar;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Participant", inversedBy="groupePrivesInscrit")
     */
    private $membres;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="groupePrive")
     */
    private $sorties;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Participant", inversedBy="groupePrivesFondateur")
     * @ORM\JoinColumn(nullable=false)
     */
    private $fondateur;

    public function __construct()
    {
        $this->membres = new ArrayCollection();
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }


    /**
     * @return Collection|Participant[]
     */
    public function getMembres(): Collection
    {
        return $this->membres;
    }

    public function addMembre(Participant $membre): self
    {
        if (!$this->membres->contains($membre)) {
            $this->membres[] = $membre;
        }

        return $this;
    }

    public function removeMembre(Participant $membre): self
    {
        if ($this->membres->contains($membre)) {
            $this->membres->removeElement($membre);
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
            $sorty->setGroupePrive($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->contains($sorty)) {
            $this->sorties->removeElement($sorty);
            // set the owning side to null (unless already changed)
            if ($sorty->getGroupePrive() === $this) {
                $sorty->setGroupePrive(null);
            }
        }

        return $this;
    }

    public function getFondateur(): ?Participant
    {
        return $this->fondateur;
    }

    public function setFondateur(?Participant $fondateur): self
    {
        $this->fondateur = $fondateur;

        return $this;
    }
}
