<?php

namespace Tests\Feature\Api\V1;

use Tests\TestCase;

class HealthControllerTest extends TestCase
{
    public function test_health_endpoint_returns_ok(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status',
                     'data' => [
                         'status',
                         'timestamp',
                         'services' => [
                             'database',
                             'cache',
                             'storage'
                         ]
                     ]
                 ]);
    }
}
