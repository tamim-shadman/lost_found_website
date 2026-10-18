<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\ItemService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();
$itemService = new ItemService();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'item_type'         => 'found',
        'title'             => $_POST['item_name'] ?? $_POST['title'] ?? '',
        'category'          => $_POST['category'] ?? 'Other',
        'specific_location' => $_POST['address'] ?? '',
        'surrounding_details'=> $_POST['address2'] ?? '',
        'district'          => $_POST['city'] ?? 'Dhaka',
        'division'          => $_POST['state'] ?? 'Dhaka',
        'date_lost_or_found'=> $_POST['date'] ?? date('Y-m-d'),
        'contact_phone'     => $_POST['phone'] ?? ($currentUser['phone'] ?? ''),
        'contact_email'     => $_POST['email'] ?? ($currentUser['email'] ?? ''),
        'reward_amount'     => 0.0,
    ];

    $file = $_FILES['image'] ?? $_FILES['picture'] ?? null;
    $userId = $currentUser ? (int)$currentUser['id'] : null;

    $res = $itemService->createItem($data, $file, $userId);

    if ($res['success']) {
        echo "<script>
            alert('Found item report submitted successfully! Thank you for your honesty.');
            window.location.href = 'recentposts.php';
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
    header("Location: submit_lostitems.php");
    exit();
}