<?php

namespace App\Entity;

use App\Repository\MangaTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaTypeRepository::class)]
class MangaType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, Manga>
     */
    #[ORM\OneToMany(targetEntity: Manga::class, mappedBy: 'mangaType')]
    private Collection $mangas;

    public function __construct()
    {
        $this->mangas = new ArrayCollection();
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
     * @return Collection<int, Manga>
     */
    public function getMangas(): Collection
    {
        return $this->mangas;
    }

    public function addManga(Manga $manga): static
    {
        if (!$this->mangas->contains($manga)) {
            $this->mangas->add($manga);
            $manga->setMangaType($this);
        }

        return $this;
    }

    public function removeManga(Manga $manga): static
    {
        if ($this->mangas->removeElement($manga)) {
            // set the owning side to null (unless already changed)
            if ($manga->getMangaType() === $this) {
                $manga->setMangaType(null);
            }
        }

        return $this;
    }
}
