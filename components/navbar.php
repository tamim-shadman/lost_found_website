<?php
declare(strict_types=1);

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();
?>
<header class="glass-navbar">
  <div class="container d-flex justify-content-between align-items-center py-2 px-3 mx-auto" style="max-width: 1320px;">
    <!-- Logo & Brand -->
    <a href="index.php" class="navbar-brand d-flex align-items-center gap-2 text-decoration-none">
      <img src="images/logo.png" alt="Lost and Found Bangladesh Logo" width="38" height="38" class="rounded-circle border border-subtle">
      <div>
        <div class="brand-title fw-bold text-light lh-1" style="font-size: 1.15rem; letter-spacing: -0.02em;">Lost & Found <span class="text-emerald" style="color: var(--color-emerald-400);">BD</span></div>
        <small class="text-muted d-none d-sm-block" style="font-size: 0.725rem;">Lost Moments, Found Stories</small>
      </div>
    </a>

    <!-- Desktop Navigation Links -->
    <nav class="d-none d-lg-flex align-items-center gap-4">
      <a href="recentposts.php" class="nav-link text-secondary fw-medium hover-light">Browse Alerts</a>
      <a href="submit_lostitems.php" class="nav-link text-secondary fw-medium hover-light">Report Item</a>
      <a href="missing_person.php" class="nav-link text-secondary fw-medium hover-light">Missing Persons</a>
      <a href="chatbot.php" class="nav-link text-secondary fw-medium hover-light">
        <i class="bi bi-chat-dots me-1 text-emerald"></i>AI Assistant
      </a>
      <a href="payment.php" class="nav-link text-secondary fw-medium hover-light">Support Platform</a>
    </nav>

    <!-- Quick Search Trigger & User Controls -->
    <div class="d-flex align-items-center gap-2">
      <!-- Command Palette Search Trigger Button -->
      <button type="button" class="btn btn-ghost btn-sm d-flex align-items-center gap-2 text-muted px-3 py-1 rounded-pill border border-subtle glass-subtle" onclick="openCommandPalette()" title="Press Ctrl+K to search">
        <i class="bi bi-search"></i>
        <span class="d-none d-md-inline" style="font-size: 0.85rem;">Quick Search...</span>
        <kbd class="d-none d-md-inline-block px-1 py-0 rounded text-muted bg-dark border border-subtle" style="font-size: 0.7rem;">Ctrl K</kbd>
      </button>

      <?php if ($currentUser): ?>
        <!-- User Profile Dropdown -->
        <div class="dropdown position-relative" id="userMenuDropdown">
          <button class="btn btn-ghost btn-sm p-1 rounded-circle d-flex align-items-center dropdown-toggle" type="button" onclick="toggleUserDropdown()" aria-expanded="false" id="userAvatarBtn">
            <img src="<?= htmlspecialchars($currentUser['avatar'] ?? 'images/user_avatar.png', ENT_QUOTES, 'UTF-8') ?>" alt="Avatar" width="34" height="34" class="rounded-circle border border-emerald">
          </button>
          <div class="dropdown-menu-custom glass-panel position-absolute end-0 mt-2 py-2 shadow-lg" id="userDropdownContent" style="width: 230px; display: none; z-index: 1050;">
            <div class="px-3 py-2 border-bottom border-subtle">
              <div class="fw-bold text-light text-truncate"><?= htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name'], ENT_QUOTES, 'UTF-8') ?></div>
              <small class="text-muted text-truncate d-block"><?= htmlspecialchars($currentUser['email'], ENT_QUOTES, 'UTF-8') ?></small>
              <span class="badge bg-emerald-subtle text-emerald mt-1"><?= ucfirst($currentUser['role']) ?></span>
            </div>
            <a href="User_profile.php" class="dropdown-item-custom px-3 py-2 d-flex align-items-center gap-2 text-secondary hover-light text-decoration-none">
              <i class="bi bi-person-circle"></i> My Profile & Claims
            </a>
            <?php if (in_array($currentUser['role'], ['admin', 'moderator'])): ?>
              <a href="admin panel/admin-dashboard.html" class="dropdown-item-custom px-3 py-2 d-flex align-items-center gap-2 text-secondary hover-light text-decoration-none">
                <i class="bi bi-shield-lock text-emerald"></i> Admin Dashboard
              </a>
            <?php endif; ?>
            <div class="border-top border-subtle my-1"></div>
            <a href="logout.php" class="dropdown-item-custom px-3 py-2 d-flex align-items-center gap-2 text-rose hover-light text-decoration-none" style="color: var(--color-rose-400);">
              <i class="bi bi-box-arrow-right"></i> Sign Out
            </a>
          </div>
        </div>
      <?php else: ?>
        <a href="login.php" class="btn btn-ghost btn-sm">Sign In</a>
        <a href="signup.php" class="btn btn-primary btn-sm btn-pill">Get Started</a>
      <?php endif; ?>

      <!-- Mobile Hamburger Toggle -->
      <button class="btn btn-ghost btn-sm d-lg-none text-light" type="button" onclick="toggleMobileMenu()" aria-label="Toggle Navigation">
        <i class="bi bi-list fs-4"></i>
      </button>
    </div>
  </div>

  <!-- Mobile Dropdown Navigation -->
  <div class="mobile-nav-panel glass-panel border-top border-subtle p-3 d-lg-none" id="mobileNavMenu" style="display: none;">
    <div class="d-flex flex-column gap-2">
      <a href="recentposts.php" class="btn btn-ghost justify-content-start text-light"><i class="bi bi-compass me-2"></i> Browse Alerts</a>
      <a href="submit_lostitems.php" class="btn btn-ghost justify-content-start text-light"><i class="bi bi-plus-circle me-2"></i> Report Lost/Found</a>
      <a href="missing_person.php" class="btn btn-ghost justify-content-start text-light"><i class="bi bi-people me-2"></i> Missing Persons</a>
      <a href="chatbot.php" class="btn btn-ghost justify-content-start text-light"><i class="bi bi-chat-dots me-2 text-emerald"></i> AI Assistant</a>
      <a href="payment.php" class="btn btn-ghost justify-content-start text-light"><i class="bi bi-credit-card me-2"></i> Support Us</a>
      <?php if ($currentUser): ?>
        <a href="User_profile.php" class="btn btn-ghost justify-content-start text-light"><i class="bi bi-person me-2"></i> My Profile</a>
        <a href="logout.php" class="btn btn-danger justify-content-start mt-2"><i class="bi bi-box-arrow-right me-2"></i> Sign Out</a>
      <?php else: ?>
        <div class="d-flex gap-2 mt-2">
          <a href="login.php" class="btn btn-secondary flex-grow-1">Sign In</a>
          <a href="signup.php" class="btn btn-primary flex-grow-1">Sign Up</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</header>

<!-- Global Command Palette Modal -->
<div class="cmd-palette-backdrop" id="globalCmdPalette" onclick="closeCommandPalette(event)">
  <div class="cmd-palette-modal glass-panel" onclick="event.stopPropagation()">
    <div class="d-flex align-items-center px-3 border-bottom border-subtle">
      <i class="bi bi-search text-muted fs-5 me-2"></i>
      <input type="text" class="cmd-search-input" id="cmdSearchInput" placeholder="Search lost items, found valuables, missing persons..." autocomplete="off" oninput="handleCmdSearch(this.value)">
      <button type="button" class="btn btn-ghost btn-sm text-muted" onclick="closeCommandPalette(null)">ESC</button>
    </div>
    <div class="cmd-results-list" id="cmdResultsContainer">
      <div class="text-muted p-3 text-center" style="font-size: 0.9rem;">
        Type keywords (e.g. <em>laptop</em>, <em>wallet</em>, <em>Dhaka</em>, <em>passport</em>)
      </div>
    </div>
  </div>
</div>

<script>
function toggleUserDropdown() {
  const menu = document.getElementById('userDropdownContent');
  menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function toggleMobileMenu() {
  const menu = document.getElementById('mobileNavMenu');
  menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

function openCommandPalette() {
  const palette = document.getElementById('globalCmdPalette');
  palette.classList.add('active');
  const input = document.getElementById('cmdSearchInput');
  setTimeout(() => input.focus(), 50);
}

function closeCommandPalette(e) {
  if (e && e.target !== document.getElementById('globalCmdPalette')) return;
  document.getElementById('globalCmdPalette').classList.remove('active');
}

// Global Keyboard Shortcut: Ctrl+K / Cmd+K
document.addEventListener('keydown', function(e) {
  if ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k') {
    e.preventDefault();
    openCommandPalette();
  }
  if (e.key === 'Escape') {
    document.getElementById('globalCmdPalette').classList.remove('active');
  }
});

// Close dropdown on outside click
document.addEventListener('click', function(e) {
  const userBtn = document.getElementById('userAvatarBtn');
  const userMenu = document.getElementById('userDropdownContent');
  if (userMenu && userBtn && !userBtn.contains(e.target) && !userMenu.contains(e.target)) {
    userMenu.style.display = 'none';
  }
});

let searchDebounceTimeout = null;
function handleCmdSearch(query) {
  clearTimeout(searchDebounceTimeout);
  const container = document.getElementById('cmdResultsContainer');
  if (!query || query.trim().length === 0) {
    container.innerHTML = '<div class="text-muted p-3 text-center" style="font-size: 0.9rem;">Type keywords to search...</div>';
    return;
  }

  searchDebounceTimeout = setTimeout(() => {
    fetch('api/items.php?action=list&limit=6&query=' + encodeURIComponent(query))
      .then(r => r.json())
      .then(data => {
        if (data.success && data.data.items && data.data.items.length > 0) {
          let html = '';
          data.data.items.forEach(item => {
            const badgeClass = item.item_type === 'lost' ? 'badge-lost' : 'badge-found';
            html += `
              <div class="cmd-item" onclick="window.location.href='recentposts.php?search=${encodeURIComponent(item.title)}'">
                <div class="d-flex align-items-center gap-3">
                  <span class="badge ${badgeClass}">${item.item_type}</span>
                  <div>
                    <div class="fw-semibold text-light">${item.title}</div>
                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>${item.district}, ${item.division}</small>
                  </div>
                </div>
                <i class="bi bi-arrow-right-short text-muted fs-4"></i>
              </div>
            `;
          });
          container.innerHTML = html;
        } else {
          container.innerHTML = '<div class="text-muted p-3 text-center">No reports matching your search.</div>';
        }
      })
      .catch(() => {
        container.innerHTML = '<div class="text-danger p-3 text-center">Error fetching search results.</div>';
      });
  }, 200);
}
</script>
