<?php
// Quick connection check script
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Brand;
use App\Models\Car;
use App\Models\Role;
use App\Models\Permission;

echo "\n=== DATABASE CONNECTION ===\n";
try {
    DB::connection()->getPdo();
    echo "✅ MySQL Connected: " . DB::connection()->getDatabaseName() . "\n";
    echo "   Host: " . env('DB_HOST') . ":" . env('DB_PORT') . "\n";
} catch (\Exception $e) {
    echo "❌ DB Error: " . $e->getMessage() . "\n";
}

echo "\n=== DATABASE TABLES & RECORDS ===\n";
$tables = [
    'users'               => User::count(),
    'roles'               => Role::count(),
    'permissions'         => Permission::count(),
    'brands'              => Brand::count(),
    'cars'                => Car::count(),
    'favorites'           => DB::table('favorites')->count(),
    'requests'            => DB::table('requests')->count(),
    'audit_logs'          => DB::table('audit_logs')->count(),
    'media'               => DB::table('media')->count(),
    'personal_access_tokens' => DB::table('personal_access_tokens')->count(),
];
foreach ($tables as $table => $count) {
    echo "   {$table}: {$count} records\n";
}

echo "\n=== ROLES & PERMISSIONS ===\n";
$roles = Role::with('permissions')->get();
foreach ($roles as $role) {
    echo "   [{$role->name}] → " . $role->permissions->count() . " permissions\n";
}

echo "\n=== CACHE ===\n";
try {
    \Illuminate\Support\Facades\Cache::put('test_key', 'ok', 10);
    $val = \Illuminate\Support\Facades\Cache::get('test_key');
    echo ($val === 'ok') ? "✅ Cache (file) working\n" : "❌ Cache not working\n";
} catch (\Exception $e) {
    echo "❌ Cache Error: " . $e->getMessage() . "\n";
}

echo "\n=== STORAGE ===\n";
try {
    \Illuminate\Support\Facades\Storage::disk('local')->put('test.txt', 'ok');
    \Illuminate\Support\Facades\Storage::disk('local')->delete('test.txt');
    echo "✅ Storage (local) writable\n";
} catch (\Exception $e) {
    echo "❌ Storage Error: " . $e->getMessage() . "\n";
}

echo "\n=== API ROUTES COUNT ===\n";
$routes = \Illuminate\Support\Facades\Route::getRoutes();
$apiRoutes = collect($routes->getRoutes())->filter(fn($r) => str_starts_with($r->uri(), 'api/v1'));
echo "   Total API v1 routes: " . $apiRoutes->count() . "\n";

echo "\n=== APP CONFIG ===\n";
echo "   APP_ENV:  " . config('app.env') . "\n";
echo "   APP_URL:  " . config('app.url') . "\n";
echo "   DB:       " . config('database.default') . " → " . config('database.connections.mysql.database') . "\n";
echo "   CACHE:    " . config('cache.default') . "\n";
echo "   QUEUE:    " . config('queue.default') . "\n";
echo "   SESSION:  " . config('session.driver') . "\n";

echo "\n✅ All checks done.\n\n";
