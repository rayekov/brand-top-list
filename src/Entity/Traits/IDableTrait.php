<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IDableTrait
{
  #[ORM\Id]
  #[ORM\GeneratedValue]
  #[ORM\Column]
  private ?int $id = null;

  public function getId(): ?int
  {
    return $this->id;
  }
}
