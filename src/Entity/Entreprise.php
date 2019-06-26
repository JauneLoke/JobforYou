<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EntrepriseRepository")
 */
class Entreprise
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=128)
     */
    private $denomination;

    /**
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $nomMarque;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateAdd;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateUpd;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $codePostal;

    /**
     * @ORM\Column(type="string", length=200)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=15, nullable=true)
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siteWeb;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $siret;

    /**
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $nafApe;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $surface;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\User", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $reservationOk;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $secteursActivites;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $reseauxSociaux;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Emplois", mappedBy="Entreprise", orphanRemoval=true)
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

    public function getDenomination(): ?string
    {
        return $this->denomination;
    }

    public function setDenomination(string $denomination): self
    {
        $this->denomination = $denomination;

        return $this;
    }

    public function getNomMarque(): ?string
    {
        return $this->nomMarque;
    }

    public function setNomMarque(?string $nomMarque): self
    {
        $this->nomMarque = $nomMarque;

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

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    public function setSiteWeb(?string $siteWeb): self
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getNafApe(): ?string
    {
        return $this->nafApe;
    }

    public function setNafApe(?string $nafApe): self
    {
        $this->nafApe = $nafApe;

        return $this;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(?int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getReservationOk(): ?bool
    {
        return $this->reservationOk;
    }

    public function setReservationOk(?bool $reservationOk): self
    {
        $this->reservationOk = $reservationOk;

        return $this;
    }

    public function getSecteursActivites(): ?string
    {
        return $this->secteursActivites;
    }

    public function setSecteursActivites(?string $secteursActivites): self
    {
        $this->secteursActivites = $secteursActivites;

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

    public function getReseauxSociaux(): ?object
    {
        return json_decode($this->reseauxSociaux);
    }

    public function setReseauxSociaux(?array $reseauxSociaux): self
    {
        $this->reseauxSociaux = json_encode($reseauxSociaux);

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
            $emplois->setEntreprise($this);
        }

        return $this;
    }

    public function removeEmplois(Emplois $emplois): self
    {
        if ($this->emplois->contains($emplois)) {
            $this->emplois->removeElement($emplois);
            // set the owning side to null (unless already changed)
            if ($emplois->getEntreprise() === $this) {
                $emplois->setEntreprise(null);
            }
        }

        return $this;
    }
}
