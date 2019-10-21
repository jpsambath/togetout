<?php

namespace App\Entity;

<<<<<<< HEAD
use App\DBAL\Types\EtatEnum;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
=======
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
>>>>>>> origin/master

/**
 * @ORM\Entity(repositoryClass="App\Repository\EtatRepository")
 */
class Etat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
<<<<<<< HEAD
     * @ORM\Column(type="EtatEnum", nullable="false")
     * @DoctrineAssert\Enum(entity="EtatEnum")
=======
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max="255")
>>>>>>> origin/master
     * @Assert\NotBlank()
     */
    private $libelle;

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
}
