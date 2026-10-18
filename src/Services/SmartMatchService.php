<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use PDO;

class SmartMatchService
{
    /**
     * Find best potential matching items for a given item ID.
     */
    public function getMatchesForItem(int $itemId): array
    {
        $db = Database::getConnection();
        
        $stmt = $db->prepare("SELECT * FROM items WHERE id = ?");
        $stmt->execute([$itemId]);
        $target = $stmt->fetch();

        if (!$target) {
            return [];
        }

        // Look for items of the opposite type
        $oppositeType = ($target['item_type'] === 'lost') ? 'found' : 'lost';

        $candidateStmt = $db->prepare("
            SELECT * FROM items 
            WHERE item_type = ? AND status != 'resolved'
            ORDER BY id DESC
            LIMIT 100
        ");
        $candidateStmt->execute([$oppositeType]);
        $candidates = $candidateStmt->fetchAll();

        $scored = [];
        foreach ($candidates as $cand) {
            $score = $this->calculateSimilarityScore($target, $cand);
            if ($score >= 35) { // Threshold for displaying candidate match
                $cand['match_score'] = $score;
                $cand['match_confidence'] = $score >= 75 ? 'high' : ($score >= 50 ? 'medium' : 'potential');
                $scored[] = $cand;
            }
        }

        usort($scored, fn($a, $b) => $b['match_score'] <=> $a['match_score']);
        return array_slice($scored, 0, 10);
    }

    /**
     * Retrieve all top system-wide match pairs across the platform.
     */
    public function getSystemMatchPairs(): array
    {
        $db = Database::getConnection();
        $lostStmt = $db->query("SELECT * FROM items WHERE item_type = 'lost' AND status != 'resolved' ORDER BY id DESC LIMIT 50");
        $lostItems = $lostStmt->fetchAll();

        $foundStmt = $db->query("SELECT * FROM items WHERE item_type = 'found' AND status != 'resolved' ORDER BY id DESC LIMIT 50");
        $foundItems = $foundStmt->fetchAll();

        $pairs = [];
        foreach ($lostItems as $lost) {
            foreach ($foundItems as $found) {
                $score = $this->calculateSimilarityScore($lost, $found);
                if ($score >= 60) {
                    $pairs[] = [
                        'score' => $score,
                        'confidence' => $score >= 80 ? 'high' : 'medium',
                        'lost_item' => $lost,
                        'found_item' => $found,
                    ];
                }
            }
        }

        usort($pairs, fn($a, $b) => $b['score'] <=> $a['score']);
        return array_slice($pairs, 0, 15);
    }

    /**
     * Compute a weighted 0-100 similarity score between two belongings.
     */
    public function calculateSimilarityScore(array $itemA, array $itemB): int
    {
        $score = 0;

        // 1. Category exact match (25 points)
        if (strcasecmp($itemA['category'] ?? '', $itemB['category'] ?? '') === 0) {
            $score += 25;
        }

        // 2. Division & District Match (25 points)
        $divA = strtolower(trim($itemA['division'] ?? ''));
        $divB = strtolower(trim($itemB['division'] ?? ''));
        $distA = strtolower(trim($itemA['district'] ?? ''));
        $distB = strtolower(trim($itemB['district'] ?? ''));

        if ($divA !== '' && $divA === $divB) {
            $score += 15;
            if ($distA !== '' && $distA === $distB) {
                $score += 10;
            }
        }

        // 3. Title & Description Keyword Overlap (35 points)
        $tokensA = $this->extractTokens(($itemA['title'] ?? '') . ' ' . ($itemA['description'] ?? ''));
        $tokensB = $this->extractTokens(($itemB['title'] ?? '') . ' ' . ($itemB['description'] ?? ''));

        if (!empty($tokensA) && !empty($tokensB)) {
            $intersection = array_intersect($tokensA, $tokensB);
            $tokenSimilarity = (count($intersection) * 2) / (count($tokensA) + count($tokensB));
            $score += (int)round($tokenSimilarity * 35);
        }

        // 4. Incident Date Proximity (15 points)
        $dateA = strtotime($itemA['date_lost_or_found'] ?? '');
        $dateB = strtotime($itemB['date_lost_or_found'] ?? '');
        if ($dateA && $dateB) {
            $daysDiff = abs($dateA - $dateB) / (60 * 60 * 24);
            if ($daysDiff <= 3) {
                $score += 15;
            } elseif ($daysDiff <= 7) {
                $score += 10;
            } elseif ($daysDiff <= 14) {
                $score += 5;
            }
        }

        return min(100, max(0, $score));
    }

    private function extractTokens(string $text): array
    {
        $clean = strtolower(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $text));
        $words = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);
        $stopWords = ['the', 'a', 'an', 'and', 'or', 'in', 'on', 'at', 'with', 'my', 'of', 'for', 'to', 'was', 'is'];
        return array_values(array_filter($words, fn($w) => strlen($w) > 2 && !in_array($w, $stopWords, true)));
    }
}
