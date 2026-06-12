<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $plexName;

    /**
     * @var array<string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $roles = [];

    #[ORM\Column(length: 255)]
    private string $password;

    /**
     * @var Collection<int, MovieShow>
     */
    #[ORM\OneToMany(targetEntity: MovieShow::class, mappedBy: 'user')]
    private Collection $movieShows;

    /**
     * @var Collection<int, GameTracker>
     */
    #[ORM\OneToMany(targetEntity: GameTracker::class, mappedBy: 'user')]
    private Collection $gameTrackers;

    /**
     * @var Collection<int, MusicListen>
     */
    #[ORM\OneToMany(targetEntity: MusicListen::class, mappedBy: 'user')]
    private Collection $musicListens;

    /**
     * @var Collection<int, MangaTomeRead>
     */
    #[ORM\OneToMany(targetEntity: MangaTomeRead::class, mappedBy: 'user')]
    private Collection $mangaTomeReads;

    /**
     * @var Collection<int, EpisodeShow>
     */
    #[ORM\OneToMany(targetEntity: EpisodeShow::class, mappedBy: 'user')]
    private Collection $episodeShows;

    public function __construct()
    {
        $this->movieShows = new ArrayCollection();
        $this->gameTrackers = new ArrayCollection();
        $this->musicListens = new ArrayCollection();
        $this->mangaTomeReads = new ArrayCollection();
        $this->episodeShows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlexName(): ?string
    {
        return $this->plexName;
    }

    public function setPlexName(string $plexName): static
    {
        $this->plexName = $plexName;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return $this->plexName;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }

    /** @param array<string> $roles */
    public function setRoles(array $roles): static
    {
        $this->roles = $roles;
        return $this;
    }

    public function eraseCredentials(): void
    {

    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

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
            $movieShow->setUser($this);
        }

        return $this;
    }

    public function removeMovieShow(MovieShow $movieShow): static
    {
        if ($this->movieShows->removeElement($movieShow)) {
            // set the owning side to null (unless already changed)
            if ($movieShow->getUser() === $this) {
                $movieShow->setUser(null);
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
            $gameTracker->setUser($this);
        }

        return $this;
    }

    public function removeGameTracker(GameTracker $gameTracker): static
    {
        if ($this->gameTrackers->removeElement($gameTracker)) {
            // set the owning side to null (unless already changed)
            if ($gameTracker->getUser() === $this) {
                $gameTracker->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MusicListen>
     */
    public function getMusicListens(): Collection
    {
        return $this->musicListens;
    }

    public function addMusicListen(MusicListen $musicListen): static
    {
        if (!$this->musicListens->contains($musicListen)) {
            $this->musicListens->add($musicListen);
            $musicListen->setUser($this);
        }

        return $this;
    }

    public function removeMusicListen(MusicListen $musicListen): static
    {
        if ($this->musicListens->removeElement($musicListen)) {
            // set the owning side to null (unless already changed)
            if ($musicListen->getUser() === $this) {
                $musicListen->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MangaTomeRead>
     */
    public function getMangaTomeReads(): Collection
    {
        return $this->mangaTomeReads;
    }

    public function addMangaTomeRead(MangaTomeRead $mangaTomeRead): static
    {
        if (!$this->mangaTomeReads->contains($mangaTomeRead)) {
            $this->mangaTomeReads->add($mangaTomeRead);
            $mangaTomeRead->setUser($this);
        }

        return $this;
    }

    public function removeMangaTomeRead(MangaTomeRead $mangaTomeRead): static
    {
        if ($this->mangaTomeReads->removeElement($mangaTomeRead)) {
            // set the owning side to null (unless already changed)
            if ($mangaTomeRead->getUser() === $this) {
                $mangaTomeRead->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, EpisodeShow>
     */
    public function getEpisodeShows(): Collection
    {
        return $this->episodeShows;
    }

    public function addEpisodeShow(EpisodeShow $episodeShow): static
    {
        if (!$this->episodeShows->contains($episodeShow)) {
            $this->episodeShows->add($episodeShow);
            $episodeShow->setUser($this);
        }

        return $this;
    }

    public function removeEpisodeShow(EpisodeShow $episodeShow): static
    {
        if ($this->episodeShows->removeElement($episodeShow)) {
            // set the owning side to null (unless already changed)
            if ($episodeShow->getUser() === $this) {
                $episodeShow->setUser(null);
            }
        }

        return $this;
    }
}
