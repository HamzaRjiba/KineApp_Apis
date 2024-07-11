<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Programme
 *
 * @ORM\Table(name="programme", indexes={@ORM\Index(name="kineId", columns={"kineId"})})
 * @ORM\Entity
 */
class Programme
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
     * @var int
     *
     * @ORM\Column(name="kineId", type="integer", nullable=false)
     */
    private $kineid;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=false)
     */
    private $nom;

     /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $nomprog;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbseance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $temps;

    /**
     * @var string|null
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

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

    public function getKineid(): ?int
    {
        return $this->kineid;
    }

    public function setKineid(int $kineid): self
    {
        $this->kineid = $kineid;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getNomprog(): ?string
    {
        return $this->nomprog;
    }

    public function setNomprog(?string $nomprog): self
    {
        $this->nomprog = $nomprog;

        return $this;
    }

    public function getNbseance(): ?int
    {
        return $this->nbseance;
    }

    public function setNbseance(?int $nbseance): self
    {
        $this->nbseance = $nbseance;

        return $this;
    }

    public function getTemps(): ?int
    {
        return $this->temps;
    }

    public function setTemps(?int $temps): self
    {
        $this->temps = $temps;

        return $this;
    }


}
