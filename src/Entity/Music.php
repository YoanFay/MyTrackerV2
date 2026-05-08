<?php

namespace App\Entity;

use App\Repository\MusicRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicRepository::class)]
class Music
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(nullable: true)]
    private ?int $duration = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mbid = null;

    #[ORM\ManyToOne(inversedBy: 'music')]
    private ?MusicArtist $musicArtist = null;

    /**
     * @var Collection<int, MusicListen>
     */
    #[ORM\OneToMany(targetEntity: MusicListen::class, mappedBy: 'music')]
    private Collection $musicListens;

    /**
     * @var Collection<int, MBIDTag>
     */
    #[ORM\ManyToMany(targetEntity: MBIDTag::class, inversedBy: 'music')]
    private Collection $musicTags;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $plexId = null;

    public function __construct()
    {
        $this->musicListens = new ArrayCollection();
        $this->musicTags = new ArrayCollection();
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): static
    {
        $this->duration = $duration;

        return $this;
    }

    public function getMbid(): ?string
    {
        return $this->mbid;
    }

    public function setMbid(?string $mbid): static
    {
        $this->mbid = $mbid;

        return $this;
    }

    public function getMusicArtist(): ?MusicArtist
    {
        return $this->musicArtist;
    }

    public function setMusicArtist(?MusicArtist $musicArtist): static
    {
        $this->musicArtist = $musicArtist;

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
            $musicListen->setMusic($this);
        }

        return $this;
    }

    public function removeMusicListen(MusicListen $musicListen): static
    {
        if ($this->musicListens->removeElement($musicListen)) {
            // set the owning side to null (unless already changed)
            if ($musicListen->getMusic() === $this) {
                $musicListen->setMusic(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MBIDTag>
     */
    public function getMusicTags(): Collection
    {
        return $this->musicTags;
    }

    public function addMusicTag(MBIDTag $musicTag): static
    {
        if (!$this->musicTags->contains($musicTag)) {
            $this->musicTags->add($musicTag);
        }

        return $this;
    }

    public function removeMusicTag(MBIDTag $musicTag): static
    {
        $this->musicTags->removeElement($musicTag);

        return $this;
    }

    public function getPlexId(): ?string
    {
        return $this->plexId;
    }

    public function setPlexId(string $plexId): static
    {
        $this->plexId = $plexId;

        return $this;
    }
}
