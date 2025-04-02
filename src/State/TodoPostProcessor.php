<?php

namespace App\State;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\OpenApi\Model\Response;
use ApiPlatform\State\ProcessorInterface;
use App\Dto\ReturnTodoDto;
use App\Dto\UpdateTodoDto;
use App\Entity\Todo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class TodoPostProcessor implements ProcessorInterface
{
    private $entityManager;
    private $security;

    public function __construct(EntityManagerInterface $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): ReturnTodoDto|null|JsonResponse
    
    {
        if($operation instanceof Post) {
            $todo = new Todo();
            $user = $this->security->getUser();
            $todo->setTitle($data->title);
            $todo->setDescription($data->description);
            $todo->setStatus('pending');
            $todo->setCreatedBy($user);
            $this->entityManager->persist($todo);
            $this->entityManager->flush();
            return $this->ReturnData($todo);
        }

        if($operation instanceof Put && $data instanceof UpdateTodoDto && isset($uriVariables['id'])){
            $todo = $this->entityManager->getRepository(Todo::class)->find($uriVariables['id']);
            $todo->setTitle($data->title);
            $todo->setDescription($data->description);
            $todo->setStatus($data->status);
            $this->entityManager->persist($todo);
            $this->entityManager->flush();
            return new JsonResponse([
                'status' => '200',
                'message' => 'Todo updated successfully',
            ], 200);
        }
        return null;
    }

    function ReturnData(Todo $todo): ReturnTodoDto {
        return new ReturnTodoDto(
            $todo->getId(),
            $todo->getTitle(),
            $todo->getDescription(),
            $todo->getStatus()
        );
    }
}
