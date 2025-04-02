<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Get;
use ApiPlatform\OpenApi\Model\Operation;
use ApiPlatform\OpenApi\Model\Response;
use App\Dto\CreateTodoDto;
use App\Dto\ReturnTodoDto;
use App\Dto\UpdateTodoDto;
use App\Repository\TodoRepository;
use App\State\GetTodoProvider;
use App\State\TodoPostProcessor;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TodoRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            provider: GetTodoProvider::class,
            paginationItemsPerPage: 10,
            paginationClientItemsPerPage: true
          ),
        new Post(
            openapi: new Operation(
                responses: [
                    '200' => new Response(
                        description: 'Ok',
                        content: new \ArrayObject([
                            'application/json' => [
                                'example' => [
                                    'id' => 1,
                                    'title' => 'Sample Todo',
                                    'description' => 'This is a sample todo item.'
                                ]
                            ]
                        ])
                    )
                ]
            ),
            processor: TodoPostProcessor::class,
            input: CreateTodoDto::class, 
            output: ReturnTodoDto::class,
        ),
        new Put(
            processor: TodoPostProcessor::class,
            input: UpdateTodoDto::class,
            security: "is_granted('ROLE_ADMIN') or object.createdBy == user",
            securityMessage: "Sorry, You do not have info permissions.",
            output: ReturnTodoDto::class
        ),
        new Delete(security: "is_granted('ROLE_ADMIN') or object.createdBy == user"),
        new Get(security: "is_granted('ROLE_ADMIN') or object.createdBy == user",
            securityMessage: "Sorry, You do not have info permissions."
        )
    ],
)]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial', 'status' => 'partial'])]
class Todo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\ManyToOne(inversedBy: 'todo')]
    #[ORM\JoinColumn(nullable: false)]
    public ?User $createdBy = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

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
}
