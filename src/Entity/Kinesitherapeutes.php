<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Kinesitherapeutes
 *
 * @ORM\Table(name="kinesitherapeutes")
 * @ORM\Entity
 */
class Kinesitherapeutes implements UserInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255, nullable=false)
     */
    private $prenom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_naissance", type="date", nullable=false)
     */
    private $dateNaissance;

    /**
     * @var string
     *
     * @ORM\Column(name="genre", type="string", length=255, nullable=false)
     */
    private $genre;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=255, nullable=false)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=255, nullable=false)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="piece", type="string", length=255, nullable=false)
     */
    private $piece;

    /**
     * @ORM\Column(name="photo",type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="mdp", type="string", length=255, nullable=false)
     */
    private $mdp;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomcabinet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $telcabinet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $adressecabinet;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $mailcabinet;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt ;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $updatedAt ;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getDateNaissance(): ?\DateTimeInterface
    {
        return $this->dateNaissance;
    }

    public function setDateNaissance(\DateTimeInterface $dateNaissance): self
    {
        $this->dateNaissance = $dateNaissance;

        return $this;
    }

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPiece(): ?string
    {
        return $this->piece;
    }

    public function setPiece(string $piece): self
    {
        $this->piece = $piece;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getMdp(): ?string
    {
        return $this->mdp;
    }

    public function setMdp(string $mdp): self
    {
        $this->mdp = $mdp;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getNomcabinet(): ?string
    {
        return $this->nomcabinet;
    }

    public function setNomcabinet(string $nomcabinet): self
    {
        $this->nomcabinet = $nomcabinet;

        return $this;
    }

    public function getTelcabinet(): ?string
    {
        return $this->telcabinet;
    }

    public function setTelcabinet(string $telcabinet): self
    {
        $this->telcabinet = $telcabinet;

        return $this;
    }

    public function getAdressecabinet(): ?string
    {
        return $this->adressecabinet;
    }

    public function setAdressecabinet(string $adressecabinet): self
    {
        $this->adressecabinet = $adressecabinet;

        return $this;
    }

    public function getMailcabinet(): ?string
    {
        return $this->mailcabinet;
    }

    public function setMailcabinet(string $mailcabinet): self
    {
        $this->mailcabinet = $mailcabinet;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getRoles()
    {
        // Return an array of roles for this user, e.g. ['ROLE_PATIENT']
    }

    public function getPassword()
    {
        return $this->mdp;
    }

    public function getSalt()
    {
        // Leave this empty unless you are using "bcrypt" for password hashing
        // In that case, you should return a unique salt for each user
    }

    public function getUsername()
    {
        return $this->email;
    }

    public function eraseCredentials()
    {
        // This method is required by the UserInterface, but can usually be left empty
    }


}
