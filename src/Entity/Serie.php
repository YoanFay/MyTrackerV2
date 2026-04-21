<?php

namespace App\Entity;

use App\Repository\SerieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieRepository::class)]
class Serie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $plexId = null;

    #[ORM\Column(nullable: true)]
    private ?int $tvdbId = null;

    #[ORM\Column]
    private ?bool $isVfName = false;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $status = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $firstAired = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $lastAired = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $nextAired = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $lastSeasonName = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nextAiredFormat = null;

    #[ORM\Column(nullable: true)]
    private ?int $score = 0;

    #[ORM\ManyToOne(inversedBy: 'series')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SerieType $serieType = null;

    /**
     * @var Collection<int, SerieUpdate>
     */
    #[ORM\OneToMany(targetEntity: SerieUpdate::class, mappedBy: 'serie')]
    private Collection $serieUpdates;

    /**
     * @var Collection<int, Episode>
     */
    #[ORM\OneToMany(targetEntity: Episode::class, mappedBy: 'serie')]
    private Collection $episodes;

    /**
     * @var Collection<int, InvolvedSerieCompany>
     */
    #[ORM\OneToMany(targetEntity: InvolvedSerieCompany::class, mappedBy: 'serie')]
    private Collection $involvedSerieCompanies;

    /**
     * @var Collection<int, TVDBTag>
     */
    #[ORM\ManyToMany(targetEntity: TVDBTag::class, inversedBy: 'series')]
    private Collection $tvdbTags;

    /**
     * @var Collection<int, TVDBGenre>
     */
    #[ORM\ManyToMany(targetEntity: TVDBGenre::class, inversedBy: 'series')]
    private Collection $tvdbGenres;

    /**
     * @var Collection<int, AnimeTheme>
     */
    #[ORM\ManyToMany(targetEntity: AnimeTheme::class, inversedBy: 'series')]
    private Collection $animeThemes;

    /**
     * @var Collection<int, AnimeGenre>
     */
    #[ORM\ManyToMany(targetEntity: AnimeGenre::class, inversedBy: 'series')]
    private Collection $animeGenres;

    public function __construct()
    {
        $this->serieUpdates = new ArrayCollection();
        $this->episodes = new ArrayCollection();
        $this->involvedSerieCompanies = new ArrayCollection();
        $this->tvdbTags = new ArrayCollection();
        $this->tvdbGenres = new ArrayCollection();
        $this->animeThemes = new ArrayCollection();
        $this->animeGenres = new ArrayCollection();
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

    public function getPlexId(): ?string
    {
        return $this->plexId;
    }

    public function setPlexId(?string $plexId): static
    {
        $this->plexId = $plexId;

        return $this;
    }

    public function getTvdbId(): ?int
    {
        return $this->tvdbId;
    }

    public function setTvdbId(?int $tvdbId): static
    {
        $this->tvdbId = $tvdbId;

        return $this;
    }

    public function isVfName(): ?bool
    {
        return $this->isVfName;
    }

    public function setIsVfName(bool $isVfName): static
    {
        $this->isVfName = $isVfName;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(?string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getFirstAired(): ?\DateTime
    {
        return $this->firstAired;
    }

    public function setFirstAired(?\DateTime $firstAired): static
    {
        $this->firstAired = $firstAired;

        return $this;
    }

    public function getLastAired(): ?\DateTime
    {
        return $this->lastAired;
    }

    public function setLastAired(?\DateTime $lastAired): static
    {
        $this->lastAired = $lastAired;

        return $this;
    }

    public function getNextAired(): ?\DateTime
    {
        return $this->nextAired;
    }

    public function setNextAired(?\DateTime $nextAired): static
    {
        $this->nextAired = $nextAired;

        return $this;
    }

    public function getNameEng(): ?string
    {
        return $this->nameEng;
    }

    public function setNameEng(?string $nameEng): static
    {
        $this->nameEng = $nameEng;

        return $this;
    }

    public function getLastSeasonName(): ?string
    {
        return $this->lastSeasonName;
    }

    public function setLastSeasonName(?string $lastSeasonName): static
    {
        $this->lastSeasonName = $lastSeasonName;

        return $this;
    }

    public function getNextAiredFormat(): ?string
    {
        return $this->nextAiredFormat;
    }

    public function setNextAiredFormat(?string $nextAiredFormat): static
    {
        $this->nextAiredFormat = $nextAiredFormat;

        return $this;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): static
    {
        $this->score = $score;

        return $this;
    }

    public function getSerieType(): ?SerieType
    {
        return $this->serieType;
    }

    public function setSerieType(?SerieType $serieType): static
    {
        $this->serieType = $serieType;

        return $this;
    }

    /**
     * @return Collection<int, SerieUpdate>
     */
    public function getSerieUpdates(): Collection
    {
        return $this->serieUpdates;
    }

    public function addSerieUpdate(SerieUpdate $serieUpdate): static
    {
        if (!$this->serieUpdates->contains($serieUpdate)) {
            $this->serieUpdates->add($serieUpdate);
            $serieUpdate->setSerie($this);
        }

        return $this;
    }

    public function removeSerieUpdate(SerieUpdate $serieUpdate): static
    {
        if ($this->serieUpdates->removeElement($serieUpdate)) {
            // set the owning side to null (unless already changed)
            if ($serieUpdate->getSerie() === $this) {
                $serieUpdate->setSerie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Episode>
     */
    public function getEpisodes(): Collection
    {
        return $this->episodes;
    }

    public function addEpisode(Episode $episode): static
    {
        if (!$this->episodes->contains($episode)) {
            $this->episodes->add($episode);
            $episode->setSerie($this);
        }

        return $this;
    }

    public function removeEpisode(Episode $episode): static
    {
        if ($this->episodes->removeElement($episode)) {
            // set the owning side to null (unless already changed)
            if ($episode->getSerie() === $this) {
                $episode->setSerie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InvolvedSerieCompany>
     */
    public function getInvolvedSerieCompanies(): Collection
    {
        return $this->involvedSerieCompanies;
    }

    public function addInvolvedSerieCompany(InvolvedSerieCompany $involvedSerieCompany): static
    {
        if (!$this->involvedSerieCompanies->contains($involvedSerieCompany)) {
            $this->involvedSerieCompanies->add($involvedSerieCompany);
            $involvedSerieCompany->setSerie($this);
        }

        return $this;
    }

    public function removeInvolvedSerieCompany(InvolvedSerieCompany $involvedSerieCompany): static
    {
        if ($this->involvedSerieCompanies->removeElement($involvedSerieCompany)) {
            // set the owning side to null (unless already changed)
            if ($involvedSerieCompany->getSerie() === $this) {
                $involvedSerieCompany->setSerie(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, TVDBTag>
     */
    public function getTvdbTags(): Collection
    {
        return $this->tvdbTags;
    }

    public function addTvdbTag(TVDBTag $tvdbTag): static
    {
        if (!$this->tvdbTags->contains($tvdbTag)) {
            $this->tvdbTags->add($tvdbTag);
        }

        return $this;
    }

    public function removeTvdbTag(TVDBTag $tvdbTag): static
    {
        $this->tvdbTags->removeElement($tvdbTag);

        return $this;
    }

    /**
     * @return Collection<int, TVDBGenre>
     */
    public function getTvdbGenres(): Collection
    {
        return $this->tvdbGenres;
    }

    public function addTvdbGenre(TVDBGenre $tvdbGenre): static
    {
        if (!$this->tvdbGenres->contains($tvdbGenre)) {
            $this->tvdbGenres->add($tvdbGenre);
        }

        return $this;
    }

    public function removeTvdbGenre(TVDBGenre $tvdbGenre): static
    {
        $this->tvdbGenres->removeElement($tvdbGenre);

        return $this;
    }

    /**
     * @return Collection<int, AnimeTheme>
     */
    public function getAnimeThemes(): Collection
    {
        return $this->animeThemes;
    }

    public function addAnimeTheme(AnimeTheme $animeTheme): static
    {
        if (!$this->animeThemes->contains($animeTheme)) {
            $this->animeThemes->add($animeTheme);
        }

        return $this;
    }

    public function removeAnimeTheme(AnimeTheme $animeTheme): static
    {
        $this->animeThemes->removeElement($animeTheme);

        return $this;
    }

    /**
     * @return Collection<int, AnimeGenre>
     */
    public function getAnimeGenres(): Collection
    {
        return $this->animeGenres;
    }

    public function addAnimeGenre(AnimeGenre $animeGenre): static
    {
        if (!$this->animeGenres->contains($animeGenre)) {
            $this->animeGenres->add($animeGenre);
        }

        return $this;
    }

    public function removeAnimeGenre(AnimeGenre $animeGenre): static
    {
        $this->animeGenres->removeElement($animeGenre);

        return $this;
    }
}
