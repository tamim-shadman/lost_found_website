<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\ChatbotService;
use App\Utils\Security;

Security::initSession();
$chatbot = new ChatbotService();

$txt = trim($_POST['txt'] ?? $_POST['message'] ?? '');
$sessionId = session_id();

$response = $chatbot->getAnswer($txt, $sessionId);
echo $response['reply'];