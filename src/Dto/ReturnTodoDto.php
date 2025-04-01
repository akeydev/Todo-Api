<?php
namespace App\Dto;

final class ReturnTodoDto
{
    public $id;
    public $title;
    public $description;
    public $status;

    public function __construct(int $id, string $title, string $description, string $status)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->status = $status;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getStatus() : string {
        return $this->status;
    }
}
