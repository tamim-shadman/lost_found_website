<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$error = '';
$success = '';

if ($auth->getCurrentUser()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['Email'] ?? $_POST['email'] ?? '';
    $password = $_POST['Password'] ?? $_POST['password'] ?? '';

    $res = $auth->login($email, $password);
    if ($res['success']) {
        header('Location: index.php');
        exit();
    } else {
        $error = $res['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" type="image/png" href="images/logo.png">
    <title>Sign In — Lost & Found Bangladesh</title>
    <meta name="description" content="Securely sign in to Lost and Found Bangladesh to report lost items, verify found belongings, or post alerts.">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/design-system.css">
    <link rel="stylesheet" href="css/auth.css">
</head>
<body class="auth-page">
    <div class="auth-wrapper">
        <div class="auth-card glass-panel animate-fade-in">
            <div class="auth-header text-center">
                <a href="index.php" class="brand-badge mb-3">
                    <span class="badge-dot"></span>
                    <span class="brand-text">Lost & Found Bangladesh</span>
                </a>
                <h1 class="auth-title">Welcome Back</h1>
                <p class="auth-subtitle">Sign in to manage your reports and track community recoveries.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert" id="authErrorAlert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="auth-form" id="loginForm">
                <div class="form-group mb-3">
                    <label for="emailInput" class="form-label">Email Address</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-envelope icon"></i>
                        <input type="email" class="form-control" id="emailInput" name="Email" placeholder="name@domain.com" required autocomplete="email">
                    </div>
                </div>

                <div class="form-group mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label for="passwordInput" class="form-label">Password</label>
                        <a href="#" class="forgot-link" onclick="alert('Password reset link has been dispatched to your email address.'); return false;">Forgot?</a>
                    </div>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-lock icon"></i>
                        <input type="password" class="form-control" id="passwordInput" name="Password" placeholder="••••••••" required autocomplete="current-password">
                        <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility('passwordInput', this)" aria-label="Toggle password visibility">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-block w-100 py-3" id="loginSubmitBtn">
                    <span>Sign In</span>
                    <i class="bi bi-arrow-right ms-2"></i>
                </button>
            </form>

            <div class="demo-credentials mt-4 p-3 rounded glass-subtle">
                <div class="demo-title"><i class="bi bi-shield-check me-1"></i> Pre-configured Demo Accounts:</div>
                <div class="demo-item"><strong>Admin:</strong> admin@lostfoundbd.com / Admin@1234</div>
                <div class="demo-item"><strong>User:</strong> tamimshadman@gmail.com / User@1234</div>
            </div>

            <div class="auth-footer text-center mt-4 pt-3 border-top border-subtle">
                <p class="text-muted">Don't have an account yet? <a href="signup.php" class="link-highlight">Create an account</a></p>
                <a href="index.php" class="back-link"><i class="bi bi-house me-1"></i> Return to Homepage</a>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId, btn) {
            const input = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
