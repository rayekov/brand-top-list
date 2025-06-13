<?php

namespace App\Entity\Traits;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait UuidableTrait
{
  #[ORM\Column(type: Types::GUID, unique: true)]
  private ?string $uuid = null;

  #[ORM\PrePersist]
  public function setUuidValue(): void
  {
    $this->uuid = empty($this->uuid) ? Uuid::v4() : $this->uuid;
  }

  public function getUuid(): ?string
  {
    return $this->uuid;
  }

  public function setUuid(string $uuid): static
  {
    $this->uuid = $uuid;
    return $this;
  }
}
