<?php

namespace App\Entity;

use App\Repository\SerieCompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SerieCompanyRepository::class)]
class SerieCompany
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    /**
     * @var Collection<int, InvolvedSerieCompany>
     */
    #[ORM\OneToMany(targetEntity: InvolvedSerieCompany::class, mappedBy: 'company')]
    private Collection $involvedSerieCompanies;

    public function __construct()
    {
        $this->involvedSerieCompanies = new ArrayCollection();
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
     * @return Collection<int, InvolvedSerieCompany>
     */
    public function getInvolvedSerieCompanies(): Collection
    {
        return $this->involvedSerieCompanies;
    }

    public function addInvolvedSerieCompany(InvolvedSerieCompany $involvedSerieCompany): static
    {
        if (!$this->involvedSerieCompanies->contains($involvedSerieCompany)) {
            $this->involvedSerieCompanies->add($involvedSerieCompany);
            $involvedSerieCompany->setCompany($this);
        }

        return $this;
    }

    public function removeInvolvedSerieCompany(InvolvedSerieCompany $involvedSerieCompany): static
    {
        if ($this->involvedSerieCompanies->removeElement($involvedSerieCompany)) {
            // set the owning side to null (unless already changed)
            if ($involvedSerieCompany->getCompany() === $this) {
                $involvedSerieCompany->setCompany(null);
            }
        }

        return $this;
    }
}
