<?php

namespace App\Entity;

use App\Repository\TVDBTagTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TVDBTagTypeRepository::class)]
class TVDBTagType
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
     * @var Collection<int, TVDBTag>
     */
    #[ORM\OneToMany(targetEntity: TVDBTag::class, mappedBy: 'tvdbTagType')]
    private Collection $tVDBTags;

    public function __construct()
    {
        $this->tVDBTags = new ArrayCollection();
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
     * @return Collection<int, TVDBTag>
     */
    public function getTVDBTags(): Collection
    {
        return $this->tVDBTags;
    }

    public function addTVDBTag(TVDBTag $tVDBTag): static
    {
        if (!$this->tVDBTags->contains($tVDBTag)) {
            $this->tVDBTags->add($tVDBTag);
            $tVDBTag->setTvdbTagType($this);
        }

        return $this;
    }

    public function removeTVDBTag(TVDBTag $tVDBTag): static
    {
        if ($this->tVDBTags->removeElement($tVDBTag)) {
            // set the owning side to null (unless already changed)
            if ($tVDBTag->getTvdbTagType() === $this) {
                $tVDBTag->setTvdbTagType(null);
            }
        }

        return $this;
    }
}
