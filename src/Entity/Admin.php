<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdminRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ApiResource(
 * normalizationContext={"groups"={"user:read"}},
 *  itemOperations={
 *      "get"={
 *         "name"="showOneAdmin"
 * },
 *      "updateAdmin"={
 *          "name"="updateAdmin"
 * }
 * },
 *  collectionOperations={
 *      "addAmin"={
 *          "name"="addAdmin"
 * },
 *      "showAdmins"={
 *          "name"="showAdmins"
 * }
 * }
 * )
 * @ORM\Entity(repositoryClass=AdminRepository::class)
 * @ORM\Table(name="`admin`")
 */
class Admin extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
