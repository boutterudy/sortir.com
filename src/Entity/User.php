<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="L'utilisateur doit avoir un pseudo")
     * @Assert\Unique(message="Ce pseudo est déjà pris")
     */
    private $nickName;


    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Regex(
     *      pattern = "/^(?=.*\d)(?=.*[A-Z])(?=.*[@#$%])(?!.*(.)\1{2}).*[a-z]/m",
     *      message="Votre mot de passe doit comporter au moins huit caractères, dont des lettres majuscules et minuscules, un chiffre et un symbole.")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'utilisateur doit avoir un nom")
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="L'utilisateur doit avoir un prénom")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Regex(
     *     pattern="^(?:(?:\+|00)33[\s.-]{0,3}(?:\(0\)[\s.-]{0,3})?|0)[1-9](?:(?:[\s.-]?\d{2}){4}|\d{2}(?:[\s.-]?\d{3}){2})$"
     *     message="Le numéro de téléphone saisi n'est pas valide")
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="L'email saisi est invalide")
     */
    private $email;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull(message="Merci de préciser si cet utilisateur est administrateur")
     */
    private $isAdmin;

    /**
     * @ORM\Column(type="boolean")
     * @Assert\NotNull(message="Merci de préciser si cet utilisateur est actif")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\Image(mimeTypesMessage="Ce type de fichier n'est pas pris en compte")
     */
    private $imageFile;

    /**
     * @ORM\OneToMany(targetEntity=Outing::class, mappedBy="organizer")
     */
    private $organizedOutings;

    /**
     * @ORM\ManyToMany(targetEntity=Outing::class, mappedBy="users")
     */
    private $outings;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotNull(message="Chaque utilisateur doit être rattaché à un campus")
     * @Assert\Type("App\Entity\Campus", message="Ce campus n'est pas valide"
     */
    private $campus;

    public function __construct()
    {
        $this->organizedOutings = new ArrayCollection();
        $this->outings = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNickName(): ?string
    {
        return $this->nickName;
    }

    public function setNickName(string $nickName): self
    {
        $this->nickName = $nickName;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->nickName;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->nickName;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        return $this->isAdmin?['ROLE_ADMIN']:['ROLE_USER'];
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsAdmin(): ?bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): self
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(?string $imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    /**
     * @return Collection|Outing[]
     */
    public function getOrganizedOutings(): Collection
    {
        return $this->organizedOutings;
    }

    public function addOrganizedOuting(Outing $organizedOuting): self
    {
        if (!$this->organizedOutings->contains($organizedOuting)) {
            $this->organizedOutings[] = $organizedOuting;
            $organizedOuting->setOrganizer($this);
        }

        return $this;
    }

    public function removeOrganizedOuting(Outing $organizedOuting): self
    {
        if ($this->organizedOutings->removeElement($organizedOuting)) {
            // set the owning side to null (unless already changed)
            if ($organizedOuting->getOrganizer() === $this) {
                $organizedOuting->setOrganizer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Outing[]
     */
    public function getOutings(): Collection
    {
        return $this->outings;
    }

    public function addOuting(Outing $outing): self
    {
        if (!$this->outings->contains($outing)) {
            $this->outings[] = $outing;
            $outing->addUser($this);
        }

        return $this;
    }

    public function removeOuting(Outing $outing): self
    {
        if ($this->outings->removeElement($outing)) {
            $outing->removeUser($this);
        }

        return $this;
    }

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }
}
