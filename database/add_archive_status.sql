-- Migration: Add status and finalized_at columns to tb_sk_archives
-- Run this SQL in phpMyAdmin or MySQL CLI

-- Add status column (draft or final)
ALTER TABLE `tb_sk_archives` 
ADD COLUMN `status` VARCHAR(20) DEFAULT 'draft' AFTER `no_surat`;

-- Add finalized_at column for tracking when SK was finalized
ALTER TABLE `tb_sk_archives` 
ADD COLUMN `finalized_at` DATETIME NULL AFTER `updated_at`;

-- Update existing records: mark non-DRAFT records as final
UPDATE `tb_sk_archives` 
SET `status` = 'final', `finalized_at` = `updated_at`
WHERE `no_surat` NOT LIKE 'DRAFT%';

-- Update existing records: mark DRAFT records as draft
UPDATE `tb_sk_archives` 
SET `status` = 'draft'
WHERE `no_surat` LIKE 'DRAFT%';
