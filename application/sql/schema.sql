-- 1. Master Templates Table
CREATE TABLE IF NOT EXISTS tb_templates (
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
CREATE TABLE IF NOT EXISTS tb_sk_archives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_surat VARCHAR(100),
    template_id INT,
    
    -- Save the USER INPUT DATA as JSON, not the final HTML
    input_data_json LONGTEXT, 
    
    generated_file_path VARCHAR(255),
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
