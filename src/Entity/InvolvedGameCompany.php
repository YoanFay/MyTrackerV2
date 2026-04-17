<?php

namespace App\Entity;

use App\Repository\InvolvedGameCompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvolvedGameCompanyRepository::class)]
class InvolvedGameCompany
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'involvedGameCompanies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Game $game = null;

    #[ORM\ManyToOne(inversedBy: 'involvedGameCompanies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?GameCompany $gameCompany = null;

    #[ORM\Column]
    private ?bool $isDeveloper = null;

    #[ORM\Column]
    private ?bool $isPorting = null;

    #[ORM\Column]
    private ?bool $isPublisher = null;

    #[ORM\Column]
    private ?bool $isSupporting = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): static
    {
        $this->game = $game;

        return $this;
    }

    public function getGameCompany(): ?GameCompany
    {
        return $this->gameCompany;
    }

    public function setGameCompany(?GameCompany $gameCompany): static
    {
        $this->gameCompany = $gameCompany;

        return $this;
    }

    public function isDeveloper(): ?bool
    {
        return $this->isDeveloper;
    }

    public function setIsDeveloper(bool $isDeveloper): static
    {
        $this->isDeveloper = $isDeveloper;

        return $this;
    }

    public function isPorting(): ?bool
    {
        return $this->isPorting;
    }

    public function setIsPorting(bool $isPorting): static
    {
        $this->isPorting = $isPorting;

        return $this;
    }

    public function isPublisher(): ?bool
    {
        return $this->isPublisher;
    }

    public function setIsPublisher(bool $isPublisher): static
    {
        $this->isPublisher = $isPublisher;

        return $this;
    }

    public function isSupporting(): ?bool
    {
        return $this->isSupporting;
    }

    public function setIsSupporting(bool $isSupporting): static
    {
        $this->isSupporting = $isSupporting;

        return $this;
    }
}
