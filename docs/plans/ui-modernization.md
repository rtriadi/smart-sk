# UI Modernization & Logic Cleanup Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task.

**Goal:** Remove "Kop Surat" functionality globally and redesign the "Create/Edit Template" views to use a unified "IDE-style" header, eliminating disjointed forms.

**Architecture:**
- **Layout:** Unified Header (Inputs + Actions) + Full-screen Editor.
- **Logic:** Strip "Kop Surat" logic from Vue and PHP views.

**Tech Stack:** PHP (CI3), Vue.js, Tailwind CSS.

---

### Task 1: Redesign Template Builder Header (IDE Style)

**Files:**
- Modify: `application/views/templates/create_view.php`
- Modify: `application/views/templates/edit_view.php`

**Step 1: Merge Form into Header**
Move the "Nama SK", "Kategori", and "Pola Nomor" inputs *inside* the top Navy header bar.
- Style: Transparent inputs with bottom border (`border-b border-slate-600 focus:border-teal-500`) to look like a file title.
- Layout: Flexbox row. `[Back] [Title Input] [Category Select] [Pattern Input] [Save Button]`.

**Step 2: Remove Old Form Container**
Delete the `grid-cols-3` white container that sits above the editor.

**Step 3: Ensure Vue Sync**
Ensure the inputs still model to `form.nama_sk` etc. so the existing save logic works.

---

### Task 2: Remove Kop Surat (Smart Editor & Settings)

**Files:**
- Modify: `application/views/sk_editor/settings_view.php`
- Modify: `application/views/sk_editor/editor_view.php`
- Modify: `assets/js/sk_editor_vue.js`

**Step 1: Clean Settings View**
Remove the "Kop Surat" section (Show Toggle, Inputs, Logo Upload) from `settings_view.php`.

**Step 2: Clean Editor View**
Remove "Tampilkan Kop Surat" toggle and logic from `editor_view.php`.

**Step 3: Clean Vue Logic**
- In `sk_editor_vue.js`:
    - Remove `showKop` from `globalSettings`.
    - Update `previewHtml` computed property to **STOP** injecting the Kop HTML.
    - Remove the code that replaces `{{globalSettings.kopLogo}}` etc.

---

### Task 3: Smart Editor Sidebar Polish

**Files:**
- Modify: `application/views/sk_editor/editor_view.php`

**Step 1: Modernize Form**
- Remove "Accordion" style if it feels cluttered. Use simple bold headers `text-xs uppercase text-slate-500 mb-2`.
- Ensure inputs are consistent with the new Navy/Teal theme (Task 1).

