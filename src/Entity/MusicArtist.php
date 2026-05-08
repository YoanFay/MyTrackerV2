<?php

namespace App\Entity;

use App\Repository\MusicArtistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicArtistRepository::class)]
class MusicArtist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Music>
     */
    #[ORM\OneToMany(targetEntity: Music::class, mappedBy: 'musicArtist')]
    private Collection $music;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $mbid = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $plexId = null;

    public function __construct()
    {
        $this->music = new ArrayCollection();
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

    /**
     * @return Collection<int, Music>
     */
    public function getMusic(): Collection
    {
        return $this->music;
    }

    public function addMusic(Music $music): static
    {
        if (!$this->music->contains($music)) {
            $this->music->add($music);
            $music->setMusicArtist($this);
        }

        return $this;
    }

    public function removeMusic(Music $music): static
    {
        if ($this->music->removeElement($music)) {
            // set the owning side to null (unless already changed)
            if ($music->getMusicArtist() === $this) {
                $music->setMusicArtist(null);
            }
        }

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
