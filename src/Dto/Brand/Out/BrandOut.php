<?php

namespace App\Dto\Brand\Out;

class BrandOut
{
    private ?int $brandId = null;
    private ?string $uuid = null;
    private ?string $brandName = null;
    private ?string $brandImage = null;
    private ?int $rating = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;

    public function getBrandId(): ?int
    {
        return $this->brandId;
    }

    public function setBrandId(?int $brandId): static
    {
        $this->brandId = $brandId;
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

    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    public function setBrandName(?string $brandName): static
    {
        $this->brandName = $brandName;
        return $this;
    }

    public function getBrandImage(): ?string
    {
        return $this->brandImage;
    }

    public function setBrandImage(?string $brandImage): static
    {
        $this->brandImage = $brandImage;
        return $this;
    }

    public function getRating(): ?int
    {
        return $this->rating;
    }

    public function setRating(?int $rating): static
    {
        $this->rating = $rating;
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
