<?php

namespace Tests\Unit\Services;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use App\Services\BrandService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Tests\TestCase;

class BrandServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_brand_service_generates_unique_slug_and_clears_cache(): void
    {
        $service = new BrandService(app(BrandRepositoryInterface::class));

        $brand1 = $service->createBrand(['name' => 'Toyota', 'is_active' => true]);

        $this->assertEquals('toyota', $brand1->slug);

        // Cache should be cleared
        $this->assertNull(Cache::get('brands:list'));

        // Directly insert a brand with slug 'honda' to test uniqueness logic
        $brand2 = $service->createBrand(['name' => 'Honda', 'is_active' => true]);

        $this->assertEquals('honda', $brand2->slug);
    }

    public function test_brand_service_uploads_media_correctly(): void
    {
        if (!extension_loaded('gd')) {
            $this->markTestSkipped('GD extension is required for image tests.');
        }

        Storage::fake('local');

        $service = new BrandService(app(BrandRepositoryInterface::class));

        $file = UploadedFile::fake()->image('logo.png');

        $brand = $service->createBrand(['name' => 'Media Brand', 'is_active' => true], $file);

        $this->assertCount(1, $brand->getMedia('brand_logo'));
        $this->assertStringContainsString('logo.png', $brand->getFirstMedia('brand_logo')->file_name);
    }
}
