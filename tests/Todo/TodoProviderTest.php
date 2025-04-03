<?php

namespace App\Tests;

use ApiPlatform\Doctrine\Orm\Paginator;
use ApiPlatform\Metadata\GetCollection;
use App\Entity\User;
use App\State\GetTodoProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TodoProviderTest extends KernelTestCase
{
    public function testContainerService(): void
    {
        self::bootKernel();
        $container = static::getContainer();
        $this->assertNotNull($container);
    }

    public function testGetTodoProviderService(): void
    {
        self::bootKernel();
        
        $container = static::getContainer();
        $security = $container->get('security.helper');
        
        $operation = $container->get('api_platform.operation.get_collection');
        $this->assertInstanceOf(GetCollection::class, $operation);

        $user = $security->getUser();
        $this->assertInstanceOf(User::class, $user);
        
        $getTodoProvider = $container->get(GetTodoProvider::class);
        $this->assertInstanceOf(GetTodoProvider::class, $getTodoProvider);

        $result = $getTodoProvider->provide();
        $this->assertInstanceOf(Paginator::class, $result);
    }
}
