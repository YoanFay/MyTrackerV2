<?php

namespace App\Entity;

use App\Repository\GameReleaseStatusRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameReleaseStatusRepository::class)]
class GameReleaseStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    /**
     * @var Collection<int, GameRelease>
     */
    #[ORM\OneToMany(targetEntity: GameRelease::class, mappedBy: 'status')]
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
            $gameRelease->setStatus($this);
        }

        return $this;
    }

    public function removeGameRelease(GameRelease $gameRelease): static
    {
        if ($this->gameReleases->removeElement($gameRelease)) {
            // set the owning side to null (unless already changed)
            if ($gameRelease->getStatus() === $this) {
                $gameRelease->setStatus(null);
            }
        }

        return $this;
    }
}
