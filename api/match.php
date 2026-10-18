<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Services\SmartMatchService;
use App\Utils\Security;

Security::initSession();
$matcher = new SmartMatchService();

$rawBody = file_get_contents('php://input');
$jsonBody = json_decode($rawBody, true) ?? [];
$input = array_merge($_GET, $_POST, $jsonBody);
$action = $input['action'] ?? 'system_pairs';

try {
    switch ($action) {
        case 'item_matches':
            $itemId = (int)($input['item_id'] ?? 0);
            if ($itemId <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Valid item_id is required.']);
                break;
            }
            $matches = $matcher->getMatchesForItem($itemId);
            echo json_encode([
                'success' => true,
                'item_id' => $itemId,
                'count'   => count($matches),
                'matches' => $matches
            ]);
            break;

        case 'system_pairs':
        default:
            $pairs = $matcher->getSystemMatchPairs();
            echo json_encode([
                'success' => true,
                'count'   => count($pairs),
                'pairs'   => $pairs
            ]);
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Smart matching error: ' . $e->getMessage()]);
}
