<?php

namespace App\Entity;

use App\Repository\MangaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaRepository::class)]
class Manga
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    private ?string $slug = null;

    #[ORM\ManyToOne(inversedBy: 'mangas')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MangaType $mangaType = null;

    /**
     * @var Collection<int, MangaTome>
     */
    #[ORM\OneToMany(targetEntity: MangaTome::class, mappedBy: 'manga')]
    private Collection $mangaTomes;

    /**
     * @var Collection<int, InvolvedMangaCompany>
     */
    #[ORM\OneToMany(targetEntity: InvolvedMangaCompany::class, mappedBy: 'manga')]
    private Collection $involvedMangaCompanies;

    /**
     * @var Collection<int, MangaGenre>
     */
    #[ORM\ManyToMany(targetEntity: MangaGenre::class, inversedBy: 'mangas')]
    private Collection $mangaGenres;

    /**
     * @var Collection<int, MangaTheme>
     */
    #[ORM\ManyToMany(targetEntity: MangaTheme::class, inversedBy: 'mangas')]
    private Collection $mangaThemes;

    public function __construct()
    {
        $this->mangaTomes = new ArrayCollection();
        $this->involvedMangaCompanies = new ArrayCollection();
        $this->mangaGenres = new ArrayCollection();
        $this->mangaThemes = new ArrayCollection();
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

    public function getMangaType(): ?MangaType
    {
        return $this->mangaType;
    }

    public function setMangaType(?MangaType $mangaType): static
    {
        $this->mangaType = $mangaType;

        return $this;
    }

    /**
     * @return Collection<int, MangaTome>
     */
    public function getMangaTomes(): Collection
    {
        return $this->mangaTomes;
    }

    public function addMangaTome(MangaTome $mangaTome): static
    {
        if (!$this->mangaTomes->contains($mangaTome)) {
            $this->mangaTomes->add($mangaTome);
            $mangaTome->setManga($this);
        }

        return $this;
    }

    public function removeMangaTome(MangaTome $mangaTome): static
    {
        if ($this->mangaTomes->removeElement($mangaTome)) {
            // set the owning side to null (unless already changed)
            if ($mangaTome->getManga() === $this) {
                $mangaTome->setManga(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, InvolvedMangaCompany>
     */
    public function getInvolvedMangaCompanies(): Collection
    {
        return $this->involvedMangaCompanies;
    }

    public function addInvolvedMangaCompany(InvolvedMangaCompany $involvedMangaCompany): static
    {
        if (!$this->involvedMangaCompanies->contains($involvedMangaCompany)) {
            $this->involvedMangaCompanies->add($involvedMangaCompany);
            $involvedMangaCompany->setManga($this);
        }

        return $this;
    }

    public function removeInvolvedMangaCompany(InvolvedMangaCompany $involvedMangaCompany): static
    {
        if ($this->involvedMangaCompanies->removeElement($involvedMangaCompany)) {
            // set the owning side to null (unless already changed)
            if ($involvedMangaCompany->getManga() === $this) {
                $involvedMangaCompany->setManga(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MangaGenre>
     */
    public function getMangaGenres(): Collection
    {
        return $this->mangaGenres;
    }

    public function addMangaGenre(MangaGenre $mangaGenre): static
    {
        if (!$this->mangaGenres->contains($mangaGenre)) {
            $this->mangaGenres->add($mangaGenre);
        }

        return $this;
    }

    public function removeMangaGenre(MangaGenre $mangaGenre): static
    {
        $this->mangaGenres->removeElement($mangaGenre);

        return $this;
    }

    /**
     * @return Collection<int, MangaTheme>
     */
    public function getMangaThemes(): Collection
    {
        return $this->mangaThemes;
    }

    public function addMangaTheme(MangaTheme $mangaTheme): static
    {
        if (!$this->mangaThemes->contains($mangaTheme)) {
            $this->mangaThemes->add($mangaTheme);
        }

        return $this;
    }

    public function removeMangaTheme(MangaTheme $mangaTheme): static
    {
        $this->mangaThemes->removeElement($mangaTheme);

        return $this;
    }
}
