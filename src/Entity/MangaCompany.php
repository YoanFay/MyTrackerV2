<?php

namespace App\Entity;

use App\Repository\MangaCompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MangaCompanyRepository::class)]
class MangaCompany
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    /**
     * @var Collection<int, InvolvedMangaCompany>
     */
    #[ORM\OneToMany(targetEntity: InvolvedMangaCompany::class, mappedBy: 'mangaCompany')]
    private Collection $involvedMangaCompanies;

    public function __construct()
    {
        $this->involvedMangaCompanies = new ArrayCollection();
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
            $involvedMangaCompany->setMangaCompany($this);
        }

        return $this;
    }

    public function removeInvolvedMangaCompany(InvolvedMangaCompany $involvedMangaCompany): static
    {
        if ($this->involvedMangaCompanies->removeElement($involvedMangaCompany)) {
            // set the owning side to null (unless already changed)
            if ($involvedMangaCompany->getMangaCompany() === $this) {
                $involvedMangaCompany->setMangaCompany(null);
            }
        }

        return $this;
    }
}
