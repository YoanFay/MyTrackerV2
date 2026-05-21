<?php

namespace App\Entity;

use App\Repository\SerieUpdateRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieUpdateRepository::class)]
class SerieUpdate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statusOld = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $statusNew = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $airedOld = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $airedNew = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $airedTypeOld = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $airedTypeNew = null;

    #[ORM\ManyToOne(inversedBy: 'serieUpdates')]
    #[ORM\JoinColumn(nullable: false)]
    private Serie $serie;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private DateTime $updateDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatusOld(): ?string
    {
        return $this->statusOld;
    }

    public function setStatusOld(?string $statusOld): static
    {
        $this->statusOld = $statusOld;

        return $this;
    }

    public function getStatusNew(): ?string
    {
        return $this->statusNew;
    }

    public function setStatusNew(?string $statusNew): static
    {
        $this->statusNew = $statusNew;

        return $this;
    }

    public function getAiredOld(): ?DateTime
    {
        return $this->airedOld;
    }

    public function setAiredOld(?DateTime $airedOld): static
    {
        $this->airedOld = $airedOld;

        return $this;
    }

    public function getAiredNew(): ?DateTime
    {
        return $this->airedNew;
    }

    public function setAiredNew(?DateTime $airedNew): static
    {
        $this->airedNew = $airedNew;

        return $this;
    }

    public function getAiredTypeOld(): ?string
    {
        return $this->airedTypeOld;
    }

    public function setAiredTypeOld(?string $airedTypeOld): static
    {
        $this->airedTypeOld = $airedTypeOld;

        return $this;
    }

    public function getAiredTypeNew(): ?string
    {
        return $this->airedTypeNew;
    }

    public function setAiredTypeNew(?string $airedTypeNew): static
    {
        $this->airedTypeNew = $airedTypeNew;

        return $this;
    }

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setSerie(?Serie $serie): static
    {
        $this->serie = $serie;

        return $this;
    }

    public function getUpdateDate(): ?DateTime
    {
        return $this->updateDate;
    }

    public function setUpdateDate(DateTime $updateDate): static
    {
        $this->updateDate = $updateDate;

        return $this;
    }
}
