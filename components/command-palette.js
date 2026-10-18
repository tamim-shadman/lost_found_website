/**
 * LOST & FOUND BANGLADESH — GLOBAL OMNIBAR & COMMAND PALETTE (Cmd+K)
 * Version 3.0.0 "Phoenix Shell"
 * Provides instant fuzzy search, route jumping, theme toggling, and keyboard navigation.
 */

(function () {
  'use strict';

  // 1. Theme Engine Initialization
  const savedTheme = localStorage.getItem('lf_theme') || 'dark';
  document.documentElement.setAttribute('data-theme', savedTheme);

  window.toggleTheme = function () {
    const current = document.documentElement.getAttribute('data-theme') || 'dark';
    const next = current === 'dark' ? 'light' : 'dark';
    document.documentElement.setAttribute('data-theme', next);
    localStorage.setItem('lf_theme', next);
    const themeIcon = document.getElementById('themeToggleIcon');
    if (themeIcon) {
      themeIcon.className = next === 'dark' ? 'bi bi-moon-stars-fill' : 'bi bi-sun-fill';
    }
  };

  // 2. Command Palette DOM Template
  const cmdPaletteHtml = `
    <div class="cmd-palette-backdrop" id="globalCmdPalette" onclick="closeCmdPalette(event)">
      <div class="cmd-palette-modal glass-panel" onclick="event.stopPropagation()">
        <div class="d-flex align-items-center px-3 border-bottom border-subtle">
          <i class="bi bi-search text-muted fs-5 me-2"></i>
          <input type="text" class="cmd-search-input" id="cmdSearchInput" placeholder="Type a command, search items, or jump to route..." autocomplete="off">
          <kbd class="px-2 py-1 text-xs rounded border border-subtle text-muted" style="font-size: 0.75rem;">ESC</kbd>
        </div>
        <div class="cmd-results-list" id="cmdResultsList">
          <!-- Dynamic results -->
        </div>
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top border-subtle bg-surface-subtle" style="font-size: 0.75rem; color: var(--text-muted);">
          <div>
            <span><kbd>↑</kbd> <kbd>↓</kbd> Navigate</span>
            <span class="ms-2"><kbd>↵</kbd> Select</span>
          </div>
          <div>
            <span>Press <kbd>Ctrl+K</kbd> anywhere</span>
          </div>
        </div>
      </div>
    </div>
  `;

  // Inject Command Palette if not present
  if (!document.getElementById('globalCmdPalette')) {
    const div = document.createElement('div');
    div.innerHTML = cmdPaletteHtml;
    document.body.appendChild(div.firstElementChild);
  }

  // 3. Command Palette Actions & Items
  const staticActions = [
    { title: 'Browse Lost & Found Alerts', icon: 'bi-grid-fill', url: 'recentposts.html', category: 'Navigation', shortcut: 'G R' },
    { title: 'Publish Lost or Found Report', icon: 'bi-plus-circle-fill', url: 'submit_lostitems.html', category: 'Actions', shortcut: 'C' },
    { title: 'National Missing Persons Directory', icon: 'bi-person-badge-fill', url: 'missing_person.html', category: 'Emergency', shortcut: 'G M' },
    { title: 'AI Emergency Guidance Assistant', icon: 'bi-robot', url: 'chatbot.html', category: 'Intelligence', shortcut: 'G A' },
    { title: 'Support Network & Donate', icon: 'bi-heart-fill', url: 'payment.html', category: 'Actions', shortcut: 'G D' },
    { title: 'Citizen Account & QR Shield', icon: 'bi-person-circle', url: 'User_profile.html', category: 'Account', shortcut: 'G P' },
    { title: 'Toggle Dark / Light Theme', icon: 'bi-circle-half', action: () => window.toggleTheme(), category: 'Preferences', shortcut: 'T' },
    { title: 'Emergency Police Direct Hotline (999)', icon: 'bi-telephone-fill', url: 'tel:999', category: 'Emergency', shortcut: '999' }
  ];

  let selectedIndex = 0;
  let currentResults = [];

  window.openCmdPalette = function () {
    const palette = document.getElementById('globalCmdPalette');
    const input = document.getElementById('cmdSearchInput');
    if (!palette) return;
    palette.classList.add('active');
    input.value = '';
    renderResults(staticActions);
    setTimeout(() => input.focus(), 50);
  };

  window.closeCmdPalette = function (e) {
    if (e && e.target && e.target !== document.getElementById('globalCmdPalette')) return;
    const palette = document.getElementById('globalCmdPalette');
    if (palette) palette.classList.remove('active');
  };

  function renderResults(actions) {
    currentResults = actions;
    selectedIndex = 0;
    const list = document.getElementById('cmdResultsList');
    if (!list) return;

    if (actions.length === 0) {
      list.innerHTML = `<div class="p-4 text-center text-muted">No commands or alerts matching query.</div>`;
      return;
    }

    let html = '';
    let lastCat = null;
    actions.forEach((item, idx) => {
      if (item.category && item.category !== lastCat) {
        html += `<div class="px-3 pt-2 pb-1 text-xs text-muted text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em;">${item.category}</div>`;
        lastCat = item.category;
      }
      html += `
        <div class="cmd-item ${idx === 0 ? 'selected' : ''}" onclick="executeCmdIndex(${idx})">
          <div class="d-flex align-items-center gap-2">
            <i class="bi ${item.icon || 'bi-arrow-right'} text-emerald"></i>
            <span class="text-light fw-medium">${item.title}</span>
          </div>
          ${item.shortcut ? `<kbd class="px-2 py-0.5 rounded border border-subtle text-muted" style="font-size: 0.72rem;">${item.shortcut}</kbd>` : ''}
        </div>
      `;
    });
    list.innerHTML = html;
  }

  window.executeCmdIndex = function (index) {
    const item = currentResults[index];
    if (!item) return;
    window.closeCmdPalette();
    if (item.action) {
      item.action();
    } else if (item.url) {
      window.location.href = item.url;
    }
  };

  // Keyboard navigation inside Palette & global shortcuts
  document.addEventListener('keydown', (e) => {
    // Cmd+K / Ctrl+K
    if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
      e.preventDefault();
      const palette = document.getElementById('globalCmdPalette');
      if (palette && palette.classList.contains('active')) {
        window.closeCmdPalette();
      } else {
        window.openCmdPalette();
      }
      return;
    }

    // ESC to close
    if (e.key === 'Escape') {
      window.closeCmdPalette();
    }

    const palette = document.getElementById('globalCmdPalette');
    if (!palette || !palette.classList.contains('active')) return;

    const items = palette.querySelectorAll('.cmd-item');
    if (e.key === 'ArrowDown') {
      e.preventDefault();
      if (selectedIndex < items.length - 1) {
        selectedIndex++;
        updateSelection(items);
      }
    } else if (e.key === 'ArrowUp') {
      e.preventDefault();
      if (selectedIndex > 0) {
        selectedIndex--;
        updateSelection(items);
      }
    } else if (e.key === 'Enter') {
      e.preventDefault();
      window.executeCmdIndex(selectedIndex);
    }
  });

  function updateSelection(items) {
    items.forEach((el, idx) => {
      if (idx === selectedIndex) {
        el.classList.add('selected');
        el.scrollIntoView({ block: 'nearest' });
      } else {
        el.classList.remove('selected');
      }
    });
  }

  // Live Omnibar Input Search
  document.addEventListener('input', (e) => {
    if (e.target && e.target.id === 'cmdSearchInput') {
      const q = e.target.value.toLowerCase().trim();
      if (!q) {
        renderResults(staticActions);
        return;
      }
      const filtered = staticActions.filter(a => a.title.toLowerCase().includes(q) || (a.category && a.category.toLowerCase().includes(q)));
      
      // Also query live items if query length >= 2
      if (q.length >= 2) {
        fetch(`api/items.php?action=list&query=${encodeURIComponent(q)}&limit=5`)
          .then(r => r.json())
          .then(res => {
            let combined = [...filtered];
            if (res.success && res.data && res.data.items) {
              const itemActions = res.data.items.map(i => ({
                title: `${i.item_type === 'lost' ? '🔴 Lost' : '🟢 Found'}: ${i.title} (${i.district})`,
                icon: 'bi-box-seam',
                url: `recentposts.html?highlight=${i.id}`,
                category: 'Matching Belongings'
              }));
              combined = combined.concat(itemActions);
            }
            renderResults(combined);
          })
          .catch(() => renderResults(filtered));
      } else {
        renderResults(filtered);
      }
    }
  });
})();
