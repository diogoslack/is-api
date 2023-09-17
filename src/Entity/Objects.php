<?php

namespace App\Entity;

use App\Repository\ObjectsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ObjectsRepository::class)]
class Objects
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'uuid')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categories $category = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Sectors $sector = null;

    #[ORM\Column(length: 50)]
    private ?string $oid = null;

    #[ORM\Column(length: 50)]
    private ?string $code = null;

    #[ORM\Column(type: 'point')]
    private $latitude = null;

    #[ORM\Column(type: 'point')]
    private $longitude = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updated_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\ManyToMany(targetEntity: Fields::class)]
    private Collection $object_props;

    public function __construct()
    {
        $this->object_props = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Categories
    {
        return $this->category;
    }

    public function setCategory(?Categories $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getSector(): ?Sectors
    {
        return $this->sector;
    }

    public function setSector(?Sectors $sector): static
    {
        $this->sector = $sector;

        return $this;
    }

    public function getOid(): ?string
    {
        return $this->oid;
    }

    public function setOid(string $oid): static
    {
        $this->oid = $oid;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getLatitude()
    {
        return $this->latitude;
    }

    public function setLatitude($latitude): static
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude()
    {
        return $this->longitude;
    }

    public function setLongitude($longitude): static
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(\DateTimeInterface $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * @return Collection<int, Fields>
     */
    public function getObjectProps(): Collection
    {
        return $this->object_props;
    }

    public function addObjectProp(Fields $objectProp): static
    {
        if (!$this->object_props->contains($objectProp)) {
            $this->object_props->add($objectProp);
        }

        return $this;
    }

    public function removeObjectProp(Fields $objectProp): static
    {
        $this->object_props->removeElement($objectProp);

        return $this;
    }
}
