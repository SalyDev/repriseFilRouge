<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ProfilRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiFilter(BooleanFilter::class, properties={"archive"})
 * @ApiResource(
 *   attributes={
 *      "security" = "is_granted('ROLE_ADMIN')",
 *      "security_message" = "Seuls les admins ont le droit d'acces à ce ressource",
 *       },
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
 *          "path"="/admin/profils/{id}",
 *          "method"="DELETE"
 * },
 * },
 *      collectionOperations={
 *          "GET"={
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
 * @UniqueEntity(
 *      fields={"libelle"},
 *      message="Un profil avec cet libellé existe déjà"
 * )

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
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archive;


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
