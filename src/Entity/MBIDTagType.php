<?php

namespace App\Entity;

use App\Repository\MBIDTagTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MBIDTagTypeRepository::class)]
class MBIDTagType
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $name;

    /**
     * @var Collection<int, MBIDTag>
     */
    #[ORM\OneToMany(targetEntity: MBIDTag::class, mappedBy: 'mbidTagType')]
    private Collection $mBIDTags;

    public function __construct()
    {
        $this->mBIDTags = new ArrayCollection();
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
     * @return Collection<int, MBIDTag>
     */
    public function getMBIDTags(): Collection
    {
        return $this->mBIDTags;
    }

    public function addMBIDTag(MBIDTag $mBIDTag): static
    {
        if (!$this->mBIDTags->contains($mBIDTag)) {
            $this->mBIDTags->add($mBIDTag);
            $mBIDTag->setMbidTagType($this);
        }

        return $this;
    }

    public function removeMBIDTag(MBIDTag $mBIDTag): static
    {
        if ($this->mBIDTags->removeElement($mBIDTag)) {
            // set the owning side to null (unless already changed)
            if ($mBIDTag->getMbidTagType() === $this) {
                $mBIDTag->setMbidTagType(null);
            }
        }

        return $this;
    }
}
