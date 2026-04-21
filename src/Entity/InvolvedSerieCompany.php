<?php

namespace App\Entity;

use App\Repository\InvolvedSerieCompanyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvolvedSerieCompanyRepository::class)]
class InvolvedSerieCompany
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'involvedSerieCompanies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Serie $serie = null;

    #[ORM\ManyToOne(inversedBy: 'involvedSerieCompanies')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SerieCompany $company = null;

    #[ORM\Column]
    private ?bool $isNetwork = false;

    #[ORM\Column]
    private ?bool $isStudio = false;

    #[ORM\Column]
    private ?bool $isProducer = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setSerie(?Serie $serie): static
    {
        $this->serie = $serie;

        return $this;
    }

    public function getCompany(): ?SerieCompany
    {
        return $this->company;
    }

    public function setCompany(?SerieCompany $company): static
    {
        $this->company = $company;

        return $this;
    }

    public function isNetwork(): ?bool
    {
        return $this->isNetwork;
    }

    public function setIsNetwork(bool $isNetwork): static
    {
        $this->isNetwork = $isNetwork;

        return $this;
    }

    public function isStudio(): ?bool
    {
        return $this->isStudio;
    }

    public function setIsStudio(bool $isStudio): static
    {
        $this->isStudio = $isStudio;

        return $this;
    }

    public function isProducer(): ?bool
    {
        return $this->isProducer;
    }

    public function setIsProducer(bool $isProducer): static
    {
        $this->isProducer = $isProducer;

        return $this;
    }
}
