<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GroupeTagsRepository;
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
 * attributes={
 *  "security"="is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR')",
 *  "security_message"="Accès refusé"
 * },
 * denormalizationContext={"groups":{"grpTags:write"}},
 * normalizationContext={"groups":{"grpTags:write"}},
 * itemOperations={
 * "get", "put",
 * "tagsOfGrpTags"={
 *      "method"="GET",
 *      "path"="/groupe_tags/{id}/tags",
 *      "normalization_context"={"groups"={"tagOfGt"}}
 * }
 * },
 * collectionOperations={
 * "post", "get"
 * }
 * )
 * @UniqueEntity(fields={"libelle"}, message="Ce groupe de tags existe déjà")
 * @ORM\Entity(repositoryClass=GroupeTagsRepository::class)
 */
class GroupeTags
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"tag:read", "grpTags:write"})
     * @Assert\NotBlank(message="Libellé obligatoire")
     */
    private $libelle;

    /**
     * @ORM\ManyToMany(targetEntity=Tag::class, inversedBy="groupeTags", cascade={"persist"})
     * @Groups({"grpTags:write", "tagOfGt"})
     * @Assert\Count(
     *      min = 1,
     *      minMessage = "Donner au moins un tag",
     * )
     */
    private $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
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
     * @return Collection|Tag[]
     */
    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);

        return $this;
    }
}
