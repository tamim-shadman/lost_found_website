# TESTING AND DEPLOYMENT GUIDE
**Lost & Found Bangladesh (L&F BD) — Nationwide Community Recovery Platform**
*Version 2.0.0 — Production Runbook & Operations Manual*

---

## 1. Verified Verification Commands

All commands below have been tested and verified in the workspace:

### 1.1 Local Development Server
Starts the autonomous dual-runtime server (supports both static/interactive HTML and mock REST APIs):
```bash
npm run dev
# Or directly:
node dev-server.mjs
```
- Listens on `http://localhost:8000`.
- Supports `/api/health.php`, `/api/auth.php`, `/api/items.php`, `/api/missing.php`, `/api/chatbot.php`, `/api/admin.php`, `/action.php`.

### 1.2 Comprehensive Verification Suite
Runs the 34-assertion end-to-end integration test suite:
```bash
npm test
# Or directly:
node test/verify.mjs
```
- Tests health endpoints, static routes, auth flows (positive, negative, duplicates), items CRUD, missing alerts, chatbot guidance, and admin claims resolution.

### 1.3 Production Build Validation
Validates that assets, schemas, and configurations are ready:
```bash
npm run build
```

---

## 2. Test Coverage & Assertion Inventory

The verification suite (`test/verify.mjs`) contains 34 verified assertions:

| Category | Assertions | Description | Result |
|---|---|---|---|
| **Infrastructure** | 1 | `GET /api/health.php` status, version, and DB driver check | `PASS` |
| **Route Delivery** | 10 | Delivery of all 10 core pages (`/`, `recentposts`, `submit_lostitems`, `missing_person`, `chatbot`, `payment`, `login`, `signup`, `User_profile`, `admin-dashboard`) | `PASS` |
| **Telemetry** | 1 | `GET /api/stats.php` verification of metric types | `PASS` |
| **Authentication** | 6 | Session check (`me`), valid login, 401 invalid password rejection, new citizen registration, 400 duplicate email rejection, profile metadata update | `PASS` |
| **Items Management**| 7 | List items, query filter, division filter, create item, get single item by ID, submit claim, delete item | `PASS` |
| **Missing Persons** | 2 | List missing persons, create emergency missing alert with GD number | `PASS` |
| **AI Assistant** | 2 | Lost item workflow guidance, police GD emergency hotline guidance | `PASS` |
| **Moderation Desk** | 5 | Dashboard metrics, list users, update user status, list claims, resolve claim with notes | `PASS` |

---

## 3. Environment Configuration (`.env.example`)

Create a `.env` file in the project root:

```ini
# Application Configuration
APP_NAME="Lost & Found Bangladesh"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lostfoundbd.com

# Database Connection (MySQL / MariaDB)
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=l_fbd
DB_USER=root
DB_PASS=your_strong_database_password
DB_CHARSET=utf8mb4

# SQLite Local Fallback (Used automatically if MySQL is unreachable)
SQLITE_PATH=database/local_dev.sqlite

# Security & Session Settings
SESSION_SECRET=e7b4c91a3f5d8e20b64791a82c4f10928e7d5a3b
CSRF_SECRET=a82d4f901c3e5b7a9f20e4d6c8b1a3e5f7a9c1d3
SESSION_LIFETIME=86400
SECURE_COOKIES=true

# Stripe Tokenized Donations (PCI-DSS Compliant)
STRIPE_PUBLIC_KEY=pk_live_your_stripe_public_key
STRIPE_SECRET_KEY=sk_live_your_stripe_secret_key

# Contact & Emergency Hotlines
EMERGENCY_POLICE_HOTLINE=999
ADMIN_CONTACT_EMAIL=support@lostfoundbd.com
```

---

## 4. Production Deployment Architectures

### Option A: Apache 2.4+ (Standard cPanel / XAMPP)

1. Ensure `mod_rewrite` and `mod_headers` are enabled.
2. Place the following `.htaccess` file in the web root:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /

    # Enforce HTTPS
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

    # Block sensitive files
    RewriteRule ^(\.env|\.git|composer\.|package\.) - [F,L]
    RewriteRule ^(database/.*\.sqlite|database/.*\.sql) - [F,L]

    # Route clean URLs to PHP files if extension omitted
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME}\.php -f
    RewriteRule ^(.*)$ $1.php [L]
</IfModule>

<IfModule mod_headers.c>
    Header always set X-Content-Type-Options "nosniff"
    Header always set X-Frame-Options "SAMEORIGIN"
    Header always set X-XSS-Protection "1; mode=block"
    Header always set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
```

### Option B: Nginx + PHP-FPM 8.2+

```nginx
server {
    listen 80;
    server_name lostfoundbd.com www.lostfoundbd.com;
    return 301 https://$host$request_uri;
}

server {
    listen 443 ssl http2;
    server_name lostfoundbd.com www.lostfoundbd.com;

    root /var/www/lost_found_website;
    index index.php index.html;

    ssl_certificate /etc/letsencrypt/live/lostfoundbd.com/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/lostfoundbd.com/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    # Security headers
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;

    # Block hidden files and databases
    location ~ /\.(env|git) {
        deny all;
    }
    location ~ ^/database/ {
        deny all;
    }

    # Static assets caching
    location ~* \.(css|js|jpg|jpeg|png|gif|ico|webp|svg)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    # Pass PHP scripts to FastCGI
    location ~ \.php$ {
        include fastcgi_params;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param HTTP_PROXY "";
    }

    location / {
        try_files $uri $uri/ $uri.php?$args;
    }
}
```

---

## 5. Database Setup & Migration Runbook

1. **Initial Migration:**
   Execute `database/schema.sql` against your target MySQL database:
   ```bash
   mysql -u root -p l_fbd < database/schema.sql
   ```
2. **Backward Compatibility Check:**
   Verify that views `signup`, `founditem`, and `missingperson` exist:
   ```sql
   SHOW FULL TABLES IN l_fbd WHERE TABLE_TYPE LIKE 'VIEW';
   ```
3. **Automated SQLite Fallback:**
   If MySQL is temporarily unavailable, `App\Config\Database` automatically initializes and connects to `database/local_dev.sqlite`, ensuring the platform never crashes during database downtime.

---

## 6. Health & Troubleshooting Runbook

- **Health Endpoint:** Probe `https://lostfoundbd.com/api/health.php`.
- **Expected Health Response:**
  ```json
  {
    "status": "healthy",
    "version": "2.0.0",
    "environment": "production",
    "timestamp": "2026-10-18T13:30:00+06:00",
    "database": {
      "status": "connected",
      "driver": "mysql"
    },
    "latency_ms": 2.14
  }
  ```
- **If Database Reports Disconnected:**
  1. Inspect MySQL service: `systemctl status mysql`.
  2. Verify credentials in `.env`.
  3. Check PHP PDO driver: `php -m | grep pdo_mysql`.
