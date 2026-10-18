<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\MissingPersonService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$missingService = new MissingPersonService();
$currentUser = $auth->getCurrentUser();
$method = $_SERVER['REQUEST_METHOD'];

$rawBody = file_get_contents('php://input');
$jsonBody = json_decode($rawBody, true) ?? [];
$input = array_merge($_GET, $_POST, $jsonBody);
$action = $input['action'] ?? ($method === 'GET' ? 'list' : 'create');

try {
    switch ($action) {
        case 'list':
            $filters = [
                'division' => $input['division'] ?? null,
                'status'   => $input['status'] ?? null,
                'gender'   => $input['gender'] ?? null,
                'query'    => $input['query'] ?? $input['search'] ?? null,
                'limit'    => $input['limit'] ?? 50,
                'offset'   => $input['offset'] ?? 0,
            ];
            $result = $missingService->getMissingPersons($filters);
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'get':
            $id = (int)($input['id'] ?? 0);
            $person = $missingService->getPersonById($id);
            if ($person) {
                echo json_encode(['success' => true, 'person' => $person]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Missing person record not found.']);
            }
            break;

        case 'create':
            $file = $_FILES['photo'] ?? $_FILES['image'] ?? null;
            $userId = $currentUser ? (int)$currentUser['id'] : null;
            $res = $missingService->reportMissingPerson($input, $file, $userId);
            http_response_code($res['success'] ? 201 : 400);
            echo json_encode($res);
            break;

        case 'sighting':
            $id = (int)($input['id'] ?? $input['person_id'] ?? 0);
            $location = $input['location'] ?? '';
            $details = $input['details'] ?? '';
            $reporterContact = $input['contact'] ?? ($currentUser['phone'] ?? 'Anonymous');
            $res = $missingService->reportSighting($id, $location, $details, $reporterContact);
            http_response_code($res['success'] ? 200 : 400);
            echo json_encode($res);
            break;

        case 'update_status':
            $id = (int)($input['id'] ?? 0);
            $status = $input['status'] ?? '';
            $isAdmin = $currentUser && in_array($currentUser['role'], ['admin', 'moderator']);
            $res = $missingService->updateStatus($id, $status, $currentUser ? (int)$currentUser['id'] : null, $isAdmin);
            echo json_encode($res);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unsupported action.']);
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Server error: ' . $e->getMessage()]);
}
