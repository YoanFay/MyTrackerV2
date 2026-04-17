<?php

namespace App\Entity;

use App\Repository\IGDBThemeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IGDBThemeRepository::class)]
class IGDBTheme
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameEng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFra = null;

    /**
     * @var Collection<int, Game>
     */
    #[ORM\ManyToMany(targetEntity: Game::class, mappedBy: 'igdbThemes')]
    private Collection $games;

    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getNameFra(): ?string
    {
        return $this->nameFra;
    }

    public function setNameFra(?string $nameFra): static
    {
        $this->nameFra = $nameFra;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->addIgdbTheme($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            $game->removeIgdbTheme($this);
        }

        return $this;
    }
}
