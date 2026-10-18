# Security Policy & Responsible Disclosure

The Lost & Found Bangladesh (L&F BD) engineering team treats the security, integrity, and privacy of citizen identity data with the highest priority.

---

## Supported Versions

We provide active security patches and updates for the following versions:

| Version | Supported          |
| ------- | ------------------ |
| 2.x     | :white_check_mark: |
| < 2.0   | :x:                |

---

## Reporting a Vulnerability

If you believe you have discovered a security vulnerability in the platform:

1. **Do not create a public GitHub issue.**
2. Send a detailed report via encrypted email to:  
   **`security@lostfoundbd.org`**
3. Please include:
   * Description of the vulnerability and attack vector.
   * Step-by-step reproduction instructions or Proof-of-Concept (PoC).
   * Potential impact on user data or platform integrity.
   * Suggested remediation steps if known.

### Response Timelines
* **Initial Acknowledgement:** Within 24 hours.
* **Triage & Severity Assessment:** Within 72 hours.
* **Fix & Release Coordination:** Within 7 to 14 business days depending on severity.

---

## Platform Security Measures

* **Authentication & Credentials:** Passwords hashed with `Argon2id` / `Bcrypt` with cryptographic salt. Zero plaintext passwords.
* **SQL Injection Prevention:** 100% of database interactions run through PDO prepared statements with strict parameter binding.
* **XSS Defense:** Context-aware escaping and DOM sanitation across all user-generated fields.
* **Payment Data Isolation (PCI-DSS):** Zero raw card numbers, CVCs, or PINs touch our database servers. All transactions are client-tokenized via Stripe API.
* **File Upload Hardening:** Strict MIME-type validation via `finfo_file`, 8MB size limits, and randomized UUID naming to prevent path traversal or shell execution.
* **Citizen Privacy Protection:** Confidential phone numbers and National IDs (NID) are shielded behind authenticated moderator verification desks and are never exposed on public feeds.
