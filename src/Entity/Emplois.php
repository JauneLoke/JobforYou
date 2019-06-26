<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmploisRepository")
 * @ORM\Table(indexes={@ORM\Index(name="searchIntitule", columns={"intitule"}, flags={"fulltext"})})
 */
class Emplois
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="emplois")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Entreprise;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $intitule;

    /**
     * @ORM\Column(type="float")
     */
    private $salaireBrut;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $ville;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $responsabilite;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $etude;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $avantage;

    /**
     * @ORM\Column(type="smallint")
     */
    private $active;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateUpd;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeContrat", inversedBy="emplois")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeContrat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\TypeExperience", inversedBy="emplois")
     * @ORM\JoinColumn(nullable=false)
     */
    private $typeExperience;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\EmploisUniqueViews", mappedBy="Emplois", orphanRemoval=true)
     */
    private $emploisUniqueViews;

    /**
     * @ORM\Column(type="integer")
     */
    private $countViewed;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Candidature", mappedBy="Emplois", orphanRemoval=true)
     */
    private $Candidat;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Candidature", mappedBy="Emplois", orphanRemoval=true)
     */
    private $candidatures;

    public function __construct()
    {
        $this->emploisUniqueViews = new ArrayCollection();
        $this->Candidat = new ArrayCollection();
        $this->candidatures = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->Entreprise;
    }

    public function setEntreprise(?Entreprise $Entreprise): self
    {
        $this->Entreprise = $Entreprise;

        return $this;
    }

    public function getIntitule(): ?string
    {
        return $this->intitule;
    }

    public function setIntitule(string $intitule): self
    {
        $this->intitule = $intitule;

        return $this;
    }

    public function getSalaireBrut(): ?float
    {
        return $this->salaireBrut;
    }

    public function setSalaireBrut(float $salaireBrut): self
    {
        $this->salaireBrut = $salaireBrut;

        return $this;
    }

    public function getVille(): ?string
    {
        return $this->ville;
    }

    public function setVille(string $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getResponsabilite(): ?string
    {
        return $this->responsabilite;
    }

    public function setResponsabilite(?string $responsabilite): self
    {
        $this->responsabilite = $responsabilite;

        return $this;
    }

    public function getEtude(): ?string
    {
        return $this->etude;
    }

    public function setEtude(?string $etude): self
    {
        $this->etude = $etude;

        return $this;
    }

    public function getAvantage(): ?string
    {
        return $this->avantage;
    }

    public function setAvantage(?string $avantage): self
    {
        $this->avantage = $avantage;

        return $this;
    }

    public function getActive(): ?int
    {
        return $this->active;
    }

    public function setActive(int $active): self
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

    public function getDateUpd(): ?\DateTimeInterface
    {
        return $this->dateUpd;
    }

    public function setDateUpd(\DateTimeInterface $dateUpd): self
    {
        $this->dateUpd = $dateUpd;

        return $this;
    }

    public function getTypeContrat(): ?TypeContrat
    {
        return $this->typeContrat;
    }

    public function setTypeContrat(?TypeContrat $typeContrat): self
    {
        $this->typeContrat = $typeContrat;

        return $this;
    }

    public function getTypeExperience(): ?TypeExperience
    {
        return $this->typeExperience;
    }

    public function setTypeExperience(?TypeExperience $typeExperience): self
    {
        $this->typeExperience = $typeExperience;

        return $this;
    }

    /**
     * @return Collection|EmploisUniqueViews[]
     */
    public function getEmploisUniqueViews(): Collection
    {
        return $this->emploisUniqueViews;
    }

    public function addEmploisUniqueView(EmploisUniqueViews $emploisUniqueView): self
    {
        if (!$this->emploisUniqueViews->contains($emploisUniqueView)) {
            $this->emploisUniqueViews[] = $emploisUniqueView;
            $emploisUniqueView->setEmplois($this);
        }

        return $this;
    }

    public function removeEmploisUniqueView(EmploisUniqueViews $emploisUniqueView): self
    {
        if ($this->emploisUniqueViews->contains($emploisUniqueView)) {
            $this->emploisUniqueViews->removeElement($emploisUniqueView);
            // set the owning side to null (unless already changed)
            if ($emploisUniqueView->getEmplois() === $this) {
                $emploisUniqueView->setEmplois(null);
            }
        }

        return $this;
    }

    public function getCountViewed(): ?int
    {
        return $this->countViewed;
    }

    public function setCountViewed(int $countViewed): self
    {
        $this->countViewed = $countViewed;

        return $this;
    }

    /**
     * @return Collection|Candidature[]
     */
    public function getCandidatures(): Collection
    {
        return $this->candidatures;
    }

    public function addCandidature(Candidature $candidature): self
    {
        if (!$this->candidatures->contains($candidature)) {
            $this->candidatures[] = $candidature;
            $candidature->setEmplois($this);
        }

        return $this;
    }

    public function removeCandidature(Candidature $candidature): self
    {
        if ($this->candidatures->contains($candidature)) {
            $this->candidatures->removeElement($candidature);
            // set the owning side to null (unless already changed)
            if ($candidature->getEmplois() === $this) {
                $candidature->setEmplois(null);
            }
        }

        return $this;
    }
}
