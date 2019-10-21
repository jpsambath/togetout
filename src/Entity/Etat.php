<?php

namespace App\Entity;

use App\DBAL\Types\EtatEnumType;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Doctrine\ORM\Mapping as ORM;

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
     * @var EtatEnum
     * @ORM\Column(type="EtatEnumType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\EtatEnumType")
    */
    private $libelle;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getLibelle(): ?EtatEnumType
    {
        return $this->libelle;
    }

    /**
     * @param mixed $libelle
     * @return Etat|null
     */
    public function setLibelle(EtatEnumType $libelle): ?self
    {
        $this->libelle = $libelle;
    }


}
