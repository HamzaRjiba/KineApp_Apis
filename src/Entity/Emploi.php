<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emploi
 *
 * @ORM\Table(name="emploi", indexes={@ORM\Index(name="kinesitherapeutes_id", columns={"kinesitherapeutes_id"})})
 * @ORM\Entity
 */
class Emploi
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
     * @ORM\Column(name="lundi", type="string", length=255, nullable=true)
     */
    private $lundi;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mardi", type="string", length=255, nullable=true)
     */
    private $mardi;

    /**
     * @var string|null
     *
     * @ORM\Column(name="mercredi", type="string", length=255, nullable=true)
     */
    private $mercredi;

    /**
     * @var string|null
     *
     * @ORM\Column(name="jeudi", type="string", length=255, nullable=true)
     */
    private $jeudi;

    /**
     * @var string|null
     *
     * @ORM\Column(name="vendredi", type="string", length=255, nullable=true)
     */
    private $vendredi;

    /**
     * @var string|null
     *
     * @ORM\Column(name="samedi", type="string", length=255, nullable=true)
     */
    private $samedi;

    /**
     * @var string|null
     *
     * @ORM\Column(name="dimanche", type="string", length=255, nullable=true)
     */
    private $dimanche;

    /**
     * @var \Kinesitherapeutes
     *
     * @ORM\ManyToOne(targetEntity="Kinesitherapeutes")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="kinesitherapeutes_id", referencedColumnName="id")
     * })
     */
    private $kinesitherapeutes;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLundi(): ?string
    {
        return $this->lundi;
    }

    public function setLundi(?string $lundi): self
    {
        $this->lundi = $lundi;

        return $this;
    }

    public function getMardi(): ?string
    {
        return $this->mardi;
    }

    public function setMardi(?string $mardi): self
    {
        $this->mardi = $mardi;

        return $this;
    }

    public function getMercredi(): ?string
    {
        return $this->mercredi;
    }

    public function setMercredi(?string $mercredi): self
    {
        $this->mercredi = $mercredi;

        return $this;
    }

    public function getJeudi(): ?string
    {
        return $this->jeudi;
    }

    public function setJeudi(?string $jeudi): self
    {
        $this->jeudi = $jeudi;

        return $this;
    }

    public function getVendredi(): ?string
    {
        return $this->vendredi;
    }

    public function setVendredi(?string $vendredi): self
    {
        $this->vendredi = $vendredi;

        return $this;
    }

    public function getSamedi(): ?string
    {
        return $this->samedi;
    }

    public function setSamedi(?string $samedi): self
    {
        $this->samedi = $samedi;

        return $this;
    }

    public function getDimanche(): ?string
    {
        return $this->dimanche;
    }

    public function setDimanche(?string $dimanche): self
    {
        $this->dimanche = $dimanche;

        return $this;
    }

    public function getKinesitherapeutes(): ?Kinesitherapeutes
    {
        return $this->kinesitherapeutes;
    }

    public function setKinesitherapeutes(?Kinesitherapeutes $kinesitherapeutes): self
    {
        $this->kinesitherapeutes = $kinesitherapeutes;

        return $this;
    }


}
