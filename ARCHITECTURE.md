# APPLICATION ARCHITECTURE DOCUMENTATION
**Lost & Found Bangladesh (L&F BD) — Nationwide Community Recovery Platform**
*Version 2.0.0 — Production Architecture Specification*

---

## 1. Executive Architecture Summary

Lost & Found Bangladesh is architected as a resilient, dual-runtime, full-stack application designed to maximize accessibility, security, and developer velocity. The platform supports nationwide reporting and recovery of lost belongings, emergency tracking of missing persons with Bangladesh Police General Diary (GD) integration, peer-to-peer ownership verification, and secure Stripe donations.

```mermaid
graph TD
    subgraph Client Layer
        Browser[Modern Web Browser / Mobile / Desktop]
        CmdPalette[Ctrl+K Command Palette]
        ModDesk[Staff Moderation Console]
    end

    subgraph Transport & API Gateway
        NginxApache[Nginx / Apache / Node.js Dev Proxy]
        HealthCheck[/api/health.php]
        AuthAPI[/api/auth.php]
        ItemsAPI[/api/items.php]
        MissingAPI[/api/missing.php]
        ChatbotAPI[/api/chatbot.php]
        AdminAPI[/api/admin.php]
    end

    subgraph PSR-4 Domain Services
        AuthService[App\\Services\\AuthService]
        ItemService[App\\Services\\ItemService]
        MissingService[App\\Services\\MissingPersonService]
        ChatbotService[App\\Services\\ChatbotService]
        PaymentService[App\\Services\\PaymentService]
        SecurityUtil[App\\Utils\\Security]
    end

    subgraph Data Persistence
        PDOSingleton[App\\Config\\Database PDO Singleton]
        MySQL[(Production MySQL 8.0+)]
        SQLite[(Local Resilient SQLite Fallback)]
        UploadStorage[images/uploads/ MIME Verified]
    end

    Browser --> NginxApache
    CmdPalette --> NginxApache
    ModDesk --> NginxApache

    NginxApache --> HealthCheck
    NginxApache --> AuthAPI
    NginxApache --> ItemsAPI
    NginxApache --> MissingAPI
    NginxApache --> ChatbotAPI
    NginxApache --> AdminAPI

    AuthAPI --> AuthService
    ItemsAPI --> ItemService
    MissingAPI --> MissingService
    ChatbotAPI --> ChatbotService
    AdminAPI --> ItemService
    AdminAPI --> AuthService

    AuthService --> SecurityUtil
    ItemService --> SecurityUtil
    PaymentService --> SecurityUtil

    AuthService --> PDOSingleton
    ItemService --> PDOSingleton
    MissingService --> PDOSingleton
    PaymentService --> PDOSingleton

    PDOSingleton -. Primary .-> MySQL
    PDOSingleton -. Fallback .-> SQLite
    SecurityUtil --> UploadStorage
```

---

## 2. Dual-Runtime Deployment Architecture

To support diverse environments (from standard shared XAMPP/cPanel hosting to Dockerized enterprise Linux clusters and local development without local PHP/MySQL servers), the application features a **Dual-Runtime Topology**:

| Component | Production Runtime | Development / Preview Runtime |
|---|---|---|
| **Web Server** | Apache 2.4+ / Nginx 1.24+ (FPM) | Node.js v22+ Native HTTP Server (`dev-server.mjs`) |
| **Backend Engine** | PHP 8.2+ (Strict Types, PSR-4) | Embedded Native Mock REST Handlers (`dev-server.mjs`) |
| **Database** | MySQL 8.0+ / MariaDB 10.6+ | SQLite (`database/local_dev.sqlite`) or In-Memory State |
| **Asset Delivery** | HTTP/2, Gzip/Brotli, Static Caching | Streaming Node.js ReadStream with MIME detection |
| **Session Model** | HTTP-only, SameSite=Lax, Secure Cookie | Memory Session Store with Token Authorization |

Both runtimes adhere to identical REST API contracts (`/api/*.php`), JSON payload schemas, and HTTP status code behaviors, enabling automated testing and continuous integration without environmental divergence.

---

## 3. Database Schema & Data Integrity (v2.0 Normalized)

The database layer was re-architected from legacy unnormalized tables (`founditem`, `missingperson`, `signup`) into a rigorous **Third Normal Form (3NF)** relational model in `database/schema.sql`:

### 3.1 Core Entity Models

1. **`users` Table:**
   - `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
   - `first_name`, `last_name`: `VARCHAR(100)`
   - `email`: `VARCHAR(191) UNIQUE` (Indexed)
   - `phone`: `VARCHAR(32)` (Resolves INT overflow on 11-digit Bangladesh phone numbers)
   - `password_hash`: `VARCHAR(255)` (Bcrypt cost 12)
   - `role`: `ENUM('user', 'moderator', 'admin') DEFAULT 'user'`
   - `status`: `ENUM('active', 'suspended', 'pending') DEFAULT 'active'`
   - `created_at`, `updated_at`: `DATETIME`

2. **`items` Table (Unified Lost & Found):**
   - `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
   - `user_id`: `BIGINT UNSIGNED FOREIGN KEY REFERENCES users(id) ON DELETE SET NULL`
   - `item_type`: `ENUM('lost', 'found') NOT NULL`
   - `title`: `VARCHAR(255) NOT NULL` (Indexed)
   - `category`: `VARCHAR(100) NOT NULL` (Indexed)
   - `description`: `TEXT`
   - `division`, `district`: `VARCHAR(100)` (Indexed)
   - `specific_location`, `surrounding_details`: `VARCHAR(255)`
   - `date_lost_or_found`: `DATE NOT NULL`
   - `picture`: `VARCHAR(255)` (Path to MIME-verified stored upload)
   - `contact_phone`, `contact_email`: `VARCHAR(100)`
   - `reward_amount`: `DECIMAL(10,2) DEFAULT 0.00`
   - `status`: `ENUM('pending', 'verified', 'resolved', 'flagged') DEFAULT 'verified'`
   - `created_at`: `DATETIME`

3. **`missing_persons` Table:**
   - `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
   - `user_id`: `BIGINT UNSIGNED FOREIGN KEY REFERENCES users(id)`
   - `full_name`: `VARCHAR(150) NOT NULL`
   - `nid`: `VARCHAR(32)` (Resolves INT overflow on 17-digit Bangladesh National ID cards)
   - `gd_number`: `VARCHAR(100)` (Bangladesh Police General Diary tracking code)
   - `last_seen_division`, `last_seen_district`: `VARCHAR(100)`
   - `status`: `ENUM('missing', 'sighted', 'reunited') DEFAULT 'missing'`

4. **`claims` Table (Ownership Verification):**
   - `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
   - `item_id`: `BIGINT UNSIGNED FOREIGN KEY REFERENCES items(id) ON DELETE CASCADE`
   - `user_id`: `BIGINT UNSIGNED FOREIGN KEY REFERENCES users(id) ON DELETE CASCADE`
   - `claimant_name`: `VARCHAR(150) NOT NULL`
   - `contact_phone`: `VARCHAR(32) NOT NULL`
   - `proof_description`: `TEXT NOT NULL`
   - `proof_image`: `VARCHAR(255)`
   - `status`: `ENUM('pending', 'approved', 'rejected') DEFAULT 'pending'`
   - `admin_notes`: `TEXT`

5. **`donations` Table (PCI-DSS Tokenized):**
   - `id`: `BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY`
   - `donor_name`, `donor_email`: `VARCHAR(191)`
   - `amount`: `DECIMAL(10,2) NOT NULL`
   - `stripe_payment_id`: `VARCHAR(255) NOT NULL UNIQUE` (Tokenized ID only — raw card numbers strictly prohibited)
   - `status`: `ENUM('succeeded', 'failed', 'refunded') DEFAULT 'succeeded'`

### 3.2 Backward Compatibility Views
For existing reporting scripts and legacy SQL queries, backward-compatible SQL views are established:
- `CREATE VIEW signup AS SELECT id, first_name AS Fname, last_name AS lname, email AS Email, phone AS Mobile, password_hash AS Password FROM users;`
- `CREATE VIEW founditem AS SELECT id, title AS item_name, category, description AS address2, specific_location AS address, district AS city, division AS state, date_lost_or_found AS date, picture, contact_phone AS phone, contact_email AS email FROM items WHERE item_type = 'found';`
- `CREATE VIEW missingperson AS SELECT id, full_name AS name, nid, contact_phone AS phone, emergency_phone, missing_date AS date, last_seen_division AS state, last_seen_district AS city, last_seen_details AS address, age, clothing_description AS address2, distinguishing_features AS landmark, gd_number, photo AS picture FROM missing_persons;`

---

## 4. Backend Service Layer & Domain Separation

Located under `src/Services/`, each domain service encapsulates business logic away from transport scripts:

### 4.1 `AuthService` (`src/Services/AuthService.php`)
- **Password Security:** Uses `password_hash($password, PASSWORD_BCRYPT, ['cost' => 12])` and `password_verify()`.
- **Session Governance:** Regenerates session IDs upon login (`session_regenerate_id(true)`) to prevent session fixation attacks.
- **RBAC:** Enforces role-based permissions (`user`, `moderator`, `admin`).

### 4.2 `ItemService` (`src/Services/ItemService.php`)
- **Query Bounding:** Parameterized multi-factor searching with pagination limits (`limit` capped at 100, `offset` validation).
- **CRUD Operations:** Atomic creation, modification, and deletion restricted to item owners or administrative staff.
- **Ownership Claims:** Manages peer-to-peer verification and claim statuses.

### 4.3 `MissingPersonService` (`src/Services/MissingPersonService.php`)
- **Urgent Broadcast:** Surfaces active missing alerts to the homepage and catalog with emergency GD metadata.
- **Police GD Verification:** Validates formatting of Thana police records.

### 4.4 `ChatbotService` (`src/Services/ChatbotService.php`)
- **Keyword Scoring Engine:** Matches citizen inquiries against bilingual keywords (English & Bengali transliteration e.g., *harie*, *paise*, *thana*, *gd*).
- **Emergency Escalation:** Routes critical distress questions to national 999 hotline advice.

### 4.5 `PaymentService` (`src/Services/PaymentService.php`)
- **PCI-DSS Compliance:** Validates Stripe payment tokens and charges without ever handling or persisting raw PAN or CVV data.

---

## 5. Security Architecture & Threat Model

The application enforces a **Zero-Trust Input Architecture**:

1. **SQL Injection Defense:** 100% of queries in both PHP PDO and SQLite fallback use parameterized prepared statements (`$stmt->prepare()` with bounded parameters). Dynamic string concatenation in SQL queries is strictly prohibited.
2. **Cross-Site Scripting (XSS):** All dynamic output rendered into HTML attributes or text nodes is escaped via `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')`.
3. **Cross-Site Request Forgery (CSRF):** State-changing actions verify a cryptographically secure token stored in `$_SESSION['csrf_token']` generated via `bin2hex(random_bytes(32))`.
4. **File Upload Safety:** Handled by `App\Utils\Security::handleUpload()`:
   - File extension whitelisting (`['jpg', 'jpeg', 'png', 'webp', 'gif']`).
   - Server-side binary MIME inspection via `finfo_file(FILEINFO_MIME_TYPE)`.
   - File size ceiling (capped at 5MB).
   - Filename sanitization with randomized prefixing to prevent directory traversal or script execution.
5. **Session Cookie Security:**
   - `HttpOnly: true` (prevents JavaScript token extraction).
   - `SameSite: Lax` (mitigates CSRF).
   - `Secure: true` (enforced when HTTPS is detected).

---

## 6. Frontend Architecture & Design Token System

The frontend is constructed using a high-performance **Vanilla CSS Design System** (`css/design-system.css`) adhering to WCAG 2.2 AA accessibility standards.

### 6.1 Design Tokens

```css
:root {
  /* Color Palette */
  --bg-canvas: #07090e;
  --bg-surface: rgba(15, 23, 42, 0.7);
  --bg-surface-elevated: #0f172a;
  
  --color-emerald-400: #34d399;
  --color-emerald-500: #10b981;
  --color-rose-500: #f43f5e;
  --color-amber-500: #f59e0b;
  --color-indigo-500: #6366f1;

  /* Typography */
  --font-sans: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
  --font-mono: 'JetBrains Mono', 'Fira Code', monospace;

  /* Elevation & Glassmorphism */
  --glass-bg: rgba(15, 23, 42, 0.75);
  --glass-border: rgba(255, 255, 255, 0.08);
  --glass-blur: blur(16px);
  --shadow-glow-emerald: 0 0 24px -4px rgba(16, 185, 129, 0.3);

  /* Spacing Scale */
  --space-1: 0.25rem;
  --space-2: 0.5rem;
  --space-3: 0.75rem;
  --space-4: 1rem;
  --space-6: 1.5rem;
  --space-8: 2rem;
  --space-12: 3rem;

  /* Radii */
  --radius-sm: 6px;
  --radius-md: 10px;
  --radius-lg: 16px;
  --radius-xl: 24px;
}
```

### 6.2 Key Interactive Innovations
- **Global Command Palette (`Ctrl+K` / `Cmd+K`):** Instant keyboard-driven navigation across catalog, report workflows, missing persons registry, and admin tools.
- **Multi-Factor Catalog Filtering:** Real-time client & server filtering by Type (`All`, `Lost`, `Found`), Category (`Electronics`, `Documents`, `Pets`, `Wallets`), Division (`Dhaka`, `Chattogram`, `Sylhet`, etc.), and search query.
- **Ownership Verification Modal:** Allows claimants to submit structured ownership proof (IMEI numbers, unique marks, purchase receipts) directly to the moderation queue.
- **Responsive Navigation Drawer:** Sliding touch-friendly drawer ensuring 100% usability across mobile (375px), tablet (768px), and widescreen desktop (1440px+).
