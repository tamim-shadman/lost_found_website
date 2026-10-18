<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\ItemService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();

if (!$currentUser || !in_array($currentUser['role'], ['admin', 'moderator'])) {
    http_response_code(403);
    die("Unauthorized.");
}

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $itemService = new ItemService();
    $itemService->deleteItem($id, (int)$currentUser['id'], true);
}

header("Location: view.php");
exit();
