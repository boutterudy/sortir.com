<?php

namespace App\Entity;

use App\Repository\StateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StateRepository::class)
 */
class Status
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Outlet::class, mappedBy="status")
     */
    private $outlets;

    public function __construct()
    {
        $this->outlets = new ArrayCollection();
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

    /**
     * @return Collection|Outlet[]
     */
    public function getOutlets(): Collection
    {
        return $this->outlets;
    }

    public function addOutlet(Outlet $outlet): self
    {
        if (!$this->outlets->contains($outlet)) {
            $this->outlets[] = $outlet;
            $outlet->setStatus($this);
        }

        return $this;
    }

    public function removeOutlet(Outlet $outlet): self
    {
        if ($this->outlets->removeElement($outlet)) {
            // set the owning side to null (unless already changed)
            if ($outlet->getStatus() === $this) {
                $outlet->setStatus(null);
            }
        }

        return $this;
    }
}
