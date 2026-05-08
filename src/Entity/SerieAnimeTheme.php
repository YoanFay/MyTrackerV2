<?php

namespace App\Entity;

use App\Repository\SerieAnimeThemeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieAnimeThemeRepository::class)]
class SerieAnimeTheme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'serieAnimeThemes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Serie $serie = null;

    #[ORM\ManyToOne(inversedBy: 'serieAnimeThemes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?AnimeTheme $animeTheme = null;

    #[ORM\Column]
    private bool $isSpoiler = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAnimeTheme(): ?AnimeTheme
    {
        return $this->animeTheme;
    }

    public function setAnimeTheme(?AnimeTheme $animeTheme): static
    {
        $this->animeTheme = $animeTheme;

        return $this;
    }

    public function isSpoiler(): bool
    {
        return $this->isSpoiler;
    }

    public function setIsSpoiler(bool $isSpoiler): static
    {
        $this->isSpoiler = $isSpoiler;

        return $this;
    }
}
