<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Brands\StoreBrandRequest;
use App\Http\Requests\Api\V1\Brands\UpdateBrandRequest;
use App\Http\Resources\Api\V1\BrandResource;
use App\Models\Brand;
use App\Services\BrandService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly BrandService $brandService
    ) {}

    /**
     * Display a listing of active brands (for dropdowns).
     */
    public function index(Request $request): JsonResponse
    {
        // If paginated is requested, typically by admin panel
        if ($request->boolean('paginate')) {
            $brands = $this->brandService->getPaginated($request->integer('per_page', 15));
            return $this->success(BrandResource::collection($brands)->response()->getData(true));
        }

        // Default: cached list of active brands
        $brands = $this->brandService->getActiveBrands();
        return $this->success(BrandResource::collection($brands));
    }

    /**
     * Store a newly created brand in storage.
     */
    public function store(StoreBrandRequest $request): JsonResponse
    {
        $brand = $this->brandService->createBrand(
            $request->validated(),
            $request->file('logo')
        );

        return $this->success(new BrandResource($brand), 'Brand created successfully.', 201);
    }

    /**
     * Display the specified brand.
     */
    public function show(int $id): JsonResponse
    {
        $brand = $this->brandService->findByIdOrFail($id);
        return $this->success(new BrandResource($brand));
    }

    /**
     * Update the specified brand in storage.
     */
    public function update(UpdateBrandRequest $request, int $id): JsonResponse
    {
        $brand = $this->brandService->findByIdOrFail($id);
        
        $brand = $this->brandService->updateBrand(
            $brand,
            $request->validated(),
            $request->file('logo')
        );

        return $this->success(new BrandResource($brand), 'Brand updated successfully.');
    }

    /**
     * Remove the specified brand from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $brand = $this->brandService->findByIdOrFail($id);
        $this->brandService->deleteBrand($brand);

        return $this->success(null, 'Brand deleted successfully.');
    }
}
