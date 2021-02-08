<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReferentielRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 * routePrefix="/admin",
 * denormalizationContext={"groups":{"referentiel:write"}},
 * normalizationContext={"groups":{"referentiel:read"}},
 * collectionOperations={
 * "post"={"security"="is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM')", "security_message"="Accès refusé"},
 * "get"={"security"="is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM') || is_granted('ROLE_APPRENANT')"},
 *  "groupCompetencesOfReferentiels"={
 *      "method"="GET",
 *      "path"="/referentiels/grpecompetences",
 *      "normalization_context"={"groups"={"referentiel:read", "group_comp:read"}},
 *      "security"="is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM')", 
 *      "security_message"="Accès refusé"
 * }
 * },
 * itemOperations={
 *  "get"={
 *      "security"="is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM') || is_granted('ROLE_APPRENANT')",
 *       "security_message"="Accès refusé"
 * },
 *  "compOfReferentief"={
 *      "name"="compOfReferentief",
 *      "method"="GET",
 *      "path"="/referentiels/{idRef}/grpecompetences/{idGc}",
 *      "normalization_context"={"groups"={"referentiel:read", "group_comp:read"}},
 *      "security"="is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM') || is_granted('ROLE_APPRENANT')",
 *      "security_message"="Accès refusé"
 * },
 *  "put"={
 *       "security"="is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM')", 
 *      "security_message"="Accès refusé"
 * },
 * "delete"={
 *       "security"="is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM')", 
 *      "security_message"="Accès refusé"
 * },
 * }
 * )
 * @ORM\Entity(repositoryClass=ReferentielRepository::class)
 * @UniqueEntity(fields={"libelle"}, message="Ce référentiel existe deja")
 */
class Referentiel
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"referentiel:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"attente:read", "promogp1:read", "promog3:read", "referentiel:write", "referentiel:read"})
     */
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity=GroupeCompetences::class, mappedBy="referentiel", cascade={"persist"})
     * @Groups({"promogp2:read", "referentiel:write", "referentiel:read"})
     */
    private $groupeCompetences;

    /**
     * @ORM\OneToMany(targetEntity=Promo::class, mappedBy="referentiel")
     */
    private $promos;

    /**
     * @ORM\Column(type="text")
     * @GRoups({"referentiel:read", "referentiel:write"})
     * @Assert\NotBlank(message="Présentation du referentiel obligatoire")
     */
    private $presentation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"referentiel:read", "referentiel:write"})
     */
    private $critereEvaluation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"referentiel:read", "referentiel:write"})
     */
    private $critereAdmission;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archive = 0;

    /**
     * @ORM\Column(type="blob", nullable=true)
     * @Groups({"referentiel:read"})
     */
    private $programme;

  

    public function __construct()
    {
        $this->groupeCompetences = new ArrayCollection();
        $this->promos = new ArrayCollection();
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
            $groupeCompetence->addReferentiel($this);
        }

        return $this;
    }

    public function removeGroupeCompetence(GroupeCompetences $groupeCompetence): self
    {
        if ($this->groupeCompetences->removeElement($groupeCompetence)) {
            $groupeCompetence->removeReferentiel($this);
        }

        return $this;
    }

    /**
     * @return Collection|Promo[]
     */
    public function getPromos(): Collection
    {
        return $this->promos;
    }

    public function addPromo(Promo $promo): self
    {
        if (!$this->promos->contains($promo)) {
            $this->promos[] = $promo;
            $promo->setReferentiel($this);
        }

        return $this;
    }

    public function removePromo(Promo $promo): self
    {
        if ($this->promos->removeElement($promo)) {
            // set the owning side to null (unless already changed)
            if ($promo->getReferentiel() === $this) {
                $promo->setReferentiel(null);
            }
        }

        return $this;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setPresentation(string $presentation): self
    {
        $this->presentation = $presentation;

        return $this;
    }

    public function getCritereEvaluation(): ?string
    {
        return $this->critereEvaluation;
    }

    public function setCritereEvaluation(?string $critereEvaluation): self
    {
        $this->critereEvaluation = $critereEvaluation;

        return $this;
    }

    public function getCritereAdmission(): ?string
    {
        return $this->critereAdmission;
    }

    public function setCritereAdmission(?string $critereAdmission): self
    {
        $this->critereAdmission = $critereAdmission;

        return $this;
    }

    public function getArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(bool $archive): self
    {
        $this->archive = $archive;

        return $this;
    }

    public function getProgramme()
    {
        if($this->programme == null){
            return $this->programme;
        }
        return base64_encode(stream_get_contents($this->programme));
    }

    public function setProgramme($programme): self
    {
        $this->programme = $programme;

        return $this;
    }
}
