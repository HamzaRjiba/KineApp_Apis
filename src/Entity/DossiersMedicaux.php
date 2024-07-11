<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DossiersMedicaux
 *
 * @ORM\Table(name="dossiers_medicaux", indexes={@ORM\Index(name="rdvcontraint", columns={"rdv"})})
 * @ORM\Entity
 */
class DossiersMedicaux
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
     * @var \DateTime
     *
     * @ORM\Column(name="date_creation", type="date", nullable=false)
     */
    private $dateCreation;

    /**
     * @var string|null
     *
     * @ORM\Column(name="document", type="text", length=65535, nullable=true)
     */
    private $document;

    /**
     * @var string|null
     *
     * @ORM\Column(name="type", type="string", length=255, nullable=true)
     */
    private $type;

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

    /**
     * @var \RendezVous
     *
     * @ORM\ManyToOne(targetEntity="RendezVous")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="rdv", referencedColumnName="id")
     * })
     */
    private $rdv;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCreation(): ?\DateTimeInterface
    {
        return $this->dateCreation;
    }

    public function setDateCreation(\DateTimeInterface $dateCreation): self
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    public function getDocument(): ?string
    {
        return $this->document;
    }

    public function setDocument(?string $document): self
    {
        $this->document = $document;

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

    public function getRdv(): ?RendezVous
    {
        return $this->rdv;
    }

    public function setRdv(?RendezVous $rdv): self
    {
        $this->rdv = $rdv;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

        return $this;
    }


}
