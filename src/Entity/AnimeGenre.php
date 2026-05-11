<?php

namespace App\Entity;

use App\Repository\AnimeGenreRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AnimeGenreRepository::class)]
class AnimeGenre
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $nameEng;

    /**
     * @var Collection<int, Serie>
     */
    #[ORM\ManyToMany(targetEntity: Serie::class, mappedBy: 'animeGenres')]
    private Collection $series;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFra = null;

    public function __construct()
    {
        $this->series = new ArrayCollection();
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

    /**
     * @return Collection<int, Serie>
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function addSeries(Serie $series): static
    {
        if (!$this->series->contains($series)) {
            $this->series->add($series);
            $series->addAnimeGenre($this);
        }

        return $this;
    }

    public function removeSeries(Serie $series): static
    {
        if ($this->series->removeElement($series)) {
            $series->removeAnimeGenre($this);
        }

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
}
