<?php

namespace Tests\Feature\Api\V1;

use App\Models\Car;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CarMediaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is required for image tests.');
        }
        Storage::fake('local');
    }

    public function test_owner_can_upload_car_media(): void
    {
        $owner = User::factory()->create();
        $car = Car::factory()->create(['owner_id' => $owner->id]);

        $file = UploadedFile::fake()->image('car_front.jpg');

        $response = $this->actingAs($owner)->postJson("/api/v1/cars/{$car->id}/media", [
            'images' => [$file],
        ]);

        $response->assertStatus(201);
        $this->assertCount(1, $car->getMedia('car_gallery'));
    }
}
