<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use PDO;

class ChatbotService
{
    public function getAnswer(string $query, string $sessionId): array
    {
        $db = Database::getConnection();
        $cleanQuery = trim($query);

        if (empty($cleanQuery)) {
            return [
                'reply' => "I am here to help! Feel free to ask about reporting lost items, finding missing persons, or how claim verification works.",
                'category' => 'General'
            ];
        }

        // Retrieve all hints
        $stmt = $db->query("SELECT * FROM chatbot_hints");
        $hints = $stmt->fetchAll();

        $bestReply = null;
        $bestCategory = 'General';
        $highestScore = 0;

        $queryLower = strtolower($cleanQuery);
        $queryWords = preg_split('/[\s,\?!]+/', $queryLower, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($hints as $hint) {
            $patterns = explode('||', strtolower($hint['question']));
            $score = 0;

            foreach ($patterns as $pattern) {
                $pattern = trim($pattern);
                if (empty($pattern)) continue;

                // Exact phrase match
                if (str_contains($queryLower, $pattern)) {
                    $score += 10;
                }

                // Word matches
                $patternWords = preg_split('/[\s,\?!]+/', $pattern, -1, PREG_SPLIT_NO_EMPTY);
                $common = array_intersect($queryWords, $patternWords);
                $score += count($common) * 3;
            }

            if ($score > $highestScore) {
                $highestScore = $score;
                $bestReply = $hint['reply'];
                $bestCategory = $hint['category'] ?? 'General';
            }
        }

        // Domain-specific intelligent fallbacks
        if ($highestScore < 3) {
            if (str_contains($queryLower, 'police') || str_contains($queryLower, 'thana') || str_contains($queryLower, 'gd')) {
                $bestReply = "For official police assistance in Bangladesh, visit your local Thana (police station) to file a General Diary (GD). You can also call the Bangladesh National Emergency Service at 999.";
                $bestCategory = "Emergency";
            } elseif (str_contains($queryLower, 'lost') || str_contains($queryLower, 'harie')) {
                $bestReply = "If you lost something, click 'Report Lost Item' on our dashboard. Include clear descriptions, serial numbers (if applicable), and last known location.";
                $bestCategory = "Reporting";
            } elseif (str_contains($queryLower, 'found') || str_contains($queryLower, 'paise')) {
                $bestReply = "Thank you for being an honest citizen! Please click 'Report Found Item'. The rightful owner will be required to submit verification proof before receiving your contact.";
                $bestCategory = "Reporting";
            } elseif (str_contains($queryLower, 'human') || str_contains($queryLower, 'person') || str_contains($queryLower, 'bacha') || str_contains($queryLower, 'manush')) {
                $bestReply = "To report a missing person, head over to the 'Missing Person' section immediately. We distribute reports across community channels and assist Bangladesh Police.";
                $bestCategory = "Missing Persons";
            } else {
                $bestReply = "Thank you for asking. You can browse recent lost & found posts, report an item, or reach our support team directly. Try asking 'How do I report a lost item?' or 'How does verification work?'";
                $bestCategory = "General";
            }
        }

        // Log messages in database
        try {
            $logStmt = $db->prepare("INSERT INTO messages (session_id, sender, message) VALUES (?, ?, ?)");
            $logStmt->execute([$sessionId, 'user', $cleanQuery]);
            $logStmt->execute([$sessionId, 'bot', $bestReply]);
        } catch (\Exception $e) {
            // Silently continue if message table write fails
        }

        return [
            'reply'    => $bestReply,
            'category' => $bestCategory,
        ];
    }

    public function getSuggestions(): array
    {
        return [
            'How do I report a lost item?',
            'How do I file a missing person report?',
            'How does claim verification work?',
            'What should I do if I find an NID or passport?',
            'Is Lost & Found Bangladesh free to use?',
        ];
    }

    public function getHistory(string $sessionId): array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT sender, message, created_at FROM messages WHERE session_id = ? ORDER BY id ASC LIMIT 50");
        $stmt->execute([$sessionId]);
        return $stmt->fetchAll();
    }
}
