<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>Support Lost & Found Bangladesh — Secure Donation Gateway</title>
    <meta name="description" content="Support Lost & Found Bangladesh. Fund community server infrastructure and nationwide missing persons search broadcasts.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <style>
      .payment-header {
        padding-top: 130px;
        padding-bottom: 25px;
      }
      .tier-selector-card {
        padding: 1.25rem;
        background: var(--bg-surface);
        border: 2px solid var(--border-subtle);
        border-radius: var(--radius-lg);
        text-align: center;
        cursor: pointer;
        transition: all var(--transition-fast);
      }
      .tier-selector-card:hover, .tier-selector-card.active {
        background: rgba(16, 185, 129, 0.1);
        border-color: var(--color-emerald-500);
        box-shadow: 0 4px 18px var(--color-emerald-glow);
      }
      .tier-amount {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--text-primary);
      }
    </style>
</head>
<body>
    <?php include __DIR__ . '/components/navbar.php'; ?>

    <main class="container mx-auto px-3 payment-header" style="max-width: 740px;">
        <div class="text-center mb-4">
            <span class="brand-badge mb-2"><i class="bi bi-heart-fill text-rose me-1"></i> Community Supported Infrastructure</span>
            <h1 class="text-light fw-bold mb-2" style="font-size: 2.2rem;">Support Lost & Found BD</h1>
            <p class="text-secondary">Your contribution directly keeps the servers online, fuels emergency alerts, and funds rewards for honest citizens.</p>
        </div>

        <div class="glass-panel p-4 p-md-5 mb-5 animate-slide-up">
            <!-- Donation Amount Preset Selector -->
            <label class="form-label mb-2 fw-bold text-light">Select Donation Tier (BDT):</label>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="tier-selector-card" onclick="selectTier(500, this)">
                        <div class="text-muted small">Bronze</div>
                        <div class="tier-amount">৳500</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="tier-selector-card active" onclick="selectTier(1000, this)">
                        <div class="text-emerald small fw-bold">Popular</div>
                        <div class="tier-amount">৳1,000</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="tier-selector-card" onclick="selectTier(2500, this)">
                        <div class="text-indigo small fw-bold">Silver</div>
                        <div class="tier-amount">৳2,500</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="tier-selector-card" onclick="selectTier(5000, this)">
                        <div class="text-amber small fw-bold">Gold Sponsor</div>
                        <div class="tier-amount">৳5,000</div>
                    </div>
                </div>
            </div>

            <form action="stripe_payment.php" method="POST" id="donationPaymentForm">
                <input type="hidden" name="amount" id="donationAmountField" value="1000">

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="donorNameInput" class="form-label">Full Name <span class="text-rose">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-person icon"></i>
                            <input type="text" class="form-control" id="donorNameInput" name="name" value="<?= htmlspecialchars($currentUser['first_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="Your name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="donorEmailInput" class="form-label">Email Address <span class="text-rose">*</span></label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-envelope icon"></i>
                            <input type="email" class="form-control" id="donorEmailInput" name="email" value="<?= htmlspecialchars($currentUser['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="name@domain.com" required>
                        </div>
                    </div>
                </div>

                <div class="p-3 rounded glass-subtle mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="text-light fw-bold"><i class="bi bi-credit-card-2-front text-emerald me-1"></i> Payment Method</span>
                        <span class="badge bg-emerald-subtle text-emerald"><i class="bi bi-shield-check me-1"></i> PCI-DSS Tokenized</span>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small">Card Details:</label>
                        <input type="text" class="form-control" id="cardNumberInput" name="card_number" placeholder="4242 •••• •••• 4242" maxlength="19">
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <input type="text" class="form-control" name="card_exp" placeholder="MM / YY" maxlength="5">
                        </div>
                        <div class="col-6">
                            <input type="password" class="form-control" name="card_cvc" placeholder="CVC" maxlength="4">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block w-100 py-3" id="paySubmitBtn">
                    <i class="bi bi-heart-fill me-2"></i>
                    <span id="btnPayLabel">Contribute ৳1,000 BDT</span>
                </button>
            </form>

            <div class="text-center text-muted small mt-4 pt-3 border-top border-subtle">
                <i class="bi bi-lock-fill text-emerald me-1"></i> Card details are processed directly via secure Stripe tokenization and are never stored on our database.
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/components/footer.php'; ?>

    <script>
      function selectTier(amount, card) {
        document.querySelectorAll('.tier-selector-card').forEach(c => c.classList.remove('active'));
        card.classList.add('active');
        document.getElementById('donationAmountField').value = amount;
        document.getElementById('btnPayLabel').textContent = 'Contribute ৳' + Number(amount).toLocaleString() + ' BDT';
      }
    </script>
</body>
</html>
