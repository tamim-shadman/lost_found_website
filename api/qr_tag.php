<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\QrTagService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$qrService = new QrTagService();
$currentUser = $auth->getCurrentUser();

$rawBody = file_get_contents('php://input');
$jsonBody = json_decode($rawBody, true) ?? [];
$input = array_merge($_GET, $_POST, $jsonBody);
$action = $input['action'] ?? 'list';

try {
    switch ($action) {
        case 'create':
            $userId = $currentUser ? (int)$currentUser['id'] : 2; // Default to demo user if anonymous in dev
            $assetName = trim($input['asset_name'] ?? '');
            $category = trim($input['category'] ?? 'General');
            $notes = trim($input['notes'] ?? '');

            if (empty($assetName)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Asset name is required.']);
                break;
            }

            $res = $qrService->createTag($userId, $assetName, $category, $notes);
            http_response_code(201);
            echo json_encode($res);
            break;

        case 'list':
            $userId = $currentUser ? (int)$currentUser['id'] : 2;
            $tags = $qrService->getUserTags($userId);
            echo json_encode(['success' => true, 'tags' => $tags]);
            break;

        case 'inspect':
            $tagCode = trim($input['tag_code'] ?? '');
            $details = $qrService->getTagDetails($tagCode);
            if ($details) {
                echo json_encode(['success' => true, 'tag' => $details]);
            } else {
                http_response_code(404);
                echo json_encode(['success' => false, 'error' => 'Asset tag code not found.']);
            }
            break;

        case 'report_found':
            $tagCode = trim($input['tag_code'] ?? '');
            $name = trim($input['finder_name'] ?? 'Honest Citizen');
            $phone = trim($input['finder_phone'] ?? '');
            $location = trim($input['found_location'] ?? '');
            $message = trim($input['message'] ?? '');

            if (empty($tagCode) || empty($phone) || empty($location)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Tag code, phone, and found location are required.']);
                break;
            }

            $res = $qrService->submitRecoveryNotice($tagCode, $name, $phone, $location, $message);
            echo json_encode($res);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unsupported action.']);
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'QR Tag service error: ' . $e->getMessage()]);
}
