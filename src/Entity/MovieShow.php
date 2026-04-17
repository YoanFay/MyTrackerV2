<?php

namespace App\Entity;

use App\Repository\MovieShowRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MovieShowRepository::class)]
class MovieShow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTime $showDate = null;

    #[ORM\ManyToOne(inversedBy: 'movieShows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'movieShows')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Movie $movie = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShowDate(): ?\DateTime
    {
        return $this->showDate;
    }

    public function setShowDate(\DateTime $showDate): static
    {
        $this->showDate = $showDate;

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

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): static
    {
        $this->movie = $movie;

        return $this;
    }
}
