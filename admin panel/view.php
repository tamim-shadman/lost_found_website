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
    header("Location: ../login.php");
    exit();
}

$itemService = new ItemService();
$res = $itemService->getItems(['limit' => 100, 'status' => 'All']);
$items = $res['items'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items — Admin Console</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/design-system.css">
</head>
<body class="p-4">
    <div class="container mx-auto" style="max-width: 1200px;">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="text-light fw-bold">Items Moderation Directory</h2>
            <a href="admin-dashboard.html" class="btn btn-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Dashboard</a>
        </div>

        <div class="glass-panel p-4">
            <div class="table-responsive">
                <table class="table text-light" style="width: 100%;">
                    <thead>
                        <tr class="text-muted border-bottom border-subtle">
                            <th>ID</th>
                            <th>Type</th>
                            <th>Item Name</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $row): ?>
                        <tr class="border-bottom border-subtle">
                            <td>#<?= (int)$row['id'] ?></td>
                            <td><span class="badge <?= $row['item_type'] === 'lost' ? 'badge-lost' : 'badge-found' ?>"><?= $row['item_type'] ?></span></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($row['district'] . ', ' . $row['division'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><span class="badge badge-verified"><?= htmlspecialchars($row['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td>
                                <a href="update.php?id=<?= (int)$row['id'] ?>" class="btn btn-secondary btn-sm p-1 px-2"><i class="bi bi-pencil"></i></a>
                                <a href="delete.php?id=<?= (int)$row['id'] ?>" class="btn btn-danger btn-sm p-1 px-2" onclick="return confirm('Confirm item removal?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>