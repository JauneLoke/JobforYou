<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TypeExperienceRepository")
 */
class TypeExperience
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $libelle;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Emplois", mappedBy="typeExperience")
     */
    private $emplois;

    public function __construct()
    {
        $this->emplois = new ArrayCollection();
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

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getDateAdd(): ?\DateTimeInterface
    {
        return $this->dateAdd;
    }

    public function setDateAdd(\DateTimeInterface $dateAdd): self
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * @return Collection|Emplois[]
     */
    public function getEmplois(): Collection
    {
        return $this->emplois;
    }

    public function addEmplois(Emplois $emplois): self
    {
        if (!$this->emplois->contains($emplois)) {
            $this->emplois[] = $emplois;
            $emplois->setTypeExperience($this);
        }

        return $this;
    }

    public function removeEmplois(Emplois $emplois): self
    {
        if ($this->emplois->contains($emplois)) {
            $this->emplois->removeElement($emplois);
            // set the owning side to null (unless already changed)
            if ($emplois->getTypeExperience() === $this) {
                $emplois->setTypeExperience(null);
            }
        }

        return $this;
    }
}
