<?php

namespace App\Entity;

use App\Repository\AnimeThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimeThemeRepository::class)]
class AnimeTheme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $nameEng;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFra = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionEng = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $descriptionFra = null;

    #[ORM\Column]
    private int $level;

    /**
     * @var Collection<int, SerieAnimeTheme>
     */
    #[ORM\OneToMany(targetEntity: SerieAnimeTheme::class, mappedBy: 'animeTheme')]
    private Collection $serieAnimeThemes;

    public function __construct()
    {
        $this->serieAnimeThemes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNameEng(): ?string
    {
        return $this->nameEng;
    }

    public function setNameEng(string $nameEng): static
    {
        $this->nameEng = $nameEng;

        return $this;
    }

    public function getNameFra(): ?string
    {
        return $this->nameFra;
    }

    public function setNameFra(?string $nameFra): static
    {
        $this->nameFra = $nameFra;

        return $this;
    }

    public function getDescriptionEng(): ?string
    {
        return $this->descriptionEng;
    }

    public function setDescriptionEng(?string $descriptionEng): static
    {
        $this->descriptionEng = $descriptionEng;

        return $this;
    }

    public function getDescriptionFra(): ?string
    {
        return $this->descriptionFra;
    }

    public function setDescriptionFra(?string $descriptionFra): static
    {
        $this->descriptionFra = $descriptionFra;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): static
    {
        $this->level = $level;

        return $this;
    }

    /**
     * @return Collection<int, SerieAnimeTheme>
     */
    public function getSerieAnimeThemes(): Collection
    {
        return $this->serieAnimeThemes;
    }

    public function addSerieAnimeTheme(SerieAnimeTheme $serieAnimeTheme): static
    {
        if (!$this->serieAnimeThemes->contains($serieAnimeTheme)) {
            $this->serieAnimeThemes->add($serieAnimeTheme);
            $serieAnimeTheme->setAnimeTheme($this);
        }

        return $this;
    }

    public function removeSerieAnimeTheme(SerieAnimeTheme $serieAnimeTheme): static
    {
        if ($this->serieAnimeThemes->removeElement($serieAnimeTheme)) {
            // set the owning side to null (unless already changed)
            if ($serieAnimeTheme->getAnimeTheme() === $this) {
                $serieAnimeTheme->setAnimeTheme(null);
            }
        }

        return $this;
    }
}
