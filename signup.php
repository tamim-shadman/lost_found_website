<?php
declare(strict_types=1);

require_once __DIR__ . '/src/autoload.php';

use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$error = '';

if ($auth->getCurrentUser()) {
    header('Location: index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['Fname'] ?? $_POST['first_name'] ?? '';
    $lastName = $_POST['lname'] ?? $_POST['last_name'] ?? '';
    $email = $_POST['Email'] ?? $_POST['email'] ?? '';
    $phone = $_POST['Mobile'] ?? $_POST['phone'] ?? '';
    $password = $_POST['password'] ?? '';

    $res = $auth->register($firstName, $lastName, $email, $phone, $password);
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
    <title>Create Account — Lost & Found Bangladesh</title>
    <meta name="description" content="Join Lost and Found Bangladesh to help reunite missing belongings and missing persons with their families.">
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
        <div class="auth-card glass-panel animate-fade-in wide-card">
            <div class="auth-header text-center">
                <a href="index.php" class="brand-badge mb-3">
                    <span class="badge-dot"></span>
                    <span class="brand-text">Lost & Found Bangladesh</span>
                </a>
                <h1 class="auth-title">Create Account</h1>
                <p class="auth-subtitle">Join thousands of citizens across Bangladesh restoring lost memories and items.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert" id="authErrorAlert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></span>
                </div>
            <?php endif; ?>

            <form action="signup.php" method="POST" class="auth-form" id="signupForm">
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="fnameInput" class="form-label">First Name</label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-person icon"></i>
                            <input type="text" class="form-control" id="fnameInput" name="Fname" placeholder="Tamim" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="lnameInput" class="form-label">Last Name</label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-person icon"></i>
                            <input type="text" class="form-control" id="lnameInput" name="lname" placeholder="Shadman" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="emailInput" class="form-label">Email Address</label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-envelope icon"></i>
                            <input type="email" class="form-control" id="emailInput" name="Email" placeholder="user@gmail.com" required autocomplete="email">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="phoneInput" class="form-label">Contact Number (BD)</label>
                        <div class="input-icon-wrapper">
                            <i class="bi bi-telephone icon"></i>
                            <input type="tel" class="form-control" id="phoneInput" name="Mobile" placeholder="017XXXXXXXX" required autocomplete="tel">
                        </div>
                    </div>
                </div>

                <div class="form-group mb-4">
                    <label for="passwordInput" class="form-label">Create Password</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-lock icon"></i>
                        <input type="password" class="form-control" id="passwordInput" name="password" placeholder="At least 6 characters" required minlength="6" autocomplete="new-password">
                        <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility('passwordInput', this)" aria-label="Toggle password visibility">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    <small class="form-text text-muted mt-1">We recommend using a strong mix of letters, numbers, and symbols.</small>
                </div>

                <button type="submit" class="btn btn-primary btn-block w-100 py-3" id="signupSubmitBtn">
                    <span>Complete Registration</span>
                    <i class="bi bi-check-circle ms-2"></i>
                </button>
            </form>

            <div class="auth-footer text-center mt-4 pt-3 border-top border-subtle">
                <p class="text-muted">Already registered? <a href="login.php" class="link-highlight">Sign in here</a></p>
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
