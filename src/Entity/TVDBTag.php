<?php

namespace App\Entity;

use App\Repository\TVDBTagRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TVDBTagRepository::class)]
class TVDBTag
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $namenameEng = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $nameFra = null;

    #[ORM\ManyToOne(inversedBy: 'tVDBTags')]
    private ?TVDBTagType $tvdbTagType = null;

    /**
     * @var Collection<int, Serie>
     */
    #[ORM\ManyToMany(targetEntity: Serie::class, mappedBy: 'tvdbTags')]
    private Collection $series;

    public function __construct()
    {
        $this->series = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNamenameEng(): ?string
    {
        return $this->namenameEng;
    }

    public function setNamenameEng(?string $namenameEng): static
    {
        $this->namenameEng = $namenameEng;

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

    public function getTvdbTagType(): ?TVDBTagType
    {
        return $this->tvdbTagType;
    }

    public function setTvdbTagType(?TVDBTagType $tvdbTagType): static
    {
        $this->tvdbTagType = $tvdbTagType;

        return $this;
    }

    /**
     * @return Collection<int, Serie>
     */
    public function getSeries(): Collection
    {
        return $this->series;
    }

    public function addSeries(Serie $series): static
    {
        if (!$this->series->contains($series)) {
            $this->series->add($series);
            $series->addTvdbTag($this);
        }

        return $this;
    }

    public function removeSeries(Serie $series): static
    {
        if ($this->series->removeElement($series)) {
            $series->removeTvdbTag($this);
        }

        return $this;
    }
}
