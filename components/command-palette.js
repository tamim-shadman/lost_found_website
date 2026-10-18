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

  // 4. Civic Web Audio Feedback Engine (Zero external dependencies)
  let soundEnabled = localStorage.getItem('lf_sound') !== 'muted';
  let audioCtx = null;

  function initAudio() {
    if (!audioCtx && (window.AudioContext || window.webkitAudioContext)) {
      audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    }
    if (audioCtx && audioCtx.state === 'suspended') {
      audioCtx.resume();
    }
  }

  window.playCivicSound = function (type = 'click') {
    if (!soundEnabled) return;
    try {
      initAudio();
      if (!audioCtx) return;
      const now = audioCtx.currentTime;
      const osc = audioCtx.createOscillator();
      const gain = audioCtx.createGain();
      osc.connect(gain);
      gain.connect(audioCtx.destination);

      if (type === 'success') {
        osc.type = 'sine';
        osc.frequency.setValueAtTime(523.25, now); // C5
        osc.frequency.exponentialRampToValueAtTime(659.25, now + 0.12); // E5
        gain.gain.setValueAtTime(0.08, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
        osc.start(now);
        osc.stop(now + 0.35);
      } else if (type === 'alert') {
        osc.type = 'triangle';
        osc.frequency.setValueAtTime(660, now);
        osc.frequency.exponentialRampToValueAtTime(440, now + 0.2);
        gain.gain.setValueAtTime(0.12, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.35);
        osc.start(now);
        osc.stop(now + 0.35);
      } else if (type === 'chime') {
        osc.type = 'sine';
        osc.frequency.setValueAtTime(440, now);
        osc.frequency.exponentialRampToValueAtTime(880, now + 0.15);
        gain.gain.setValueAtTime(0.06, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.25);
        osc.start(now);
        osc.stop(now + 0.25);
      } else { // soft click
        osc.type = 'sine';
        osc.frequency.setValueAtTime(800, now);
        gain.gain.setValueAtTime(0.04, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);
        osc.start(now);
        osc.stop(now + 0.05);
      }
    } catch (e) {}
  };

  window.toggleSound = function () {
    soundEnabled = !soundEnabled;
    localStorage.setItem('lf_sound', soundEnabled ? 'enabled' : 'muted');
    const soundIcons = document.querySelectorAll('.sound-toggle-icon');
    soundIcons.forEach(i => {
      i.className = soundEnabled ? 'bi bi-volume-up-fill sound-toggle-icon' : 'bi bi-volume-mute-fill sound-toggle-icon';
    });
    if (soundEnabled) {
      window.playCivicSound('chime');
      window.showToast('Audio feedback activated', 'info', 'Civic Sound');
    } else {
      window.showToast('Audio feedback muted', 'info', 'Civic Sound');
    }
  };

  // 5. Global Toast Notification System
  window.showToast = function (message, type = 'success', title = '') {
    let container = document.getElementById('lfToastContainer');
    if (!container) {
      container = document.createElement('div');
      container.id = 'lfToastContainer';
      container.className = 'lf-toast-container';
      document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = 'lf-toast';

    let iconClass = 'bi-check-circle-fill text-emerald';
    let defaultTitle = 'Success';
    if (type === 'error' || type === 'danger') {
      iconClass = 'bi-exclamation-triangle-fill text-rose';
      defaultTitle = 'Attention';
      window.playCivicSound('alert');
    } else if (type === 'info') {
      iconClass = 'bi-info-circle-fill text-indigo';
      defaultTitle = 'Notice';
      window.playCivicSound('chime');
    } else {
      window.playCivicSound('success');
    }

    toast.innerHTML = `
      <i class="bi ${iconClass} fs-5 mt-1"></i>
      <div class="flex-grow-1">
        <div class="fw-bold" style="font-size: 0.9rem;">${title || defaultTitle}</div>
        <div class="small text-secondary" style="font-size: 0.825rem; line-height: 1.4;">${message}</div>
      </div>
      <button type="button" class="btn-close btn-close-white small ms-2" aria-label="Close" style="font-size: 0.7rem;"></button>
      <div class="lf-toast-progress"></div>
    `;

    const closeBtn = toast.querySelector('.btn-close');
    closeBtn.onclick = () => {
      toast.classList.add('toast-exit');
      setTimeout(() => toast.remove(), 280);
    };

    container.appendChild(toast);

    setTimeout(() => {
      if (toast.parentNode) {
        toast.classList.add('toast-exit');
        setTimeout(() => toast.remove(), 280);
      }
    }, 4000);
  };

  // 6. Floating SOS Emergency Hub & Scroll-to-Top Injector
  function injectSosFloatingHub() {
    if (document.getElementById('sosFloatingHub')) return;

    const hub = document.createElement('div');
    hub.id = 'sosFloatingHub';
    hub.className = 'sos-floating-hub';
    hub.innerHTML = `
      <div class="sos-quick-panel" id="sosQuickPanel">
        <div class="text-uppercase fw-bold px-2 py-1 small text-rose" style="font-size: 0.68rem; letter-spacing: 0.05em;">
          <i class="bi bi-broadcast me-1"></i> National Emergency Hub
        </div>
        <a href="tel:999" class="btn btn-danger btn-sm text-start py-2 px-3 d-flex align-items-center gap-2">
          <i class="bi bi-shield-fill-exclamation fs-5"></i>
          <div>
            <div class="fw-bold lh-1">Police / Fire / Ambulance</div>
            <small style="font-size: 0.72rem; opacity: 0.85;">Dial Hotline 999 (24/7)</small>
          </div>
        </a>
        <a href="tel:1098" class="btn btn-secondary btn-sm text-start py-2 px-3 d-flex align-items-center gap-2">
          <i class="bi bi-people-fill text-indigo fs-5"></i>
          <div>
            <div class="fw-bold lh-1">Child Protection</div>
            <small style="font-size: 0.72rem; opacity: 0.85;">Helpline 1098</small>
          </div>
        </a>
        <a href="chatbot.html" class="btn btn-secondary btn-sm text-start py-2 px-3 d-flex align-items-center gap-2">
          <i class="bi bi-robot text-emerald fs-5"></i>
          <div>
            <div class="fw-bold lh-1">AI Emergency Assistant</div>
            <small style="font-size: 0.72rem; opacity: 0.85;">Police GD procedures & guidance</small>
          </div>
        </a>
        <a href="submit_lostitems.html?type=lost" class="btn btn-secondary btn-sm text-start py-2 px-3 d-flex align-items-center gap-2">
          <i class="bi bi-plus-circle text-amber fs-5"></i>
          <div>
            <div class="fw-bold lh-1">Report Lost / Found</div>
            <small style="font-size: 0.72rem; opacity: 0.85;">Instant incident broadcast</small>
          </div>
        </a>
      </div>

      <div class="d-flex align-items-center gap-2">
        <button type="button" class="scroll-top-btn" id="scrollTopBtn" onclick="scrollToTop()" title="Back to top" aria-label="Scroll to top">
          <i class="bi bi-arrow-up"></i>
        </button>
        <button type="button" class="sos-trigger-btn" id="sosTriggerBtn" onclick="toggleSosHub()" title="Emergency Hotlines & SOS" aria-label="Emergency Hotlines">
          <div class="sos-pulse-ring"></div>
          <i class="bi bi-shield-fill-exclamation" id="sosIcon"></i>
        </button>
      </div>
    `;
    document.body.appendChild(hub);

    // Scroll progress handler
    window.addEventListener('scroll', () => {
      const scrollBtn = document.getElementById('scrollTopBtn');
      if (scrollBtn) {
        if (window.scrollY > 250) {
          scrollBtn.classList.add('visible');
        } else {
          scrollBtn.classList.remove('visible');
        }
      }
    });
  }

  window.toggleSosHub = function () {
    const panel = document.getElementById('sosQuickPanel');
    const icon = document.getElementById('sosIcon');
    if (!panel) return;
    const isActive = panel.classList.toggle('active');
    if (isActive) {
      window.playCivicSound('alert');
      icon.className = 'bi bi-x-lg';
    } else {
      window.playCivicSound('click');
      icon.className = 'bi bi-shield-fill-exclamation';
    }
  };

  window.scrollToTop = function () {
    window.scrollTo({ top: 0, behavior: 'smooth' });
    window.playCivicSound('click');
  };

  // Close SOS panel when clicking outside
  document.addEventListener('click', (e) => {
    const hub = document.getElementById('sosFloatingHub');
    if (hub && !hub.contains(e.target)) {
      const panel = document.getElementById('sosQuickPanel');
      const icon = document.getElementById('sosIcon');
      if (panel && panel.classList.contains('active')) {
        panel.classList.remove('active');
        if (icon) icon.className = 'bi bi-shield-fill-exclamation';
      }
    }
  });

  // Inject Hub when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', injectSosFloatingHub);
  } else {
    injectSosFloatingHub();
  }
})();

