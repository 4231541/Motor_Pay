<?php

namespace App\Services;

use App\Models\Car;
use App\Models\PurchaseRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    private const CACHE_TTL_HOURS = 1;

    /**
     * Get aggregated dashboard statistics.
     */
    public function getStatistics(): array
    {
        return Cache::remember('dashboard:stats', now()->addHours(self::CACHE_TTL_HOURS), function () {
            
            $startOfMonth = Carbon::now()->startOfMonth();
            $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
            $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

            // Cars
            $totalCars = Car::count();
            $activeCars = Car::where('is_active', true)->count();
            
            // Users
            $totalUsers = User::count();
            $newUsersThisMonth = User::where('created_at', '>=', $startOfMonth)->count();
            $newUsersLastMonth = User::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->count();
            
            // Requests
            $totalRequests = PurchaseRequest::count();
            $pendingRequests = PurchaseRequest::where('status', 'received')->count();

            // Growth Calculation
            $userGrowth = $this->calculateGrowth($newUsersLastMonth, $newUsersThisMonth);

            // Top Viewed Cars
            $topViewedCars = Car::orderBy('view_count', 'desc')
                ->limit(5)
                ->get(['id', 'title', 'view_count', 'slug']);

            return [
                'totals' => [
                    'users' => $totalUsers,
                    'cars' => $totalCars,
                    'active_cars' => $activeCars,
                    'requests' => $totalRequests,
                    'pending_requests' => $pendingRequests,
                ],
                'growth' => [
                    'users_this_month' => $newUsersThisMonth,
                    'users_growth_percentage' => $userGrowth,
                ],
                'top_viewed_cars' => $topViewedCars,
            ];
        });
    }

    /**
     * Calculate percentage growth between two periods.
     */
    private function calculateGrowth(int $previous, int $current): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }
}
