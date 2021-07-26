<?php

namespace App\Entity;

use App\Repository\ImagesRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ImagesRepository::class)
 */
class Images
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $NomUrl;

    /**
     * @ORM\ManyToOne(targetEntity=Annonces::class, inversedBy="images")
     */
    private $Annonces;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomUrl(): ?string
    {
        return $this->NomUrl;
    }

    public function setNomUrl(string $NomUrl): self
    {
        $this->NomUrl = $NomUrl;

        return $this;
    }

    public function getAnnonces(): ?Annonces
    {
        return $this->Annonces;
    }

    public function setAnnonces(?Annonces $Annonces): self
    {
        $this->Annonces = $Annonces;

        return $this;
    }
}
