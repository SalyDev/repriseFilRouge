<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ApprenantRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * itemOperations={
 *      "get"={
 *          "name"="showOneApprenant"
 * },
 *      "updateApprenant"={
 *          "name"="updateApprenant",
 *          "method"="PUT"
 * }
 * },
 * collectionOperations={
 *      "addApprenant"={
 *          "name"="addApprenant",
 * },
 *      "showApprenants"={
 *          "name"="showApprenants",
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
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"profil:read"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"profil:read"})
     */
    private $telephone;

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
}
