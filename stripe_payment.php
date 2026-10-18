<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Services\PaymentService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();
$paymentService = new PaymentService();

$statusMsg = '';
$ordStatus = 'error';
$transactionId = '';
$paidAmount = 0.0;
$currency = 'BDT';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $res = $paymentService->processDonation($_POST, $currentUser ? (int)$currentUser['id'] : null);
    if ($res['success']) {
        $ordStatus = 'success';
        $statusMsg = $res['message'];
        $transactionId = $res['transaction_id'];
        $paidAmount = $res['amount'];
        $currency = $res['currency'];
    } else {
        $ordStatus = 'error';
        $statusMsg = $res['error'];
    }
} else {
    header("Location: payment.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>Payment Status — Lost & Found Bangladesh</title>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-card glass-panel animate-fade-in text-center p-5">
            <?php if ($ordStatus === 'success'): ?>
                <div class="status-icon-wrapper mb-4 text-success">
                    <i class="bi bi-patch-check-fill" style="font-size: 4rem; color: var(--color-emerald-500);"></i>
                </div>
                <h1 class="auth-title">Payment Successful!</h1>
                <p class="auth-subtitle mb-4"><?= htmlspecialchars($statusMsg, ENT_QUOTES, 'UTF-8') ?></p>

                <div class="receipt-box p-3 rounded glass-subtle text-start mb-4">
                    <div class="d-flex justify-content-between py-2 border-bottom border-subtle">
                        <span class="text-muted">Transaction ID:</span>
                        <code class="text-emerald"><?= htmlspecialchars($transactionId, ENT_QUOTES, 'UTF-8') ?></code>
                    </div>
                    <div class="d-flex justify-content-between py-2 border-bottom border-subtle">
                        <span class="text-muted">Amount Paid:</span>
                        <strong class="text-light"><?= number_format($paidAmount, 2) ?> <?= htmlspecialchars($currency, ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Security Compliance:</span>
                        <span class="badge bg-emerald-subtle text-emerald"><i class="bi bi-shield-lock me-1"></i> PCI-DSS Tokenized</span>
                    </div>
                </div>
            <?php else: ?>
                <div class="status-icon-wrapper mb-4 text-danger">
                    <i class="bi bi-x-circle-fill" style="font-size: 4rem; color: var(--color-rose-500);"></i>
                </div>
                <h1 class="auth-title">Transaction Failed</h1>
                <p class="auth-subtitle mb-4"><?= htmlspecialchars($statusMsg, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <div class="d-flex justify-content-center gap-3">
                <a href="index.php" class="btn btn-primary px-4 py-2"><i class="bi bi-house me-1"></i> Return Home</a>
                <a href="recentposts.php" class="btn btn-secondary px-4 py-2"><i class="bi bi-compass me-1"></i> Browse Alerts</a>
            </div>
        </div>
    </div>
</body>
</html>