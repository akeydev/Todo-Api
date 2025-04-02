<?php

namespace App\Story;

use App\Factory\TodoFactory;
use Zenstruck\Foundry\Story;

final class DefaultTodosStory extends Story
{
    public function build(): void
    {
        TodoFactory::createMany(100);
        // TODO build your story here (https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#stories)
    }
}
