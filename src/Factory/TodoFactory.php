<?php

namespace App\Factory;

use App\Entity\Todo;
use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Todo>
 */
final class TodoFactory extends PersistentProxyObjectFactory
{
    private static int $todoCount = 0;
    private static ?User $currentUser = null;

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Todo::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        self::$todoCount++;

        if (self::$todoCount % 50 === 1) {
            self::$currentUser = UserFactory::new()->create();
        }
        return [
            'createdBy' => self::$currentUser ?? UserFactory::new()->create(),
            'description' => self::faker()->text(55),
            'status' => self::faker()->word(),
            'title' => self::faker()->sentence(),
        ];

    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Todo $todo): void {})
        ;
    }
}
