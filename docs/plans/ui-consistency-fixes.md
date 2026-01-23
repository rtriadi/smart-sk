# UI Consistency & UX Fixes Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task.

**Goal:** Fix UI inconsistencies (colored vs gray buttons) and integrate the "Create Template" view into the Enterprise layout.

**Architecture:**
- **Layout:** Wrap `Templates::create` and `edit` in `enterprise_layout` (Partial View pattern).
- **Style:** Enforce "Amber for Edit", "Red for Delete", "Teal for Primary" globally.
- **Workflow:** Add "Use Template" button to the Template Manager list.

**Tech Stack:** PHP (CI3), Tailwind CSS.

---

### Task 1: Wrap Template Builder in Enterprise Layout

**Files:**
- Modify: `application/controllers/Templates.php`
- Modify: `application/views/templates/create_view.php`
- Modify: `application/views/templates/edit_view.php`

**Step 1: Update Controller**
Refactor `create()` and `edit()` in `Templates.php` to use the `$layout_data['page_content']` wrapper pattern used in `Sk_editor.php`.

**Step 2: Update Views**
- Remove `<html>`, `<body>`, `<head>` from `create_view.php` and `edit_view.php`.
- Remove the inline Navy/Teal config script (since the layout provides it).
- **CRITICAL:** Ensure the `assets/js/template_builder_vue.js` script is still loaded at the bottom.
- **CRITICAL:** Ensure the Vue app mount point `#template-builder-app` doesn't conflict with the outer layout (layout has `#app`? No, layout is PHP-based mostly). Layout has no Vue app root by default, so we are safe.

**Step 3: Restyle Header**
- The "Back" button and "Save" button are currently in a custom header.
- Move them to match the standard Page Header style (like in Dashboard/Settings).

---

### Task 2: Button Consistency Audit

**Files:**
- Modify: `application/views/templates/manage_view.php`
- Modify: `application/views/sk_editor/archive_view.php`

**Step 1: Template Manager Buttons**
- **Create New:** Ensure it's Teal Solid.
- **Action Column:**
    - Add **"Buat SK"** (Play/File-signature Icon) -> Link to `sk_editor/create/{id}`. Color: Teal/Emerald.
    - **Edit:** Change from Gray to Amber (`text-amber-500 hover:text-amber-600`).
    - **Delete:** Change from Gray to Red (`text-red-500 hover:text-red-600`).

**Step 2: Archive List Buttons**
- **Edit:** Amber.
- **Delete:** Red.
- **Print/Clone:** Indigo/Blue.

---

### Task 3: Form Styling Polish (Create Template)

**Files:**
- Modify: `application/views/templates/create_view.php`
- Modify: `application/views/templates/edit_view.php`

**Step 1: Input Styling**
- The "Nama SK" and "Kategori" inputs are likely raw.
- Apply the "Settings" tab style:
    - Label: `block text-sm font-medium text-gray-700 dark:text-gray-300`.
    - Input: `w-full rounded-md border-gray-300 ... focus:ring-teal-500`.
    - Layout: 2-column grid.

