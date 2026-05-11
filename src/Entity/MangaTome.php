<?php

namespace App\Entity;

use App\Repository\MangaTomeRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaTomeRepository::class)]
class MangaTome
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private int $tomeNumber;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?DateTime $releaseDate = null;

    #[ORM\Column(nullable: true)]
    private ?int $page = null;

    #[ORM\Column]
    private bool $isLastTome = false;

    #[ORM\ManyToOne(inversedBy: 'mangaTomes')]
    #[ORM\JoinColumn(nullable: false)]
    private Manga $manga;

    /**
     * @var Collection<int, MangaTomeRead>
     */
    #[ORM\OneToMany(targetEntity: MangaTomeRead::class, mappedBy: 'mangaTome')]
    private Collection $mangaTomeReads;

    public function __construct()
    {
        $this->mangaTomeReads = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTomeNumber(): ?int
    {
        return $this->tomeNumber;
    }

    public function setTomeNumber(int $tomeNumber): static
    {
        $this->tomeNumber = $tomeNumber;

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

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function setPage(?int $page): static
    {
        $this->page = $page;

        return $this;
    }

    public function isLastTome(): bool
    {
        return $this->isLastTome;
    }

    public function setIsLastTome(bool $isLastTome): static
    {
        $this->isLastTome = $isLastTome;

        return $this;
    }

    public function getManga(): ?Manga
    {
        return $this->manga;
    }

    public function setManga(?Manga $manga): static
    {
        $this->manga = $manga;

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
            $mangaTomeRead->setMangaTome($this);
        }

        return $this;
    }

    public function removeMangaTomeRead(MangaTomeRead $mangaTomeRead): static
    {
        if ($this->mangaTomeReads->removeElement($mangaTomeRead)) {
            // set the owning side to null (unless already changed)
            if ($mangaTomeRead->getMangaTome() === $this) {
                $mangaTomeRead->setMangaTome(null);
            }
        }

        return $this;
    }
}
