<?php

namespace App\Entity;

use App\Repository\EpisodeShowRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: EpisodeShowRepository::class)]
class EpisodeShow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private DateTime $showDate;

    #[ORM\ManyToOne(inversedBy: 'episodeShows')]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\ManyToOne(inversedBy: 'episodeShows')]
    #[ORM\JoinColumn(nullable: false)]
    private Episode $episode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getShowDate(): ?DateTime
    {
        return $this->showDate;
    }

    public function setShowDate(DateTime $showDate): static
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

    public function getEpisode(): ?Episode
    {
        return $this->episode;
    }

    public function setEpisode(?Episode $episode): static
    {
        $this->episode = $episode;

        return $this;
    }
}
