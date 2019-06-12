<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjetRepository")
 */
class Projet
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_debut_inscription;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_fin_inscription;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $website;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_debut_evenement;

    /**
     * @ORM\Column(type="datetime")
     */
    private $date_fin_evenement;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $pays;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Domaine", inversedBy="projets")
     */
    private $domaine;

    public function __construct()
    {
        $this->domaine = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutInscription(): ?\DateTimeInterface
    {
        return $this->date_debut_inscription;
    }

    public function setDateDebutInscription(\DateTimeInterface $date_debut_inscription): self
    {
        $this->date_debut_inscription = $date_debut_inscription;

        return $this;
    }

    public function getDateFinInscription(): ?\DateTimeInterface
    {
        return $this->date_fin_inscription;
    }

    public function setDateFinInscription(\DateTimeInterface $date_fin_inscription): self
    {
        $this->date_fin_inscription = $date_fin_inscription;

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

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website): self
    {
        $this->website = $website;

        return $this;
    }

    public function getDateDebutEvenement(): ?\DateTimeInterface
    {
        return $this->date_debut_evenement;
    }

    public function setDateDebutEvenement(\DateTimeInterface $date_debut_evenement): self
    {
        $this->date_debut_evenement = $date_debut_evenement;

        return $this;
    }

    public function getDateFinEvenement(): ?\DateTimeInterface
    {
        return $this->date_fin_evenement;
    }

    public function setDateFinEvenement(\DateTimeInterface $date_fin_evenement): self
    {
        $this->date_fin_evenement = $date_fin_evenement;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

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

    public function getPays(): ?string
    {
        return $this->pays;
    }

    public function setPays(string $pays): self
    {
        $this->pays = $pays;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    /**
     * @return Collection|Domaine[]
     */
    public function getDomaine(): Collection
    {
        return $this->domaine;
    }

    public function addDomaine(Domaine $domaine): self
    {
        if (!$this->domaine->contains($domaine)) {
            $this->domaine[] = $domaine;
        }

        return $this;
    }

    public function removeDomaine(Domaine $domaine): self
    {
        if ($this->domaine->contains($domaine)) {
            $this->domaine->removeElement($domaine);
        }

        return $this;
    }
}
