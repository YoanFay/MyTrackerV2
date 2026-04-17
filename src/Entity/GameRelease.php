<?php

namespace App\Entity;

use App\Repository\GameReleaseRepository;
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
    private ?\DateTime $releaseDate = null;

    #[ORM\ManyToOne(inversedBy: 'gameReleases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GamePlatform $gamePlatform = null;

    #[ORM\ManyToOne(inversedBy: 'gameReleases')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(\DateTime $releaseDate): static
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
}
