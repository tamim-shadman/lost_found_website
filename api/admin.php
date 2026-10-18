<?php
declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

require_once dirname(__DIR__) . '/src/autoload.php';

use App\Config\Database;
use App\Services\AuthService;
use App\Utils\Security;

Security::initSession();
$auth = new AuthService();
$currentUser = $auth->getCurrentUser();

if (!$currentUser || !in_array($currentUser['role'], ['admin', 'moderator'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized: Administrator access required.']);
    exit();
}

$db = Database::getConnection();
$rawBody = file_get_contents('php://input');
$jsonBody = json_decode($rawBody, true) ?? [];
$input = array_merge($_GET, $_POST, $jsonBody);
$action = $input['action'] ?? 'dashboard';

try {
    switch ($action) {
        case 'dashboard':
            $totalUsers = (int)$db->query("SELECT COUNT(*) FROM users")->fetchColumn();
            $pendingItems = (int)$db->query("SELECT COUNT(*) FROM items WHERE status = 'pending'")->fetchColumn();
            $verifiedItems = (int)$db->query("SELECT COUNT(*) FROM items WHERE status = 'verified'")->fetchColumn();
            $resolvedItems = (int)$db->query("SELECT COUNT(*) FROM items WHERE status = 'resolved'")->fetchColumn();
            $pendingClaims = (int)$db->query("SELECT COUNT(*) FROM claims WHERE status = 'submitted'")->fetchColumn();
            $missingCount = (int)$db->query("SELECT COUNT(*) FROM missing_persons WHERE status = 'missing'")->fetchColumn();
            $totalDonations = (float)$db->query("SELECT COALESCE(SUM(amount), 0) FROM donations WHERE status = 'succeeded'")->fetchColumn();

            echo json_encode([
                'success' => true,
                'metrics' => [
                    'total_users'     => $totalUsers,
                    'pending_items'   => $pendingItems,
                    'verified_items'  => $verifiedItems,
                    'resolved_items'  => $resolvedItems,
                    'pending_claims'  => $pendingClaims,
                    'active_missing'  => $missingCount,
                    'total_donations' => $totalDonations,
                ]
            ]);
            break;

        case 'verify_item':
            $id = (int)($input['id'] ?? 0);
            $status = in_array($input['status'] ?? '', ['verified', 'resolved', 'pending']) ? $input['status'] : 'verified';
            $stmt = $db->prepare("UPDATE items SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
            echo json_encode(['success' => true, 'message' => "Item status updated to {$status}."]);
            break;

        case 'delete_item':
            $id = (int)($input['id'] ?? 0);
            $stmt = $db->prepare("DELETE FROM items WHERE id = ?");
            $stmt->execute([$id]);
            echo json_encode(['success' => true, 'message' => 'Item removed from database.']);
            break;

        case 'list_users':
            $stmt = $db->query("SELECT id, first_name, last_name, email, phone, role, status, created_at FROM users ORDER BY id DESC LIMIT 100");
            echo json_encode(['success' => true, 'users' => $stmt->fetchAll()]);
            break;

        case 'update_user_status':
            $userId = (int)($input['user_id'] ?? 0);
            $status = in_array($input['status'] ?? '', ['active', 'suspended', 'pending']) ? $input['status'] : 'active';
            $stmt = $db->prepare("UPDATE users SET status = ? WHERE id = ?");
            $stmt->execute([$status, $userId]);
            echo json_encode(['success' => true, 'message' => "User status set to {$status}."]);
            break;

        case 'list_claims':
            $stmt = $db->query("
                SELECT c.*, i.title as item_title, i.category as item_category, u.email as claimant_email
                FROM claims c
                JOIN items i ON c.item_id = i.id
                JOIN users u ON c.user_id = u.id
                ORDER BY c.created_at DESC
                LIMIT 50
            ");
            echo json_encode(['success' => true, 'claims' => $stmt->fetchAll()]);
            break;

        case 'resolve_claim':
            $claimId = (int)($input['claim_id'] ?? 0);
            $status = in_array($input['status'] ?? '', ['approved', 'rejected']) ? $input['status'] : 'approved';
            $adminNotes = trim($input['admin_notes'] ?? '');

            $stmt = $db->prepare("UPDATE claims SET status = ?, admin_notes = ? WHERE id = ?");
            $stmt->execute([$status, $adminNotes, $claimId]);

            // If approved, mark the underlying item as resolved/claimed
            if ($status === 'approved') {
                $claim = $db->query("SELECT item_id FROM claims WHERE id = {$claimId}")->fetch();
                if ($claim) {
                    $db->prepare("UPDATE items SET status = 'resolved' WHERE id = ?")->execute([$claim['item_id']]);
                }
            }

            echo json_encode(['success' => true, 'message' => "Claim status marked as {$status}."]);
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => 'Unknown admin action.']);
            break;
    }
} catch (\Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Admin operation failed: ' . $e->getMessage()]);
}
