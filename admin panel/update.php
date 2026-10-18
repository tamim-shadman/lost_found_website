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
$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$item = $itemService->getItemById($id);

if (!$item) {
    header("Location: view.php");
    exit();
}

$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $res = $itemService->updateItem($id, $_POST, (int)$currentUser['id'], true);
    if ($res['success']) {
        header("Location: view.php");
        exit();
    } else {
        $msg = $res['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Item Report — Admin Console</title>
    <link rel="stylesheet" href="../css/design-system.css">
</head>
<body class="p-4">
    <div class="container mx-auto" style="max-width: 680px;">
        <div class="glass-panel p-4 p-md-5">
            <h3 class="text-light fw-bold mb-4">Edit Item Report #<?= $id ?></h3>
            
            <?php if (!empty($msg)): ?>
                <div class="alert alert-danger mb-3"><?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form action="update.php" method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="mb-3">
                    <label class="form-label">Title</label>
                    <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label">Category</label>
                        <input type="text" class="form-control" name="category" value="<?= htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="pending" <?= $item['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="verified" <?= $item['status'] === 'verified' ? 'selected' : '' ?>>Verified</option>
                            <option value="claimed" <?= $item['status'] === 'claimed' ? 'selected' : '' ?>>Claimed</option>
                            <option value="resolved" <?= $item['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Specific Location</label>
                    <input type="text" class="form-control" name="specific_location" value="<?= htmlspecialchars($item['specific_location'], ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="d-flex justify-content-between mt-4">
                    <a href="view.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" name="update" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
