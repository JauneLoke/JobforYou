<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CandidatureRepository")
 * @ORM\Table(name="emplois_candidats", uniqueConstraints={
 *  @ORM\UniqueConstraint(name="emploi_candidat", columns={"emplois_id", "candidat_id"})
 * })
 */
class Candidature
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Emplois", inversedBy="candidatures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Emplois;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Candidat", inversedBy="candidatures")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Candidat;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmplois(): ?Emplois
    {
        return $this->Emplois;
    }

    public function setEmplois(?Emplois $Emplois): self
    {
        $this->Emplois = $Emplois;

        return $this;
    }

    public function getCandidat(): ?Candidat
    {
        return $this->Candidat;
    }

    public function setCandidat(?Candidat $Candidat): self
    {
        $this->Candidat = $Candidat;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }
}
