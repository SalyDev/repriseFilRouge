<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\FormateurRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 * itemOperations={
 *      "updateFormateur"={
 *          "name"="updateFormateur",
 * },
 * "get"={
 *      "name"="showOneFormateur"
 * }
 * },
 *  collectionOperations={
 *      "addFormateur"={
 *          "name"="addFormateur",
 * },
 *      "showFormateurs"={
 *          "name"="showFormateurs",
 * }
 * }
 * )
 * @ORM\Entity(repositoryClass=FormateurRepository::class)
 */
class Formateur extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    protected $id;

    public function getId(): ?int
    {
        return $this->id;
    }
}
