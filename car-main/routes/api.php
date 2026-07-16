<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\AuthController;

Route::prefix('v1')->group(function () {
    Route::prefix('auth')->group(function () {
        Route::post('register', [AuthController::class, 'register']);
        Route::post('login', [AuthController::class, 'login']);
        
        Route::middleware('auth:sanctum')->group(function () {
            Route::get('me', [AuthController::class, 'me']);
            Route::post('update-profile', [AuthController::class, 'updateProfile']);
            Route::post('change-password', [AuthController::class, 'changePassword']);
            Route::post('logout', [AuthController::class, 'logout']);
        });
    });

    // =========================================================================
    // Public Catalog Routes (Brands, Models, Cars)
    // =========================================================================
    Route::get('health', [\App\Http\Controllers\Api\V1\HealthController::class, 'index']);
    
    Route::get('brands', [\App\Http\Controllers\Api\V1\BrandController::class, 'index']);
    Route::get('brands/{brand}', [\App\Http\Controllers\Api\V1\BrandController::class, 'show']);
    
    Route::get('models', [\App\Http\Controllers\Api\V1\CarModelController::class, 'index']);
    Route::get('brands/{brand}/models', [\App\Http\Controllers\Api\V1\CarModelController::class, 'getByBrand']);
    Route::get('models/{model}', [\App\Http\Controllers\Api\V1\CarModelController::class, 'show']);

    Route::get('cars', [\App\Http\Controllers\Api\V1\CarController::class, 'index']);
    Route::get('cars/featured', [\App\Http\Controllers\Api\V1\CarController::class, 'featured']);
    Route::get('cars/{slug}', [\App\Http\Controllers\Api\V1\CarController::class, 'show']);

    // =========================================================================
    // Protected Catalog Management Routes
    // =========================================================================
    Route::middleware('auth:sanctum')->group(function () {
        
        // Admin Dashboard & Logs
        Route::get('admin/dashboard', [\App\Http\Controllers\Api\V1\Admin\DashboardController::class, 'index'])
            ->middleware('role:super_admin,admin');
            
        Route::get('admin/audit-logs', [\App\Http\Controllers\Api\V1\Admin\AuditLogController::class, 'index'])
            ->middleware('role:super_admin,admin');

        // Admin User Management
        Route::prefix('admin/users')->middleware('role:super_admin,admin')->group(function () {
            Route::get('/', [\App\Http\Controllers\Api\V1\Admin\AdminUserController::class, 'index']);
            Route::post('/', [\App\Http\Controllers\Api\V1\Admin\AdminUserController::class, 'store']);
            Route::get('{user}', [\App\Http\Controllers\Api\V1\Admin\AdminUserController::class, 'show']);
            Route::put('{user}', [\App\Http\Controllers\Api\V1\Admin\AdminUserController::class, 'update']);
            Route::delete('{user}', [\App\Http\Controllers\Api\V1\Admin\AdminUserController::class, 'destroy']);
            Route::patch('{user}/roles', [\App\Http\Controllers\Api\V1\Admin\AdminUserController::class, 'assignRoles']);
        });

            
        // Cars (CRUD, Status, Media)
        Route::post('cars', [\App\Http\Controllers\Api\V1\CarController::class, 'store']);
        Route::put('cars/{uuid}', [\App\Http\Controllers\Api\V1\CarController::class, 'update']);
        Route::delete('cars/{uuid}', [\App\Http\Controllers\Api\V1\CarController::class, 'destroy']);
        Route::patch('cars/{uuid}/publish', [\App\Http\Controllers\Api\V1\CarController::class, 'publish']);
        Route::patch('cars/{uuid}/feature', [\App\Http\Controllers\Api\V1\CarController::class, 'toggleFeatured']);
        
        // Car Media
        Route::post('cars/{uuid}/media', [\App\Http\Controllers\Api\V1\CarMediaController::class, 'store']);
        Route::delete('cars/{uuid}/media/{mediaUuid}', [\App\Http\Controllers\Api\V1\CarMediaController::class, 'destroy']);
        Route::post('cars/{uuid}/media/reorder', [\App\Http\Controllers\Api\V1\CarMediaController::class, 'reorder']);
        
        // Favorites
        Route::get('favorites', [\App\Http\Controllers\Api\V1\FavoriteController::class, 'index']);
        Route::post('favorites/{carUuid}', [\App\Http\Controllers\Api\V1\FavoriteController::class, 'store']);
        Route::delete('favorites/{carUuid}', [\App\Http\Controllers\Api\V1\FavoriteController::class, 'destroy']);
        
        // Purchase & Financing Requests
        Route::get('requests', [\App\Http\Controllers\Api\V1\PurchaseRequestController::class, 'index']);
        Route::post('requests', [\App\Http\Controllers\Api\V1\PurchaseRequestController::class, 'store']);
        Route::get('requests/{purchaseRequest}', [\App\Http\Controllers\Api\V1\PurchaseRequestController::class, 'show']);
        Route::patch('requests/{purchaseRequest}/status', [\App\Http\Controllers\Api\V1\PurchaseRequestController::class, 'updateStatus']);
        Route::patch('requests/{purchaseRequest}/assign', [\App\Http\Controllers\Api\V1\PurchaseRequestController::class, 'assign']);
        Route::get('requests/{purchaseRequest}/logs', [\App\Http\Controllers\Api\V1\PurchaseRequestController::class, 'logs']);
        
        // Brands
        Route::post('brands', [\App\Http\Controllers\Api\V1\BrandController::class, 'store'])
            ->middleware('permission:brand.create');
        Route::put('brands/{brand}', [\App\Http\Controllers\Api\V1\BrandController::class, 'update'])
            ->middleware('permission:brand.update');
        Route::delete('brands/{brand}', [\App\Http\Controllers\Api\V1\BrandController::class, 'destroy'])
            ->middleware('permission:brand.delete');

        // Car Models
        Route::post('models', [\App\Http\Controllers\Api\V1\CarModelController::class, 'store'])
            ->middleware('permission:model.create');
        Route::put('models/{model}', [\App\Http\Controllers\Api\V1\CarModelController::class, 'update'])
            ->middleware('permission:model.update');
        Route::delete('models/{model}', [\App\Http\Controllers\Api\V1\CarModelController::class, 'destroy'])
            ->middleware('permission:model.delete');

    });
});
