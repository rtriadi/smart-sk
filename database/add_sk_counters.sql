-- Migration: Add SK numbering system
-- Run this SQL in phpMyAdmin

-- Create table for SK number counters
CREATE TABLE IF NOT EXISTS `tb_sk_counters` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `year` INT(4) NOT NULL,
    `category` VARCHAR(100) DEFAULT NULL,
    `last_number` INT(11) NOT NULL DEFAULT 0,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `year_category` (`year`, `category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add status and finalized_at columns to tb_sk_archives (if not exists)
-- Run these only if you haven't run add_archive_status.sql yet
-- ALTER TABLE `tb_sk_archives` ADD COLUMN `status` VARCHAR(20) DEFAULT 'draft' AFTER `no_surat`;
-- ALTER TABLE `tb_sk_archives` ADD COLUMN `finalized_at` DATETIME NULL AFTER `updated_at`;
