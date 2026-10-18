<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\ItemService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$itemService = new ItemService();
$currentUser = $auth->getCurrentUser();
$method = $_SERVER['REQUEST_METHOD'];

// Parse incoming input
$rawBody = file_get_contents('php://input');
$jsonBody = json_decode($rawBody, true) ?? [];
$input = array_merge($_GET, $_POST, $jsonBody);
$action = $input['action'] ?? ($method === 'GET' ? 'list' : 'create');

try {
    switch ($action) {
        case 'list':
            $filters = [
                'type'     => $input['type'] ?? null,
                'category' => $input['category'] ?? null,
                'division' => $input['division'] ?? null,
                'query'    => $input['query'] ?? $input['search'] ?? null,
                'status'   => $input['status'] ?? null,
                'user_id'  => $input['user_id'] ?? null,
                'limit'    => $input['limit'] ?? 50,
                'offset'   => $input['offset'] ?? 0,
            ];
            $result = $itemService->getItems($filters);
            echo json_encode(['success' => true, 'data' => $result]);
            break;

        case 'get':
            $id = (int)($input['id'] ?? 0);
            $item = $itemService->getItemById($id);
            if ($item) {
                echo json_encode(['success' => true, 'item' => $item]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Item not found.']);
            }
            break;

        case 'create':
            $file = $_FILES['picture'] ?? $_FILES['image'] ?? null;
            $userId = $currentUser ? (int)$currentUser['id'] : null;
            $res = $itemService->createItem($input, $file, $userId);
            http_response_code($res['success'] ? 201 : 400);
            echo json_encode($res);
            break;

        case 'update':
            $id = (int)($input['id'] ?? 0);
            $userId = $currentUser ? (int)$currentUser['id'] : null;
            $isAdmin = $currentUser && in_array($currentUser['role'], ['admin', 'moderator']);
            $res = $itemService->updateItem($id, $input, $userId, $isAdmin);
            http_response_code($res['success'] ? 200 : 400);
            echo json_encode($res);
            break;

        case 'delete':
            $id = (int)($input['id'] ?? 0);
            $userId = $currentUser ? (int)$currentUser['id'] : null;
            $isAdmin = $currentUser && in_array($currentUser['role'], ['admin', 'moderator']);
            $res = $itemService->deleteItem($id, $userId, $isAdmin);
            http_response_code($res['success'] ? 200 : 403);
            echo json_encode($res);
            break;

        case 'claim':
            if (!$currentUser) {
                http_response_code(401);
                echo json_encode(['success' => false, 'error' => 'Please log in to submit an ownership claim.']);
                exit();
            }
            $itemId = (int)($input['item_id'] ?? 0);
            $claimantName = $input['claimant_name'] ?? ($currentUser['first_name'] . ' ' . $currentUser['last_name']);
            $phone = $input['contact_phone'] ?? $currentUser['phone'];
            $proof = $input['proof_description'] ?? '';
            $proofFile = $_FILES['proof_image'] ?? null;

            $res = $itemService->submitClaim($itemId, (int)$currentUser['id'], $claimantName, $phone, $proof, $proofFile);
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
