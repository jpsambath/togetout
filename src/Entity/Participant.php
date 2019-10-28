<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"username"}, message="There is already an account with this username")
 * @UniqueEntity(fields={"email"}, message="There is already an account with this email")
 * @UniqueEntity(fields={"telephone"}, message="There is already an account with this phone")
 * @Serializer\ExclusionPolicy("ALL")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=180)
     * @Assert\Length(max="180")
     * @Assert\NotBlank()
     * @Serializer\Expose
     */
    private $nom;


    /**
     * @var string
     * @ORM\Column(type="string", length=180, nullable=true)
     * @Assert\Length(max="180")
     * @Serializer\Expose
     */
    private $prenom;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Length(max="180")
     * @Assert\NotBlank()
     * @Serializer\Expose
     */
    private $username;

    /**
     * @ORM\Column(type="json_array")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", length=180)
     * @Assert\Length(max="180")
     */
    private $password;

    /**
     * @var string The not-hashed password
     * @Assert\Length(max="180")
     * @Assert\NotBlank()
     * @Serializer\Expose
     */
    private $plainPassword;

    /**
     * @var string
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Serializer\Expose
     */
    private $email;

    /**
     * @var string
     * @ORM\Column(type="integer", length=15, unique=true, nullable=true)
     * @Serializer\Expose
     */
    private $telephone;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     * @Serializer\Expose
     */
    private $administrateur;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=false)
     * @Serializer\Expose
     */
    private $actif;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Sortie", inversedBy="participants")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sorties;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="organisateur")
     * @ORM\JoinColumn(nullable=true)
     */
    private $sortieCreer;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     * @ORM\JoinColumn(nullable=true)
     */
    private $site;




    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->sortieCreer = new ArrayCollection();
        $this->actif = true;
        $this->administrateur = false;
        $this->roles[] = "ROLE_USER";
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
     * @return string
     */
    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    /**
     * @param string $prenom
     */
    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    /**
     * @param string $plainPassword
     */
    public function setPlainPassword(string $plainPassword): void
    {
        $this->plainPassword = $plainPassword;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    /**
     * @param string $telephone
     */
    public function setTelephone(string $telephone): void
    {
        $this->telephone = $telephone;
    }

    /**
     * @return bool
     */
    public function isAdministrateur(): bool
    {
        return $this->administrateur;
    }

    /**
     * @param bool $administrateur
     */
    public function setAdministrateur(bool $administrateur): void
    {
        $this->administrateur = $administrateur;
    }

    /**
     * @return bool
     */
    public function isActif(): bool
    {
        return $this->actif;
    }

    /**
     * @param bool $actif
     */
    public function setActif(bool $actif): void
    {
        $this->actif = $actif;
    }



    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sortie): self
    {
        if (!$this->sorties->contains($sortie)) {
            $this->sorties[] = $sortie;
        }

        return $this;
    }

    public function removeSorty(Sortie $sortie): self
    {
        if ($this->sorties->contains($sortie)) {
            $this->sorties->removeElement($sortie);
        }

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortieCreer(): Collection
    {
        return $this->sortieCreer;
    }

    public function addSortieCreer(Sortie $sortieCreer): self
    {
        if (!$this->sortieCreer->contains($sortieCreer)) {
            $this->sortieCreer[] = $sortieCreer;
            $sortieCreer->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSortieCreer(Sortie $sortieCreer): self
    {
        if ($this->sortieCreer->contains($sortieCreer)) {
            $this->sortieCreer->removeElement($sortieCreer);
            // set the owning side to null (unless already changed)
            if ($sortieCreer->getOrganisateur() === $this) {
                $sortieCreer->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

}
