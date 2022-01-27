<?php

namespace App\Entity;

use App\Repository\PlaceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PlaceRepository::class)
 */
class Place
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez rentrer un nom pour ce lieu")
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez rentrer une adresse pour ce lieu")
     */
    private $street;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Vous devez renseigner la latitude du lieu")
     * @Assert\GreaterThanOrEqual("-90", message="La valeur saisie n'est pas valide")
     * @Assert\LessThanOrEqual ("90", message="La valeur saisie n'est pas valide")
     * @Assert\Type("float", message="La valeur saisie n'est pas valide")
     */
    private $latitude;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(message="Vous devez renseigner la longitude du lieu")
     * @Assert\GreaterThanOrEqual("-180", message="La valeur saisie n'est pas valide")
     * @Assert\LessThanOrEqual ("180", message="La valeur saisie n'est pas valide")
     * @Assert\Type("float", message="La valeur saisie n'est pas valide")
     */
    private $longitude;

    /**
     * @ORM\OneToMany(targetEntity=Outing::class, mappedBy="place", cascade={"remove"}, orphanRemoval=true)
     */
    private $outings;

    /**
     * @ORM\ManyToOne(targetEntity=Town::class, inversedBy="places")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(message="Vous devez attacher ce lieu à une ville")
     * @Assert\NotNull(message="Vous devez attacher ce lieu à une ville")
     * @Assert\Type("App\Entity\Town", message="La valeur saisie n'est pas valide")
     */
    private $town;

    public function __construct()
    {
        $this->outings = new ArrayCollection();
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

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function setStreet(string $street): self
    {
        $this->street = $street;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

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
            $outing->setPlace($this);
        }

        return $this;
    }

    public function removeOuting(Outing $outing): self
    {
        if ($this->outings->removeElement($outing)) {
            // set the owning side to null (unless already changed)
            if ($outing->getPlace() === $this) {
                $outing->setPlace(null);
            }
        }

        return $this;
    }

    public function getTown(): ?Town
    {
        return $this->town;
    }

    public function setTown(?Town $town): self
    {
        $this->town = $town;

        return $this;
    }

    public function __toString()
    {
        return $this->getName();
    }
}
