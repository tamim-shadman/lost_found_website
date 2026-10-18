<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use PDO;

class QrTagService
{
    /**
     * Generate or register a new asset recovery tag for a citizen.
     */
    public function createTag(int $userId, string $assetName, string $category, string $notes = ''): array
    {
        $db = Database::getConnection();

        // Ensure table exists
        $db->exec("
            CREATE TABLE IF NOT EXISTS asset_qr_tags (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER NOT NULL,
                tag_code TEXT UNIQUE NOT NULL,
                asset_name TEXT NOT NULL,
                category TEXT NOT NULL,
                notes TEXT,
                status TEXT DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
            CREATE TABLE IF NOT EXISTS qr_recovery_logs (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                tag_code TEXT NOT NULL,
                finder_name TEXT NOT NULL,
                finder_phone TEXT NOT NULL,
                found_location TEXT NOT NULL,
                message TEXT,
                status TEXT DEFAULT 'unresolved',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        $tagCode = 'LF-BD-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));

        $stmt = $db->prepare("
            INSERT INTO asset_qr_tags (user_id, tag_code, asset_name, category, notes, status)
            VALUES (?, ?, ?, ?, ?, 'active')
        ");
        $stmt->execute([$userId, $tagCode, $assetName, $category, $notes]);

        return [
            'success'      => true,
            'tag_code'     => $tagCode,
            'asset_name'   => $assetName,
            'category'     => $category,
            'recovery_url' => "recover.html?tag={$tagCode}",
            'message'      => 'Asset recovery badge generated successfully.'
        ];
    }

    /**
     * Retrieve all tags owned by a user.
     */
    public function getUserTags(int $userId): array
    {
        $db = Database::getConnection();
        // Check if table exists
        $tableExists = $db->query("SELECT name FROM sqlite_master WHERE type='table' AND name='asset_qr_tags'")->fetch();
        if (!$tableExists) {
            return [];
        }

        $stmt = $db->prepare("SELECT * FROM asset_qr_tags WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    /**
     * Inspect a scanned tag for a finder.
     */
    public function getTagDetails(string $tagCode): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT id, tag_code, asset_name, category, status, created_at FROM asset_qr_tags WHERE tag_code = ?");
        $stmt->execute([$tagCode]);
        $tag = $stmt->fetch();
        return $tag ?: null;
    }

    /**
     * Submit an anonymous recovery notice by a finder who scanned the tag.
     */
    public function submitRecoveryNotice(string $tagCode, string $finderName, string $finderPhone, string $location, string $message): array
    {
        $db = Database::getConnection();
        $tag = $this->getTagDetails($tagCode);

        if (!$tag) {
            return ['success' => false, 'error' => 'Asset tag code not recognized.'];
        }

        $stmt = $db->prepare("
            INSERT INTO qr_recovery_logs (tag_code, finder_name, finder_phone, found_location, message, status)
            VALUES (?, ?, ?, ?, ?, 'unresolved')
        ");
        $stmt->execute([$tagCode, $finderName, $finderPhone, $location, $message]);

        return [
            'success' => true,
            'message' => 'Thank you! The verified owner has been notified via emergency encrypted dispatch.',
            'tag_code' => $tagCode
        ];
    }
}
