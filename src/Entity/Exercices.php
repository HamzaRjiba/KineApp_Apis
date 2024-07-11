<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Exercices
 *
 * @ORM\Table(name="exercices", indexes={@ORM\Index(name="dossier_medical_id", columns={"kine_id"}), @ORM\Index(name="exercice_id", columns={"programme_id"})})
 * @ORM\Entity
 */
class Exercices
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
     * @ORM\Column(name="repetitions", type="integer", nullable=false)
     */
    private $repetitions;

    /**
     * @var int
     *
     * @ORM\Column(name="series", type="integer", nullable=false)
     */
    private $series;

    /**
     * @var string
     *
     * @ORM\Column(name="chemin", type="string", length=255, nullable=false)
     */
    private $chemin;

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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255, nullable=true)
     */
    private $nom;

    /**
     * @var \Programme
     *
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="programme_id", referencedColumnName="id")
     * })
     */
    private $programme;

    /**
     * @var \Kinesitherapeutes
     *
     * @ORM\ManyToOne(targetEntity="Kinesitherapeutes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="kine_id", referencedColumnName="id")
     * })
     */
    private $kine;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRepetitions(): ?int
    {
        return $this->repetitions;
    }

    public function setRepetitions(int $repetitions): self
    {
        $this->repetitions = $repetitions;

        return $this;
    }

    public function getSeries(): ?int
    {
        return $this->series;
    }

    public function setSeries(int $series): self
    {
        $this->series = $series;

        return $this;
    }

    public function getChemin(): ?string
    {
        return $this->chemin;
    }

    public function setChemin(string $chemin): self
    {
        $this->chemin = $chemin;

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

    public function getProgramme(): ?Programme
    {
        return $this->programme;
    }

    public function setProgramme(?Programme $programme): self
    {
        $this->programme = $programme;

        return $this;
    }

    public function getKine(): ?Kinesitherapeutes
    {
        return $this->kine;
    }

    public function setKine(?Kinesitherapeutes $kine): self
    {
        $this->kine = $kine;

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(?string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }


}
