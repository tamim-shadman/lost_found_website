<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-cache, no-store, must-revalidate');

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Config\Database;

$startTime = microtime(true);
$status = 'healthy';
$databaseStatus = 'unknown';
$databaseDriver = 'none';

try {
    $db = Database::getConnection();
    $stmt = $db->query('SELECT 1');
    if ($stmt) {
        $databaseStatus = 'connected';
        $databaseDriver = $db->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }
} catch (\Throwable $e) {
    $status = 'degraded';
    $databaseStatus = 'disconnected: ' . $e->getMessage();
}

$responseTimeMs = round((microtime(true) - $startTime) * 1000, 2);

http_response_code($status === 'healthy' ? 200 : 503);
echo json_encode([
    'success' => $status === 'healthy',
    'status' => $status,
    'version' => '2.0.0',
    'environment' => getenv('APP_ENV') ?: 'production',
    'timestamp' => date('c'),
    'database' => [
        'status' => $databaseStatus,
        'driver' => $databaseDriver
    ],
    'latency_ms' => $responseTimeMs,
    'memory_usage_mb' => round(memory_get_usage(true) / 1024 / 1024, 2)
], JSON_PRETTY_PRINT);
