<?php

namespace App\Entity;

use App\Repository\GameCompanyRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GameCompanyRepository::class)]
class GameCompany
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $createdAt = null;

    #[ORM\Column(length: 255)]
    private string $slug;

    #[ORM\Column]
    private int $igdbId;

    /**
     * @var Collection<int, GamePlatform>
     */
    #[ORM\OneToMany(targetEntity: GamePlatform::class, mappedBy: 'gameCompany')]
    private Collection $gamePlatforms;

    /**
     * @var Collection<int, InvolvedGameCompany>
     */
    #[ORM\OneToMany(targetEntity: InvolvedGameCompany::class, mappedBy: 'gameCompany')]
    private Collection $involvedGameCompanies;

    public function __construct()
    {
        $this->gamePlatforms = new ArrayCollection();
        $this->involvedGameCompanies = new ArrayCollection();
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

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

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

    public function getIgdbId(): ?int
    {
        return $this->igdbId;
    }

    public function setIgdbId(int $igdbId): static
    {
        $this->igdbId = $igdbId;

        return $this;
    }

    /**
     * @return Collection<int, GamePlatform>
     */
    public function getGamePlatforms(): Collection
    {
        return $this->gamePlatforms;
    }

    public function addGamePlatform(GamePlatform $gamePlatform): static
    {
        if (!$this->gamePlatforms->contains($gamePlatform)) {
            $this->gamePlatforms->add($gamePlatform);
            $gamePlatform->setGameCompany($this);
        }

        return $this;
    }

    public function removeGamePlatform(GamePlatform $gamePlatform): static
    {
        if ($this->gamePlatforms->removeElement($gamePlatform)) {
            // set the owning side to null (unless already changed)
            if ($gamePlatform->getGameCompany() === $this) {
                $gamePlatform->setGameCompany(null);
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
            $involvedGameCompany->setGameCompany($this);
        }

        return $this;
    }

    public function removeInvolvedGameCompany(InvolvedGameCompany $involvedGameCompany): static
    {
        if ($this->involvedGameCompanies->removeElement($involvedGameCompany)) {
            // set the owning side to null (unless already changed)
            if ($involvedGameCompany->getGameCompany() === $this) {
                $involvedGameCompany->setGameCompany(null);
            }
        }

        return $this;
    }
}
