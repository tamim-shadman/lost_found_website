# DESIGN SYSTEM SPECIFICATION: LOST & FOUND BANGLADESH
**Version:** 3.0.0 "Phoenix Design System"  
**Design Reference:** Linear / Raycast / Vercel Modern Enterprise Aesthetics  
**Creative Director & Staff Frontend Architect**

---

## 1. Visual Concept & Emotional Tone

### A. The Visual Metaphor: "The Signal in the Dark"
Lost & Found deals with stressful, high-anxiety life events—losing irreplaceable belongings or a loved family member. The visual language must communicate **calm authority, absolute dependability, modern technological precision, and unwavering hope**.

- **Surface Philosophy:** Deep, layered obsidian panels (`#090d16`, `#0e131f`, `#141a28`) acting as a quiet, focused stage, punctuated by crisp hairline borders (`rgba(255, 255, 255, 0.08)`).
- **Accent Philosophy:**
  - **Electric Emerald (`#10b981` / `#34d399`):** Represents Recovery, Success, Hope, and Verified Status.
  - **Sunset Rose (`#f43f5e` / `#fb7185`):** Represents Active Loss, Urgent Alerts, and Police Hotlines.
  - **Hyper Indigo (`#6366f1` / `#818cf8`):** Represents Algorithmic Intelligence, Smart Search, and Technology.
  - **Warm Amber (`#f59e0b` / `#fbbf24`):** Represents Pending Moderation, Claim Verification, and Community Rewards.

---

## 2. Typography & Information Hierarchy

| Role | Font Family | Weights | Usage |
|---|---|---|---|
| **Display & Headings** | `Plus Jakarta Sans` | `600`, `700`, `800` | Section heroes, alert titles, modal headers |
| **Body & UI** | `Inter` | `400`, `500`, `600` | Descriptions, metadata, form inputs, buttons |
| **Code & Identifiers** | `JetBrains Mono` | `400`, `500`, `700` | GD numbers, Transaction IDs, Sighting codes, QR tags |

### Scale:
- `Display XXL`: `2.75rem` (44px) / Line height: `1.15` / Letter spacing: `-0.03em`
- `H1`: `2.0rem` (32px) / Line height: `1.2` / Letter spacing: `-0.02em`
- `H2`: `1.5rem` (24px) / Line height: `1.3` / Letter spacing: `-0.015em`
- `H3`: `1.25rem` (20px) / Line height: `1.35` / Letter spacing: `-0.01em`
- `Body Normal`: `0.95rem` (15.2px) / Line height: `1.6`
- `Body Small`: `0.85rem` (13.6px) / Line height: `1.5`
- `Caption / Badge`: `0.75rem` (12px) / Letter spacing: `0.04em` / Uppercase

---

## 3. Centralized Token Map

```css
:root {
  /* Surfaces - Obsidian Stealth (Dark) */
  --bg-canvas: #07090e;
  --bg-surface-base: #0c1017;
  --bg-surface-elevated: #121824;
  --bg-surface-overlay: rgba(14, 19, 29, 0.82);
  --bg-surface-card: rgba(18, 24, 36, 0.7);

  /* Accents */
  --accent-emerald: #10b981;
  --accent-emerald-light: #34d399;
  --accent-emerald-glow: rgba(16, 185, 129, 0.22);

  --accent-rose: #f43f5e;
  --accent-rose-light: #fb7185;
  --accent-rose-glow: rgba(244, 63, 94, 0.22);

  --accent-indigo: #6366f1;
  --accent-indigo-light: #818cf8;
  --accent-indigo-glow: rgba(99, 102, 241, 0.25);

  --accent-amber: #f59e0b;
  --accent-amber-light: #fbbf24;

  /* Text */
  --text-primary: #f8fafc;
  --text-secondary: #94a3b8;
  --text-muted: #64748b;
  --text-dim: #475569;

  /* Hairline Borders */
  --border-subtle: rgba(255, 255, 255, 0.08);
  --border-medium: rgba(255, 255, 255, 0.15);
  --border-strong: rgba(255, 255, 255, 0.25);
  --border-emerald: rgba(16, 185, 129, 0.4);
  --border-rose: rgba(244, 63, 94, 0.4);

  /* Elevation */
  --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.3);
  --shadow-md: 0 8px 24px rgba(0, 0, 0, 0.45);
  --shadow-lg: 0 20px 48px rgba(0, 0, 0, 0.6);
  --shadow-emerald: 0 0 30px var(--accent-emerald-glow);

  /* Geometry */
  --radius-sm: 8px;
  --radius-md: 12px;
  --radius-lg: 16px;
  --radius-xl: 22px;
  --radius-pill: 9999px;

  /* Timing */
  --anim-fast: 150ms cubic-bezier(0.16, 1, 0.3, 1);
  --anim-smooth: 250ms cubic-bezier(0.16, 1, 0.3, 1);
  --anim-spring: 380ms cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Light Theme Variables (Arctic Daylight) */
[data-theme="light"] {
  --bg-canvas: #f8fafc;
  --bg-surface-base: #ffffff;
  --bg-surface-elevated: #f1f5f9;
  --bg-surface-overlay: rgba(255, 255, 255, 0.88);
  --bg-surface-card: rgba(255, 255, 255, 0.85);

  --text-primary: #0f172a;
  --text-secondary: #475569;
  --text-muted: #64748b;
  --text-dim: #94a3b8;

  --border-subtle: rgba(15, 23, 42, 0.08);
  --border-medium: rgba(15, 23, 42, 0.16);
  --border-strong: rgba(15, 23, 42, 0.28);

  --shadow-sm: 0 2px 8px rgba(15, 23, 42, 0.06);
  --shadow-md: 0 8px 24px rgba(15, 23, 42, 0.08);
  --shadow-lg: 0 20px 48px rgba(15, 23, 42, 0.12);
}
```

---

## 4. Universal Shell & Component Conventions

### A. Global Omnibar & Command Palette (`Cmd+K`)
- **Visuals:** Floating centered modal (`max-width: 620px`) with high backdrop blur (`blur(24px)`).
- **Interactions:** Auto-focused input, keyboard arrow navigation (`Up`/`Down`), enter to execute, instant search matching across both routes and data items.

### B. High-Density Alert Cards & Data Tables
- **Cards:** Crisp cover thumbnail with aspect ratio 16:9, glowing status badge in top-left, Division chip in top-right, clean typography with high contrast, and direct interactive action buttons.
- **Table Mode:** Striped subtle rows, thumbnail avatar, title, category pill, division, date, reward chip, and quick-claim button.

### C. Printable Police Flyer `@media print` Layout
- High-contrast pure black and white styling, A4 page optimization, prominent emergency red banner for Bangladesh Police, large photo, bulleted physical descriptors, and instant verification QR code.
