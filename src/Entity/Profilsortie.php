<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilsortieRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 *  denormalizationContext={"groups":{"ps:write"}},
 *  normalizationContext={"groups":{"ps:read"}},
 *  routePrefix="/admin",
 *  itemOperations={
 *      "put"={"security"="is_granted('EDIT', object)", "security_message"="Accès refusé"},
 *      "get"={"security"="is_granted('VIEW', object)", "security_message"="Accès refusé"},
 *      "delete"={"security"="is_granted('EDIT', object)"}
 * },
 * collectionOperations={
 *      "post"={"security_post_denormalize"="is_granted('EDIT', object)", "security_message"="Accès refusé"},
 *      "get"={"security"="is_granted('ROLE_ADMIN') || is_granted('ROLE_FORMATEUR') || is_granted('ROLE_CM')", "security_message"="Accès refusé"},
 * }
 * )
 * @UniqueEntity(
 *  fields={"libelle"},
 *  message="Ce profil de sortie existe déja"
 * )
 * @ORM\Entity(repositoryClass=ProfilsortieRepository::class)
 */
class Profilsortie
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"ps:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"ps:write", "ps:read", "promo_ps:read"})
     * @Assert\NotBlank(message="Libelle du profil de sortie obligatoire")
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=Apprenant::class, mappedBy="profilsortie")
     * @Groups({"ps:read"})
     * @ApiSubresource()
     */
    private $apprenants;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archive = 0;

    public function __construct()
    {
        $this->apprenants = new ArrayCollection();
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
            $apprenant->setProfilsortie($this);
        }

        return $this;
    }

    public function removeApprenant(Apprenant $apprenant): self
    {
        if ($this->apprenants->removeElement($apprenant)) {
            // set the owning side to null (unless already changed)
            if ($apprenant->getProfilsortie() === $this) {
                $apprenant->setProfilsortie(null);
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
}
