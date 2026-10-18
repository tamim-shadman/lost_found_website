# RELEASE CANDIDATE AUDIT & EVIDENCE-BASED TASK LEDGER
**Lost & Found Bangladesh (L&F BD) — Nationwide Community Recovery Platform**
*Release Candidate Cycle: v2.1.0-RC1 | Lead Auditor & Release Manager*

---

## 1. Executive Summary & Defect Ledger

An independent, rigorous quality assurance and adversarial audit was conducted on the v2.0 codebase to uncover defects, edge-case vulnerabilities, duplicate mutation risks, and incomplete workflows that escaped earlier architectural passes.

| ID | Severity | Category | Title | Root Cause | Proposed Fix | Status |
|---|---|---|---|---|---|---|
| **RC-01** | `P1 - High` | Data Integrity / Workflows | Simulated donation completion in `payment.html` without persistence | `payment.html` used a simulated `setTimeout` without calling backend persistence | Create `api/donate.php`, wire to `PaymentService` and `dev-server.mjs`, display real receipt | `TESTED & BROWSER VERIFIED` |
| **RC-02** | `P1 - High` | Concurrency & Validation | Duplicate claim generation and missing proof validation | `ItemService::submitClaim` lacked duplicate pending claim prevention and whitespace validation | Enforce unique pending claim constraints, validate phone/proof, add double-click button lock | `TESTED & BROWSER VERIFIED` |
| **RC-03** | `P2 - Medium` | Functional Parity | Missing photo upload dropzone and preview in `submit_lostitems.html` | Client HTML omitted file dropzone present in PHP view | Add photo dropzone, instant `FileReader` preview, button loading lock, and non-negative reward validation | `TESTED & BROWSER VERIFIED` |
| **RC-04** | `P2 - Medium` | User Workflows | Dead "Sighted" button and missing age boundary validation in `missing_person.html` | "Sighted" button was a stub alert; backend lacked age validation | Add interactive Sighting Report modal and API recording; validate age bounds (0–125) | `TESTED & BROWSER VERIFIED` |
| **RC-05** | `P2 - Medium` | Resilience / Error Recovery | Silent network failure and missing fallback in `chatbot.html` | Fetch catch block silently hid typing indicator without message | Add polite offline/emergency fallback bubble and disable input during flight | `TESTED & BROWSER VERIFIED` |
| **RC-06** | `P3 - Low` | UX / Usability | Catalog search required manual button submission | Search input had no debounce event listener | Add 300ms debounced live filtering on catalog and missing persons search inputs | `TESTED & BROWSER VERIFIED` |

---

## 2. Detailed Defect Investigations & Reproduction Details

### Finding RC-01: Simulated Donation Completion Without Backend Persistence
- **Severity:** `P1 — High`
- **Component:** `payment.html`, `api/donate.php`, `src/Services/PaymentService.php`
- **Evidence & Reproduction:**
  1. Navigate to `http://localhost:8000/payment.html`.
  2. Fill in donor details and click "Contribute ৳1,000 BDT".
  3. Notice `handleDonationSubmit` triggered `setTimeout(() => alert(...), 800)` without making any HTTP request to the backend.
  4. Database table `donations` and admin metrics remained unchanged.
- **Root Cause:** A dedicated REST endpoint `/api/donate.php` was missing from the API gateway.
- **Corrective Action:**
  - Created [api/donate.php](file:///h:/Projects/lost_found_website/api/donate.php) using `PaymentService::processDonation()`.
  - Added `/api/donate.php` handler to [dev-server.mjs](file:///h:/Projects/lost_found_website/dev-server.mjs) with state persistence.
  - Updated [payment.html](file:///h:/Projects/lost_found_website/payment.html) to perform real asynchronous POST requests, lock the button against double submissions, and display a verified receipt modal with the server-generated transaction ID.
- **Verification Status:** `TESTED` & `BROWSER VERIFIED`

### Finding RC-02: Duplicate Claim Generation on Repeated Submissions
- **Severity:** `P1 — High`
- **Component:** `src/Services/ItemService.php`, `recentposts.html`, `dev-server.mjs`
- **Evidence & Reproduction:**
  1. Rapidly click "Submit for Verification" in the Claim Modal.
  2. Observe multiple identical claim records created for the same user and item.
  3. Submit empty whitespace strings for proof and phone; observer unvalidated entries created.
- **Root Cause:** Absence of button disable locking on the client and missing uniqueness/validation checks in `ItemService::submitClaim()`.
- **Corrective Action:**
  - Added validation for non-empty trimmed `claimant_name`, `contact_phone`, and `proof_description`.
  - Added active pending claim query check to prevent duplicate claims for the same item by the same user.
  - Updated [recentposts.html](file:///h:/Projects/lost_found_website/recentposts.html) to disable the submit button immediately upon click and display "Submitting Verification...".
- **Verification Status:** `TESTED` & `BROWSER VERIFIED`

### Finding RC-03: Missing Photo Upload Dropzone in `submit_lostitems.html`
- **Severity:** `P2 — Medium`
- **Component:** `submit_lostitems.html`
- **Evidence & Reproduction:**
  1. Inspect `submit_lostitems.html` vs `submit_lostitems.php`.
  2. Notice `submit_lostitems.php` contained a photo upload dropzone with preview, but `submit_lostitems.html` omitted the file input entirely.
- **Root Cause:** Incomplete synchronization between the PHP template and the standalone HTML view.
- **Corrective Action:**
  - Added upload dropzone with client-side image preview via `FileReader`.
  - Added button loading state with double-click locking.
  - Sanitized reward input to ensure non-negative numeric values (`min="0"`).
- **Verification Status:** `TESTED` & `BROWSER VERIFIED`

### Finding RC-04: Dead Sighting Action and Missing Age Boundary Validation
- **Severity:** `P2 — Medium`
- **Component:** `missing_person.html`, `src/Services/MissingPersonService.php`, `api/missing.php`
- **Evidence & Reproduction:**
  1. Click "Sighted" on any missing person card; observe generic browser `alert()` with zero server persistence.
  2. Submit age as `-5` or `999`; observe backend accepting invalid ages.
- **Root Cause:** Stub button handler in `missing_person.html`; unconstrained `age` typecast in `MissingPersonService.php`.
- **Corrective Action:**
  - Enforced server-side validation for age: `0 <= age <= 125`.
  - Implemented interactive Sighting Report modal and `action=report_sighting` in `api/missing.php` and `dev-server.mjs` that captures sighting landmark, reporter contact, and timestamp.
  - Added submission locking on the "Broadcast Missing Person Alert" form.
- **Verification Status:** `TESTED` & `BROWSER VERIFIED`

### Finding RC-05: Chatbot Silent Network Failure on Slow or Broken Connection
- **Severity:** `P2 — Medium`
- **Component:** `chatbot.html`
- **Evidence & Reproduction:**
  1. Throttled/failed network request to `api/chatbot.php`.
  2. Observed `typingIndicator` disappearing, leaving the user with no feedback.
- **Root Cause:** Empty catch block in `chatbot.html` without fallback message.
- **Corrective Action:**
  - Added an emergency fallback message informing the user of the network interruption and providing the 999 hotline.
  - Added button lock and input disabling during request flight.
- **Verification Status:** `TESTED` & `BROWSER VERIFIED`

### Finding RC-06: Manual Filter Requirement on Catalog Search
- **Severity:** `P3 — Low`
- **Component:** `recentposts.html`, `missing_person.html`
- **Evidence & Reproduction:**
  1. Typing in search inputs did not trigger real-time updates until the user manually clicked "Filter" or pressed Enter.
- **Corrective Action:**
  - Added 300ms debounced input listeners on `#searchFilterInput` and `#searchMissingInput`.
- **Verification Status:** `TESTED` & `BROWSER VERIFIED`
