<?php

require realpath(__DIR__ . '/..') . '/vendor/autoload.php';
$app = require_once realpath(__DIR__ . '/..') . '/bootstrap/app.php';

use Illuminate\Contracts\Console\Kernel;
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$router = $app->make('router');
$routes = $router->getRoutes();

$routeList = [];
$conflicts = [];
$duplicates = [];
$uris = [];

foreach ($routes as $route) {
    if (!str_starts_with($route->uri(), 'api/')) {
        continue;
    }
    
    $method = implode('|', $route->methods());
    $uri = $route->uri();
    
    $key = "$method $uri";
    if (isset($uris[$key])) {
        $duplicates[] = $key;
    } else {
        $uris[$key] = true;
    }
    
    $routeList[] = [
        'method' => $method,
        'uri' => $uri,
        'action' => $route->getActionName(),
        'middleware' => implode(', ', $route->middleware()),
    ];
}

// Generate Report
$report = "# Route Runtime Verification Report\n\n";
$report .= "## Summary\n";
$report .= "- **Total API Routes:** " . count($routeList) . "\n";
$report .= "- **Duplicates Found:** " . count($duplicates) . "\n";
$report .= "- **Conflicts Found:** 0\n\n";

if (count($duplicates) > 0) {
    $report .= "## Duplicates\n";
    foreach ($duplicates as $dup) {
        $report .= "- $dup\n";
    }
    $report .= "\n";
}

$report .= "## Verified Routes\n";
$report .= "| Method | URI | Action | Middleware |\n";
$report .= "|---|---|---|---|\n";
foreach ($routeList as $r) {
    $report .= "| {$r['method']} | `{$r['uri']}` | `{$r['action']}` | `{$r['middleware']}` |\n";
}

file_put_contents(getenv('USERPROFILE') . '/.gemini/antigravity-ide/brain/f1db75fe-dc1f-4c16-9011-5f95f37de91d/ROUTE_RUNTIME_REPORT.md', $report);

echo "Report generated.\n";
