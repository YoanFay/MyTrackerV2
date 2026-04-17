<?php

namespace App\Entity;

use App\Repository\MBIDTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MBIDTagRepository::class)]
class MBIDTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $plexId = null;

    #[ORM\ManyToOne(inversedBy: 'mBIDTags')]
    private ?MBIDTagType $mbidTagType = null;

    /**
     * @var Collection<int, Music>
     */
    #[ORM\ManyToMany(targetEntity: Music::class, mappedBy: 'musicTags')]
    private Collection $music;

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

    public function getPlexId(): ?string
    {
        return $this->plexId;
    }

    public function setPlexId(?string $plexId): static
    {
        $this->plexId = $plexId;

        return $this;
    }

    public function getMbidTagType(): ?MBIDTagType
    {
        return $this->mbidTagType;
    }

    public function setMbidTagType(?MBIDTagType $mbidTagType): static
    {
        $this->mbidTagType = $mbidTagType;

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
            $music->addMusicTag($this);
        }

        return $this;
    }

    public function removeMusic(Music $music): static
    {
        if ($this->music->removeElement($music)) {
            $music->removeMusicTag($this);
        }

        return $this;
    }
}
