<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use App\Repository\GroupeCompetencesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 * normalizationContext={"groups":"group_comp:read"},
 * denormalizationContext={"groups":"group_comp:write"},
 * routePrefix="/admin",
 * itemOperations={
 *  "put"={
 *      "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR') or is_granted('ROLE_CM')",
 *      "security_message"="Accès refusé"
 * },
 *  "get"={"security"="is_granted('ACCESS2', object)", "security_message"="Accès refusé"},
 *  "delete"={
 *      "security"="is_granted('ROLE_ADMIN')",
 *      "security_message"="Accès refusé"
 *  }
 * },
 * collectionOperations={
 *  "get"={"security"="is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM')", "security_message"="Accès refusé"},
 *  "allCompetences"={
 *      "method"="GET",
 *      "path"="/groupe_competences/competences",
 *      "security"="is_granted('ROLE_ADMIN')",
 *      "security_message"="Accès refusé"
 * },
 *  "post"={
 *      "security"="is_granted('ROLE_ADMIN')",
 *      "security_message"="Accès refusé"
 * }
 * }
 * )
 * @ORM\Entity(repositoryClass=GroupeCompetencesRepository::class)
 * @UniqueEntity(fields={"libelle"}, message="Ce groupe de competences existe déja")
 */
class GroupeCompetences
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"group_comp:read"})
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Competence::class, inversedBy="groupeCompetences", cascade={"persist"})
     * @Groups({"group_comp:read","group_comp:write", "promogp2:read", "referentiel:read"})
     * @ApiSubresource()
     * )
     */
    private $competences = [];

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"competence:write","competence:read","group_comp:read", "group_comp:write", "promogp2:read", "referentiel:read"})
     * @Assert\NotBlank(message="Champs obligatoire")
     */
    private $libelle;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"group_comp:read", "group_comp:write", "referentiel:read"})
     */
    private $descriptif;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archive = 0;


    /**
     * @ORM\ManyToMany(targetEntity=Referentiel::class, inversedBy="groupeCompetences")
     */
    private $referentiel;



    public function __construct()
    {
        $this->competences = new ArrayCollection();
        $this->referentiel = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Competence[]
     */
    public function getCompetences(): Collection
    {
        return $this->competences;
        // $thecompetences = new ArrayCollection();
        // foreach ($this->competences as $competence) {
        //     if($competence->getArchive() == false){
        //         $thecompetences->add($competence);
        //     }
        // }
        // return $thecompetences;
    }


    public function addCompetence(Competence $competence): self
    {
        if (!$this->competences->contains($competence)) {
            $this->competences[] = $competence;
        }

        return $this;
    }

    public function removeCompetence(Competence $competence): self
    {
        $this->competences->removeElement($competence);

        return $this;
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
     * @return Collection|Referentiel[]
     */
    public function getReferentiel(): Collection
    {
        return $this->referentiel;
    }

    public function addReferentiel(Referentiel $referentiel): self
    {
        if (!$this->referentiel->contains($referentiel)) {
            $this->referentiel[] = $referentiel;
        }

        return $this;
    }

    public function removeReferentiel(Referentiel $referentiel): self
    {
        $this->referentiel->removeElement($referentiel);

        return $this;
    }
}
