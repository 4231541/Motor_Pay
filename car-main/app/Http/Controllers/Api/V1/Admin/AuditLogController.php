<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Api\V1\ApiController;
use App\Models\AuditLog;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuditLogController extends ApiController
{
    use ApiResponse;

    /**
     * @OA\Get(
     *     path="/api/v1/admin/audit-logs",
     *     summary="List Audit Logs",
     *     description="Retrieve a paginated list of system audit logs.",
     *     operationId="getAuditLogs",
     *     tags={"Admin Audit Logs"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        
        $logs = AuditLog::with('user:id,name,email')
            ->latest('created_at')
            ->paginate($perPage);

        $paginated = $logs->toArray();

        return $this->success([
            'data'  => $paginated['data'],
            'meta'  => [
                'current_page' => $paginated['current_page'],
                'last_page'    => $paginated['last_page'],
                'per_page'     => $paginated['per_page'],
                'total'        => $paginated['total'],
            ],
            'links' => [
                'first' => $paginated['first_page_url'],
                'last'  => $paginated['last_page_url'],
                'prev'  => $paginated['prev_page_url'],
                'next'  => $paginated['next_page_url'],
            ],
        ]);
    }
}
