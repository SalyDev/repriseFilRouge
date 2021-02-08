<?php

namespace App\Entity;

use App\Entity\Groupe;
use Doctrine\ORM\Mapping as ORM;
use App\Controller\ApprenantController;
use App\Repository\ApprenantRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 * normalizationContext={"groups"={"user:read"}},
 * collectionOperations={
 *       "showAllEmails"={
 *          "security"="is_granted('ROLE_ADMIN')",
 *          "security_message"="Accès refusé",
 *          "path"="/admin/apprenant/emails",
 *          "method"="GET",
 *          "normalization_context"={"groups"="emails:read"},
 * }
 * }
 * )
 * @ORM\Entity(repositoryClass=ApprenantRepository::class)
 */
class Apprenant extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read", "emails:read"})
     */
    public $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="integer", length=255)
     * @Groups({"user:read"})
     */
    private $telephone;

    /**
     * @ORM\ManyToMany(targetEntity=Groupe::class, mappedBy="apprenants")
     * @Groups({"attente:read"})
     */
    private $groupes;

    /**
     * @ORM\ManyToOne(targetEntity=Profilsortie::class, inversedBy="apprenants")
     * @Groups({"promo_ps:read", "user:read"})
     */
    private $profilsortie;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     * @Groups({"promogp1:read"})
     */
    private $hasJoinPromo;


    public function __construct()
    {
        $this->groupes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }

    public function getTelephone(): ?int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * @return Collection|Groupe[]
     */
    public function getGroupes(): Collection
    {
        return $this->groupes;
    }

    public function addGroupe(Groupe $groupe): self
    {
        if (!$this->groupes->contains($groupe)) {
            $this->groupes[] = $groupe;
            $groupe->addApprenant($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->removeElement($groupe)) {
            $groupe->removeApprenant($this);
        }

        return $this;
    }

    public function getProfilsortie(): ?Profilsortie
    {
        return $this->profilsortie;
    }

    public function setProfilsortie(?Profilsortie $profilsortie): self
    {
        $this->profilsortie = $profilsortie;

        return $this;
    }

    public function getHasJoinPromo(): ?bool
    {
        return $this->hasJoinPromo;
    }

    public function setHasJoinPromo(?bool $hasJoinPromo): self
    {
        $this->hasJoinPromo = $hasJoinPromo;

        return $this;
    }
}
