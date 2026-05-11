<?php

namespace App\Entity;

use App\Repository\GamePlatformRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GamePlatformRepository::class)]
class GamePlatform
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(length: 255)]
    private string $slug;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $releaseDate = null;

    #[ORM\ManyToOne(inversedBy: 'gamePlatforms')]
    private ?GameCompany $gameCompany = null;

    /**
     * @var Collection<int, GameRelease>
     */
    #[ORM\OneToMany(targetEntity: GameRelease::class, mappedBy: 'gamePlatform')]
    private Collection $gameReleases;

    public function __construct()
    {
        $this->gameReleases = new ArrayCollection();
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

    public function getReleaseDate(): ?DateTime
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?DateTime $releaseDate): static
    {
        $this->releaseDate = $releaseDate;

        return $this;
    }

    public function getGameCompany(): ?GameCompany
    {
        return $this->gameCompany;
    }

    public function setGameCompany(?GameCompany $gameCompany): static
    {
        $this->gameCompany = $gameCompany;

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
            $gameRelease->setGamePlatform($this);
        }

        return $this;
    }

    public function removeGameRelease(GameRelease $gameRelease): static
    {
        if ($this->gameReleases->removeElement($gameRelease)) {
            // set the owning side to null (unless already changed)
            if ($gameRelease->getGamePlatform() === $this) {
                $gameRelease->setGamePlatform(null);
            }
        }

        return $this;
    }
}
