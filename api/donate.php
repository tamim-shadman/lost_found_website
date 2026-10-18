<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\PaymentService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();
$paymentService = new PaymentService();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed. Use POST.']);
    exit();
}

$raw = file_get_contents('php://input');
$json = json_decode($raw, true) ?? [];
$input = array_merge($_POST, $json);

$res = $paymentService->processDonation($input, $currentUser ? (int)$currentUser['id'] : null);
http_response_code($res['success'] ? 200 : 400);
echo json_encode($res);
