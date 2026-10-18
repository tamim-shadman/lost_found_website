<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$method = $_SERVER['REQUEST_METHOD'];

// Parse JSON input body if present
$rawBody = file_get_contents('php://input');
$jsonBody = json_decode($rawBody, true) ?? [];
$input = array_merge($_GET, $_POST, $jsonBody);
$action = $input['action'] ?? '';

try {
    switch ($action) {
        case 'login':
            $email = $input['email'] ?? $input['Email'] ?? '';
            $password = $input['password'] ?? $input['Password'] ?? '';
            $res = $auth->login($email, $password);
            http_response_code($res['success'] ? 200 : 401);
            echo json_encode($res);
            break;

        case 'register':
            $firstName = $input['first_name'] ?? $input['Fname'] ?? '';
            $lastName = $input['last_name'] ?? $input['lname'] ?? '';
            $email = $input['email'] ?? $input['Email'] ?? '';
            $phone = $input['phone'] ?? $input['Mobile'] ?? '';
            $password = $input['password'] ?? '';
            $res = $auth->register($firstName, $lastName, $email, $phone, $password);
            http_response_code($res['success'] ? 201 : 400);
            echo json_encode($res);
            break;

        case 'logout':
            $auth->logout();
            echo json_encode(['success' => true, 'message' => 'Logged out successfully.']);
            break;

        case 'me':
            $user = $auth->getCurrentUser();
            if ($user) {
                echo json_encode(['success' => true, 'user' => $user]);
            } else {
                echo json_encode(['success' => false, 'user' => null]);
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unknown action. Supported: login, register, logout, me.']);
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Internal server error: ' . $e->getMessage()]);
}
