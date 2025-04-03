<?php

namespace App\Tests;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\GraphQl\Operation;
use ApiPlatform\Metadata\Operation\Factory\OperationMetadataFactoryInterface;
use App\Entity\User;
use App\State\GetTodoProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Bundle\SecurityBundle\Security;

class TodoProviderTest extends KernelTestCase
{
    private $security;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->security = $this->createMock(Security::class);
        static::getContainer()->set('security.helper', $this->security);
    }

    public function testGetTodoProviderService(): void
    {
        
        $operation = $this->createMock(Operation::class);

        $getTodoProvider = static::getContainer()->get(GetTodoProvider::class);

        $user = new User();
        $this->security
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($user);
            
        $this->security->method('getUser')->willReturn($user);

        $this->assertInstanceOf(GetTodoProvider::class, $getTodoProvider);
        
        $context['filters'] = ['title' => 'en'];
        $uriVariables = [];
        $result = $getTodoProvider->provide($operation, $uriVariables, $context);
        $this->assertInstanceOf(Paginator::class, $result);
    }
}
