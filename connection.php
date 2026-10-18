<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Config\Database;

try {
    $pdo = Database::getConnection();
} catch (\Throwable $e) {
    die("Database initialization error: " . $e->getMessage());
}

// Backward-compatible mysqli connection wrapper (only if mysqli extension exists)
$con = null;
$conn = null;

if (extension_loaded('mysqli')) {
    $host = getenv('DB_HOST') ?: '127.0.0.1';
    $user = getenv('DB_USER') ?: 'root';
    $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
    $dbName = getenv('DB_NAME') ?: 'l&fbd';

    try {
        @$con = new mysqli($host, $user, $pass, $dbName);
        if ($con->connect_error) {
            $con = null;
        } else {
            $conn = $con;
        }
    } catch (\Throwable $ex) {
        $con = null;
        $conn = null;
    }
}