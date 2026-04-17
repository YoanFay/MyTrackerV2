<?php

namespace App\Entity;

use App\Repository\InvolvedMangaCompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvolvedMangaCompanyRepository::class)]
class InvolvedMangaCompany
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'involvedMangaCompanies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Manga $manga = null;

    #[ORM\ManyToOne(inversedBy: 'involvedMangaCompanies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?MangaCompany $mangaCompany = null;

    #[ORM\Column]
    private ?bool $isAuthor = null;

    #[ORM\Column]
    private ?bool $isEditor = null;

    #[ORM\Column]
    private ?bool $isDesigner = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMangaCompany(): ?MangaCompany
    {
        return $this->mangaCompany;
    }

    public function setMangaCompany(?MangaCompany $mangaCompany): static
    {
        $this->mangaCompany = $mangaCompany;

        return $this;
    }

    public function isAuthor(): ?bool
    {
        return $this->isAuthor;
    }

    public function setIsAuthor(bool $isAuthor): static
    {
        $this->isAuthor = $isAuthor;

        return $this;
    }

    public function isEditor(): ?bool
    {
        return $this->isEditor;
    }

    public function setIsEditor(bool $isEditor): static
    {
        $this->isEditor = $isEditor;

        return $this;
    }

    public function isDesigner(): ?bool
    {
        return $this->isDesigner;
    }

    public function setIsDesigner(bool $isDesigner): static
    {
        $this->isDesigner = $isDesigner;

        return $this;
    }
}
