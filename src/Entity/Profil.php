<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 *   attributes={
 *      "pagination_items_per_page"=2,
 *      "security" = "is_granted('ROLE_ADMIN')",
 *      "security_message" = "Seuls les admins ont le droit d'acces à ce ressource",
 *       },
 *      normalizationContext={"groups"={"profil:read"}},
 *     itemOperations={
 *      "GET"={
 *          "path"="/admin/profils/{id}",
 *           "method"="GET",
 * },
 *      "PUT"={
 *          "path"="/admin/profils/{id}",
 *           "method"="PUT"
 * },
 *      "archiveProfil"={
 *          "name"="archiveProfil",
 *          "path"="/admin/profils/{id}",
 *          "method"="DELETE"
 * },
 * },
 *      collectionOperations={
 *          "listeOfProfilsNoArchives"={
 *              "name"="listeOfProfilsNoArchives",
 *              "method"="GET",
 *              "path"="/admin/profils",
 * },
 *          "POST"={
 *              "method"="POST",
 *              "path"="/admin/profils"
 * }
 * }
 * )
 * @ORM\Entity(repositoryClass=ProfilRepository::class)
 */
class Profil
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(
     *      message="Libellé Obligatoire"
     * )
     * @Groups({"profil:read"})
     */
    private $libelle;

    /**
     * @ORM\OneToMany(targetEntity=User::class, mappedBy="profil")
     * @ApiSubresource()
     * @Groups({"profil:read"})
     */
    private $users;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $etat;

    public function __construct()
    {
        $this->users = new ArrayCollection();
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
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setProfil($this);
        }

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getProfil() === $this) {
                $user->setProfil(null);
            }
        }

        return $this;
    }

    public function getEtat(): ?string
    {
        return $this->etat;
    }

    public function setEtat(?string $etat)
    {
        $this->etat = $etat;

        return $this;
    }
}
