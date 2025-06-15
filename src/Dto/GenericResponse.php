<?php

namespace App\Dto;

class GenericResponse
{
    private $data = null;
    private ?int $code = null;
    private ?string $status = null;
    private array $messages = [];

    public function __construct($data, $status, $code, $messages = [])
    {
        $this->data = $data;
        $this->status = $status;
        $this->code = $code;
        $this->messages = $messages;
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData($data): static
    {
        $this->data = $data;
        return $this;
    }

    public function getCode(): ?int
    {
        return $this->code;
    }

    public function setCode(int $code): static
    {
        $this->code = $code;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;
        return $this;
    }

    public function getMessages(): array
    {
        return $this->messages;
    }

    public function setMessages(array $messages): static
    {
        $this->messages = $messages;
        return $this;
    }

    public function toArray(): array
    {
        return [
            "code" => $this->code,
            "status" => $this->status,
            "messages" => $this->messages,
            "data" => $this->data
        ];
    }
}
