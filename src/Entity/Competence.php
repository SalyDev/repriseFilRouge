<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CompetenceRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 * routePrefix="/admin",
 * normalizationContext={"groups":{"competence:read"}},
 * denormalizationContext={"groups":{"competence:write"}},
 * itemOperations={
 *      "put"={"security"="is_granted('EDIT', object)", "security_message"="Accès refusé"},
 *      "get"={"security"="is_granted('VIEW', object)", "security_message"="Accès refusé"},
 *      "delete"={"security"="is_granted('EDIT', object)", "security_message"="Accès refusé"}
 * },
 * collectionOperations={
 *      "post"={"security"="is_granted('ROLE_ADMIN')", "security_message"="Accès refusé"},
 *      "get"={"security"="is_granted('ROLE_ADMIN')", "security_message"="Accès refusé"}
 * }
 * )
 * @ORM\Entity(repositoryClass=CompetenceRepository::class)
 * @UniqueEntity(fields={"libelle"}, message="Ce competence existe deja")
 */
class Competence
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"competence:read", "group_comp:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"competence:read", "referentiel:read", "competence:write", "group_comp:read", "promogp2:read", "group_comp:write"})
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"competence:read", "competence:write", "group_comp:read", "promogp2:read", "group_comp:write"})
     * @Assert\NotBlank(message="Descriptif obligatoire")
     */
    private $descriptif;

    /**
     * @ORM\OneToMany(targetEntity=Niveau::class, mappedBy="competence", cascade={"persist"})
     * @Groups({"competence:read", "group_comp:read", "competence:write", "promogp2:read"})
     */
    private $niveaux;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archive = 0;

    /**
     * @ORM\ManyToMany(targetEntity=GroupeCompetences::class, mappedBy="competences", cascade={"persist"})
     * @Groups({"competence:read", "competence:write"})
     */
    private $groupeCompetences;

 
    private $action;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"competence:read", "competence:write"})
     */
    private $etat;

    public function __construct()
    {
        $this->niveaux = new ArrayCollection();
        $this->groupeCompetences = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

        return $this;
    }

    public function getDescriptif(): ?string
    {
        return $this->descriptif;
    }

    public function setDescriptif(string $descriptif): self
    {
        $this->descriptif = $descriptif;

        return $this;
    }

    /**
     * @return Collection|Niveau[]
     */
    public function getNiveaux(): Collection
    {
        return $this->niveaux;
    }

    public function addNiveau(Niveau $niveau): self
    {
        if (!$this->niveaux->contains($niveau)) {
            $this->niveaux[] = $niveau;
            $niveau->setCompetence($this);
        }

        return $this;
    }

    public function removeNiveau(Niveau $niveau): self
    {
        if ($this->niveaux->removeElement($niveau)) {
            // set the owning side to null (unless already changed)
            if ($niveau->getCompetence() === $this) {
                $niveau->setCompetence(null);
            }
        }

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

    /**
     * @return Collection|GroupeCompetences[]
     */
    public function getGroupeCompetences(): Collection
    {
        return $this->groupeCompetences;
    }

    public function addGroupeCompetence(GroupeCompetences $groupeCompetence): self
    {
        if (!$this->groupeCompetences->contains($groupeCompetence)) {
            $this->groupeCompetences[] = $groupeCompetence;
            $groupeCompetence->addCompetence($this);
        }

        return $this;
    }

    public function removeGroupeCompetence(GroupeCompetences $groupeCompetence): self
    {
        if ($this->groupeCompetences->removeElement($groupeCompetence)) {
            $groupeCompetence->removeCompetence($this);
        }

        return $this;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }

    public function setAction(?string $action): self
    {
        $this->action = $action;

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat): self
    {
        $this->etat = $etat;

        return $this;
    }
}
