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

$type = $_GET['type'] ?? '';
$category = $_GET['category'] ?? 'All';
$division = $_GET['division'] ?? 'All';
$query = $_GET['search'] ?? $_GET['query'] ?? '';

$filters = [
    'type'     => $type !== 'all' && !empty($type) ? $type : null,
    'category' => $category,
    'division' => $division,
    'query'    => $query,
    'limit'    => 60,
];

$res = $itemService->getItems($filters);
$items = $res['items'];
$total = $res['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>Browse Lost & Found Alerts — Lost & Found BD</title>
    <meta name="description" content="Explore verified lost and found item alerts across all 8 divisions in Bangladesh. Search by category, city, or keyword.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
      .catalog-header {
        padding-top: 130px;
        padding-bottom: 25px;
      }
      .filter-bar {
        background: var(--bg-surface-glass);
        border: 1px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        padding: 1.25rem;
      }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="container mx-auto px-3" style="max-width: 1320px;">
        <div class="catalog-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <span class="brand-badge mb-2">Verified Public Registry</span>
                    <h1 class="text-light fw-bold mb-1" style="font-size: 2.2rem;">Lost & Found Alerts Catalog</h1>
                    <p class="text-secondary mb-0">Browse active community reports across Bangladesh (<?= number_format($total) ?> reports found)</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="submit_lostitems.php?type=lost" class="btn btn-danger btn-sm btn-pill px-3 py-2">
                        <i class="bi bi-search me-1"></i> Report Lost
                    </a>
                    <a href="submit_lostitems.php?type=found" class="btn btn-primary btn-sm btn-pill px-3 py-2">
                        <i class="bi bi-box-seam me-1"></i> Report Found
                    </a>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar mb-4">
            <form action="recentposts.php" method="GET" class="row g-3 align-items-center">
                <div class="col-lg-4 col-md-12">
                    <div class="input-icon-wrapper">
                        <i class="bi bi-search icon"></i>
                        <input type="text" class="form-control" name="search" value="<?= htmlspecialchars($query, ENT_QUOTES, 'UTF-8') ?>" placeholder="Search keyword, brand, location...">
                    </div>
                </div>

                <div class="col-sm-4 col-lg-2">
                    <select class="form-select" name="type">
                        <option value="all" <?= empty($type) || $type === 'all' ? 'selected' : '' ?>>All Report Types</option>
                        <option value="lost" <?= $type === 'lost' ? 'selected' : '' ?>>Lost Items</option>
                        <option value="found" <?= $type === 'found' ? 'selected' : '' ?>>Found Valuables</option>
                    </select>
                </div>

                <div class="col-sm-4 col-lg-3">
                    <select class="form-select" name="category">
                        <option value="All" <?= $category === 'All' ? 'selected' : '' ?>>All Categories</option>
                        <option value="Electronics" <?= $category === 'Electronics' ? 'selected' : '' ?>>Electronics & Gadgets</option>
                        <option value="Wallet/Cards" <?= $category === 'Wallet/Cards' ? 'selected' : '' ?>>Wallet, Cards & Money</option>
                        <option value="Documents" <?= $category === 'Documents' ? 'selected' : '' ?>>ID & Documents</option>
                        <option value="Bags" <?= $category === 'Bags' ? 'selected' : '' ?>>Bags & Luggage</option>
                        <option value="Jewelry" <?= $category === 'Jewelry' ? 'selected' : '' ?>>Jewelry & Watches</option>
                        <option value="Pets" <?= $category === 'Pets' ? 'selected' : '' ?>>Pets & Animals</option>
                        <option value="Other" <?= $category === 'Other' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>

                <div class="col-sm-4 col-lg-2">
                    <select class="form-select" name="division">
                        <option value="All" <?= $division === 'All' ? 'selected' : '' ?>>All Divisions</option>
                        <option value="Dhaka" <?= $division === 'Dhaka' ? 'selected' : '' ?>>Dhaka</option>
                        <option value="Chattogram" <?= $division === 'Chattogram' ? 'selected' : '' ?>>Chattogram</option>
                        <option value="Sylhet" <?= $division === 'Sylhet' ? 'selected' : '' ?>>Sylhet</option>
                        <option value="Rajshahi" <?= $division === 'Rajshahi' ? 'selected' : '' ?>>Rajshahi</option>
                        <option value="Khulna" <?= $division === 'Khulna' ? 'selected' : '' ?>>Khulna</option>
                        <option value="Barishal" <?= $division === 'Barishal' ? 'selected' : '' ?>>Barishal</option>
                        <option value="Rangpur" <?= $division === 'Rangpur' ? 'selected' : '' ?>>Rangpur</option>
                        <option value="Mymensingh" <?= $division === 'Mymensingh' ? 'selected' : '' ?>>Mymensingh</option>
                    </select>
                </div>

                <div class="col-lg-1 col-md-12 text-end">
                    <button type="submit" class="btn btn-primary w-100 py-2"><i class="bi bi-filter"></i> Filter</button>
                </div>
            </form>
        </div>

        <!-- Items Grid -->
        <?php if (!empty($items)): ?>
            <div class="items-grid mb-5">
                <?php foreach ($items as $item): ?>
                    <?php
                        $badgeClass = $item['item_type'] === 'lost' ? 'badge-lost' : 'badge-found';
                        $typeLabel = ucfirst($item['item_type']);
                    ?>
                    <div class="item-card">
                        <div class="item-card-media">
                            <img src="<?= htmlspecialchars($item['picture'] ?: 'images/default_item.jpg', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                            <span class="badge <?= $badgeClass ?> item-card-badge"><?= $typeLabel ?></span>
                            <?php if ((float)$item['reward_amount'] > 0): ?>
                                <span class="item-card-reward"><i class="bi bi-gift-fill me-1"></i>৳<?= number_format((float)$item['reward_amount']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="item-card-body">
                            <div class="text-muted small text-uppercase mb-1" style="font-size: 0.725rem; font-weight: 700;"><?= htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8') ?></div>
                            <h3 class="item-card-title"><?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <p class="item-card-desc"><?= htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8') ?></p>
                            
                            <div class="item-card-meta">
                                <span><i class="bi bi-geo-alt me-1 text-emerald"></i><?= htmlspecialchars($item['district'] . ', ' . $item['division'], ENT_QUOTES, 'UTF-8') ?></span>
                                <span><i class="bi bi-calendar3 me-1"></i><?= htmlspecialchars($item['date_lost_or_found'], ENT_QUOTES, 'UTF-8') ?></span>
                            </div>

                            <div class="mt-3 pt-2 border-top border-subtle d-flex gap-2">
                                <button type="button" class="btn btn-secondary btn-sm flex-grow-1" onclick="openItemModal(<?= (int)$item['id'] ?>)">
                                    <i class="bi bi-eye"></i> Details
                                </button>
                                <?php if ($item['item_type'] === 'found'): ?>
                                    <button type="button" class="btn btn-primary btn-sm flex-grow-1" onclick="claimItem(<?= (int)$item['id'] ?>, '<?= addslashes(htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8')) ?>')">
                                        <i class="bi bi-hand-index-thumb"></i> Claim
                                    </button>
                                <?php else: ?>
                                    <a href="tel:<?= htmlspecialchars($item['contact_phone'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-ghost btn-sm border border-subtle flex-grow-1">
                                        <i class="bi bi-telephone text-emerald"></i> Call
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="glass-panel text-center p-5 mb-5">
                <i class="bi bi-search text-muted mb-3 d-block" style="font-size: 3rem;"></i>
                <h3 class="text-light fw-bold">No Matching Reports</h3>
                <p class="text-muted">We could not find any reports matching your filters. Try clearing some search options.</p>
                <a href="recentposts.php" class="btn btn-secondary mt-2">Clear All Filters</a>
            </div>
        <?php endif; ?>
    </main>

    <!-- Details Modal -->
    <div class="cmd-palette-backdrop" id="itemDetailModal" onclick="closeItemModal(event)">
      <div class="cmd-palette-modal glass-panel p-4" onclick="event.stopPropagation()" style="max-width: 600px;">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-subtle">
          <h4 class="fw-bold text-light mb-0" id="modalItemTitle">Item Details</h4>
          <button type="button" class="btn btn-ghost btn-sm text-muted" onclick="closeItemModal(null)">✕</button>
        </div>
        <div id="modalItemBody" class="py-2 text-start">
          <div class="text-center py-4"><span class="badge-dot"></span> Loading report...</div>
        </div>
      </div>
    </div>

    <!-- Claim Modal -->
    <div class="cmd-palette-backdrop" id="claimModal" onclick="closeClaimModal(event)">
      <div class="cmd-palette-modal glass-panel p-4" onclick="event.stopPropagation()" style="max-width: 520px;">
        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom border-subtle">
          <h4 class="fw-bold text-light mb-0">Submit Ownership Claim</h4>
          <button type="button" class="btn btn-ghost btn-sm text-muted" onclick="closeClaimModal(null)">✕</button>
        </div>
        <form id="claimSubmitForm" onsubmit="handleClaimSubmit(event)">
          <input type="hidden" id="claimItemId" name="item_id">
          <div class="mb-3">
            <label class="form-label">Claiming Item:</label>
            <input type="text" class="form-control" id="claimItemNameDisplay" readonly>
          </div>
          <div class="mb-3">
            <label class="form-label">Describe Ownership Proof:</label>
            <textarea class="form-control" name="proof_description" id="claimProofDesc" rows="3" placeholder="Provide serial numbers, receipts, matching stickers..." required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact Phone Number:</label>
            <input type="tel" class="form-control" name="contact_phone" id="claimPhone" placeholder="017XXXXXXXX" required>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2" id="claimSubmitBtn">
            <span>Submit for Verification</span>
          </button>
        </form>
      </div>
    </div>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
      function openItemModal(id) {
        const modal = document.getElementById('itemDetailModal');
        modal.classList.add('active');
        const body = document.getElementById('modalItemBody');
        const title = document.getElementById('modalItemTitle');
        body.innerHTML = '<div class="text-center py-4"><span class="badge-dot"></span> Loading details...</div>';

        fetch('api/items.php?action=get&id=' + id)
          .then(r => r.json())
          .then(data => {
            if (data.success && data.item) {
              const item = data.item;
              title.textContent = item.title;
              body.innerHTML = `
                <div class="mb-3" style="max-height: 240px; overflow: hidden; border-radius: var(--radius-md);">
                  <img src="${item.picture || 'images/default_item.jpg'}" alt="${item.title}" style="width: 100%; height: 220px; object-fit: cover;">
                </div>
                <div class="d-flex gap-2 mb-3">
                  <span class="badge ${item.item_type === 'lost' ? 'badge-lost' : 'badge-found'}">${item.item_type}</span>
                  <span class="badge badge-verified">${item.category}</span>
                  ${item.reward_amount > 0 ? `<span class="badge bg-amber-subtle text-amber">৳${item.reward_amount} Reward</span>` : ''}
                </div>
                <p class="text-light mb-3">${item.description}</p>
                <div class="p-3 rounded glass-subtle mb-3 small">
                  <div><strong>Location:</strong> ${item.specific_location}, ${item.district}, ${item.division}</div>
                  ${item.surrounding_details ? `<div><strong>Surroundings:</strong> ${item.surrounding_details}</div>` : ''}
                  <div><strong>Date:</strong> ${item.date_lost_or_found}</div>
                </div>
                <div class="d-flex gap-2">
                  <a href="tel:${item.contact_phone}" class="btn btn-secondary flex-grow-1"><i class="bi bi-telephone"></i> Call: ${item.contact_phone}</a>
                  ${item.item_type === 'found' ? `<button type="button" class="btn btn-primary flex-grow-1" onclick="closeItemModal(null); claimItem(${item.id}, '${item.title}');"><i class="bi bi-hand-index"></i> Claim Valuables</button>` : ''}
                </div>
              `;
            } else {
              body.innerHTML = '<div class="text-danger p-3">Failed to load item report.</div>';
            }
          });
      }

      function closeItemModal(e) {
        if (e && e.target !== document.getElementById('itemDetailModal')) return;
        document.getElementById('itemDetailModal').classList.remove('active');
      }

      function claimItem(id, title) {
        document.getElementById('claimItemId').value = id;
        document.getElementById('claimItemNameDisplay').value = title;
        document.getElementById('claimModal').classList.add('active');
      }

      function closeClaimModal(e) {
        if (e && e.target !== document.getElementById('claimModal')) return;
        document.getElementById('claimModal').classList.remove('active');
      }

      function handleClaimSubmit(e) {
        e.preventDefault();
        const itemId = document.getElementById('claimItemId').value;
        const proof = document.getElementById('claimProofDesc').value;
        const phone = document.getElementById('claimPhone').value;
        const btn = document.getElementById('claimSubmitBtn');

        btn.disabled = true;
        btn.innerHTML = 'Submitting claim...';

        fetch('api/items.php', {
          method: 'POST',
          headers: {'Content-Type': 'application/json'},
          body: JSON.stringify({
            action: 'claim',
            item_id: itemId,
            proof_description: proof,
            contact_phone: phone
          })
        })
        .then(r => r.json())
        .then(data => {
          btn.disabled = false;
          btn.innerHTML = 'Submit for Verification';
          if (data.success) {
            alert('Claim submitted! Our moderation desk will review your details.');
            closeClaimModal(null);
          } else {
            alert(data.error || 'Claim submission failed.');
          }
        })
        .catch(() => {
          btn.disabled = false;
          btn.innerHTML = 'Submit for Verification';
          alert('Network error submitting claim.');
        });
      }
    </script>
</body>
</html>