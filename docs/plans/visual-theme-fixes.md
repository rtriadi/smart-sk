# Visual Theme Fixes Implementation Plan

> **For Claude:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development to implement this plan task-by-task.

**Goal:** Ensure the Template Builder (Create/Edit) and SK Editor views match the new Navy/Teal Enterprise theme.

**Architecture:**
- **Theme:** Navy (`#0f172a`), Slate (`#1e293b`), Teal (`#14b8a6`).
- **Components:** Vue.js templates in `template_builder_vue.js` and `editor_view.php`.

**Tech Stack:** Vue.js 3, Tailwind CSS.

---

### Task 1: Update Template Builder Theme

**Files:**
- Modify: `assets/js/template_builder_vue.js`

**Step 1: Header Styling**
- Current: `bg-gray-800 border-gray-700`.
- Target: `bg-slate-900 border-slate-800`.
- Badge: `bg-blue-900 text-blue-200` -> `bg-teal-900 text-teal-200 border-teal-700`.

**Step 2: Sidebar Styling**
- Current: `bg-gray-50 border-gray-200`.
- Target: `bg-white border-slate-200` (Clean look) OR `bg-slate-50`.
- Tabs: Active state `text-blue-600 border-blue-600` -> `text-teal-600 border-teal-600`.

**Step 3: Editor Background**
- Current: `bg-gray-100`.
- Target: `bg-slate-200` (Matches Archives/Dashboard content area).

---

### Task 2: Update SK Editor Theme

**Files:**
- Modify: `application/views/sk_editor/editor_view.php`

**Step 1: Sidebar Styling**
- Current: `bg-white dark:bg-gray-800`.
- Target: `bg-white dark:bg-slate-900` (Matches Global Sidebar).
- Header: `bg-white dark:bg-gray-900` -> `bg-slate-900` (Already fixed in previous turn? Double check).

**Step 2: Input Styling (Dynamic Form)**
- Inputs: `focus:border-indigo-500` -> `focus:border-teal-500`, `focus:ring-teal-500`.
- Checkboxes/Toggles: `peer-checked:bg-indigo-600` -> `peer-checked:bg-teal-600`.

**Step 3: Buttons**
- Verify all buttons use Teal/Amber/Red logic.

