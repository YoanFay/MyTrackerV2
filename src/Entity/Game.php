<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameRepository::class)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\Column(nullable: true)]
    private ?float $rating = null;

    #[ORM\Column(nullable: true)]
    private ?int $ratingCount = null;

    #[ORM\Column(nullable: true)]
    private ?float $aggregatedRating = null;

    #[ORM\Column(nullable: true)]
    private ?int $aggregatedRatingCount = null;

    #[ORM\Column]
    private ?int $igdbId = null;

    #[ORM\ManyToOne(targetEntity: self::class, inversedBy: 'gameChilds')]
    private ?self $gameParent = null;

    /**
     * @var Collection<int, self>
     */
    #[ORM\OneToMany(targetEntity: self::class, mappedBy: 'gameParent')]
    private Collection $gameChilds;

    /**
     * @var Collection<int, GameRelease>
     */
    #[ORM\OneToMany(targetEntity: GameRelease::class, mappedBy: 'game')]
    private Collection $gameReleases;

    /**
     * @var Collection<int, GameTracker>
     */
    #[ORM\OneToMany(targetEntity: GameTracker::class, mappedBy: 'game')]
    private Collection $gameTrackers;

    /**
     * @var Collection<int, InvolvedGameCompany>
     */
    #[ORM\OneToMany(targetEntity: InvolvedGameCompany::class, mappedBy: 'game')]
    private Collection $involvedGameCompanies;

    /**
     * @var Collection<int, PlayerPerspective>
     */
    #[ORM\ManyToMany(targetEntity: PlayerPerspective::class, inversedBy: 'games')]
    private Collection $playerPerspectives;

    /**
     * @var Collection<int, GameMode>
     */
    #[ORM\ManyToMany(targetEntity: GameMode::class, inversedBy: 'games')]
    private Collection $gameModes;

    /**
     * @var Collection<int, IGDBGenre>
     */
    #[ORM\ManyToMany(targetEntity: IGDBGenre::class, inversedBy: 'games')]
    private Collection $igdbGenres;

    /**
     * @var Collection<int, IGDBTheme>
     */
    #[ORM\ManyToMany(targetEntity: IGDBTheme::class, inversedBy: 'games')]
    private Collection $igdbThemes;

    /**
     * @var Collection<int, GameCollection>
     */
    #[ORM\ManyToMany(targetEntity: GameCollection::class, inversedBy: 'games')]
    private Collection $gameCollections;

    public function __construct()
    {
        $this->gameChilds = new ArrayCollection();
        $this->gameReleases = new ArrayCollection();
        $this->gameTrackers = new ArrayCollection();
        $this->involvedGameCompanies = new ArrayCollection();
        $this->playerPerspectives = new ArrayCollection();
        $this->gameModes = new ArrayCollection();
        $this->igdbGenres = new ArrayCollection();
        $this->igdbThemes = new ArrayCollection();
        $this->gameCollections = new ArrayCollection();
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

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(?float $rating): static
    {
        $this->rating = $rating;

        return $this;
    }

    public function getRatingCount(): ?int
    {
        return $this->ratingCount;
    }

    public function setRatingCount(?int $ratingCount): static
    {
        $this->ratingCount = $ratingCount;

        return $this;
    }

    public function getAggregatedRating(): ?float
    {
        return $this->aggregatedRating;
    }

    public function setAggregatedRating(?float $aggregatedRating): static
    {
        $this->aggregatedRating = $aggregatedRating;

        return $this;
    }

    public function getAggregatedRatingCount(): ?int
    {
        return $this->aggregatedRatingCount;
    }

    public function setAggregatedRatingCount(?int $aggregatedRatingCount): static
    {
        $this->aggregatedRatingCount = $aggregatedRatingCount;

        return $this;
    }

    public function getIgdbId(): ?int
    {
        return $this->igdbId;
    }

    public function setIgdbId(int $igdbId): static
    {
        $this->igdbId = $igdbId;

        return $this;
    }

    public function getGameParent(): ?self
    {
        return $this->gameParent;
    }

    public function setGameParent(?self $gameParent): static
    {
        $this->gameParent = $gameParent;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getGameChilds(): Collection
    {
        return $this->gameChilds;
    }

    public function addGameChild(self $gameChild): static
    {
        if (!$this->gameChilds->contains($gameChild)) {
            $this->gameChilds->add($gameChild);
            $gameChild->setGameParent($this);
        }

        return $this;
    }

    public function removeGameChild(self $gameChild): static
    {
        if ($this->gameChilds->removeElement($gameChild)) {
            // set the owning side to null (unless already changed)
            if ($gameChild->getGameParent() === $this) {
                $gameChild->setGameParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GameRelease>
     */
    public function getGameReleases(): Collection
    {
        return $this->gameReleases;
    }

    public function addGameRelease(GameRelease $gameRelease): static
    {
        if (!$this->gameReleases->contains($gameRelease)) {
            $this->gameReleases->add($gameRelease);
            $gameRelease->setGame($this);
        }

        return $this;
    }

    public function removeGameRelease(GameRelease $gameRelease): static
    {
        if ($this->gameReleases->removeElement($gameRelease)) {
            // set the owning side to null (unless already changed)
            if ($gameRelease->getGame() === $this) {
                $gameRelease->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GameTracker>
     */
    public function getGameTrackers(): Collection
    {
        return $this->gameTrackers;
    }

    public function addGameTracker(GameTracker $gameTracker): static
    {
        if (!$this->gameTrackers->contains($gameTracker)) {
            $this->gameTrackers->add($gameTracker);
            $gameTracker->setGame($this);
        }

        return $this;
    }

    public function removeGameTracker(GameTracker $gameTracker): static
    {
        if ($this->gameTrackers->removeElement($gameTracker)) {
            // set the owning side to null (unless already changed)
            if ($gameTracker->getGame() === $this) {
                $gameTracker->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InvolvedGameCompany>
     */
    public function getInvolvedGameCompanies(): Collection
    {
        return $this->involvedGameCompanies;
    }

    public function addInvolvedGameCompany(InvolvedGameCompany $involvedGameCompany): static
    {
        if (!$this->involvedGameCompanies->contains($involvedGameCompany)) {
            $this->involvedGameCompanies->add($involvedGameCompany);
            $involvedGameCompany->setGame($this);
        }

        return $this;
    }

    public function removeInvolvedGameCompany(InvolvedGameCompany $involvedGameCompany): static
    {
        if ($this->involvedGameCompanies->removeElement($involvedGameCompany)) {
            // set the owning side to null (unless already changed)
            if ($involvedGameCompany->getGame() === $this) {
                $involvedGameCompany->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PlayerPerspective>
     */
    public function getPlayerPerspectives(): Collection
    {
        return $this->playerPerspectives;
    }

    public function addPlayerPerspective(PlayerPerspective $playerPerspective): static
    {
        if (!$this->playerPerspectives->contains($playerPerspective)) {
            $this->playerPerspectives->add($playerPerspective);
        }

        return $this;
    }

    public function removePlayerPerspective(PlayerPerspective $playerPerspective): static
    {
        $this->playerPerspectives->removeElement($playerPerspective);

        return $this;
    }

    /**
     * @return Collection<int, GameMode>
     */
    public function getGameModes(): Collection
    {
        return $this->gameModes;
    }

    public function addGameMode(GameMode $gameMode): static
    {
        if (!$this->gameModes->contains($gameMode)) {
            $this->gameModes->add($gameMode);
        }

        return $this;
    }

    public function removeGameMode(GameMode $gameMode): static
    {
        $this->gameModes->removeElement($gameMode);

        return $this;
    }

    /**
     * @return Collection<int, IGDBGenre>
     */
    public function getIgdbGenres(): Collection
    {
        return $this->igdbGenres;
    }

    public function addIgdbGenre(IGDBGenre $igdbGenre): static
    {
        if (!$this->igdbGenres->contains($igdbGenre)) {
            $this->igdbGenres->add($igdbGenre);
        }

        return $this;
    }

    public function removeIgdbGenre(IGDBGenre $igdbGenre): static
    {
        $this->igdbGenres->removeElement($igdbGenre);

        return $this;
    }

    /**
     * @return Collection<int, IGDBTheme>
     */
    public function getIgdbThemes(): Collection
    {
        return $this->igdbThemes;
    }

    public function addIgdbTheme(IGDBTheme $igdbTheme): static
    {
        if (!$this->igdbThemes->contains($igdbTheme)) {
            $this->igdbThemes->add($igdbTheme);
        }

        return $this;
    }

    public function removeIgdbTheme(IGDBTheme $igdbTheme): static
    {
        $this->igdbThemes->removeElement($igdbTheme);

        return $this;
    }

    /**
     * @return Collection<int, GameCollection>
     */
    public function getGameCollections(): Collection
    {
        return $this->gameCollections;
    }

    public function addGameCollection(GameCollection $gameCollection): static
    {
        if (!$this->gameCollections->contains($gameCollection)) {
            $this->gameCollections->add($gameCollection);
        }

        return $this;
    }

    public function removeGameCollection(GameCollection $gameCollection): static
    {
        $this->gameCollections->removeElement($gameCollection);

        return $this;
    }
}
