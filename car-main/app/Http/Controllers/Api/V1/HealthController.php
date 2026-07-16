<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\V1\ApiController;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class HealthController extends ApiController
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/v1/health",
     *     summary="System Health Check",
     *     description="Checks the availability of the database, cache, and storage systems.",
     *     operationId="systemHealth",
     *     tags={"System"},
     *     @OA\Response(
     *         response=200,
     *         description="System is healthy",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="string", example="healthy"),
     *                 @OA\Property(property="services", type="object",
     *                     @OA\Property(property="database", type="string", example="connected"),
     *                     @OA\Property(property="cache", type="string", example="connected"),
     *                     @OA\Property(property="storage", type="string", example="writable")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $services = [
            'database' => $this->checkDatabase(),
            'cache' => $this->checkCache(),
            'storage' => $this->checkStorage(),
        ];

        $isHealthy = !in_array('error', array_values($services), true);
        $statusCode = $isHealthy ? 200 : 503;

        return response()->json([
            'status' => $isHealthy ? 'success' : 'error',
            'data' => [
                'status' => $isHealthy ? 'healthy' : 'degraded',
                'timestamp' => now()->toISOString(),
                'services' => $services,
            ]
        ], $statusCode);
    }

    private function checkDatabase(): string
    {
        try {
            DB::connection()->getPdo();
            return 'connected';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkCache(): string
    {
        try {
            Cache::set('health_check', 'ok', 10);
            return Cache::get('health_check') === 'ok' ? 'connected' : 'error';
        } catch (\Exception $e) {
            return 'error';
        }
    }

    private function checkStorage(): string
    {
        try {
            // Using default local storage compatible with shared hosting
            $disk = Storage::disk('local');
            $disk->put('health_check.txt', 'ok');
            $disk->delete('health_check.txt');
            return 'writable';
        } catch (\Exception $e) {
            return 'error';
        }
    }
}
