<?php

namespace App\Entity;

use App\Repository\OutingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Vtiful\Kernel\Format;


/**
 * @ORM\Entity(repositoryClass=OutingRepository::class)
 * @Assert\Expression(
 *     "this.getStartAt() > this.getLimitSubscriptionDate()",
 *     message="La date limite ne peut pas être postérieure à la date de début"
 * )
 */
class Outing
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom de la sortie doit être renseigné")
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(message="La date de début doit être renseignée")
     * @Assert\GreaterThan("today", message="Le date de début n'est pas valide")
     */
    private $startAt;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="La durée de l'activité doit être renseignée")
     * @Assert\Positive(message="La durée n'est pas valide")
     */
    private $duration;

// @Assert\LessThanOrEqual(value="$startAt", message="La date limite ne peut pas être postérieure à la date de début")

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan ("today", message="La date limite ne peut pas être antérieure à aujourd'hui")
     */
    private $limitSubscriptionDate;

    /**
     * @ORM\Column(type="text")
     */
    private $about;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="organizedOutings")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Il faut un organisateur pour cette sortie")
     * @Assert\NotNull(message="Il faut un organisateur pour cette sortie")
     * @Assert\Type("App\Entity\User", message="L'entrée est invalide")
     */
    private $organizer;

    /**
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="outings")
     */
    private $users;

    /**
     * @ORM\ManyToOne(targetEntity=Campus::class, inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Il faut attacher cette sortie à un campus")
     * @Assert\NotNull(message="Il faut attacher cette sortie à un campus")
     * @Assert\Type("App\Entity\Campus", message="L'entrée est invalide")
     */
    private $campus;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class, inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\ManyToOne(targetEntity=Place::class, inversedBy="outings")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Il faut attacher cette sortie à un lieu")
     * @Assert\NotNull(message="Il faut attacher cette sortie à un lieu")
     * @Assert\Type("App\Entity\Place", message="L'entrée est invalide")

     */
    private $place;

    /**
     * @ORM\Column(type="integer")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Il faut indiquer un nombre maximal de participants")
     * @Assert\Positive(message="Il faut un moins une place disponible")
     */
    private $maxUsers;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getLimitSubscriptionDate(): ?\DateTimeInterface
    {
        return $this->limitSubscriptionDate;
    }

    public function setLimitSubscriptionDate(\DateTimeInterface $limitSubscriptionDate): self
    {
        $this->limitSubscriptionDate = $limitSubscriptionDate;

        return $this;
    }

    public function getAbout(): ?string
    {
        return $this->about;
    }

    public function setAbout(string $about): self
    {
        $this->about = $about;

        return $this;
    }

    public function getOrganizer(): ?User
    {
        return $this->organizer;
    }

    public function setOrganizer(?User $organizer): self
    {
        $this->organizer = $organizer;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        $this->users->removeElement($user);

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

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPlace(): ?Place
    {
        return $this->place;
    }

    public function setPlace(?Place $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getMaxUsers(): ?int
    {
        return $this->maxUsers;
    }

    public function setMaxUsers(int $maxUsers): self
    {
        $this->maxUsers = $maxUsers;

        return $this;
    }

    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    public function __toString()
    {
        return $this->getName() . ' | '
            . $this->getCampus() . ' | '
            . $this->getStartAt()->format('d/m/Y') . ' | '
            . $this->getOrganizer();
    }

}
