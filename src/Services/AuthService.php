<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use App\Utils\Security;
use PDO;

class AuthService
{
    public function register(string $firstName, string $lastName, string $email, string $phone, string $password): array
    {
        Security::initSession();
        $db = Database::getConnection();

        $firstName = trim($firstName);
        $lastName = trim($lastName);
        $email = strtolower(trim($email));
        $phone = trim($phone);

        if (empty($firstName) || empty($lastName) || empty($email) || empty($phone) || empty($password)) {
            return ['success' => false, 'error' => 'All registration fields are required.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'error' => 'Please provide a valid email address.'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'error' => 'Password must be at least 6 characters long.'];
        }

        // Check if user already exists
        $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            return ['success' => false, 'error' => 'An account with this email address already exists.'];
        }

        // Hash password securely
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

        $insertStmt = $db->prepare("
            INSERT INTO users (first_name, last_name, email, phone, password_hash, role, status)
            VALUES (?, ?, ?, ?, ?, 'user', 'active')
        ");
        $insertStmt->execute([$firstName, $lastName, $email, $phone, $hash]);
        $newId = (int)$db->lastInsertId();

        // Establish session
        $_SESSION['user_id'] = $newId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_name'] = $firstName . ' ' . $lastName;
        $_SESSION['user_role'] = 'user';

        return [
            'success' => true,
            'user' => [
                'id'         => $newId,
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $email,
                'phone'      => $phone,
                'role'       => 'user',
            ]
        ];
    }

    public function login(string $email, string $password): array
    {
        Security::initSession();
        $db = Database::getConnection();

        $email = strtolower(trim($email));

        if (empty($email) || empty($password)) {
            return ['success' => false, 'error' => 'Email and password are required.'];
        }

        // Look up by indexed email with prepared statement
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        if ($user['status'] === 'suspended') {
            return ['success' => false, 'error' => 'This account has been suspended. Please contact support.'];
        }

        // Verify password hash or legacy fallback if existing demo data
        $isMatch = password_verify($password, $user['password_hash']);
        if (!$isMatch && $password === $user['password_hash']) {
            // Upgrade legacy plain-text password to bcrypt hash automatically
            $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
            $upStmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
            $upStmt->execute([$newHash, $user['id']]);
            $isMatch = true;
        }

        if (!$isMatch) {
            return ['success' => false, 'error' => 'Invalid email or password.'];
        }

        // Establish authenticated session
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_role'] = $user['role'];

        return [
            'success' => true,
            'user' => [
                'id'         => (int)$user['id'],
                'first_name' => $user['first_name'],
                'last_name'  => $user['last_name'],
                'email'      => $user['email'],
                'phone'      => $user['phone'],
                'role'       => $user['role'],
                'avatar'     => $user['avatar'],
            ]
        ];
    }

    public function logout(): void
    {
        Security::initSession();
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public function getCurrentUser(): ?array
    {
        Security::initSession();
        if (empty($_SESSION['user_id'])) {
            return null;
        }

        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, first_name, last_name, email, phone, role, avatar, status, created_at FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([(int)$_SESSION['user_id']]);
        $user = $stmt->fetch();

        return $user ?: null;
    }

    public function requireAuth(?string $requiredRole = null): ?array
    {
        $user = $this->getCurrentUser();
        if (!$user) {
            header('Location: login.php');
            exit();
        }

        if ($requiredRole !== null && $user['role'] !== $requiredRole && $user['role'] !== 'admin') {
            http_response_code(403);
            die("Access Denied: Insufficient permissions.");
        }

        return $user;
    }
}
