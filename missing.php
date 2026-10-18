<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\MissingPersonService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();
$missingService = new MissingPersonService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $file = $_FILES['photo'] ?? $_FILES['image'] ?? null;
    $userId = $currentUser ? (int)$currentUser['id'] : null;

    $res = $missingService->reportMissingPerson($_POST, $file, $userId);

    if ($res['success']) {
        echo "<script>
            alert('Missing person alert broadcasted successfully.');
            window.location.href = 'missing_person.php';
        </script>";
        exit();
    } else {
        $errMsg = addslashes($res['error']);
        echo "<script>
            alert('Error: {$errMsg}');
            window.history.back();
        </script>";
        exit();
    }
} else {
    header("Location: missing_person.php");
    exit();
}
