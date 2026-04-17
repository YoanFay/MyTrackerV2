<?php

namespace App\Entity;

use App\Repository\MusicListenRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MusicListenRepository::class)]
class MusicListen
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $listenAt = null;

    #[ORM\ManyToOne(inversedBy: 'musicListens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Music $music = null;

    #[ORM\ManyToOne(inversedBy: 'musicListens')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getListenAt(): ?\DateTime
    {
        return $this->listenAt;
    }

    public function setListenAt(\DateTime $listenAt): static
    {
        $this->listenAt = $listenAt;

        return $this;
    }

    public function getMusic(): ?Music
    {
        return $this->music;
    }

    public function setMusic(?Music $music): static
    {
        $this->music = $music;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }
}
