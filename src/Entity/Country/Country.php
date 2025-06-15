<?php

namespace App\Entity\Country;

use App\Entity\Traits\IDableTrait;
use App\Entity\Traits\TimestampableTrait;
use App\Entity\Traits\UuidableTrait;
use App\Entity\TopList\TopListEntry;
use App\Repository\Country\CountryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ORM\Table(name: 'countries')]
#[ORM\HasLifecycleCallbacks]
class Country
{
    use IDableTrait, UuidableTrait, TimestampableTrait;

    #[ORM\Column(length: 2, unique: true)]
    private ?string $isoCode = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(options: ["default" => false])]
    private ?bool $isDefault = false;

    #[ORM\OneToMany(mappedBy: 'country', targetEntity: TopListEntry::class, orphanRemoval: true)]
    private Collection $topListEntries;

    public function __construct()
    {
        $this->topListEntries = new ArrayCollection();
    }

    public function getIsoCode(): ?string
    {
        return $this->isoCode;
    }

    public function setIsoCode(string $isoCode): static
    {
        $this->isoCode = strtoupper($isoCode);
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function isDefault(): ?bool
    {
        return $this->isDefault;
    }

    public function setIsDefault(bool $isDefault): static
    {
        $this->isDefault = $isDefault;
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
            $topListEntry->setCountry($this);
        }
        return $this;
    }

    public function removeTopListEntry(TopListEntry $topListEntry): static
    {
        if ($this->topListEntries->removeElement($topListEntry)) {
            if ($topListEntry->getCountry() === $this) {
                $topListEntry->setCountry(null);
            }
        }
        return $this;
    }
}
