<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PromoRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ApiResource(
 * denormalizationContext={"groups":{"promo:write"}},
 * routePrefix="/admin",
 *   attributes={
 *      "security" = "is_granted('ROLE_ADMIN') or is_granted('ROLE_FORMATEUR')",
 *      "security_message" = "Seuls les admins ont le droit d'acces à ce ressource",
 *   },
 * itemOperations={
 *  "get"={
 *  "normalization_context"={"groups"="promogp1:read"},
 * },
 * "referentiel_gc_c_one"={
 *    "path"="/promos/{id}/referentiels",
 *    "method"="get",
 *    "normalization_context"={"groups"="promogp2:read"},
 * },
 * "attenteOfOnePromo"={
 *   "route_name"="attenteOfOnePromo",
 * },
 * "getAppOfGroupOfPromo"={
 *      "route_name"="getAppOfGroupOfPromo",
 * },
 * "updateStudentPromo"={
 *  "route_name"="updateStudentPromo"
 * },
 * "updateTeacherPromo"={
 *  "route_name"="updateTeacherPromo"
 * },
 * "updateGroupStatus"={
 *   "route_name"="updateGroupStatus",
 * },
 * "item_apprenant_group_principal"={
 *      "path"="/promos/{id}/principal",
 *      "method"="GET",
 *      "normalization_context"={"groups"={"promog3:read"}}
 * },
 * "apprenant_promo_profilsortie"={
 *      "path"="/promos/{id}/profilsorties",
 *      "method"="GET",
 *      "normalization_context"={"groups"={"promo_ps:read"}}
 * },
 * "apprenant_promo_ps"={
 *  "route_name"="apprenant_promo_ps",
 * },
 * "delete",
 * },
 * collectionOperations={
 * "post"={
 *      "route_name"="addPromo",
 * },
 *  "get"={
 *      "normalization_context"={"groups"="promogp1:read"}
 * },
 * "apprenantsEnAttente"={
 *  "route_name"="apprenantsEnAttente",
 * },
 * "apprenants_group_principal"={
 *      "path"="/promos/principal",
 *      "method"="GET",
 *      "normalization_context"={"groups"={"promog3:read"}}
 * },
 * }
 * )
 * @ORM\Entity(repositoryClass=PromoRepository::class)
 * @UniqueEntity(fields={"titre"}, message="Ce promo existe déjà")
 */
class Promo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"promogp1:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"promo:write", "promogp1:read"})
     */
    private $lieu;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promo:write", "promogp1:read"})
     */
    private $referenceagate;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promo:write", "promogp1:read"})
     */
    private $choixdefabrique = 'Sonatel Academy';

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promo:write", "promogp1:read", "promo_ps:read"})
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=300)
     * @Groups({"promogp1:read","promog3:read"})
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Groupe::class, mappedBy="promo")
     * @Groups({"promo:write", "promogp1:read", "promog3:read", "promogp1:write", "promo_ps:read"})
     */
    private $groupes;

    /**
     * @ORM\Column(type="blob", nullable=true)
     * @Groups({"promogp1:read", "promo:write"})
     */
    private $avatar;

    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"promogp1:read", "promo:write"})
     */
    private $datedebut;

    /**
     * @ORM\ManyToOne(targetEntity=Referentiel::class, inversedBy="promos")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"promogp2:read", "promogp1:read", "promo:write"})
     */
    private $referentiel;

    /**
     * @ORM\Column(type="boolean")
     */
    private $archive = 0;


    /**
     * @ORM\Column(type="date", nullable=true)
     * @Groups({"promogp1:read", "promo:write"})
     */
    private $datefin;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"promogp1:read", "promo:write"})
     */
    private $langue;


    public function __construct()
    {
        $this->groupes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getReferenceagate(): ?string
    {
        return $this->referenceagate;
    }

    public function setReferenceagate(string $referenceagate): self
    {
        $this->referenceagate = $referenceagate;

        return $this;
    }

    public function getChoixdefabrique(): ?string
    {
        return $this->choixdefabrique;
    }

    public function setChoixdefabrique(string $choixdefabrique): self
    {
        $this->choixdefabrique = $choixdefabrique;

        return $this;
    }


    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

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
            $groupe->setPromo($this);
        }

        return $this;
    }

    public function removeGroupe(Groupe $groupe): self
    {
        if ($this->groupes->removeElement($groupe)) {
            // set the owning side to null (unless already changed)
            if ($groupe->getPromo() === $this) {
                $groupe->setPromo(null);
            }
        }

        return $this;
    }

    public function getAvatar()
    {
        if($this->avatar == null){
            return $this->avatar;
        }
        return base64_encode(stream_get_contents($this->avatar));
    }

    public function setAvatar($avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

 

    public function getDatedebut(): ?\DateTimeInterface
    {
        return $this->datedebut;
    }

    public function setDatedebut(?\DateTimeInterface $datedebut): self
    {
        $this->datedebut = $datedebut;

        return $this;
    }

    public function getReferentiel(): ?Referentiel
    {
        return $this->referentiel;
    }

    public function setReferentiel(?Referentiel $referentiel): self
    {
        $this->referentiel = $referentiel;

        return $this;
    }

    public function getArchive(): ?bool
    {
        return $this->archive;
    }

    public function setArchive(bool $archive): self
    {
        $this->archive = $archive;

        return $this;
    }



    public function getDatefin(): ?\DateTimeInterface
    {
        return $this->datefin;
    }

    public function setDatefin(?\DateTimeInterface $datefin): self
    {
        $this->datefin = $datefin;

        return $this;
    }

    public function getLangue(): ?string
    {
        return $this->langue;
    }

    public function setLangue(string $langue): self
    {
        $this->langue = $langue;

        return $this;
    }

}
