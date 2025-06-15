<?php

namespace App\Entity\Brand;

use App\Entity\Traits\IDableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\UuidableTrait;
use App\Entity\TopList\TopListEntry;
use App\Repository\Brand\BrandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BrandRepository::class)]
#[ORM\Table(name: 'brands')]
#[ORM\HasLifecycleCallbacks]
class Brand
{
    use IDableTrait, UuidableTrait, TimestampableTrait;

    #[ORM\Column(length: 255)]
    private ?string $brandName = null;

    #[ORM\Column(length: 500)]
    private ?string $brandImage = null;

    #[ORM\Column]
    private ?int $rating = null;

    #[ORM\OneToMany(mappedBy: 'brand', targetEntity: TopListEntry::class, orphanRemoval: true)]
    private Collection $topListEntries;

    public function __construct()
    {
        $this->topListEntries = new ArrayCollection();
    }

    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    public function setBrandName(string $brandName): static
    {
        $this->brandName = $brandName;
        return $this;
    }

    public function getBrandImage(): ?string
    {
        return $this->brandImage;
    }

    public function setBrandImage(string $brandImage): static
    {
        $this->brandImage = $brandImage;
        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(int $rating): static
    {
        $this->rating = $rating;
        return $this;
    }

    /**
     * @return Collection<int, TopListEntry>
     */
    public function getTopListEntries(): Collection
    {
        return $this->topListEntries;
    }

    public function addTopListEntry(TopListEntry $topListEntry): static
    {
        if (!$this->topListEntries->contains($topListEntry)) {
            $this->topListEntries->add($topListEntry);
            $topListEntry->setBrand($this);
        }
        return $this;
    }

    public function removeTopListEntry(TopListEntry $topListEntry): static
    {
        if ($this->topListEntries->removeElement($topListEntry)) {
            if ($topListEntry->getBrand() === $this) {
                $topListEntry->setBrand(null);
            }
        }
        return $this;
    }
}
