<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\CMRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource(
 * itemOperations={
 *      "updateCm"={
 *          "name"="updateCm",
 * },
 * "get"={
 *      "name"="showOneCm"
 * }
 * },
 *  collectionOperations={
 *      "addFormateur"={
 *          "name"="addCm",
 * },
 *      "showFormateurs"={
 *          "name"="showCm",
 * }
 * }
 * )
 * @ORM\Entity(repositoryClass=CMRepository::class)
 */
class CM extends User
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
