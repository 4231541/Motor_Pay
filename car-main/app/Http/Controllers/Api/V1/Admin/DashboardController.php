<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\ApiController;
use App\Services\DashboardService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class DashboardController extends ApiController
{
    use ApiResponse;

    public function __construct(private readonly DashboardService $dashboardService) {}

    /**
     * @OA\Get(
     *     path="/api/v1/admin/dashboard",
     *     summary="Get Admin Dashboard Statistics",
     *     description="Returns aggregated metrics for the admin dashboard.",
     *     operationId="getDashboardStats",
     *     tags={"Admin Dashboard"},
     *     security={{"sanctum":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="totals", type="object"),
     *                 @OA\Property(property="growth", type="object"),
     *                 @OA\Property(property="top_viewed_cars", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        $stats = $this->dashboardService->getStatistics();

        return $this->success($stats);
    }
}
