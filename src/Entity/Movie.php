<?php

namespace App\Entity;

use App\Repository\MovieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieRepository::class)]
class Movie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $tmbdId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $plexId = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column]
    private ?bool $updated = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $releaseDate = null;

    /**
     * @var Collection<int, MovieShow>
     */
    #[ORM\OneToMany(targetEntity: MovieShow::class, mappedBy: 'movie')]
    private Collection $movieShows;

    /**
     * @var Collection<int, TMDBGenre>
     */
    #[ORM\ManyToMany(targetEntity: TMDBGenre::class, inversedBy: 'movies')]
    private Collection $tmdbGenres;

    public function __construct()
    {
        $this->movieShows = new ArrayCollection();
        $this->tmdbGenres = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;

        return $this;
    }

    public function getTmbdId(): ?string
    {
        return $this->tmbdId;
    }

    public function setTmbdId(?string $tmbdId): static
    {
        $this->tmbdId = $tmbdId;

        return $this;
    }

    public function getPlexId(): ?string
    {
        return $this->plexId;
    }

    public function setPlexId(?string $plexId): static
    {
        $this->plexId = $plexId;

        return $this;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function isUpdated(): ?bool
    {
        return $this->updated;
    }

    public function setUpdated(bool $updated): static
    {
        $this->updated = $updated;

        return $this;
    }

    public function getReleaseDate(): ?\DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?\DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    /**
     * @return Collection<int, MovieShow>
     */
    public function getMovieShows(): Collection
    {
        return $this->movieShows;
    }

    public function addMovieShow(MovieShow $movieShow): static
    {
        if (!$this->movieShows->contains($movieShow)) {
            $this->movieShows->add($movieShow);
            $movieShow->setMovie($this);
        }

        return $this;
    }

    public function removeMovieShow(MovieShow $movieShow): static
    {
        if ($this->movieShows->removeElement($movieShow)) {
            // set the owning side to null (unless already changed)
            if ($movieShow->getMovie() === $this) {
                $movieShow->setMovie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TMDBGenre>
     */
    public function getTmdbGenres(): Collection
    {
        return $this->tmdbGenres;
    }

    public function addTmdbGenre(TMDBGenre $tmdbGenre): static
    {
        if (!$this->tmdbGenres->contains($tmdbGenre)) {
            $this->tmdbGenres->add($tmdbGenre);
        }

        return $this;
    }

    public function removeTmdbGenre(TMDBGenre $tmdbGenre): static
    {
        $this->tmdbGenres->removeElement($tmdbGenre);

        return $this;
    }
}
