<?php

namespace App\Tests\Todo;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Todo;
use App\Entity\User;
use App\Factory\TodoFactory;
use Zenstruck\Foundry\Test\Factories;
use Zenstruck\Foundry\Test\ResetDatabase;

class TodosTest  extends ApiTestCase
{
    use ResetDatabase, Factories;
    public $token;
    
    function setUp(): void
    {
        TodoFactory::createMany(100);
        $this->token = $this->getToken();    
    }

    public function testGetCollection(): void
    {   
        $response = static::createClient()->request('GET', '/api/todos',[
            'auth_bearer' => $this->token
        ]);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');
        $this->assertJsonContains(['@context' => '/api/contexts/Todo',
        '@id' => '/api/todos',
        '@type' => 'Collection',
        'totalItems' => 50,
        'view' => [
            "@id" => "/api/todos?page=1",
            "@type"=> "PartialCollectionView",
        ],]);

        $this->assertCount(10, $response->toArray()['member']);
        $this->assertMatchesResourceCollectionJsonSchema(Todo::class);
    }


    protected function getToken($body = []): string
    {
        $user = static::getContainer()->get('doctrine')->getRepository(User::class)->findOneUserByRole(false);
        if (!$user) {
            throw new \RuntimeException('No users found in the database.');
        }

        $email = $user->getEmail();
        $response = static::createClient()->request('POST', '/auth', ['json' => $body ?: [
            'email' => $email,
            'password' => '12345',
        ]]);
        $this->assertResponseIsSuccessful();
        $data = $response->toArray();
        return $data['token'];
    }


    public function testCreateTodo(): void
    {

        $response = static::createClient()->request('POST', '/api/todos', [
            'auth_bearer' => $this->token,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'title' => 'New Todo',
                'description' => 'This is a new todo item.'
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            "@context" => [
        "@vocab" => "http://localhost/api/docs.jsonld#",
        "hydra" => "http://www.w3.org/ns/hydra/core#",
        "id" => "ReturnTodoDto/id",
        "title" => "ReturnTodoDto/title",
        "description" => "ReturnTodoDto/description",
        "status" => "ReturnTodoDto/status"
    ],
    "@type" => "ReturnTodoDto",
    "id" => 101,
    "title" => "New Todo",
    "description" => "This is a new todo item.",
    "status" => "pending"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Todo::class);
    }

    public function testGetTodoById(): void
    {
        $response = static::createClient()->request('GET', '/api/todos/1', [
            'auth_bearer' => $this->token,
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/ld+json; charset=utf-8');

        $this->assertJsonContains([
            '@context' => '/api/contexts/Todo',
            '@type' => 'Todo',
            '@id' => '/api/todos/1',
        ]);

        $this->assertMatchesResourceItemJsonSchema(Todo::class);
    }

    public function testUpdateTodo(): void
    {
        $token = $this->getToken();

        $response = static::createClient()->request('PUT', '/api/todos/1', [
            'auth_bearer' => $token,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'title' => 'Updated Todo',
                'description' => 'This is an updated todo item.',
                'status' => 'done',
            ],
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $this->assertJsonContains([
            "status" => '200',
            "message" => "Todo updated successfully"
        ]);

        $this->assertMatchesResourceItemJsonSchema(Todo::class);
    }

    public function testDeleteTodo(): void
    {
        $token = $this->getToken();

        $response = static::createClient()->request('DELETE', '/api/todos/1', [
            'auth_bearer' => $token,
        ]);

        $this->assertResponseStatusCodeSame(204);

        $response = static::createClient()->request('GET', '/api/todos/1', [
            'auth_bearer' => $token,
        ]);
        $this->assertResponseStatusCodeSame(404);
    }

    public function testUpdateTodoCreatedByAnotherUser(): void
    {
        $response = static::createClient()->request('PUT', '/api/todos/56', [
            'auth_bearer' => $this->token,
            'headers' => [
                'Content-Type' => 'application/ld+json',
            ],
            'json' => [
                'title' => 'Updated Todo by Another User',
                'description' => 'This is an attempt to update another user\'s todo.',
                'status' => 'done',
            ],
        ]);

        // Assert that the update is forbidden
        $this->assertResponseStatusCodeSame(403);
        $this->assertJsonContains([
            'message' => 'Sorry, You do not have info permissions.',
        ]);
    }
}
