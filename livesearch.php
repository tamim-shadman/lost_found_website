<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>Instant Live Search — Lost & Found Bangladesh</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
      .search-page-container {
        padding-top: 140px;
        min-height: 80vh;
      }
      .search-box-wrapper {
        position: relative;
        width: 100%;
        max-width: 680px;
        margin: 0 auto;
      }
      .live-search-input {
        width: 100%;
        padding: 1.15rem 1.5rem 1.15rem 3.25rem;
        font-size: 1.25rem;
        background: var(--bg-surface-elevated);
        border: 2px solid var(--border-medium);
        border-radius: var(--radius-xl);
        color: var(--text-primary);
        box-shadow: 0 12px 35px rgba(0, 0, 0, 0.4);
        outline: none;
        transition: all var(--transition-smooth);
      }
      .live-search-input:focus {
        border-color: var(--color-emerald-500);
        box-shadow: 0 0 0 4px var(--color-emerald-glow);
      }
      .search-input-icon {
        position: absolute;
        left: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        font-size: 1.35rem;
        color: var(--text-muted);
        pointer-events: none;
      }
      .live-results-dropdown {
        position: absolute;
        top: calc(100% + 12px);
        left: 0;
        right: 0;
        z-index: 100;
        background: var(--bg-surface-elevated);
        border: 1px solid var(--border-medium);
        border-radius: var(--radius-lg);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.6);
        max-height: 420px;
        overflow-y: auto;
      }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="container mx-auto px-3 search-page-container">
        <div class="text-center mb-5">
            <span class="brand-badge mb-3">Live Search Engine</span>
            <h1 class="text-light fw-bold mb-2" style="font-size: 2.5rem;">Find What You're Looking For</h1>
            <p class="text-secondary">Type below to instantly match lost valuables, found belongings, or missing person reports.</p>
        </div>

        <div class="search-box-wrapper mb-5">
            <i class="bi bi-search search-input-icon"></i>
            <input type="text" class="live-search-input" id="mainSearchInput" placeholder="Type item name, division, or landmark..." autocomplete="off">
            <div class="live-results-dropdown" id="mainResultsDropdown" style="display: none;"></div>
        </div>

        <div class="text-center">
            <div class="text-muted small text-uppercase fw-bold mb-3" style="letter-spacing: 0.05em;">Popular Searches in Bangladesh:</div>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <button class="filter-pill-btn" onclick="applyPresetSearch('MacBook')">MacBook</button>
                <button class="filter-pill-btn" onclick="applyPresetSearch('Wallet')">Wallet</button>
                <button class="filter-pill-btn" onclick="applyPresetSearch('Passport')">Passport</button>
                <button class="filter-pill-btn" onclick="applyPresetSearch('iPhone')">iPhone</button>
                <button class="filter-pill-btn" onclick="applyPresetSearch('Airport')">Dhaka Airport</button>
                <button class="filter-pill-btn" onclick="applyPresetSearch('Chattogram')">Chattogram</button>
                <button class="filter-pill-btn" onclick="applyPresetSearch('Keys')">Keys</button>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
      const searchInput = document.getElementById('mainSearchInput');
      const resultsBox = document.getElementById('mainResultsDropdown');
      let debounce = null;

      searchInput.addEventListener('input', function() {
        clearTimeout(debounce);
        const query = this.value.trim();

        if (query.length === 0) {
          resultsBox.style.display = 'none';
          resultsBox.innerHTML = '';
          return;
        }

        debounce = setTimeout(() => {
          fetch('action.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: 'query=' + encodeURIComponent(query)
          })
          .then(r => r.text())
          .then(html => {
            resultsBox.style.display = 'block';
            resultsBox.innerHTML = html;
          })
          .catch(() => {
            resultsBox.style.display = 'none';
          });
        }, 150);
      });

      function applyPresetSearch(text) {
        searchInput.value = text;
        searchInput.dispatchEvent(new Event('input'));
        searchInput.focus();
      }

      function selectSearchResult(id, title) {
        window.location.href = 'recentposts.php?search=' + encodeURIComponent(title);
      }

      document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsBox.contains(e.target)) {
          resultsBox.style.display = 'none';
        }
      });
    </script>
</body>
</html>