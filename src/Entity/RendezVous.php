<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * RendezVous
 *
 * @ORM\Table(name="rendez_vous", indexes={@ORM\Index(name="patient_id", columns={"patient_id"}), @ORM\Index(name="kine_id", columns={"kine_id"})})
 * @ORM\Entity
 */
class RendezVous
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
     * @var string|null
     *
     * @ORM\Column(name="date_rendez_vous", type="string", length=255, nullable=false)
     */
    private $dateRendezVous;

     /**
     * @var string
     *
     * @ORM\Column(name="horaire", type="string", length=255, nullable=true)
     */
    private $horaire;


   
    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="string", length=255, nullable=false)
     */
    private $motif;

    /**
     * @var string
     *
     * @ORM\Column(name="remarques", type="text", length=65535, nullable=true)
     */
    private $remarques;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $statut;

   

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

    public function getDateRendezVous(): ?string
    {
        return $this->dateRendezVous;
    }

    public function setDateRendezVous(string $dateRendezVous): self
    {
        $this->dateRendezVous = $dateRendezVous;

        return $this;
    }


    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;

        return $this;
    }

    public function getRemarques(): ?string
    {
        return $this->remarques;
    }

    public function setRemarques(?string $remarques): self
    {
        $this->remarques = $remarques;

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

    public function getHoraire(): ?string
    {
        return $this->horaire;
    }

    public function setHoraire(?string $horaire): self
    {
        $this->horaire = $horaire;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }


}
