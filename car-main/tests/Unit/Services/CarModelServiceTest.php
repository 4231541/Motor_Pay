<?php

namespace Tests\Unit\Services;

use App\Models\Brand;
use App\Models\CarModel;
use App\Repositories\Contracts\CarModelRepositoryInterface;
use App\Services\CarModelService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CarModelServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_car_model_service_clears_cache_and_generates_slugs(): void
    {
        $brand = Brand::factory()->create();
        
        Cache::tags(['models'])->put("models:list:{$brand->id}", collect([]));
        
        $service = new CarModelService(app(CarModelRepositoryInterface::class));
        
        $model = $service->createModel([
            'brand_id' => $brand->id,
            'name' => 'Model X',
            'is_active' => true,
        ]);
        
        $this->assertEquals('model-x', $model->slug);
        $this->assertNull(Cache::get("models:list:{$brand->id}"));
    }
}
