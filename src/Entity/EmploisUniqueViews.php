<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\EmploisUniqueViewsRepository")
 * @ORM\Table(indexes={@ORM\Index(name="adresseIp", columns={"adresse_ip"})})
 */
class EmploisUniqueViews
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Emplois", inversedBy="emploisUniqueViews")
     * @ORM\JoinColumn(nullable=false)
     */
    private $Emplois;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $adresseIp;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateVisited;

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

    public function getAdresseIp(): ?string
    {
        return $this->adresseIp;
    }

    public function setAdresseIp(string $adresseIp): self
    {
        $this->adresseIp = $adresseIp;

        return $this;
    }

    public function getDateVisited(): ?\DateTimeInterface
    {
        return $this->dateVisited;
    }

    public function setDateVisited(\DateTimeInterface $dateVisited): self
    {
        $this->dateVisited = $dateVisited;

        return $this;
    }
}
