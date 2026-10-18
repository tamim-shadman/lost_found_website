<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Config\Database;
use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->requireAuth(); // Requires login, redirects if not authenticated

$db = Database::getConnection();
$userId = (int)$currentUser['id'];

// Fetch user's reported items
$itemsStmt = $db->prepare("SELECT * FROM items WHERE user_id = ? ORDER BY created_at DESC");
$itemsStmt->execute([$userId]);
$myItems = $itemsStmt->fetchAll();

// Fetch user's submitted claims
$claimsStmt = $db->prepare("
    SELECT c.*, i.title as item_title, i.category as item_category, i.picture as item_picture, i.district, i.division
    FROM claims c
    JOIN items i ON c.item_id = i.id
    WHERE c.user_id = ?
    ORDER BY c.created_at DESC
");
$claimsStmt->execute([$userId]);
$myClaims = $claimsStmt->fetchAll();

// Handle Account Update POST
$updateMsg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $fn = trim($_POST['first_name'] ?? '');
    $ln = trim($_POST['last_name'] ?? '');
    $ph = trim($_POST['phone'] ?? '');

    if (!empty($fn) && !empty($ln) && !empty($ph)) {
        $up = $db->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE id = ?");
        $up->execute([$fn, $ln, $ph, $userId]);
        $updateMsg = 'Profile updated successfully!';
        $currentUser = $auth->getCurrentUser();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>My Profile & Claims — Lost & Found Bangladesh</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
      .profile-header {
        padding-top: 130px;
        padding-bottom: 30px;
      }
      .profile-avatar-big {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        border: 2px solid var(--color-emerald-500);
        object-fit: cover;
      }
      .profile-nav-pill {
        background: transparent;
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
        padding: 0.65rem 1.25rem;
        border-radius: var(--radius-md);
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-fast);
        display: flex;
        align-items: center;
        gap: 0.5rem;
      }
      .profile-nav-pill:hover, .profile-nav-pill.active {
        background: var(--bg-surface-elevated);
        color: #ffffff;
        border-color: var(--color-emerald-500);
      }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="container mx-auto px-3 profile-header" style="max-width: 1100px;">
        <!-- User Banner Card -->
        <div class="glass-panel p-4 mb-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
            <div class="d-flex align-items-center gap-3">
                <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'images/user_avatar.png', ENT_QUOTES, 'UTF-8') ?>" alt="Avatar" class="profile-avatar-big">
                <div>
                    <h2 class="text-light fw-bold mb-1" style="font-size: 1.6rem;">
                        <?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>
                    <div class="text-muted small">
                        <i class="bi bi-envelope me-1"></i><?= htmlspecialchars($currentUser['email'], ENT_QUOTES, 'UTF-8') ?>
                        <span class="mx-2">•</span>
                        <i class="bi bi-telephone me-1"></i><?= htmlspecialchars($currentUser['phone'], ENT_QUOTES, 'UTF-8') ?>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="submit_lostitems.php?type=lost" class="btn btn-danger btn-sm"><i class="bi bi-plus-circle me-1"></i> Post Lost Ad</a>
                <a href="submit_lostitems.php?type=found" class="btn btn-primary btn-sm"><i class="bi bi-box-seam me-1"></i> Report Found Ad</a>
            </div>
        </div>

        <?php if (!empty($updateMsg)): ?>
            <div class="alert alert-success bg-emerald-subtle text-emerald p-3 rounded mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i><?= htmlspecialchars($updateMsg, ENT_QUOTES, 'UTF-8') ?>
            </div>
        <?php endif; ?>

        <!-- Tab Navigation -->
        <div class="d-flex gap-2 mb-4 overflow-x-auto pb-1">
            <button class="profile-nav-pill active" id="pillItems" onclick="switchProfileTab('items')">
                <i class="bi bi-collection"></i> My Reported Items (<?= count($myItems) ?>)
            </button>
            <button class="profile-nav-pill" id="pillClaims" onclick="switchProfileTab('claims')">
                <i class="bi bi-hand-index-thumb"></i> Claim Status Tracking (<?= count($myClaims) ?>)
            </button>
            <button class="profile-nav-pill" id="pillSettings" onclick="switchProfileTab('settings')">
                <i class="bi bi-gear"></i> Account Settings
            </button>
        </div>

        <!-- Tab 1: My Reported Items -->
        <div id="tabContentItems">
            <?php if (!empty($myItems)): ?>
                <div class="items-grid">
                    <?php foreach ($myItems as $item): ?>
                        <div class="item-card">
                            <div class="item-card-media">
                                <img src="<?= htmlspecialchars($item['picture'] ?: 'images/default_item.jpg', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>">
                                <span class="badge <?= $item['item_type'] === 'lost' ? 'badge-lost' : 'badge-found' ?> item-card-badge"><?= ucfirst($item['item_type']) ?></span>
                                <span class="badge badge-verified position-absolute bottom-0 start-0 m-2"><?= ucfirst($item['status']) ?></span>
                            </div>
                            <div class="item-card-body">
                                <h3 class="item-card-title"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <p class="item-card-desc"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></p>
                                <div class="item-card-meta">
                                    <span><i class="bi bi-geo-alt me-1"></i><?= htmlspecialchars($item['district'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span><?= htmlspecialchars($item['date_lost_or_found'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <div class="mt-3 pt-2 border-top border-subtle d-flex gap-2">
                                    <button type="button" class="btn btn-secondary btn-sm flex-grow-1" onclick="openItemModal(<?= (int)$item['id'] ?>)">View</button>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteMyItem(<?= (int)$item['id'] ?>)"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="glass-panel text-center p-5">
                    <i class="bi bi-folder-x text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                    <h4 class="text-light fw-bold">No Items Reported Yet</h4>
                    <p class="text-secondary small mb-3">You haven't posted any lost or found items under this account.</p>
                    <a href="submit_lostitems.php" class="btn btn-primary btn-sm">Report an Item Now</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab 2: Claims Tracking -->
        <div id="tabContentClaims" style="display: none;">
            <?php if (!empty($myClaims)): ?>
                <div class="d-flex flex-column gap-3">
                    <?php foreach ($myClaims as $c): ?>
                        <div class="glass-panel p-4 d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-amber-subtle text-amber"><?= strtoupper($c['status']) ?></span>
                                    <span class="text-muted small">Submitted on <?= htmlspecialchars($c['created_at'], ENT_QUOTES, 'UTF-8') ?></span>
                                </div>
                                <h4 class="text-light fw-bold mb-1"><?= htmlspecialchars($c['item_title'], ENT_QUOTES, 'UTF-8') ?></h4>
                                <div class="text-secondary small mb-2"><strong>Proof Provided:</strong> <?= htmlspecialchars($c['proof_description'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php if (!empty($c['admin_notes'])): ?>
                                    <div class="p-2 rounded glass-subtle text-emerald small"><i class="bi bi-info-circle me-1"></i>Moderator note: <?= htmlspecialchars($c['admin_notes'], ENT_QUOTES, 'UTF-8') ?></div>
                                <?php endif; ?>
                            </div>
                            <span class="badge badge-verified p-2"><i class="bi bi-shield-check me-1"></i> Review Desk</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="glass-panel text-center p-5">
                    <i class="bi bi-hand-index-thumb text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                    <h4 class="text-light fw-bold">No Active Claims</h4>
                    <p class="text-secondary small mb-3">When you claim found property from our catalog, track its verification status here.</p>
                    <a href="recentposts.php?type=found" class="btn btn-secondary btn-sm">Browse Found Items</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Tab 3: Account Settings -->
        <div id="tabContentSettings" style="display: none;">
            <div class="glass-panel p-4 p-md-5" style="max-width: 620px;">
                <h4 class="text-light fw-bold mb-4">Edit Personal Information</h4>
                <form action="User_profile.php" method="POST">
                    <input type="hidden" name="action" value="update_profile">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" class="form-control" name="first_name" value="<?= htmlspecialchars($currentUser['first_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" class="form-control" name="last_name" value="<?= htmlspecialchars($currentUser['last_name'], ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Address (Immutable)</label>
                        <input type="email" class="form-control" value="<?= htmlspecialchars($currentUser['email'], ENT_QUOTES, 'UTF-8') ?>" disabled>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Contact Phone</label>
                        <input type="tel" class="form-control" name="phone" value="<?= htmlspecialchars($currentUser['phone'], ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary px-4 py-2">Save Profile Changes</button>
                </form>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
      function switchProfileTab(tab) {
        document.getElementById('tabContentItems').style.display = tab === 'items' ? 'block' : 'none';
        document.getElementById('tabContentClaims').style.display = tab === 'claims' ? 'block' : 'none';
        document.getElementById('tabContentSettings').style.display = tab === 'settings' ? 'block' : 'none';

        document.getElementById('pillItems').className = 'profile-nav-pill ' + (tab === 'items' ? 'active' : '');
        document.getElementById('pillClaims').className = 'profile-nav-pill ' + (tab === 'claims' ? 'active' : '');
        document.getElementById('pillSettings').className = 'profile-nav-pill ' + (tab === 'settings' ? 'active' : '');
      }

      function deleteMyItem(id) {
        if (!confirm('Are you sure you want to remove this report?')) return;
        fetch('api/items.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({ action: 'delete', id: id })
        })
        .then(r => r.json())
        .then(data => {
          if (data.success) {
            alert('Item removed.');
            window.location.reload();
          } else {
            alert(data.error || 'Failed to remove item.');
          }
        });
      }
    </script>
</body>
</html>
