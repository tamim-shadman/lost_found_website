<?php
declare(strict_types=1);

namespace App\Services;

use App\Config\Database;
use App\Utils\Security;
use PDO;

class MissingPersonService
{
    public function getMissingPersons(array $filters = []): array
    {
        $db = Database::getConnection();
        $conditions = ["1=1"];
        $params = [];

        if (!empty($filters['status']) && $filters['status'] !== 'All') {
            $conditions[] = "status = ?";
            $params[] = $filters['status'];
        }

        if (!empty($filters['division']) && $filters['division'] !== 'All') {
            $conditions[] = "last_seen_division = ?";
            $params[] = $filters['division'];
        }

        if (!empty($filters['gender']) && $filters['gender'] !== 'All') {
            $conditions[] = "gender = ?";
            $params[] = $filters['gender'];
        }

        if (!empty($filters['query'])) {
            $search = '%' . trim($filters['query']) . '%';
            $conditions[] = "(full_name LIKE ? OR clothing_description LIKE ? OR last_seen_details LIKE ? OR last_seen_district LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        $whereClause = implode(" AND ", $conditions);
        $limit = isset($filters['limit']) ? max(1, min(100, (int)$filters['limit'])) : 50;
        $offset = isset($filters['offset']) ? max(0, (int)$filters['offset']) : 0;

        $sql = "SELECT * FROM missing_persons WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$limit} OFFSET {$offset}";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $persons = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) as total FROM missing_persons WHERE {$whereClause}";
        $countStmt = $db->prepare($countSql);
        $countStmt->execute($params);
        $total = (int)$countStmt->fetchColumn();

        return [
            'persons' => $persons,
            'total'   => $total,
        ];
    }

    public function getPersonById(int $id): ?array
    {
        $db = Database::getConnection();
        $stmt = $db->prepare("SELECT * FROM missing_persons WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $person = $stmt->fetch();
        return $person ?: null;
    }

    public function reportMissingPerson(array $data, ?array $file = null, ?int $userId = null): array
    {
        $db = Database::getConnection();

        $fullName = trim($data['full_name'] ?? $data['name'] ?? '');
        $nid = trim($data['nid'] ?? '');
        $phone = trim($data['contact_phone'] ?? $data['phone'] ?? '');
        $emergencyPhone = trim($data['emergency_phone'] ?? '');
        $missingDate = !empty($data['missing_date']) ? $data['missing_date'] : (!empty($data['Date']) ? $data['Date'] : date('Y-m-d'));
        $division = trim($data['last_seen_division'] ?? $data['state'] ?? 'Dhaka');
        $district = trim($data['last_seen_district'] ?? $data['city'] ?? 'Dhaka');
        $lastSeenDetails = trim($data['last_seen_details'] ?? $data['address2'] ?? 'Not specified');
        $age = (int)($data['age'] ?? $data['Age'] ?? 0);
        if ($age < 0 || $age > 125) {
            return ['success' => false, 'error' => 'Please provide a realistic age between 0 and 125.'];
        }

        $gender = in_array(strtolower($data['gender'] ?? ''), ['male', 'female', 'other']) ? strtolower($data['gender']) : 'male';
        $height = trim($data['height'] ?? '');
        $hairColor = trim($data['hair_color'] ?? $data['haircolor'] ?? 'Black');
        $eyeColor = trim($data['eye_color'] ?? $data['eyecolor'] ?? 'Brown');
        $clothing = trim($data['clothing_description'] ?? $data['Cloths'] ?? '');
        $features = trim($data['distinguishing_features'] ?? '');
        $gdNumber = trim($data['gd_number'] ?? '');

        if (empty($fullName) || empty($phone) || empty($clothing)) {
            return ['success' => false, 'error' => 'Full name, emergency contact phone, and clothing description are required.'];
        }

        $photoPath = 'images/default_person.jpg';
        if ($file !== null && !empty($file['tmp_name'])) {
            $upload = Security::handleImageUpload($file, 'images/missing/');
            if ($upload['success']) {
                $photoPath = $upload['path'];
            }
        }

        $stmt = $db->prepare("
            INSERT INTO missing_persons (
                user_id, full_name, nid, contact_phone, emergency_phone,
                missing_date, last_seen_division, last_seen_district, last_seen_details,
                age, gender, height, hair_color, eye_color,
                clothing_description, distinguishing_features, photo, gd_number, status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'missing')
        ");

        $stmt->execute([
            $userId,
            $fullName,
            $nid,
            $phone,
            $emergencyPhone,
            $missingDate,
            $division,
            $district,
            $lastSeenDetails,
            $age,
            $gender,
            $height,
            $hairColor,
            $eyeColor,
            $clothing,
            $features,
            $photoPath,
            $gdNumber,
        ]);

        return [
            'success' => true,
            'id'      => (int)$db->lastInsertId(),
            'message' => 'Missing person report broadcasted across Bangladesh Police and community network.',
        ];
    }

    public function reportSighting(int $id, string $location, string $details, string $reporterContact): array
    {
        $db = Database::getConnection();
        $person = $this->getPersonById($id);

        if (!$person) {
            return ['success' => false, 'error' => 'Person record not found.'];
        }

        $location = trim($location);
        $details = trim($details);
        if (empty($location) || empty($details)) {
            return ['success' => false, 'error' => 'Sighting location and details are required.'];
        }

        $note = "\n[SIGHTING REPORTED " . date('Y-m-d H:i') . "] Location: {$location} - Details: {$details} (Reporter Contact: {$reporterContact})";
        $newDetails = $person['last_seen_details'] . $note;

        $stmt = $db->prepare("UPDATE missing_persons SET status = 'sighted', last_seen_details = ? WHERE id = ?");
        $stmt->execute([$newDetails, $id]);

        return [
            'success' => true,
            'message' => 'Sighting report recorded and forwarded to Bangladesh Police and family contact.'
        ];
    }

    public function updateStatus(int $id, string $status, ?int $userId = null, bool $isAdmin = false): array
    {
        $db = Database::getConnection();
        $allowed = ['missing', 'sighted', 'found_safe', 'resolved'];
        if (!in_array($status, $allowed)) {
            return ['success' => false, 'error' => 'Invalid status option.'];
        }

        $stmt = $db->prepare("UPDATE missing_persons SET status = ? WHERE id = ?");
        $stmt->execute([$status, $id]);

        return ['success' => true, 'message' => "Report status updated to {$status}."];
    }
}
