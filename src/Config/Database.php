<?php
declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;
    private static string $driver = 'mysql';

    public static function getConnection(): PDO
    {
        if (self::$instance !== null) {
            return self::$instance;
        }

        $host = getenv('DB_HOST') ?: '127.0.0.1';
        $port = getenv('DB_PORT') ?: '3306';
        $dbName = getenv('DB_NAME') ?: 'l&fbd';
        $user = getenv('DB_USER') ?: 'root';
        $pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';

        // Attempt MySQL PDO connection
        try {
            $dsn = "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4";
            $pdo = new PDO($dsn, $user, $pass, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
            self::$instance = $pdo;
            self::$driver = 'mysql';
            return self::$instance;
        } catch (PDOException $e) {
            // Graceful fallback to SQLite for local development & zero-dependency portability
            $sqlitePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'local_dev.sqlite';
            $isNewDb = !file_exists($sqlitePath);

            $pdo = new PDO("sqlite:{$sqlitePath}", null, null, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);

            if ($isNewDb) {
                self::initializeSqliteSchema($pdo);
            }

            self::$instance = $pdo;
            self::$driver = 'sqlite';
            return self::$instance;
        }
    }

    public static function getDriver(): string
    {
        return self::$driver;
    }

    private static function initializeSqliteSchema(PDO $pdo): void
    {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                first_name TEXT NOT NULL,
                last_name TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                phone TEXT NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT DEFAULT 'user',
                avatar TEXT DEFAULT 'images/user_avatar.png',
                status TEXT DEFAULT 'active',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS items (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                item_type TEXT NOT NULL,
                title TEXT NOT NULL,
                category TEXT NOT NULL,
                description TEXT NOT NULL,
                division TEXT NOT NULL,
                district TEXT NOT NULL,
                specific_location TEXT NOT NULL,
                surrounding_details TEXT,
                date_lost_or_found DATE NOT NULL,
                picture TEXT DEFAULT 'images/default_item.jpg',
                contact_phone TEXT NOT NULL,
                contact_email TEXT NOT NULL,
                reward_amount REAL DEFAULT 0.00,
                status TEXT DEFAULT 'verified',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS missing_persons (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                user_id INTEGER,
                full_name TEXT NOT NULL,
                nid TEXT,
                contact_phone TEXT NOT NULL,
                emergency_phone TEXT,
                missing_date DATE NOT NULL,
                last_seen_division TEXT NOT NULL,
                last_seen_district TEXT NOT NULL,
                last_seen_details TEXT NOT NULL,
                age INTEGER NOT NULL,
                gender TEXT DEFAULT 'male',
                height TEXT,
                hair_color TEXT DEFAULT 'Black',
                eye_color TEXT DEFAULT 'Brown',
                clothing_description TEXT NOT NULL,
                distinguishing_features TEXT,
                photo TEXT DEFAULT 'images/default_person.jpg',
                gd_number TEXT,
                status TEXT DEFAULT 'missing',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS claims (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                item_id INTEGER NOT NULL,
                user_id INTEGER NOT NULL,
                claimant_name TEXT NOT NULL,
                contact_phone TEXT NOT NULL,
                proof_description TEXT NOT NULL,
                proof_image TEXT,
                status TEXT DEFAULT 'submitted',
                admin_notes TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS chatbot_hints (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                question TEXT NOT NULL,
                reply TEXT NOT NULL,
                category TEXT DEFAULT 'General'
            );

            CREATE TABLE IF NOT EXISTS messages (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                session_id TEXT NOT NULL,
                sender TEXT NOT NULL,
                message TEXT NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS donations (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                donor_name TEXT NOT NULL,
                donor_email TEXT NOT NULL,
                amount REAL NOT NULL,
                currency TEXT DEFAULT 'BDT',
                payment_method TEXT DEFAULT 'Stripe',
                stripe_payment_id TEXT NOT NULL,
                status TEXT DEFAULT 'succeeded',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS feedback_inquiries (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL,
                email TEXT NOT NULL,
                subject TEXT NOT NULL,
                message TEXT NOT NULL,
                type TEXT DEFAULT 'inquiry',
                status TEXT DEFAULT 'unread',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Seed default users (password: Admin@1234, User@1234)
        $adminPass = password_hash('Admin@1234', PASSWORD_BCRYPT, ['cost' => 12]);
        $userPass = password_hash('User@1234', PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone, password_hash, role, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute(['System', 'Administrator', 'admin@lostfoundbd.com', '01711000001', $adminPass, 'admin', 'active']);
        $stmt->execute(['Tamim', 'Shadman', 'tamimshadman@gmail.com', '01715000002', $userPass, 'user', 'active']);
        $stmt->execute(['Ratul', 'Bhattacharjee', 'ratul.bhattarcharjee@northsouth.edu', '01915286868', $userPass, 'user', 'active']);

        // Seed default items
        $itemStmt = $pdo->prepare("INSERT INTO items (user_id, item_type, title, category, description, division, district, specific_location, surrounding_details, date_lost_or_found, picture, contact_phone, contact_email, reward_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $itemStmt->execute([2, 'lost', 'MacBook Pro 14" Space Gray', 'Electronics', 'Lost in a black Tomtoc sleeve containing university project and research files.', 'Dhaka', 'Dhaka', 'Hazrat Shahjalal International Airport, Terminal 2', 'Near boarding gate 6 waiting lounge.', '2026-10-15', 'images/logan-weaver-lgnwvr-jM_xJyX-sPI-unsplash.jpg', '01715000002', 'tamimshadman@gmail.com', 5000.00, 'verified']);
        $itemStmt->execute([3, 'found', 'Black Leather Bellroy Wallet', 'Wallet/Cards', 'Found black leather wallet containing a National ID card, NSU student ID, and BRAC Bank debit card.', 'Dhaka', 'Dhaka', 'Bashundhara R/A Gate 1 Bus Stand', 'Near the university shuttle stop.', '2026-10-16', 'images/OIP.jpeg', '01915286868', 'ratul.bhattarcharjee@northsouth.edu', 0.00, 'verified']);
        $itemStmt->execute([2, 'found', 'iPhone 15 Pro Titanium Blue', 'Electronics', 'Found phone with cracked screen protector in a clear case with cat sticker.', 'Chattogram', 'Chattogram', 'GEC Circle, Well Food Bakery', 'Left on table 4 beside the counter.', '2026-10-17', 'images/pexels-fernando-arcos-190819.jpg', '01812345678', 'gec.recovery@gmail.com', 0.00, 'verified']);

        // Seed missing persons
        $mpStmt = $pdo->prepare("INSERT INTO missing_persons (user_id, full_name, nid, contact_phone, emergency_phone, missing_date, last_seen_division, last_seen_district, last_seen_details, age, gender, height, hair_color, eye_color, clothing_description, distinguishing_features, photo, gd_number, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $mpStmt->execute([2, 'Mohammad Abdul Rahim', '19582691234567890', '01711223344', '01899887766', '2026-10-10', 'Dhaka', 'Mirpur', 'Last seen leaving home for Friday prayers at Mirpur-10 Central Mosque.', 68, 'male', '5 ft 6 in', 'Gray/White', 'Brown', 'White Panjabi, white Pajama, and brown leather sandals.', 'Mild memory loss. Small scar on left forehead.', 'images/default_person.jpg', 'GD-8492/Mirpur', 'missing']);
        $mpStmt->execute([3, 'Sadia Akter Mim', '19982619472658193', '01999881122', '01700112233', '2026-10-12', 'Chattogram', 'Agrabad', 'Last seen waiting near Badamtoli intersection around 5:30 PM.', 24, 'female', '5 ft 2 in', 'Black', 'Dark Brown', 'Navy blue Kurti, white scarf, carrying a brown canvas backpack.', 'Wears round metallic spectacles.', 'images/default_person.jpg', 'GD-1042/Agrabad', 'missing']);

        // Seed chatbot hints
        $hintStmt = $pdo->prepare("INSERT INTO chatbot_hints (question, reply, category) VALUES (?, ?, ?)");
        $hintStmt->execute(['Hi||Hello||Hey', 'Hello! I am your Lost & Found Bangladesh Assistant. How can I assist you with recovering or reporting an item today?', 'Greetings']);
        $hintStmt->execute(['How do I report a lost item?', 'Click the "Report Lost Item" button in the navigation bar or home screen. Fill in the item description, location, date, and optionally upload a clear photo and reward details.', 'Reporting']);
        $hintStmt->execute(['How do I report a missing person?', 'Navigate to the "Missing Person" section. You can submit full details including last seen location, age, physical attributes, police GD number, and emergency contact details.', 'Missing Persons']);
        $hintStmt->execute(['How does the verification process work?', 'When someone claims a found item, our moderation desk reviews the submitted proof (serial numbers, receipts, matching photos) to ensure items are safely returned to legitimate owners.', 'Verification']);
        $hintStmt->execute(['Is this service free to use?', 'Yes! Reporting and searching for lost items and missing persons is 100% free for everyone in Bangladesh. You can optionally donate or offer a community reward.', 'General']);
    }
}
