<?php

namespace App\Dto\Brand\In;

class BrandCreateIn
{
    private ?string $brandName = null;
    private ?string $brandImage = null;
    private ?int $rating = null;

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
}
