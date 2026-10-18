<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Services\ChatbotService;
use App\Utils\Security;

Security::initSession();
$chatbot = new ChatbotService();

$rawBody = file_get_contents('php://input');
$jsonBody = json_decode($rawBody, true) ?? [];
$input = array_merge($_GET, $_POST, $jsonBody);

$action = $input['action'] ?? 'ask';
$sessionId = $input['session_id'] ?? session_id();

try {
    switch ($action) {
        case 'ask':
            $message = trim((string)($input['message'] ?? $input['txt'] ?? $input['query'] ?? ''));
            if ($message === '') {
                http_response_code(400);
                echo json_encode(['success' => false, 'error' => 'Query message cannot be empty.']);
                break;
            }
            $response = $chatbot->getAnswer($message, $sessionId);
            echo json_encode(['success' => true, 'data' => $response]);
            break;

        case 'suggestions':
            $suggestions = $chatbot->getSuggestions();
            echo json_encode(['success' => true, 'suggestions' => $suggestions]);
            break;

        case 'history':
            $history = $chatbot->getHistory($sessionId);
            echo json_encode(['success' => true, 'history' => $history]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unsupported action.']);
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Chatbot error: ' . $e->getMessage()]);
}
