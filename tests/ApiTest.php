<?php

namespace Tests;

class ApiTest extends TestCase
{
    public function testPing(): void
    {
        $response = $this->getJson('/api/ping');

        $response->assertStatus(200)
                 ->assertJson(['pong' => true]);
    }

    public function testGetUsers(): void
    {
        config(['services.internal_api.key' => 'apikey123']);

        $response = $this->getJson('/api/internal/users', [
            'X-Internal-Api-Key' => config('services.internal_api.key'),
        ]);

        $response->assertStatus(200);
    }

    public function testGetUsersUnauthorized(): void
    {
        // Sätt rätt nyckel i config
        config(['services.internal_api.key' => 'apikey123']);

        // Skicka FEL nyckel i headern
        $response = $this->getJson('/api/internal/users', [
            'X-Internal-Api-Key' => 'wrong-key',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthorized']);
    }

}
