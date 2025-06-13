<?php

namespace App\Entity\TopList;

use App\Entity\Brand\Brand;
use App\Entity\Country\Country;
use App\Entity\Traits\IDableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\UuidableTrait;
use App\Repository\TopList\TopListEntryRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TopListEntryRepository::class)]
#[ORM\Table(name: 'top_list_entries')]
#[ORM\HasLifecycleCallbacks]
#[ORM\UniqueConstraint(name: 'unique_brand_country_position', columns: ['brand_id', 'country_id', 'position'])]
class TopListEntry
{
    use IDableTrait, UuidableTrait, TimestampableTrait;

    #[ORM\ManyToOne(inversedBy: 'topListEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Brand $brand = null;

    #[ORM\ManyToOne(inversedBy: 'topListEntries')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Country $country = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column(options: ["default" => true])]
    private ?bool $isActive = true;

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): static
    {
        $this->brand = $brand;
        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }
}
