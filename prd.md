# PRODUCT REQUIREMENTS DOCUMENT (PRD)
# SK Generator Smart Editor (Universal Engine)

**Project Name:** SK Generator Smart Editor - Pengadilan Agama Gorontalo
**Target Users:** Sub Bagian Kepegawaian & Ortala
**Tech Stack:** CodeIgniter 3 (HMVC Recommended), MySQL, jQuery, DOMPDF/MPDF
**Status:** Final Specification for Development
**Author:** PRD Consultant Expert (Faiz Intifada)

---

# 1. EXECUTIVE SUMMARY (One Pager)

## 1.1 Overview
The "SK Generator Smart Editor" is a web-based document automation platform designed to replace manual Word processing for official decrees (Surat Keputusan). Unlike standard form-fillers, this system uses a **Dynamic JSON-to-HTML Engine**, allowing the "Kepegawaian & Ortala" team to generate highly variable document formats—ranging from simple Personnel Decrees to complex Organizational Decrees with dynamic tables—without requiring code changes for every new template.

## 1.2 Problem Statement
* **Static Limitation:** Hardcoding PHP views for every new SK type is unscalable and slow.
* **Complex Formatting:** Organizational SKs (Ortala) contain complex tables and nested lists (e.g., Team Structures, Performance Weights) that break easily in standard editors.
* **Data Integrity:** Manual copy-pasting leads to errors in NIP, Names, and Dates.

## 1.3 Objectives
1.  **100% Dynamic Template Support:** Handle any SK format (Text-heavy or Table-heavy) via Database configuration.
2.  **Smart Sidebar:** Input forms are generated automatically based on the selected template's JSON config.
3.  **Complex Attachment Handling:** Capable of rendering intricate tables (e.g., "Lampiran Penilaian Kinerja") using a hybrid Rich-Text block system.

## 1.4 Persona
* **Kasubbag Kepegawaian & Ortala:** Needs a tool that ensures legal formatting (Tata Naskah Dinas) is perfect, while offering the flexibility to create custom committees or assessment teams dynamically.

---

# 2. FUNCTIONAL SPECIFICATIONS

## 2.1 Core Feature: The Dynamic Sidebar Engine
Instead of hardcoding inputs in HTML, the system reads a JSON configuration to build the form.

* **Logic:** `GET template_id` -> `READ form_config (JSON)` -> `RENDER Input Fields`.
* **Input Types Supported:**
    * `text`, `textarea`, `date`, `number`, `select` (Dropdown).
    * `richtext` (Mini Summernote/CKEditor for "Menimbang/Mengingat").
    * `repeater` (Dynamic "Add Row" for Lists/Legal Basis).
    * `table_block` (Special input to inject pre-formatted HTML tables for Attachments).

## 2.2 Core Feature: The Live Preview Canvas
* **WYSIWYG:** A split-screen view. Left = Input Form, Right = A4 Paper Preview.
* **Real-time Binding:** JavaScript listens to `input` events on the sidebar and updates the DOM in the Canvas via `id` matching or data-binding attributes.

## 2.3 Feature: Master Data Integration
* **NIP Lookup:** When user types NIP, system fetches `Nama`, `Pangkat`, `Jabatan` from `tb_pegawai` via AJAX and auto-fills the corresponding `{{placeholders}}`.

## 2.4 Feature: Output & Archiving
* **PDF Generation:** Server-side rendering using `dompdf` or `mpdf`.
* **Word Export:** Conversion using `PHPWord` (with disclaimer on complex table layouts).
* **Searchable Archive:** Stores the **JSON Data** used to generate the SK, allowing exact re-creation and deep searching.

---

# 3. TECHNICAL BLUEPRINT (Logic & Schema)

## 3.1 Database Schema
This is the heart of the dynamic system.

```sql
-- 1. Master Templates Table
CREATE TABLE tb_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sk VARCHAR(100),       -- e.g., "SK Kenaikan Pangkat", "SK Tim Kerja"
    kategori ENUM('kepegawaian', 'ortala'),
    nomor_pattern VARCHAR(100), -- e.g., "W26-A/SK.OT1.6/{bulan}/{tahun}"
    
    -- THE HTML CORE: Contains HTML with {{variable}} placeholders
    html_pattern LONGTEXT, 
    
    -- THE LOGIC CORE: Defines sidebar inputs in JSON
    form_config LONGTEXT, 
    
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Archives Table
CREATE TABLE tb_sk_archives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_surat VARCHAR(100),
    template_id INT,
    
    -- Save the USER INPUT DATA as JSON, not the final HTML
    input_data_json LONGTEXT, 
    
    generated_file_path VARCHAR(255),
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

3.2 Logic 1: JSON Config Structure (form_config)
Save this structure in tb_templates.form_config.

Example for Complex SK (Ortala):

[
  {
    "section": "Header Info",
    "fields": [
      { "label": "Nomor Surat", "variable": "no_sk", "type": "text", "auto_gen": true },
      { "label": "Tentang", "variable": "judul_sk", "type": "textarea" }
    ]
  },
  {
    "section": "Konsiderans (Menimbang)",
    "fields": [
      { 
        "label": "Poin Menimbang", 
        "variable": "list_menimbang", 
        "type": "repeater", 
        "fields": [ { "type": "textarea", "rows": 2 } ]
      }
    ]
  },
  {
    "section": "Lampiran (Tabel Kompleks)",
    "fields": [
      { 
        "label": "Isi Lampiran", 
        "variable": "konten_lampiran", 
        "type": "richtext_block", 
        "default_content": "<table>...template tabel...</table>" 
      }
    ]
  }
]

3.3 Logic 2: HTML Pattern Structure (html_pattern)
Save this in tb_templates.html_pattern. Note the Handlebars-style logic ({{#each}}) which needs to be parsed by your JS or PHP engine.

<div class="a4-page">
    <div class="header-kop">
        <img src="assets/img/kop_pta_gorontalo.png" style="width:100%">
    </div>

    <div class="judul-sk">
        <h3>KEPUTUSAN KETUA PENGADILAN TINGGI AGAMA GORONTALO</h3>
        <p>NOMOR: {{no_sk}}</p>
        <p>TENTANG</p>
        <p>{{judul_sk}}</p>
    </div>

    <div class="konsiderans">
        <table>
            <tr>
                <td width="100">Menimbang</td>
                <td>:</td>
                <td>
                    <ol type="a">
                        {{#each list_menimbang}}
                        <li>{{this}}</li>
                        {{/each}}
                    </ol>
                </td>
            </tr>
            <tr>
                <td width="100">Mengingat</td>
                <td>:</td>
                <td>
                    <ol type="1">
                        {{#each list_mengingat}}
                        <li>{{this}}</li>
                        {{/each}}
                    </ol>
                </td>
            </tr>
        </table>
    </div>

    <div class="diktum">
        <p>MEMUTUSKAN:</p>
        <p>Menetapkan: {{judul_sk}}</p>
    </div>
    
    <div class="ttd-area">
        <p>Ditetapkan di Gorontalo</p>
        <p>Pada tanggal {{tanggal_sk}}</p>
        <br>
        <p>KETUA,</p>
        <br><br>
        <p><b>{{nama_penandatangan}}</b></p>
        <p>NIP. {{nip_penandatangan}}</p>
    </div>
</div>

<div class="page-break"></div>
<div class="a4-page">
    <h3>LAMPIRAN KEPUTUSAN...</h3>
    
    <div class="lampiran-content">
        {{{konten_lampiran}}}
    </div>
</div>

3.4 Logic 3: Controller Logic Concept (PHP CodeIgniter)
Disclaimer: Pseudo-code for logic flow.

public function generate_preview() {
    $template_id = $this->input->post('id');
    $input_data  = $this->input->post('data'); // JSON from sidebar
    
    $template = $this->db->get_where('tb_templates', ['id' => $template_id])->row();
    $html = $template->html_pattern;
    
    // 1. Simple String Replace
    foreach ($input_data as $key => $value) {
        if (!is_array($value)) {
            $html = str_replace("{{" . $key . "}}", $value, $html);
        }
    }
    
    // 2. Complex Repeater Logic (Must use a Helper or Template Engine)
    // Recommendation: Use a JS-based Parser on Client Side for Live Preview
    // And use a PHP Parser (like Mustache.php) for PDF Generation
}

4. IMPLEMENTATION GUIDE
4.1 Step-by-Step Development
Week 1: Database & Admin CRUD

Set up the tb_templates table.

Build a simple "Template Manager" where you can paste the HTML code and the JSON config.

Week 2: The Builder Interface

Create the view: Left Sidebar (Empty) + Right Canvas (Empty).

Write JS Logic: Fetch JSON form_config -> Loop -> Append Inputs to Sidebar.

Week 3: The Binding Logic

Write JS Logic: Listen to Input Change -> Update Preview Canvas DOM.

Implement "Add Row" functionality for repeater inputs.

Week 4: Printing & Attachments

Integrate CKEditor for the Attachment section.

Configure dompdf to handle Page Breaks correctly (page-break-inside: avoid).

4.2 Handling Complex Tables (The "SK 138/2025" Case)
For tables with merged cells (Colspan/Rowspan):

Strategy: Do NOT try to build a "Rowspan/Colspan Generator" in the sidebar. It's too hard for users.

Solution: Provide HTML Snippets.

In the Template Database, store the Empty Table Structure (with borders and headers already set) inside the default_value of the Rich Text input.

When the user loads the SK, the table appears in the editor. They just type the numbers/text into the cells.

5. OPEN ISSUES & QA
Q: How to ensure PDF looks exactly like HTML?

A: Use CSS units in pt or mm, not px. Set the body width to 210mm (A4). Use a print-specific stylesheet (@media print).

Q: Formatting breaks on Word Export.

A: This is expected. Complex HTML tables often need manual adjustment in Word. Add a disclaimer: "Word Export is for raw editing; layout may shift. Use PDF for official distribution."