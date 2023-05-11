<?php

namespace App\Entity;

use App\Repository\HistoriqueRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HistoriqueRepository::class)]
class Historique
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $chargement = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $extraction = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $dateChargement = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date_debut_extraction = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date_fin_extraction = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChargement(): ?string
    {
        return $this->chargement;
    }

    public function setChargement(?string $chargement): self
    {
        $this->chargement = $chargement;

        return $this;
    }

    public function getExtraction(): ?string
    {
        return $this->extraction;
    }

    public function setExtraction(?string $extraction): self
    {
        $this->extraction = $extraction;

        return $this;
    }

    public function getDateChargement(): ?\DateTimeInterface
    {
        return $this->dateChargement;
    }

    public function setDateChargement(?\DateTimeInterface $dateChargement): self
    {
        $this->dateChargement = $dateChargement;

        return $this;
    }

    public function getDateDebutExtraction(): ?\DateTimeInterface
    {
        return $this->date_debut_extraction;
    }

    public function setDateDebutExtraction(?\DateTimeInterface $date_debut_extraction): self
    {
        $this->date_debut_extraction = $date_debut_extraction;

        return $this;
    }

    public function getDateFinExtraction(): ?\DateTimeInterface
    {
        return $this->date_fin_extraction;
    }

    public function setDateFinExtraction(\DateTimeInterface $date_fin_extraction): self
    {
        $this->date_fin_extraction = $date_fin_extraction;

        return $this;
    }
}
