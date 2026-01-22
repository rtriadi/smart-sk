# Complete Enterprise Redesign (Phase 2) Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task.

**Goal:** Complete the "Enterprise Professional" redesign by styling the missing pages (Login, Archives, Editor Header) and adding a prominent "Create SK" workflow.

**Architecture:**
- **Layout:** Continue using `enterprise_layout.php`.
- **Components:**
    - **Global Sidebar:** Add "Buat SK" (Create SK) primary button.
    - **Modal:** Create a reusable `TemplateSelectorModal` (Vue/HTML) to handle the "Create SK" flow.
    - **Login:** Redesign to a modern "Split Screen" layout.
    - **Archives:** Redesign to High-Density Table.

**Tech Stack:** PHP (CI3), Tailwind CSS, Vue.js.

---

### Task 1: "Create SK" Sidebar Button & Template Selector

**Files:**
- Modify: `application/views/layout/enterprise_layout.php`
- Modify: `application/controllers/Sk_editor.php`
- Create: `application/views/sk_editor/template_selector_modal.php` (or inline in layout)

**Step 1: Sidebar Button**
In `enterprise_layout.php`, add a large, teal "Buat SK Baru" button at the top of the sidebar.

**Step 2: Template Selection Modal**
Since "Create" requires a template ID, the button must open a modal.
- Create a simple Modal (hidden by default) listing all available templates.
- Use a `select` or a grid of cards.
- "Lanjut" (Continue) button redirects to `sk_editor/create/{id}`.
- *Data Source:* We need `$templates` available globally in the layout.
    - *Refactor:* The Controller currently passes `$templates` only to specific views.
    - *Fix:* Update `Sk_editor` constructor or layout loader to ensuring `$templates` is always available? Or use AJAX to fetch them when modal opens. **Decision: AJAX Fetch** to keep layout lightweight.

**Step 3: Implement AJAX Fetch**
Add JS to the layout: `fetch('sk_editor/api_get_templates')`.
Add `api_get_templates` to Controller.

---

### Task 2: Archives List Redesign

**Files:**
- Modify: `application/views/sk_editor/archive_view.php`

**Step 1: Remove Shell**
Strip `<html>`, `<body>`.

**Step 2: Enterprise Table**
Copy the table style from `manage_view.php` (High density, uppercase headers).
Columns: No. Surat, Template, Date, Status (Draft/Final), Actions.

**Step 3: Actions**
- Edit (Pen)
- PDF (File-pdf)
- Clone (Copy)
- Delete (Trash)

---

### Task 3: Login Page Redesign

**Files:**
- Modify: `application/views/login.php`

**Step 1: Modern Layout**
- **Split Screen:** Left side (Navy 900) with Logo/Branding. Right side (White) with Form.
- **Typography:** Inter font.
- **Form:** Clean inputs with floating labels or simple stack. Teal primary button.

---

### Task 4: Editor Header Polish

**Files:**
- Modify: `application/views/sk_editor/editor_view.php`

**Step 1: Header Consistency**
The Editor is full screen, but its header should match the Dashboard's aesthetic (Navy/Teal).
- Ensure the "Back" button is clear (`< Dashboard`).
- Ensure the "Save" and "Export" buttons use the standard Teal/Green classes.

**Step 2: Remove Legacy CSS**
Check if any `style` blocks are overriding Tailwind defaults negatively.

