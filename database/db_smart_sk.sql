-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2026 at 06:07 AM
-- Server version: 10.4.6-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_smart_sk`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_pejabat`
--

CREATE TABLE `tb_pejabat` (
  `id` int(11) NOT NULL,
  `nama` varchar(150) NOT NULL,
  `nip` varchar(50) DEFAULT NULL,
  `jabatan` varchar(150) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'aktif',
  `is_default` tinyint(1) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_pejabat`
--

INSERT INTO `tb_pejabat` (`id`, `nama`, `nip`, `jabatan`, `status`, `is_default`, `created_at`) VALUES
(1, 'Abdul Hakim, S.Ag., S.H., M.H.', '196807031992021001', 'Ketua', 'aktif', 0, '2026-05-07 08:55:06');

-- --------------------------------------------------------

--
-- Table structure for table `tb_sk_archives`
--

CREATE TABLE `tb_sk_archives` (
  `id` int(11) NOT NULL,
  `no_surat` varchar(100) DEFAULT NULL,
  `status` varchar(20) DEFAULT 'draft',
  `template_id` int(11) DEFAULT NULL,
  `input_data_json` longtext DEFAULT NULL,
  `settings_json` longtext DEFAULT NULL,
  `generated_file_path` varchar(255) DEFAULT NULL,
  `created_by` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT NULL,
  `finalized_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `tb_sk_categories`
--

CREATE TABLE `tb_sk_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_sk_categories`
--

INSERT INTO `tb_sk_categories` (`id`, `category_name`, `created_at`) VALUES
(1, 'Perencanaan, TI dan Pelaporan', '2026-05-07 08:56:37');

-- --------------------------------------------------------

--
-- Table structure for table `tb_sk_counters`
--

CREATE TABLE `tb_sk_counters` (
  `id` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `last_number` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `tb_templates`
--

CREATE TABLE `tb_templates` (
  `id` int(11) NOT NULL,
  `nama_sk` varchar(100) DEFAULT NULL,
  `kategori` varchar(50) DEFAULT NULL,
  `nomor_pattern` varchar(100) DEFAULT NULL,
  `html_pattern` longtext DEFAULT NULL,
  `form_config` longtext DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_templates`
--

INSERT INTO `tb_templates` (`id`, `nama_sk`, `kategori`, `nomor_pattern`, `html_pattern`, `form_config`, `created_at`) VALUES
(1, 'SK TIDAK ADA LAMPIRAN', 'Perencanaan, TI dan Pelaporan', '{nomor}/W26-A1/SK/{bulan}/{tahun}', '<div style=\"text-align: center; margin-bottom: 20px;\">\r\n<p style=\"font-size: 14pt; font-weight: bold; margin: 0;\">KEPUTUSAN</p>\r\n<p style=\"font-size: 14pt; font-weight: bold; margin: 0;\">{{jabatan_penandatangan}}</p>\r\n<p style=\"font-size: 12pt; margin: 5px 0;\">NOMOR: {{no_sk}}</p>\r\n<p style=\"font-size: 12pt; font-weight: bold; margin: 10px 0;\">TENTANG</p>\r\n<p style=\"font-size: 12pt; font-weight: bold; margin: 0;\">{{judul_sk}}</p>\r\n</div>\r\n\r\n<p style=\"text-align: center; font-weight: bold; margin: 20px 0;\">{{jabatan_penandatangan}}</p>\r\n\r\n<table style=\"width: 100%; border: none; margin-bottom: 15px;\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 120px; vertical-align: top; border: none; padding: 5px 0;\">Menimbang</td>\r\n<td style=\"width: 20px; vertical-align: top; border: none; padding: 5px 0;\">:</td>\r\n<td style=\"vertical-align: top; border: none; padding: 5px 0;\">\r\n<ol type=\"a\" style=\"margin: 0; padding-left: 20px;\">\r\n{{#each list_menimbang}}<li>{{this}}</li>{{/each}}\r\n</ol>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n\r\n<table style=\"width: 100%; border: none; margin-bottom: 15px;\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 120px; vertical-align: top; border: none; padding: 5px 0;\">Mengingat</td>\r\n<td style=\"width: 20px; vertical-align: top; border: none; padding: 5px 0;\">:</td>\r\n<td style=\"vertical-align: top; border: none; padding: 5px 0;\">\r\n<ol type=\"1\" style=\"margin: 0; padding-left: 20px;\">\r\n{{#each list_mengingat}}<li>{{this}}</li>{{/each}}\r\n</ol>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n\r\n<table style=\"width: 100%; border: none; margin-bottom: 15px;\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 120px; vertical-align: top; border: none; padding: 5px 0;\">Memperhatikan</td>\r\n<td style=\"width: 20px; vertical-align: top; border: none; padding: 5px 0;\">:</td>\r\n<td style=\"vertical-align: top; border: none; padding: 5px 0;\">\r\n<ol type=\"1\" style=\"margin: 0; padding-left: 20px;\">\r\n{{#each list_memperhatikan}}<li>{{this}}</li>{{/each}}\r\n</ol>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n\r\n<p style=\"text-align: center; font-weight: bold; margin: 20px 0;\">MEMUTUSKAN:</p>\r\n\r\n<table style=\"width: 100%; border: none; margin-bottom: 20px;\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 120px; vertical-align: top; border: none; padding: 5px 0;\">Menetapkan</td>\r\n<td style=\"width: 20px; vertical-align: top; border: none; padding: 5px 0;\">:</td>\r\n<td style=\"vertical-align: top; border: none; padding: 5px 0;\">{{diktum_placeholder}}</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n\r\n<div style=\"margin-top: 40px;\">\r\n<table style=\"width: 100%; border: none;\">\r\n<tbody>\r\n<tr>\r\n<td style=\"width: 50%; border: none;\"></td>\r\n<td style=\"text-align: center; border: none;\">\r\n<p style=\"margin: 0;\">Ditetapkan di {{tempat_penetapan}}</p>\r\n<p style=\"margin: 0;\">Pada tanggal {{tanggal_indo}}</p>\r\n<p style=\"margin: 0;\">{{tanggal_hijri}}</p>\r\n<p style=\"margin: 30px 0 5px 0; font-weight: bold;\">{{jabatan_penandatangan}}</p>\r\n<p style=\"margin: 60px 0 5px 0; font-weight: bold; text-decoration: underline;\">{{nama_penandatangan}}</p>\r\n<p style=\"margin: 0;\">NIP. {{nip_penandatangan}}</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</div>', '[{\"variable\":\"no_sk\",\"label\":\"Nomor SK\",\"type\":\"text\"},{\"variable\":\"tanggal_sk\",\"label\":\"Tanggal SK\",\"type\":\"date\"},{\"variable\":\"judul_sk\",\"label\":\"Judul/Tentang SK\",\"type\":\"textarea\"},{\"variable\":\"list_menimbang\",\"label\":\"Menimbang (Poin-poin)\",\"type\":\"repeater\"},{\"variable\":\"list_mengingat\",\"label\":\"Mengingat (Dasar Hukum)\",\"type\":\"repeater\"},{\"variable\":\"list_memperhatikan\",\"label\":\"Memperhatikan\",\"type\":\"repeater\"},{\"variable\":\"list_diktum\",\"label\":\"Diktum (KESATU, KEDUA, dst)\",\"type\":\"repeater\"},{\"variable\":\"nama_penandatangan\",\"label\":\"Nama Penandatangan\",\"type\":\"text\"},{\"variable\":\"jabatan_penandatangan\",\"label\":\"Jabatan Penandatangan\",\"type\":\"text\"},{\"variable\":\"nip_penandatangan\",\"label\":\"NIP Penandatangan\",\"type\":\"text\"},{\"variable\":\"tempat_penetapan\",\"label\":\"Tempat Penetapan\",\"type\":\"text\"},{\"variable\":\"tanggal_hijri\",\"label\":\"Tanggal Hijriah\",\"type\":\"text\"},{\"type\":\"settings\",\"variable\":\"_global_settings\",\"label\":\"Global Settings\",\"layout\":{\"paperSize\":\"A4\",\"orientation\":\"portrait\",\"margins\":{\"top\":2.54,\"right\":2.54,\"bottom\":2.54,\"left\":2.54}}}]', '2026-05-07 08:57:36');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `id_user` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_pengguna` varchar(100) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id_user`, `username`, `password`, `nama_pengguna`, `created_at`) VALUES
(1, 'admin', 'd033e22ae348aeb5660fc2140aec35850c4da997', 'Administrator', '2026-05-07 08:40:11');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_pejabat`
--
ALTER TABLE `tb_pejabat`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_sk_archives`
--
ALTER TABLE `tb_sk_archives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_sk_categories`
--
ALTER TABLE `tb_sk_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_sk_counters`
--
ALTER TABLE `tb_sk_counters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `year_category` (`year`,`category`);

--
-- Indexes for table `tb_templates`
--
ALTER TABLE `tb_templates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_pejabat`
--
ALTER TABLE `tb_pejabat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_sk_archives`
--
ALTER TABLE `tb_sk_archives`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_sk_categories`
--
ALTER TABLE `tb_sk_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_sk_counters`
--
ALTER TABLE `tb_sk_counters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tb_templates`
--
ALTER TABLE `tb_templates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
