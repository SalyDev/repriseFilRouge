<?php

namespace App\Entity;

use App\Entity\Apprenant;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 *  routePrefix="/admin/groupes",
 *  attributes={
 *      "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR')",
 *      "security_message"="Accès refusé"
 * },
 * collectionOperations={
 *      "get"={
 *          "path"="/apprenants",
 *          "normalization_context"={"groups"="groupe_apprenants:read"}
 * },
 *   "showGroups"={
 *          "path"="",
 *          "normalization_context"={"groups"="groupe_all:read"},
 *          "method"="GET"
 * },
 *      "add_group"={
 *          "path"="",
 *          "method"="POST"
 * }
 * },
 * itemOperations={
 *      "get"={
 *          "path"="/{id}",
 *          "normalization_context"={"groups"="groupe_all:read"},
 * },
 *      "updateGroup"={
 *          "path"="/{id}",
 *          "method"="PUT"
 * },
 *      "removeApprenantOfGroup"={
 *          "name"="removeApprenantOfGroup",
 *          "method"="DELETE"
 * },
 *      "delete"={
 *          "path"="/{id}"
 * }
 * }
 * )
 * @ORM\Entity(repositoryClass=GroupeRepository::class)
 * @UniqueEntity(
 *      fields={"nom"},
 *      message="Un groupe portant ce nom existe déja"
 * )
 */
class Groupe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Apprenant::class, inversedBy="groupes")
     * @Groups({"groupe_apprenants:read", "groupe_all:read", "promo:write", "promog3:read", "promogp1:write", "promo_ps:read", "promogp1:read"})
     */
    private $apprenants;

    /**
     * @ORM\ManyToMany(targetEntity=Formateur::class, inversedBy="groupes")
     * @Groups({"groupe_all:read", "promogp1:read"})
     */
    private $formateurs;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promogp1:read"})
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity=Promo::class, inversedBy="groupes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"attente:read"})
     */
    private $promo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $statut;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $type;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archive = 0;

    public function __construct()
    {
        $this->apprenants = new ArrayCollection();
        $this->formateurs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Apprenant[]
     */
    public function getApprenants(): Collection
    {
        return $this->apprenants;
    }

    public function addApprenant(Apprenant $apprenant): self
    {
        if (!$this->apprenants->contains($apprenant)) {
            $this->apprenants[] = $apprenant;
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        $this->apprenants->removeElement($apprenant);

        return $this;
    }

    /**
     * @return Collection|Formateur[]
     */
    public function getFormateurs(): Collection
    {
        return $this->formateurs;
    }

    public function addFormateur(Formateur $formateur): self
    {
        if (!$this->formateurs->contains($formateur)) {
            $this->formateurs[] = $formateur;
        }

        return $this;
    }

    public function removeFormateur(Formateur $formateur): self
    {
        $this->formateurs->removeElement($formateur);

        return $this;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPromo(): ?Promo
    {
        return $this->promo;
    }

    public function setPromo(?Promo $promo): self
    {
        $this->promo = $promo;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(?string $statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type): self
    {
        $this->type = $type;

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
}
