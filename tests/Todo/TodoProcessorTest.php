<?php

namespace App\Tests\Todo;

use App\Entity\Todo;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class TodoProcessorTest extends KernelTestCase
{
    private EntityManagerInterface $entityManager;
    private Security $security;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
        $this->security = $kernel->getContainer()->get('security.helper');
    }

    public function testCreateTodo(): void
    {
        $data = (object) [
            'title' => 'Test Todo',
            'description' => 'This is a test description',
        ];

        $user = $this->security->getUser();

        $todo = new Todo();
        $todo->setTitle($data->title);
        $todo->setDescription($data->description);
        $todo->setStatus('pending');
        $todo->setCreatedBy($user);

        $this->entityManager->persist($todo);
        $this->entityManager->flush();

        $this->assertNotNull($todo->getId());
        $this->assertEquals('Test Todo', $todo->getTitle());
        $this->assertEquals('This is a test description', $todo->getDescription());
        $this->assertEquals('pending', $todo->getStatus());

        $this->assertSame($user, $todo->getCreatedBy());
    }

    public function testUpdateTodo(): void
    {
        $todo = new Todo();
        $todo->setTitle('Old Title');
        $todo->setDescription('Old Description');
        $todo->setStatus('pending');

        $this->entityManager->persist($todo);
        $this->entityManager->flush();

        $data = (object) [
            'title' => 'Updated Title',
            'description' => 'Updated Description',
            'status' => 'completed',
        ];

        $todo->setTitle($data->title);
        $todo->setDescription($data->description);
        $todo->setStatus($data->status);

        $this->entityManager->persist($todo);
        $this->entityManager->flush();
       
        $this->assertEquals('Updated Title', $todo->getTitle());
        $this->assertEquals('Updated Description', $todo->getDescription());
        $this->assertEquals('completed', $todo->getStatus());
    }
}