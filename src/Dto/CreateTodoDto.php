<?php

NameSpace App\Dto;


use Symfony\Component\Validator\Constraints as Assert;

class CreateTodoDto
{
    #[Assert\NotBlank(message: 'Title cannot be blank')]
    public string $title;

    #[Assert\NotBlank(message: 'Description cannot be blank')]
    public string $description;
}
