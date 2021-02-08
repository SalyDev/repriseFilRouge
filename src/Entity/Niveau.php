<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\NiveauRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *  routePrefix="/admin",
 *  denormalizationContext={"groups":{"niveau:write"}}
 * )
 * @ORM\Entity(repositoryClass=NiveauRepository::class)
 */
class Niveau
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"competence:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Competence::class, inversedBy="niveaux", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $competence;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archive = 0;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"niveau:write", "group_comp:read", "competence:write", "promogp2:read", "competence:read"})
     *@Assert\NotBlank(message="Critere d'evaluations obligatoire")
     */
    private $critereEvaluation;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"niveau:write", "group_comp:read", "competence:write", "promogp2:read", "competence:read"})
     *@Assert\NotBlank(message="Actions obligatoire")
     */
    private $actions;

    public function getId(): ?int
    {
        return $this->id;
    }


    public function getCompetence(): ?Competence
    {
        return $this->competence;
    }

    public function setCompetence(?Competence $competence): self
    {
        $this->competence = $competence;

        return $this;
    }

    public function getArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(?bool $archive): self
    {
        $this->archive = $archive;

        return $this;
    }

    public function getCritereEvaluation(): ?string
    {
        return $this->critereEvaluation;
    }

    public function setCritereEvaluation(string $critereEvaluation): self
    {
        $this->critereEvaluation = $critereEvaluation;

        return $this;
    }

    public function getActions(): ?string
    {
        return $this->actions;
    }

    public function setActions(string $actions): self
    {
        $this->actions = $actions;

        return $this;
    }
}
