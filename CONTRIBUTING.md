# Contributing to Lost & Found Bangladesh

Thank you for your interest in contributing to **Lost & Found Bangladesh (L&F BD)**! This is a nationwide civic public safety platform dedicated to reuniting lost belongings, securing citizen identity credentials, and broadcasting police-coordinated missing person alerts.

To maintain code craftsmanship, reliability, and security, please review the following contribution guidelines before submitting pull requests.

---

## Code of Conduct

This project adheres to the [Contributor Covenant](CODE_OF_CONDUCT.md). By participating, you are expected to uphold this code of conduct. Please report unacceptable behavior to `moderation@lostfoundbd.org`.

---

## Development Workflow

### 1. Fork & Clone
1. Fork the repository on GitHub: `https://github.com/tamim-shadman/lost_found_website`
2. Clone your fork locally:
   ```bash
   git clone https://github.com/<your-username>/lost_found_website.git
   cd lost_found_website
   ```

### 2. Branching Convention
Create a descriptive topic branch off `main`:
* Features: `feat/smart-match-fuzzy-search`
* Bug Fixes: `fix/claim-modal-focus-trap`
* Performance: `perf/image-webp-compression`
* Documentation: `docs/api-endpoints-reference`

### 3. Setting Up Local Environment
The application supports both a fast zero-dependency developer environment and full production LEMP/LAMP stacks:

* **Quickstart Dev Environment (Zero Configuration):**
  Requires Node.js 18+:
  ```bash
  npm start
  # Spawns server at http://localhost:8000 with auto-seeded SQLite database
  ```

* **Full PHP + MySQL Environment:**
  Requires PHP 8.2+ and MySQL/MariaDB:
  ```bash
  cp .env.example .env
  # Update DB credentials in .env
  mysql -u root -p < database/schema.sql
  php -S localhost:8000
  ```

### 4. Running Verification Tests
All PRs must pass the comprehensive end-to-end test suite before merge:
```bash
npm test
# Or directly: node test/verify.mjs
```
The suite verifies:
* Health and environment probes
* All UI routes (200 OK & content rendering)
* Authentication lifecycle & negative credential checks
* Item CRUD, category filters, and claim duplicate rejection
* Missing persons GD alerts and sighting dispatch
* Dual-mode Smart Match Engine similarity telemetry
* QR Asset Shield generation & anonymous return routing
* Stripe tokenized payment simulation & boundary validations

---

## Code Craftsmanship & Standards

### PHP Backend
* Adhere to **PSR-12** coding style.
* Strict typing and prepared statements (`PDO`) are mandatory for **all** database interactions.
* Never expose raw SQL queries or unescaped user input.
* Maintain the modular service architecture in `src/Services/`.

### Frontend & Styling
* **Design System First:** Use predefined CSS custom properties from [`css/design-system.css`](file:///h:/Projects/lost_found_website/css/design-system.css). Do not introduce ad-hoc utility frameworks or TailwindCSS without explicit architectural approval.
* **Dual-Theme Fidelity:** All new UI components must be styled and tested in both **Obsidian Dark Mode** and **Clean Slate Light Mode**.
* **Accessibility (WCAG 2.2 AA):**
  * All interactive elements must have unique `id` attributes and appropriate `aria-*` tags.
  * Maintain minimum 4.5:1 text-to-background contrast ratios.
  * Form inputs must include matching `<label>` elements.
* **Performance:** Keep assets lightweight. No heavy third-party bundle dependencies.

---

## Submitting a Pull Request

1. Commit your changes with clear, semantic commit messages:
   ```bash
   git commit -m "feat(matching): add phonetic similarity ranking to smart match"
   ```
2. Push to your fork:
   ```bash
   git push origin feat/your-feature-name
   ```
3. Open a Pull Request targeting `main`.
4. Fill out the Pull Request template completely, including:
   * Description of changes
   * Test results (`50 passed, 0 failed`)
   * Screenshots / video recordings for UI changes
5. Wait for automated CI checks to pass and request a code review.

Thank you for helping keep Bangladesh safe and connected! 🇧🇩
