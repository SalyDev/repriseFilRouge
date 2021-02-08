<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UserRepository;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * 
 * @ApiFilter(BooleanFilter::class, properties={"archive"})
 * @ApiResource(
 *      normalizationContext={"groups"={"user:read"}},
 *      itemOperations={
 * "get",
 *          "archiveUser"={
 *              "security"="is_granted('ROLE_ADMIN')",
 *              "security_message"="Accès refusé",
 *              "path"="/admin/users/{id}",
 *              "method"="DELETE"
 * },
 * }
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"apprenant"="Apprenant", "formateur"="Formateur", "admin"="Admin", "cm"="CM", "user"="User"})
 * @UniqueEntity(
 *      fields={"email"},
 *      message="Un utilisateur avec cet email existe deja"
 * )
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"attente:read"})
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(
     *      message="Email Obligatoire"
     * )
     * @Assert\Regex(
     *     pattern="/^[a-zA-Z0-9_.-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/",
     *     message="Email invalide"
     * )
     * @Groups({"user:read","ps:read", "promo:write", "promogp1:read", "attente:read", "promog3:read", "promo_ps:read", "emails:read"})
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     * @Groups({"user:read"})
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string", nullable=false)
     * @Assert\NotBlank(
     *      message="Mot de passe Obligatoire"
     * )
     * @Groups({"user:read"})
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(
     *      message="Prénom Obligatoire"
     * )
     * @Assert\NotNull(
     *      message="Prénom vide"
     * )
     * @Groups({"user:read","groupe_all:read", "groupe_apprenants:read", "ps:read", "promogp1:read"})
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     * @Assert\NotBlank(
     *      message="Nom Obligatoire"
     * )
     * @Groups({"user:read","groupe_apprenants:read", "groupe_all:read", "ps:read", "promogp1:read"})
     */
    private $nom;

    /**
     * @ORM\ManyToOne(targetEntity=Profil::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(
     *      message="Profil Obligatoire"
     * )
     * @Groups({"user:read"})
     */
    private $profil;

    /**
     * @ORM\Column(type="blob", nullable=true)
     * @Groups({"user:read", "promogp1:read"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $archive = 0;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"user:read"})
     */
    private $genre;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        // $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

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

    public function getProfil(): ?Profil
    {
        return $this->profil;
    }

    public function setProfil(?Profil $profil): self
    {
        $this->profil = $profil;

        return $this;
    }

    public function getAvatar()
    {
        if(!$this->avatar){
            return null;
        }
        return base64_encode(stream_get_contents($this->avatar));
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

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

    public function getGenre(): ?string
    {
        return $this->genre;
    }

    public function setGenre(string $genre): self
    {
        $this->genre = $genre;

        return $this;
    }


}
