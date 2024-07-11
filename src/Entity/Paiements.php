<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paiements
 *
 * @ORM\Table(name="paiements", indexes={@ORM\Index(name="patient_id", columns={"patient_id"}), @ORM\Index(name="kine_id", columns={"kine_id"})})
 * @ORM\Entity
 */
class Paiements
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
     * @ORM\Column(name="montant", type="decimal", precision=10, scale=2, nullable=false)
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="moyen_de_paiement", type="string", length=255, nullable=false)
     */
    private $moyenDePaiement;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $updatedAt ;

    /**
     * @var \Patients
     *
     * @ORM\ManyToOne(targetEntity="Patients")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="patient_id", referencedColumnName="id")
     * })
     */
    private $patient;

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

   

    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setMontant(string $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getMoyenDePaiement(): ?string
    {
        return $this->moyenDePaiement;
    }

    public function setMoyenDePaiement(string $moyenDePaiement): self
    {
        $this->moyenDePaiement = $moyenDePaiement;

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

    public function getPatient(): ?Patients
    {
        return $this->patient;
    }

    public function setPatient(?Patients $patient): self
    {
        $this->patient = $patient;

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


}
