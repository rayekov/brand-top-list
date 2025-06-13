<?php

namespace App\Dto\TopList\Out;

use App\Dto\Brand\Out\BrandOut;
use App\Dto\Country\Out\CountryOut;

class TopListEntryOut
{
    private ?int $id = null;
    private ?string $uuid = null;
    private ?int $position = null;
    private ?bool $isActive = null;
    private ?BrandOut $brand = null;
    private ?CountryOut $country = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): static
    {
        $this->id = $id;
        return $this;
    }

    public function getUuid(): ?string
    {
        return $this->uuid;
    }

    public function setUuid(?string $uuid): static
    {
        $this->uuid = $uuid;
        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): static
    {
        $this->position = $position;
        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): static
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getBrand(): ?BrandOut
    {
        return $this->brand;
    }

    public function setBrand(?BrandOut $brand): static
    {
        $this->brand = $brand;
        return $this;
    }

    public function getCountry(): ?CountryOut
    {
        return $this->country;
    }

    public function setCountry(?CountryOut $country): static
    {
        $this->country = $country;
        return $this;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
}
