-- 1. Users Table
CREATE TABLE IF NOT EXISTS tb_users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL, -- SHA1 hash
    nama_pengguna VARCHAR(100),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Default Admin (Password: admin)
INSERT INTO tb_users (username, password, nama_pengguna) 
SELECT 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrator'
WHERE NOT EXISTS (SELECT 1 FROM tb_users WHERE username = 'admin');

-- 2. Master Pejabat Table
CREATE TABLE IF NOT EXISTS tb_pejabat (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(150) NOT NULL,
    nip VARCHAR(50),
    jabatan VARCHAR(150),
    status VARCHAR(20) DEFAULT 'aktif', -- aktif, non-aktif
    is_default TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 3. Categories Table
CREATE TABLE IF NOT EXISTS tb_sk_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. Master Templates Table
CREATE TABLE IF NOT EXISTS tb_templates (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama_sk VARCHAR(100),
    kategori ENUM('kepegawaian', 'ortala'), -- Legacy enum, but maybe we should link to tb_sk_categories? 
    -- The controller uses the ID from tb_sk_categories as 'kategori' input? 
    -- Let's check Templates.php. It saves 'kategori' input.
    -- If input is from dropdown of categories, it stores the ID.
    -- But the schema had ENUM. This might be a mismatch in original code.
    -- Let's stick to the original schema for this table to avoid breaking legacy queries if any.
    -- Actually, allow VARCHAR to be safe if it stores ID or Name.
    -- Original schema: kategori ENUM('kepegawaian', 'ortala')
    -- Let's modify it to VARCHAR(50) to support dynamic categories from tb_sk_categories.
    nomor_pattern VARCHAR(100),
    html_pattern LONGTEXT,
    form_config LONGTEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Alter tb_templates to change ENUM to VARCHAR if it exists
SET @exist := (SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = DATABASE() AND table_name = 'tb_templates' AND column_name = 'kategori');
SET @sql := IF(@exist > 0, 'ALTER TABLE tb_templates MODIFY COLUMN kategori VARCHAR(50)', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;

-- 5. Archives Table
CREATE TABLE IF NOT EXISTS tb_sk_archives (
    id INT AUTO_INCREMENT PRIMARY KEY,
    no_surat VARCHAR(100),
    template_id INT,
    input_data_json LONGTEXT,
    generated_file_path VARCHAR(255),
    created_by VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
