<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 *
 *  UniqueEntity permet de s'assurer qu'un utilisateur
 * ne puisse pas s'inscrire 2 fois avec le même email.
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="Ce compte existe déjà !"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     * @Assert\Length(
     *     max="80",
     *     maxMessage="Ce champ est limité à {{ limit }} caractères."
     * )
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\NotBlank(message="Ce champ est obligatoire", groups={"registration"})
     * @Assert\Length(
     *     max="80",
     *     maxMessage="Ce champ est limité à {{ limit }} caractères.",
     *     groups={"registration"}
     * )
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\Email(message="Vérifiez votre email", groups={"registration"})
     * @Assert\NotBlank(message="Vous devez saisir votre email", groups={"registration"})
     * @Assert\Length(
     *     max="80",
     *     maxMessage="Ce champ est limité à {{ limit }} caractères.",
     *     groups={"registration"}
     * )
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Saisissez votre mot de passe.", groups={"registration"})
     * @Assert\Length(
     *     min="8",
     *     minMessage="Votre mot de passe est trop court. {{ limit }} caractères min.",
     *     max="20",
     *     maxMessage="Votre mot de passe est trop long. {{ limit }} caractères max.",
     *     groups={"registration"}
     * )
     * @Assert\Regex(
     *     pattern="/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])[a-zA-Z0-9]+$/",
     *     message="Votre mot de passe doit contenir au moins une majuscule et un chiffre."
     * )
     */

    private $password;

    /**
     * @ORM\Column(type="array")
     */
    private $role = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Projet", mappedBy="user")
     */
    private $projets;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Projet")
     */
    private $favoris;

    public function __construct()
    {
        $this->projets = new ArrayCollection();
        $this->favoris = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(?string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getRoles(): ?array
    {
        return $this->role;
    }

    public function setRoles(array $role): self
    {
        $this->role = $role;

        return $this;
    }

    /**
     * @return Collection|Projet[]
     */
    public function getProjets(): Collection
    {
        return $this->projets;
    }

    public function addProjet(Projet $projet): self
    {
        if (!$this->projets->contains($projet)) {
            $this->projets[] = $projet;
            $projet->setUser($this);
        }

        return $this;
    }

    public function removeProjet(Projet $projet): self
    {
        if ($this->projets->contains($projet)) {
            $this->projets->removeElement($projet);
            // set the owning side to null (unless already changed)
            if ($projet->getUser() === $this) {
                $projet->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
       return null;
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
       return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {

    }

    /**
     * @return Collection|Projet[]
     */
    public function getFavoris(): Collection
    {
        return $this->favoris;
    }

    public function addFavori(Projet $favori): self
    {
        if (!$this->favoris->contains($favori)) {
            $this->favoris[] = $favori;
        }

        return $this;
    }

    public function removeFavori(Projet $favori): self
    {
        if ($this->favoris->contains($favori)) {
            $this->favoris->removeElement($favori);
        }

        return $this;
    }
}
