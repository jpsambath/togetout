<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LieuRepository")
 */
class Lieu
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max="180")
     * @Assert\NotBlank()
     */
    private $nom;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max="180")
     * @Assert\NotBlank()
     */
    private $rue;

    /**
     * @ORM\Column(type="float")
     * @Assert\Regex( pattern="/^[0-9]{1,},[0-9]$/")
     * @Assert\NotBlank()
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     * @Assert\Regex( pattern="/[0-9]/")
     * @Assert\NotBlank()
     */
    private $longitude;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Ville")
     * @ORM\JoinColumn(nullable=false)
     */
    private $ville;


    /**
     * @return int|null
     *
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */

    public function getNom(): ?string
    {
        return $this->nom;
    }

    /**
     * @param string $nom
     * @return $this
     *
     */
    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getRue(): ?string
    {
        return $this->rue;
    }

    /**
     * @param string $rue
     * @return $this
     */
    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     * @return $this
     */
    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     * @return $this
     */
    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    /**
     * @return Ville|null
     */
    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    /**
     * @param Ville|null $ville
     * @return $this
     */
    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

}
