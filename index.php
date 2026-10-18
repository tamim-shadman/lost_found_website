<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\ItemService;
use App\Services\MissingPersonService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();

$itemService = new ItemService();
$missingService = new MissingPersonService();

$stats = $itemService->getStatistics();
$recentItemsRes = $itemService->getItems(['limit' => 8]);
$recentItems = $recentItemsRes['items'];

$missingPersonsRes = $missingService->getMissingPersons(['status' => 'missing', 'limit' => 4]);
$missingPersons = $missingPersonsRes['persons'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>Lost & Found Bangladesh — Nationwide Recovery Platform</title>
    <meta name="description" content="Bangladesh's official community recovery network for lost belongings, found items, and missing persons. Fast, free, and secure.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
      .hero-section {
        padding-top: 130px;
        padding-bottom: 70px;
        position: relative;
      }
      .stat-counter-card {
        padding: 1.5rem;
        text-align: center;
        border-radius: var(--radius-lg);
        background: var(--bg-surface-glass);
        border: 1px solid var(--border-subtle);
      }
      .stat-counter-value {
        font-size: 2.25rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        line-height: 1;
        margin-bottom: 0.35rem;
      }
      .filter-pill-btn {
        background: var(--bg-surface);
        border: 1px solid var(--border-subtle);
        color: var(--text-secondary);
        padding: 0.45rem 1.15rem;
        border-radius: var(--radius-pill);
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all var(--transition-fast);
      }
      .filter-pill-btn:hover, .filter-pill-btn.active {
        background: var(--color-emerald-500);
        color: #ffffff;
        border-color: var(--color-emerald-400);
        box-shadow: 0 4px 12px var(--color-emerald-glow);
      }
      .missing-alert-card {
        background: linear-gradient(145deg, rgba(239, 68, 68, 0.08), rgba(14, 18, 26, 0.85));
        border: 1px solid rgba(239, 68, 68, 0.25);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: transform var(--transition-smooth);
      }
      .missing-alert-card:hover {
        transform: translateY(-4px);
        border-color: rgba(239, 68, 68, 0.45);
      }
    </style>
</head>
<body>
    <!-- Top Navigation -->
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <!-- Main Container -->
    <main>
      <!-- Hero Section -->
      <section class="hero-section text-center">
        <div class="container mx-auto px-3" style="max-width: 1040px;">
          <!-- Status Announcement Pill -->
          <div class="brand-badge mb-3 animate-fade-in">
            <span class="badge-dot"></span>
            <span>Over 1,240+ Lost Belongings Safely Reunited Across Bangladesh</span>
          </div>

          <!-- Headline -->
          <h1 class="display-4 fw-extrabold text-light mb-3 animate-slide-up" style="font-size: clamp(2.2rem, 5vw, 3.8rem); line-height: 1.15; letter-spacing: -0.03em;">
            Lost Moments, <span class="text-gradient">Found Stories.</span>
          </h1>
          <p class="lead text-secondary mx-auto mb-4 animate-slide-up" style="max-width: 720px; font-size: 1.15rem;">
            Bangladesh's intelligent lost and found community network. Report lost items, broadcast missing person alerts, or return found belongings to their rightful owners.
          </p>

          <!-- Action Buttons -->
          <div class="d-flex flex-wrap justify-content-center gap-3 mb-5 animate-slide-up">
            <a href="submit_lostitems.php?type=lost" class="btn btn-danger btn-pill px-4 py-3" style="font-size: 1rem;">
              <i class="bi bi-search me-1"></i> Report Lost Item
            </a>
            <a href="submit_lostitems.php?type=found" class="btn btn-primary btn-pill px-4 py-3" style="font-size: 1rem;">
              <i class="bi bi-box-seam me-1"></i> Report Found Item
            </a>
            <a href="missing_person.php" class="btn btn-secondary btn-pill px-4 py-3" style="font-size: 1rem;">
              <i class="bi bi-person-fill-exclamation me-1 text-rose"></i> Missing Persons
            </a>
            <a href="chatbot.php" class="btn btn-ghost btn-pill px-4 py-3 border border-subtle" style="font-size: 1rem;">
              <i class="bi bi-robot text-emerald me-1"></i> Ask AI Assistant
            </a>
          </div>

          <!-- Platform Live Stats Grid -->
          <div class="row g-3 justify-content-center text-start">
            <div class="col-6 col-md-3">
              <div class="stat-counter-card glass-panel">
                <div class="stat-counter-value text-emerald"><?= number_format($stats['total_items']) ?>+</div>
                <div class="text-muted" style="font-size: 0.85rem; font-weight: 600;">Active Reports</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="stat-counter-card glass-panel">
                <div class="stat-counter-value text-light"><?= number_format($stats['resolved_items']) ?>+</div>
                <div class="text-muted" style="font-size: 0.85rem; font-weight: 600;">Items Recovered</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="stat-counter-card glass-panel">
                <div class="stat-counter-value text-indigo"><?= number_format($stats['missing_persons']) ?></div>
                <div class="text-muted" style="font-size: 0.85rem; font-weight: 600;">Missing Persons Tracked</div>
              </div>
            </div>
            <div class="col-6 col-md-3">
              <div class="stat-counter-card glass-panel">
                <div class="stat-counter-value text-amber"><?= $stats['recovery_rate_pct'] ?>%</div>
                <div class="text-muted" style="font-size: 0.85rem; font-weight: 600;">Community Success Rate</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      <!-- Urgent Missing Persons Section -->
      <?php if (!empty($missingPersons)): ?>
      <section class="py-5 border-top border-subtle" style="background: rgba(239, 68, 68, 0.02);">
        <div class="container mx-auto px-3" style="max-width: 1320px;">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-4">
            <div>
              <div class="d-flex align-items-center gap-2">
                <span class="badge badge-missing">Emergency Alert</span>
                <span class="text-muted" style="font-size: 0.85rem;">Bangladesh Police Coordinated</span>
              </div>
              <h2 class="text-light fw-bold mt-1" style="font-size: 1.75rem;">Urgent: Missing Persons</h2>
            </div>
            <a href="missing_person.php" class="btn btn-secondary btn-sm btn-pill">
              <span>View All Missing Persons</span>
              <i class="bi bi-arrow-right"></i>
            </a>
          </div>

          <div class="row g-4">
            <?php foreach ($missingPersons as $person): ?>
            <div class="col-12 col-sm-6 col-lg-3">
              <div class="missing-alert-card h-100 d-flex flex-column">
                <div style="height: 200px; overflow: hidden; position: relative;">
                  <img src="<?= htmlspecialchars($person['photo'] ?? 'images/default_person.jpg', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($person['full_name'], ENT_QUOTES, 'UTF-8') ?>" style="width: 100%; height: 100%; object-fit: cover;">
                  <span class="badge badge-missing position-absolute top-0 start-0 m-2">Age: <?= (int)$person['age'] ?> yrs</span>
                  <?php if (!empty($person['gd_number'])): ?>
                    <span class="badge bg-dark text-muted position-absolute bottom-0 start-0 m-2" style="font-size: 0.65rem; border: 1px solid var(--border-subtle);"><?= htmlspecialchars($person['gd_number'], ENT_QUOTES, 'UTF-8') ?></span>
                  <?php endif; ?>
                </div>
                <div class="p-3 d-flex flex-column flex-grow-1">
                  <h3 class="fw-bold text-light mb-1" style="font-size: 1.1rem;"><?= htmlspecialchars($person['full_name'], ENT_QUOTES, 'UTF-8') ?></h3>
                  <div class="text-rose mb-2" style="font-size: 0.8rem;">
                    <i class="bi bi-geo-alt-fill me-1"></i>Last seen: <?= htmlspecialchars($person['last_seen_district'] . ', ' . $person['last_seen_division'], ENT_QUOTES, 'UTF-8') ?>
                  </div>
                  <p class="text-secondary small mb-3 flex-grow-1" style="line-height: 1.4;">
                    <?= htmlspecialchars(mb_strimwidth($person['clothing_description'], 0, 90, '...'), ENT_QUOTES, 'UTF-8') ?>
                  </p>
                  <a href="tel:<?= htmlspecialchars($person['contact_phone'], ENT_QUOTES, 'UTF-8') ?>" class="btn btn-danger btn-sm w-100 py-2">
                    <i class="bi bi-telephone-fill me-1"></i> Contact Family: <?= htmlspecialchars($person['contact_phone'], ENT_QUOTES, 'UTF-8') ?>
                  </a>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>
      <?php endif; ?>

      <!-- Recent Lost & Found Feed Section -->
      <section class="py-5 border-top border-subtle">
        <div class="container mx-auto px-3" style="max-width: 1320px;">
          <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4">
            <div>
              <span class="brand-badge mb-2">Live Community Feed</span>
              <h2 class="text-light fw-bold" style="font-size: 1.75rem;">Recent Lost & Found Reports</h2>
            </div>

            <!-- Division & Type Filter Pills -->
            <div class="d-flex flex-wrap gap-2">
              <button class="filter-pill-btn active" onclick="filterItems('all', this)">All Reports</button>
              <button class="filter-pill-btn" onclick="filterItems('lost', this)">Lost Only</button>
              <button class="filter-pill-btn" onclick="filterItems('found', this)">Found Only</button>
              <a href="recentposts.php" class="btn btn-ghost btn-sm text-emerald ms-md-2">
                <span>Explore Full Catalog</span>
                <i class="bi bi-arrow-right"></i>
              </a>
            </div>
          </div>

          <!-- Cards Grid -->
          <div class="items-grid" id="itemsFeedGrid">
            <?php foreach ($recentItems as $item): ?>
              <?php
                $badgeClass = $item['item_type'] === 'lost' ? 'badge-lost' : 'badge-found';
                $typeLabel = ucfirst($item['item_type']);
              ?>
              <div class="item-card feed-card" data-type="<?= htmlspecialchars($item['item_type'], ENT_QUOTES, 'UTF-8') ?>">
                <div class="item-card-media">
                  <img src="<?= htmlspecialchars($item['picture'] ?: 'images/default_item.jpg', ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8') ?>" loading="lazy">
                  <span class="badge <?= $badgeClass ?> item-card-badge"><?= $typeLabel ?></span>
                  <?php if ((float)$item['reward_amount'] > 0): ?>
                    <span class="item-card-reward"><i class="bi bi-gift-fill me-1"></i>৳<?= number_format((float)$item['reward_amount']) ?> Reward</span>
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
                        <i class="bi bi-telephone text-emerald"></i> Contact
                      </a>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </section>

      <!-- How It Works Section -->
      <section class="py-5 border-top border-subtle" style="background: rgba(14, 18, 26, 0.4);">
        <div class="container mx-auto px-3 text-center" style="max-width: 1040px;">
          <span class="brand-badge mb-2">Safe & Transparent Workflow</span>
          <h2 class="text-light fw-bold mb-4" style="font-size: 2rem;">How Lost & Found BD Works</h2>

          <div class="row g-4 text-start">
            <div class="col-md-4">
              <div class="glass-panel p-4 h-100">
                <div class="badge bg-emerald-subtle text-emerald fs-5 p-3 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                  1
                </div>
                <h4 class="fw-bold text-light mb-2">Submit Verified Report</h4>
                <p class="text-secondary small mb-0">Post detailed information including location, specific surroundings, pictures, and any available serial numbers or markers.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="glass-panel p-4 h-100">
                <div class="badge bg-indigo-subtle text-indigo fs-5 p-3 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                  2
                </div>
                <h4 class="fw-bold text-light mb-2">Automated Smart Matching</h4>
                <p class="text-secondary small mb-0">Our platform indexes location keywords, categories, and dates to surface matching reports immediately across all 8 divisions.</p>
              </div>
            </div>
            <div class="col-md-4">
              <div class="glass-panel p-4 h-100">
                <div class="badge bg-amber-subtle text-amber fs-5 p-3 rounded-circle mb-3 d-inline-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                  3
                </div>
                <h4 class="fw-bold text-light mb-2">Secure Proof Handover</h4>
                <p class="text-secondary small mb-0">Claimants provide ownership proof (receipts, matching serial numbers, unreleased details) before any physical handover takes place.</p>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Item Details & Claim Modal -->
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

    <!-- Claim Submission Modal -->
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
            <textarea class="form-control" name="proof_description" id="claimProofDesc" rows="3" placeholder="Provide unique identifying marks, invoice/receipt details, serial numbers, wallpaper on phone..." required></textarea>
          </div>
          <div class="mb-3">
            <label class="form-label">Contact Phone Number:</label>
            <input type="tel" class="form-control" name="contact_phone" id="claimPhone" placeholder="017XXXXXXXX" required>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2" id="claimSubmitBtn">
            <span>Submit for Moderator Verification</span>
          </button>
        </form>
      </div>
    </div>

    <!-- Site Footer -->
    <?php include __DIR__ . '/components/footer.php'; ?>

    <!-- Interactive Scripts -->
    <script>
      function filterItems(type, btn) {
        document.querySelectorAll('.filter-pill-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const cards = document.querySelectorAll('.feed-card');
        cards.forEach(card => {
          if (type === 'all' || card.getAttribute('data-type') === type) {
            card.style.display = 'flex';
          } else {
            card.style.display = 'none';
          }
        });
      }

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
          btn.innerHTML = 'Submit for Moderator Verification';
          if (data.success) {
            alert('Claim submitted! Our moderation desk will review your details.');
            closeClaimModal(null);
          } else {
            alert(data.error || 'Claim submission failed.');
          }
        })
        .catch(() => {
          btn.disabled = false;
          btn.innerHTML = 'Submit for Moderator Verification';
          alert('Network error submitting claim.');
        });
      }
    </script>
</body>
</html>