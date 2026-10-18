<!-- Modern Flagship Site Footer -->
<footer class="site-footer mt-5 pt-5 pb-4">
  <div class="container mx-auto px-3" style="max-width: 1320px;">
    <div class="row g-4 mb-4">
      <!-- Column 1: Brand & National Trust Signature -->
      <div class="col-12 col-md-6 col-lg-4">
        <div class="d-flex align-items-center gap-2 mb-3">
          <img src="images/logo.png" alt="Lost and Found Bangladesh Logo" width="36" height="36" class="rounded-circle border border-emerald">
          <div>
            <span class="footer-brand-title">Lost &amp; Found <span style="color: var(--color-emerald-400);">Bangladesh</span></span>
            <div class="text-muted small" style="font-size: 0.75rem;">National Recovery &amp; Public Safety Network</div>
          </div>
        </div>
        <p class="text-secondary mb-3" style="font-size: 0.9rem; line-height: 1.6;">
          Bangladesh's nationwide community recovery network. Dedicated to reuniting lost belongings, securing citizen identity credentials, and locating missing persons in partnership with citizens and Bangladesh Police.
        </p>
        <div class="d-flex flex-wrap align-items-center gap-2">
          <span class="badge-trust-tag"><span class="badge-dot"></span> 24/7 Moderation Desk</span>
          <span class="badge-trust-tag"><i class="bi bi-shield-check text-emerald"></i> GD Verified</span>
          <span class="badge-trust-tag"><i class="bi bi-broadcast text-rose"></i> 8 Divisions Radar</span>
        </div>
      </div>

      <!-- Column 2: Platform Navigation & Tools -->
      <div class="col-6 col-md-3 col-lg-2">
        <h6 class="footer-section-title"><i class="bi bi-compass text-emerald me-1"></i> Navigation</h6>
        <ul class="footer-nav-list">
          <li><a href="recentposts.html?type=lost" class="footer-nav-link"><i class="bi bi-chevron-right text-emerald" style="font-size: 0.7rem;"></i> Lost Belongings</a></li>
          <li><a href="recentposts.html?type=found" class="footer-nav-link"><i class="bi bi-chevron-right text-emerald" style="font-size: 0.7rem;"></i> Found Valuables</a></li>
          <li><a href="missing_person.html" class="footer-nav-link"><i class="bi bi-chevron-right text-emerald" style="font-size: 0.7rem;"></i> Missing Persons</a></li>
          <li><a href="submit_lostitems.html" class="footer-nav-link"><i class="bi bi-chevron-right text-emerald" style="font-size: 0.7rem;"></i> File New Report</a></li>
          <li><a href="User_profile.html" class="footer-nav-link"><i class="bi bi-chevron-right text-emerald" style="font-size: 0.7rem;"></i> QR Asset Shield</a></li>
          <li><a href="chatbot.html" class="footer-nav-link"><i class="bi bi-chevron-right text-emerald" style="font-size: 0.7rem;"></i> AI Assistant</a></li>
        </ul>
      </div>

      <!-- Column 3: Emergency Dispatch & Hotlines -->
      <div class="col-6 col-md-3 col-lg-3">
        <h6 class="footer-section-title"><i class="bi bi-telephone-outbound text-rose me-1"></i> Hotlines</h6>
        <div class="d-flex flex-column gap-2">
          <a href="tel:999" class="footer-hotline-chip">
            <span><i class="bi bi-shield-fill-exclamation text-rose me-1"></i> National Police</span>
            <span class="badge bg-rose-subtle text-rose fw-bold">999</span>
          </a>
          <a href="tel:100" class="footer-hotline-chip">
            <span><i class="bi bi-telephone-inbound-fill text-emerald me-1"></i> Police Help Desk</span>
            <span class="badge bg-emerald-subtle text-emerald fw-bold">100</span>
          </a>
          <a href="tel:1098" class="footer-hotline-chip">
            <span><i class="bi bi-people-fill text-indigo me-1"></i> Child Protection</span>
            <span class="badge bg-indigo-subtle text-indigo fw-bold">1098</span>
          </a>
          <a href="tel:16263" class="footer-hotline-chip">
            <span><i class="bi bi-hospital-fill text-amber me-1"></i> Health Service</span>
            <span class="badge bg-amber-subtle text-amber fw-bold">16263</span>
          </a>
        </div>
      </div>

      <!-- Column 4: Trust & Infrastructure Support -->
      <div class="col-12 col-md-6 col-lg-3">
        <h6 class="footer-section-title"><i class="bi bi-heart-pulse text-emerald me-1"></i> Public Trust</h6>
        <p class="text-secondary small mb-3">
          100% free non-profit civic infrastructure powered by community goodwill. Your reports are encrypted and strictly verified.
        </p>
        <a href="payment.html" class="btn btn-glow-emerald w-100 py-2 d-flex align-items-center justify-content-center gap-2 mb-3">
          <i class="bi bi-heart-fill text-rose"></i> Support Platform
        </a>
        <div class="d-flex flex-wrap gap-2 text-muted" style="font-size: 0.75rem;">
          <span><i class="bi bi-shield-lock-fill text-emerald me-1"></i> 256-Bit SSL</span>
          <span>•</span>
          <span><i class="bi bi-patch-check-fill text-emerald me-1"></i> Community Backed</span>
        </div>
      </div>
    </div>

    <!-- Sub-footer bar -->
    <div class="sub-footer-bar">
      <div class="text-secondary small">
        &copy; <?= date('Y') ?> <strong>Lost &amp; Found Bangladesh</strong>. Nationwide Public Safety &amp; Community Recovery Platform.
      </div>
      <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small">
        <a href="#" class="footer-nav-link" onclick="alert('Privacy Policy: All personal contact info is safeguarded. NIDs & serial numbers are never exposed to unauthenticated scrapers.'); return false;">Privacy Policy</a>
        <span>•</span>
        <a href="#" class="footer-nav-link" onclick="alert('Terms of Service: Fraudulent or deceptive reports will be permanently banned and reported to law enforcement.'); return false;">Terms of Service</a>
        <span>•</span>
        <a href="chatbot.html" class="footer-nav-link">Support FAQ</a>
        <span>•</span>
        <a href="admin panel/admin-dashboard.html" class="footer-nav-link text-emerald"><i class="bi bi-lock me-1"></i> Staff Portal</a>
      </div>
    </div>
  </div>
</footer>

