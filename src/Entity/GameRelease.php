<?php

namespace App\Entity;

use App\Repository\GameReleaseRepository;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameReleaseRepository::class)]
class GameRelease
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private DateTime $releaseDate;

    #[ORM\ManyToOne(inversedBy: 'gameReleases')]
    #[ORM\JoinColumn(nullable: false)]
    private GamePlatform $gamePlatform;

    #[ORM\ManyToOne(inversedBy: 'gameReleases')]
    #[ORM\JoinColumn(nullable: false)]
    private Game $game;

    #[ORM\ManyToOne(inversedBy: 'gameReleases')]
    #[ORM\JoinColumn(nullable: false)]
    private GameReleaseStatus $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReleaseDate(): ?DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

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

    public function getStatus(): ?GameReleaseStatus
    {
        return $this->status;
    }

    public function setStatus(?GameReleaseStatus $status): static
    {
        $this->status = $status;

        return $this;
    }
}
