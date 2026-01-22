# Visual Template Builder (Option A) Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task.

**Goal:** Transform the raw JSON/HTML template creation process into a user-friendly WYSIWYG editor where users can "Write & Tag" variables.

**Architecture:**
- **Frontend:** Implement a new `template_builder.js` module using Vue.js and TinyMCE.
- **Data Flow:**
    - User edits document in TinyMCE.
    - User selects text -> clicks "Add Variable".
    - System replaces text with `{{variable}}` token.
    - System adds variable definition to a reactive `form_config` array.
    - On Save, the system serializes the `form_config` to JSON and the `html_pattern` to the hidden input fields expected by the controller.
- **Backend:** Reuse existing `Templates.php` CRUD (no changes needed if we maintain the input field names).

**Tech Stack:** Vue.js 3 (CDN), TinyMCE 5 (CDN), Tailwind CSS.

---

### Task 1: Environment & UI Shell Prep

**Files:**
- Modify: `application/views/templates/create_view.php`
- Modify: `application/views/templates/edit_view.php`
- Create: `assets/js/template_builder_vue.js`

**Step 1: Create the Vue.js Application Shell**
Create `assets/js/template_builder_vue.js` that will mount to `#template-builder-app`.
It should have reactive state for:
- `docContent` (HTML string)
- `formConfig` (Array of objects)
- `activeVariable` (For editing)

**Step 2: Refactor `create_view.php`**
Replace the raw `textarea` inputs with the Vue.js mount point.
- Hide the original `html_pattern` and `form_config` textareas (keep them as hidden inputs for the form submission).
- Add the container `<div id="template-builder-app">`.
- Add script tag to load `assets/js/template_builder_vue.js`.

**Step 3: Refactor `edit_view.php`**
Apply the same changes as `create_view.php`, but ensure it initializes the Vue app with existing data (`$template->html_pattern`, `$template->form_config`).

**Step 4: Verify**
Open `/templates/create` and ensure the Vue app mounts (console log "Template Builder Mounted").

---

### Task 2: Implement TinyMCE with "Magic Variable" Button

**Files:**
- Modify: `assets/js/template_builder_vue.js`

**Step 1: Initialize TinyMCE**
Init TinyMCE on a textarea inside the Vue app.
Configure it to use the custom fonts (Bookman Old Style) to match the SK Editor.

**Step 2: Add Custom Toolbar Button**
Add a "✨ Make Variable" button to the TinyMCE toolbar.

**Step 3: Implement Selection Logic**
When "Make Variable" is clicked:
1. Get the selected text (e.g., "John Doe").
2. Open a Vue Modal asking for:
   - **Variable Name:** (Auto-slugify selection, e.g., `john_doe` -> `nama_pegawai`?) -> Let user edit.
   - **Label:** (Default to selection).
   - **Type:** Text, Date, Number, Select.

**Step 4: Token Replacement**
On confirmation:
1. Replace selection in Editor with `<span class="variable-token" data-var="var_name">{{var_name}}</span>` (or just `{{var_name}}` text).
   *Decision:* Use simple `{{var_name}}` text for compatibility with Mustache backend.
2. Add the variable definition to `formConfig` array.

---

### Task 3: Variable Management Sidebar

**Files:**
- Modify: `assets/js/template_builder_vue.js`
- Modify: `application/views/templates/create_view.php` (Structure)

**Step 1: Build the Sidebar**
Create a sidebar on the right of the editor.
Loop through `formConfig` and display cards for each variable.

**Step 2: Edit/Delete Variables**
- **Edit:** Click card -> Open Modal -> Update Label/Type/Width.
- **Delete:** Click X -> Remove from `formConfig` -> *Optional:* Scan content and warn if `{{var}}` still exists.

**Step 3: Sync to Hidden Inputs**
Create a `saveTemplate()` function that:
1. Gets content from TinyMCE -> `html_pattern` input.
2. Stringifies `formConfig` -> `form_config` input.
3. Submits the actual HTML form.

---

### Task 4: Layout & Typography Settings

**Files:**
- Modify: `assets/js/template_builder_vue.js`

**Step 1: Add "Page Setup" Tab**
In the sidebar, add a tab for "Layout".
Controls:
- Margins (Top, Bottom, Left, Right).
- Paper Size (A4, F4).
- Orientation.
- Font Size/Line Height.

**Step 2: Inject CSS**
Make the TinyMCE editor reflect these settings in real-time (using `content_style`).

**Step 3: Save to Config**
Save these settings into a reserved section of `formConfig` or a separate `layout_settings` field?
*Decision:* Save into `formConfig` as a special hidden field named `_global_settings` or similar, OR just rely on the existing `globalSettings` logic in `sk_editor_vue.js`.
*Refinement:* The user wants the template to define this. So we should save it as `defaults` in the `formConfig`.
Let's add a special entry in `formConfig` array: `{ type: 'settings', settings: { ... } }`.

---

### Task 5: User Verification Loop

**Step 1: Manual Test**
Create a "Surat Cuti" template using the new builder.
- Type text.
- Create "Nama", "Tanggal Mulai", "Tanggal Selesai" variables.
- Save.

**Step 2: End-User Test**
Go to `sk_editor/index`.
Open the new "Surat Cuti".
Verify the form appears correctly and the preview generates.

