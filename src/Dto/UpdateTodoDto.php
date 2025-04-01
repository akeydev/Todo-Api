<?php

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateTodoDto
{
    #[Assert\NotBlank(message: 'Title cannot be blank')]
    public string $title;

    #[Assert\NotBlank(message: 'Description cannot be blank')]
    public string $description;

    #[Assert\NotBlank(message: 'Status cannot be blank')]
    public string $status;
}