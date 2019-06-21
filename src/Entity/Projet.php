<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez choisir un titre")
     * @Assert\Length(
     *     max="255", maxMessage="Votre titre ne doit pas dépasser {{ limit }} caractères",
     *     min="10", minMessage="Votre titre doit contenir minimum {{ limit }} caractères"
     * )
     */
    private $titre;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Domaine", inversedBy="projets")
     * @Assert\Count(min=1,
     *     minMessage="Vous devez choisir au moins une catégorie")
     */
    private $domaine;

    /**
     * @ORM\Column(type="date")
     */
    private $date_debut_inscription;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="date_debut_inscription",
     *     message="La date de fin d'inscription doit être supérieure ou égale à la date de début d'inscription"
     * )
     */
    private $date_fin_inscription;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Vous devez rédiger une description")
     * @Assert\Length(
     *     max="5000", maxMessage="La description ne doit pas dépasser {{ limit }} caractères",
     *     min="10", minMessage="La description doit contenir minimum {{ limit }} caractères"
     * )
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Vous devez saisir une adresse de site")
     * @Assert\Url
     */
    private $website;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThan(
     *     propertyPath="date_fin_inscription",
     *     message="La date du début d'évènement doit être supérieure à la date de la fin d'inscription"
     * )
     */
    private $date_debut_evenement;

    /**
     * @ORM\Column(type="date")
     * @Assert\GreaterThanOrEqual(
     *     propertyPath="date_debut_evenement",
     *     message="La date de fin d'évènement doit être supérieure ou égale à la date de début d'évènement"
     * )
     */
    private $date_fin_evenement;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Image(
     *     mimeTypesMessage="Vérifiez le format de votre image",
     *     maxSize="1M", maxSizeMessage="Attention, votre image est trop lourde"
     * )
     * @Assert\NotBlank(message="Vous devez choisir une image")
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\NotBlank(message="Vous devez choisir une ville")
     * @Assert\Length(
     *     max="80", maxMessage="Votre saisie ne doit pas dépasser {{ limit }} caractères",
     *     min="2", minMessage="Votre saisie doit contenir minimum {{ limit }} caractères"
     * )
     */
    private $ville;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $pays;

    /**
     * @ORM\Column(type="integer")
     */
    private $Budget;

    /**
     * @ORM\Column(type="integer")
     */
    private $frais;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(
     *     maxSize = "1024k",
     *     mimeTypes = {"application/pdf", "application/x-pdf"},
     *     mimeTypesMessage = "Le format du document doit être en PDF"
     * )
     */
    private $document;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="projets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;


    public function __construct()
    {
        $this->domaine = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImage()
    {
        return $this->image;
    }

    public function setImage($image): self
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

    public function getBudget(): ?int
    {
        return $this->Budget;
    }

    public function setBudget(int $Budget): self
    {
        $this->Budget = $Budget;

        return $this;
    }

    public function getFrais(): ?int
    {
        return $this->frais;
    }

    public function setFrais(int $frais): self
    {
        $this->frais = $frais;

        return $this;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function setDocument($document): self
    {
        $this->document = $document;

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
}
