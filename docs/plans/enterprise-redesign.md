# Enterprise Professional UI/UX Redesign Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task.

**Goal:** Transform the application into an "Enterprise Professional" dashboard with a fixed sidebar, high-density layout, and professional "Deep Navy/Teal" aesthetic.

**Architecture:**
- **Layout:** Master layout (`enterprise_layout.php`) using Tailwind CSS Grid/Flexbox.
- **Components:** Modular view fragments for Sidebar, Header, and Dashboard Widgets.
- **Styling:** Tailwind configuration update (in-file or via CDN config object) to define the specific color palette.

**Tech Stack:** PHP (CodeIgniter 3), Vue.js 3, Tailwind CSS (CDN).

---

### Task 1: Enterprise Layout Foundation

**Files:**
- Create: `application/views/layout/enterprise_layout.php`
- Modify: `application/controllers/Sk_editor.php` (temporarily to test layout)

**Step 1: Define the Master Layout Template**
Create `enterprise_layout.php` with:
- **Tailwind Config:** Define `colors.navy` (#0f172a), `colors.teal` (#14b8a6).
- **Structure:**
    - `<div class="flex h-screen bg-slate-50">`
    - Sidebar (Fixed width `w-64`, bg-navy-900, text-white).
    - Main Area (`flex-1 flex flex-col overflow-hidden`).
        - Header (Height `h-16`, bg-white, border-b).
        - Content Scrollable (`flex-1 overflow-y-auto p-6`).

**Step 2: Implement Navigation Sidebar**
Add navigation links with FontAwesome icons:
- Dashboard (Home)
- Templates (File Alt)
- Archives (Archive)
- Settings (Cog)
- *Style:* `hover:bg-navy-800`, `text-slate-300`, `active:text-white`.

**Step 3: Implement Top Header**
Add:
- Breadcrumbs or Page Title.
- User Profile dropdown (Avatar + Name).
- Quick Action Button (optional).

**Step 4: Connect Controller**
Update `Sk_editor.php`'s `index()` function to load `sk_editor/dashboard` *inside* the new layout structure (pass `$content` view).
*Refactor Note:* CI3 usually handles layouts by loading header/footer views or a wrapper. We'll use the Wrapper approach: `$this->load->view('layout/enterprise_layout', $data);` where `$data['page_content']` is the sub-view path.

---

### Task 2: Dashboard Content Redesign

**Files:**
- Modify: `application/views/sk_editor/dashboard.php`

**Step 1: Stats Grid**
Replace existing cards with "Enterprise Stats":
- 4-column grid.
- Cards: "Total Templates", "Archives Created", "Active Users", "Recent Activity".
- Style: White bg, `border border-slate-200`, `shadow-sm` (no heavy drop shadows).
- Typography: Label uppercase small (`text-xs font-bold text-slate-500`), Value large (`text-2xl font-bold text-slate-800`).

**Step 2: Recent Archives Table**
Replace list/table with a high-density Data Table:
- Headers: `bg-slate-50 text-xs uppercase font-bold text-slate-500`.
- Rows: `border-b border-slate-100 hover:bg-slate-50`.
- Columns: No. Surat, Template Name, Date, Creator, Actions.
- Actions: Icon-only buttons (`text-slate-400 hover:text-teal-600`).

**Step 3: Quick Actions Panel**
Add a sidebar widget (right side of dashboard?) or top section:
- "Create New SK" (Primary Button).
- "Manage Templates" (Secondary).

---

### Task 3: Template List Redesign

**Files:**
- Modify: `application/views/templates/manage_view.php`

**Step 1: Apply Enterprise Layout**
Ensure this view fits into the `$page_content` slot.

**Step 2: Redesign List as Cards or Table?**
*Decision:* **Table** for Enterprise feel.
- Columns: Template Name, Category, Last Updated, Actions.
- Add "Filter/Search" bar at top (Input field + Category Dropdown).

**Step 3: Create Button**
Top-right "New Template" button (Teal solid).

---

### Task 4: Integration & Cleanup

**Files:**
- Modify: `application/controllers/Sk_editor.php`
- Modify: `application/controllers/Templates.php`

**Step 1: Update All Controllers**
Ensure `Templates`, `Settings`, and `Auth` (maybe) use the new layout logic.
*Helper:* Create a private `_render($view, $data)` method in the core controller or just duplicate the layout loading logic for now (CI3 doesn't have native layouts).

**Step 2: Remove Legacy Assets**
Remove old CSS/JS links if no longer used.

---

### Task 5: Design Polish (The "Wow" Factor)

**Files:**
- Modify: `application/views/layout/enterprise_layout.php`

**Step 1: Micro-interactions**
- Hover states on Sidebar (subtle fade).
- Transitions on buttons.

**Step 2: Typography Tuning**
- Force `font-family: 'Inter', sans-serif`.
- Tighten letter-spacing on headers.

**Step 3: Empty States**
- Add nice SVG illustrations for "No Archives" or "No Templates".

