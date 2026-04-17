<?php

namespace App\Entity;

use App\Repository\GameTrackerRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameTrackerRepository::class)]
class GameTracker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $startDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $endDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $completeDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $endTime = null;

    #[ORM\Column(nullable: true)]
    private ?int $completeTime = null;

    #[ORM\Column]
    private ?bool $isNoComplete = null;

    #[ORM\ManyToOne(inversedBy: 'gameTrackers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?GamePlatform $gamePlatform = null;

    #[ORM\ManyToOne(inversedBy: 'gameTrackers')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): ?\DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTime $startDate): static
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): ?\DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?\DateTime $endDate): static
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getCompleteDate(): ?\DateTime
    {
        return $this->completeDate;
    }

    public function setCompleteDate(?\DateTime $completeDate): static
    {
        $this->completeDate = $completeDate;

        return $this;
    }

    public function getEndTime(): ?int
    {
        return $this->endTime;
    }

    public function setEndTime(?int $endTime): static
    {
        $this->endTime = $endTime;

        return $this;
    }

    public function getCompleteTime(): ?int
    {
        return $this->completeTime;
    }

    public function setCompleteTime(?int $completeTime): static
    {
        $this->completeTime = $completeTime;

        return $this;
    }

    public function isNoComplete(): ?bool
    {
        return $this->isNoComplete;
    }

    public function setIsNoComplete(bool $isNoComplete): static
    {
        $this->isNoComplete = $isNoComplete;

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

    public function getGamePlatform(): ?GamePlatform
    {
        return $this->gamePlatform;
    }

    public function setGamePlatform(?GamePlatform $gamePlatform): static
    {
        $this->gamePlatform = $gamePlatform;

        return $this;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }
}
