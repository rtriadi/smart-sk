-- Migration: Add missing columns to tb_sk_archives
-- Required for save/load settings and tracking updates

-- Add settings_json column for storing paper/margin/typography settings
ALTER TABLE `tb_sk_archives` 
ADD COLUMN IF NOT EXISTS `settings_json` LONGTEXT AFTER `input_data_json`;

-- Add updated_at column for tracking last update time
ALTER TABLE `tb_sk_archives` 
ADD COLUMN IF NOT EXISTS `updated_at` DATETIME NULL AFTER `created_at`;
