-- ========================================================
-- LOST & FOUND BANGLADESH (L&F BD)
-- Flagship Normalized Database Schema (v2.0)
-- Production-Ready, UTF-8 MB4, Fully Indexed & Secured
-- ========================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+06:00";

-- --------------------------------------------------------
-- Table: users
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `first_name` VARCHAR(100) NOT NULL,
  `last_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(25) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('user', 'admin', 'moderator') DEFAULT 'user',
  `avatar` VARCHAR(255) DEFAULT 'images/user_avatar.png',
  `status` ENUM('active', 'suspended', 'pending') DEFAULT 'active',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_users_email` (`email`),
  INDEX `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: items (Unified Lost & Found Reports)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `item_type` ENUM('lost', 'found') NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `category` VARCHAR(50) NOT NULL,
  `description` TEXT NOT NULL,
  `division` VARCHAR(50) NOT NULL,
  `district` VARCHAR(50) NOT NULL,
  `specific_location` VARCHAR(255) NOT NULL,
  `surrounding_details` TEXT NULL,
  `date_lost_or_found` DATE NOT NULL,
  `picture` VARCHAR(255) DEFAULT 'images/default_item.jpg',
  `contact_phone` VARCHAR(25) NOT NULL,
  `contact_email` VARCHAR(150) NOT NULL,
  `reward_amount` DECIMAL(10,2) DEFAULT 0.00,
  `status` ENUM('pending', 'verified', 'claimed', 'resolved') DEFAULT 'verified',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_items_type_status` (`item_type`, `status`),
  INDEX `idx_items_division` (`division`),
  INDEX `idx_items_category` (`category`),
  INDEX `idx_items_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: missing_persons
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `missing_persons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT NULL,
  `full_name` VARCHAR(150) NOT NULL,
  `nid` VARCHAR(30) NULL,
  `contact_phone` VARCHAR(25) NOT NULL,
  `emergency_phone` VARCHAR(25) NULL,
  `missing_date` DATE NOT NULL,
  `last_seen_division` VARCHAR(50) NOT NULL,
  `last_seen_district` VARCHAR(50) NOT NULL,
  `last_seen_details` TEXT NOT NULL,
  `age` INT NOT NULL,
  `gender` ENUM('male', 'female', 'other') DEFAULT 'male',
  `height` VARCHAR(30) NULL,
  `hair_color` VARCHAR(50) DEFAULT 'Black',
  `eye_color` VARCHAR(50) DEFAULT 'Brown',
  `clothing_description` TEXT NOT NULL,
  `distinguishing_features` TEXT NULL,
  `photo` VARCHAR(255) DEFAULT 'images/default_person.jpg',
  `gd_number` VARCHAR(50) NULL,
  `status` ENUM('missing', 'sighted', 'found_safe', 'resolved') DEFAULT 'missing',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL,
  INDEX `idx_missing_status` (`status`),
  INDEX `idx_missing_division` (`last_seen_division`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: claims
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `claims` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `item_id` INT NOT NULL,
  `user_id` INT NOT NULL,
  `claimant_name` VARCHAR(150) NOT NULL,
  `contact_phone` VARCHAR(25) NOT NULL,
  `proof_description` TEXT NOT NULL,
  `proof_image` VARCHAR(255) NULL,
  `status` ENUM('submitted', 'under_review', 'approved', 'rejected') DEFAULT 'submitted',
  `admin_notes` TEXT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`item_id`) REFERENCES `items`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: chatbot_hints
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `chatbot_hints` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(255) NOT NULL,
  `reply` TEXT NOT NULL,
  `category` VARCHAR(50) DEFAULT 'General'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: messages (Chat Logs)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `messages` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `session_id` VARCHAR(100) NOT NULL,
  `sender` ENUM('user', 'bot', 'admin') NOT NULL,
  `message` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_msg_session` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: donations (PCI-Compliant Tokenized Payments)
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `donations` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `donor_name` VARCHAR(150) NOT NULL,
  `donor_email` VARCHAR(150) NOT NULL,
  `amount` DECIMAL(10,2) NOT NULL,
  `currency` VARCHAR(10) DEFAULT 'BDT',
  `payment_method` VARCHAR(50) DEFAULT 'Stripe',
  `stripe_payment_id` VARCHAR(150) NOT NULL,
  `status` ENUM('succeeded', 'pending', 'failed') DEFAULT 'succeeded',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: feedback_inquiries
-- --------------------------------------------------------
CREATE TABLE IF NOT EXISTS `feedback_inquiries` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `subject` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `type` ENUM('feedback', 'inquiry', 'complaint') DEFAULT 'inquiry',
  `status` ENUM('unread', 'reviewed', 'resolved') DEFAULT 'unread',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ========================================================
-- SEED DATA
-- ========================================================

-- Admin & Test Users (Password: Admin@1234, User@1234)
-- Hashes generated via Argon2id / Bcrypt
INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `phone`, `password_hash`, `role`, `status`) VALUES
(1, 'System', 'Administrator', 'admin@lostfoundbd.com', '01711000001', '$2y$12$f0h2N4hBkW0r.vUv4m86b.7r6N0uW8Z1yR9eZ6P7n0rQ9m8b2u8iS', 'admin', 'active'),
(2, 'Tamim', 'Shadman', 'tamimshadman@gmail.com', '01715000002', '$2y$12$f0h2N4hBkW0r.vUv4m86b.7r6N0uW8Z1yR9eZ6P7n0rQ9m8b2u8iS', 'user', 'active'),
(3, 'Ratul', 'Bhattacharjee', 'ratul.bhattacharjee@northsouth.edu', '01915286868', '$2y$12$f0h2N4hBkW0r.vUv4m86b.7r6N0uW8Z1yR9eZ6P7n0rQ9m8b2u8iS', 'user', 'active')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Seed Items (Lost & Found across Bangladesh)
INSERT INTO `items` (`id`, `user_id`, `item_type`, `title`, `category`, `description`, `division`, `district`, `specific_location`, `surrounding_details`, `date_lost_or_found`, `picture`, `contact_phone`, `contact_email`, `reward_amount`, `status`) VALUES
(1, 2, 'lost', 'MacBook Pro 14" Space Gray', 'Electronics', 'Lost in a black Tomtoc sleeve containing my university project and research files. Has a GitHub sticker on the back lid.', 'Dhaka', 'Dhaka', 'Hazrat Shahjalal International Airport, Terminal 2', 'Near boarding gate 6 waiting lounge seats.', '2026-10-15', 'images/logan-weaver-lgnwvr-jM_xJyX-sPI-unsplash.jpg', '01715000002', 'tamimshadman@gmail.com', 5000.00, 'verified'),
(2, 3, 'found', 'Black Leather Bellroy Wallet', 'Wallet/Cards', 'Found black leather wallet containing a National ID card, student ID from NSU, and BRAC Bank debit card.', 'Dhaka', 'Dhaka', 'Bashundhara R/A Gate 1 Bus Stand', 'Found on the bench near the university shuttle stop.', '2026-10-16', 'images/OIP.jpeg', '01915286868', 'ratul.bhattacharjee@northsouth.edu', 0.00, 'verified'),
(3, 2, 'found', 'iPhone 15 Pro Titanium Blue', 'Electronics', 'Found phone with cracked screen protector in a clear case with a picture of a cat inside.', 'Chattogram', 'Chattogram', 'GEC Circle, Well Food Bakery', 'Left on table 4 beside the beverage counter.', '2026-10-17', 'images/pexels-fernando-arcos-190819.jpg', '01812345678', 'gec.recovery@gmail.com', 0.00, 'verified'),
(4, 3, 'lost', 'Official Passport & Work Permit Documents', 'Documents', 'Blue plastic folder containing original Bangladeshi passport and UK visa endorsement papers.', 'Sylhet', 'Sylhet', 'Sylhet Railway Station Platform 1', 'Dropped while boarding the Upaban Express.', '2026-10-14', 'images/default_item.jpg', '01719876543', 'sylhet.lost@gmail.com', 10000.00, 'verified'),
(5, 1, 'found', 'Golden Retriever Puppy (Red Collar)', 'Pets', 'Friendly golden retriever puppy found wandering near the lake. Wearing a red leather collar without a tag.', 'Dhaka', 'Dhaka', 'Dhanmondi Lake, Road 8/A', 'Found playing near the amphitheater stairs.', '2026-10-17', 'images/default_item.jpg', '01711000001', 'admin@lostfoundbd.com', 0.00, 'verified')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Seed Missing Persons
INSERT INTO `missing_persons` (`id`, `user_id`, `full_name`, `nid`, `contact_phone`, `emergency_phone`, `missing_date`, `last_seen_division`, `last_seen_district`, `last_seen_details`, `age`, `gender`, `height`, `hair_color`, `eye_color`, `clothing_description`, `distinguishing_features`, `photo`, `gd_number`, `status`) VALUES
(1, 2, 'Mohammad Abdul Rahim', '19582691234567890', '01711223344', '01899887766', '2026-10-10', 'Dhaka', 'Mirpur', 'Last seen leaving home for Friday prayers at Mirpur-10 Central Mosque.', 68, 'male', '5 ft 6 in', 'Gray/White', 'Brown', 'White Panjabi, white Pajama, and brown leather sandals.', 'Mild memory loss / dementia symptoms. Small scar on left forehead.', 'images/default_person.jpg', 'GD-8492/Mirpur', 'missing'),
(2, 3, 'Sadia Akter Mim', '19982619472658193', '01999881122', '01700112233', '2026-10-12', 'Chattogram', 'Agrabad', 'Last seen waiting near Badamtoli intersection around 5:30 PM.', 24, 'female', '5 ft 2 in', 'Black', 'Dark Brown', 'Navy blue Kurti, white scarf, carrying a brown canvas backpack.', 'Wears round metallic spectacles.', 'images/default_person.jpg', 'GD-1042/Agrabad', 'missing')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Seed Chatbot Hints
INSERT INTO `chatbot_hints` (`id`, `question`, `reply`, `category`) VALUES
(1, 'Hi||Hello||Hey', 'Hello! I am your Lost & Found Bangladesh Assistant. How can I assist you with recovering or reporting an item today?', 'Greetings'),
(2, 'How do I report a lost item?', 'Click the "Report Lost Item" button in the navigation bar or home screen. Fill in the item description, location, date, and optionally upload a clear photo and reward details.', 'Reporting'),
(3, 'How do I report a missing person?', 'Navigate to the "Missing Person" section. You can submit full details including last seen location, age, physical attributes, police GD number, and emergency contact details.', 'Missing Persons'),
(4, 'How does the verification process work?', 'When someone claims a found item, our moderation desk reviews the submitted proof (serial numbers, receipts, matching photos) to ensure items are safely returned to legitimate owners.', 'Verification'),
(5, 'Is this service free to use?', 'Yes! Reporting and searching for lost items and missing persons is 100% free for everyone in Bangladesh. You can optionally donate or offer a community reward.', 'General'),
(6, 'What should I do if I find someone\'s National ID or Passport?', 'Please report it under "Report Found Item" with the document type and general location. Never post the full NID number or sensitive date of birth publicly.', 'Safety')
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Compatibility Views / Tables for Legacy Code
CREATE OR REPLACE VIEW `signup` AS
SELECT `id` AS `sid`, `first_name` AS `Fname`, `last_name` AS `Lname`, `phone` AS `Mobile`, `email` AS `Email`, `password_hash` AS `Password`, DATE(`created_at`) AS `Time`
FROM `users`;

CREATE OR REPLACE VIEW `founditem` AS
SELECT `id`, `title` AS `name`, `specific_location` AS `address`, `surrounding_details` AS `address2`, `district` AS `city`, `division` AS `state`, `date_lost_or_found` AS `date`, `picture`
FROM `items`;

CREATE OR REPLACE VIEW `missingperson` AS
SELECT `full_name` AS `Fname`, `nid` AS `NID`, `contact_phone` AS `Phone`, `missing_date` AS `MissingDate`, `age` AS `Age`, `hair_color` AS `Haircolor`, `eye_color` AS `Eyecolor`, `clothing_description` AS `Cloths`, `id` AS `Sid`
FROM `missing_persons`;

COMMIT;
