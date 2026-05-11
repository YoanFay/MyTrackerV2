<?php

namespace App\Entity;

use App\Repository\MangaTomeReadRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaTomeReadRepository::class)]
class MangaTomeRead
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private DateTime $startDate;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $endDate = null;

    #[ORM\ManyToOne(inversedBy: 'mangaTomeReads')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(inversedBy: 'mangaTomeReads')]
    #[ORM\JoinColumn(nullable: false)]
    private MangaTome $mangaTome;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMangaTome(): ?MangaTome
    {
        return $this->mangaTome;
    }

    public function setMangaTome(?MangaTome $mangaTome): static
    {
        $this->mangaTome = $mangaTome;

        return $this;
    }
}
