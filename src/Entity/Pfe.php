<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\PfeController;
use App\Repository\PfeRepository;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[ORM\Entity(repositoryClass: PfeRepository::class)]
#[ApiResource(operations: [
    new GetCollection(), // GET /api/pfes
    new Post(controller: PfeController::class, deserialize: false),
])]
#[Vich\Uploadable]
class Pfe
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private string $titre;

    #[ORM\Column(type: 'string', length: 50)]
    private string $type; // monome ou binome

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nom1;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $nom2;

    #[ORM\Column(type: 'string', length: 50)]
    private string $specialite;

    #[ORM\Column(type: 'json')]
    private array $technologies = [];

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $autreTechnologie;

    #[ORM\Column(type: 'string', length: 255)]
    private string $encadrePar;

    #[ORM\Column(type: 'text')]
    private string $resume;

    #[ORM\ManyToOne(targetEntity: Fichier::class, cascade: ["persist", "remove"])]
    private ?Fichier $fichier = null;


    // Getters and setters ...
    public function getId(): ?int
    {
        return $this->id;
    }


    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getNom1(): ?string
    {
        return $this->nom1;
    }

    public function setNom1(?string $nom1): self
    {
        $this->nom1 = $nom1;

        return $this;
    }

    public function getNom2(): ?string
    {
        return $this->nom2;
    }

    public function setNom2(?string $nom2): self
    {
        $this->nom2 = $nom2;

        return $this;
    }

    public function getSpecialite(): ?string
    {
        return $this->specialite;
    }

    public function setSpecialite(string $specialite): self
    {
        $this->specialite = $specialite;

        return $this;
    }

    public function getTechnologies(): ?array
    {
        return $this->technologies;
    }

    public function setTechnologies(array $technologies): self
    {
        $this->technologies = $technologies;

        return $this;
    }

    public function getAutreTechnologie(): ?string
    {
        return $this->autreTechnologie;
    }

    public function setAutreTechnologie(?string $autreTechnologie): self
    {
        $this->autreTechnologie = $autreTechnologie;

        return $this;
    }

    public function getEncadrePar(): ?string
    {
        return $this->encadrePar;
    }

    public function setEncadrePar(string $encadrePar): self
    {
        $this->encadrePar = $encadrePar;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }
    public function getFichier(): ?Fichier
    {
        return $this->fichier;
    }

    public function setFichier(?Fichier $fichier): self
    {
        $this->fichier = $fichier;
        return $this;
    }

}
