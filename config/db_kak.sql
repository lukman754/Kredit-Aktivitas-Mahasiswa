-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2025 at 09:55 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kak`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `activity_id` int(11) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `activity_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`activity_id`, `category_id`, `activity_name`) VALUES
(1, 1, 'Pemakalah dalam pertemuan Ilmiah'),
(2, 1, 'Publikasi Ilmiah dalam Jurnal Ilmiah'),
(3, 1, 'Menulis Artikel di Media Massa'),
(4, 1, 'Berperan aktif sebagai peserta pada pertemuan ilmiah/Minat Bakat'),
(5, 1, 'Berperan aktif mengikuti kegiatan kepedulian sosial/Pengabdian Masyarakat'),
(6, 1, 'Ikut serta dalam kompetisi bidang ilmiah/Minat'),
(7, 1, 'Menjadi Asisten Praktikum/laboratorium/mentor'),
(8, 1, 'Ikut Serta dalam pameran'),
(9, 1, 'Proyek Riset'),
(10, 1, 'Mahasiswa Bekerja'),
(11, 1, 'Pendaftaran Paten Nasional'),
(12, 1, 'Hak Cipta Buku (Membuat Buku Ber-ISBN)'),
(13, 1, 'Karya Cipta lagu yang telah dipublikasikan/rekaman/diakui'),
(14, 1, 'Karya cipta seni tari yang telah dipentaskan/didokumentasikan'),
(15, 1, 'Hak Kekayaan Intelektual (HKI)'),
(16, 2, 'Berperan aktif dalam organisasi kemahasiswaan (HIMA/UKM)'),
(17, 2, 'Berperan aktif sebagai panitia dalam suatu kegiatan tingkat Universitas Pamulang'),
(18, 2, 'Berperan aktif sebagai panitia dalam suatu kegiatan tingkat Nasional'),
(19, 2, 'Berperan aktif sebagai panitia dalam suatu kegiatan tingkat Internasional'),
(20, 2, 'Berperan serta aktif sebagai peserta pelatihan kepemimpinan'),
(21, 2, 'Berperan serta aktif sebagai peserta'),
(22, 2, 'Berperan aktif dalam mempromosikan UNPAM'),
(23, 2, 'Upacara Bendera'),
(24, 2, 'Magang Internal UNPAM'),
(25, 2, 'Berperan aktif mengusulkan proposal dalam kompetisi yang diselenggarakan oleh kemdikbudristek'),
(26, 2, 'Berperan aktif sebagai tim Operator Tracer Study'),
(27, 2, 'Berperan Aktif sebagai panitia penyenggara Kejuaraan tingkat Nasional'),
(28, 2, 'Juri/Pelatih Regional/Nasional/Internasional'),
(29, 3, 'Organisasi Tingkat Internasional'),
(30, 3, 'Organisasi Tingkat Nasional/Provinsi'),
(31, 3, 'Organisasi Tingkat Kabupaten/Kecamatan'),
(32, 3, 'Organisasi Tingkat RT/RW');

-- --------------------------------------------------------

--
-- Table structure for table `activity_documents`
--

CREATE TABLE `activity_documents` (
  `doc_id` int(11) NOT NULL,
  `activity_id` int(11) NOT NULL,
  `doc_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_documents`
--

INSERT INTO `activity_documents` (`doc_id`, `activity_id`, `doc_type_id`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 2, 1),
(8, 2, 2),
(9, 2, 3),
(10, 2, 4),
(11, 2, 5),
(12, 2, 6),
(13, 3, 1),
(14, 3, 7),
(15, 3, 5),
(16, 4, 1),
(17, 4, 2),
(18, 4, 3),
(19, 4, 4),
(20, 4, 5),
(21, 4, 6),
(22, 5, 1),
(23, 5, 2),
(24, 5, 3),
(25, 5, 4),
(26, 5, 5),
(27, 5, 6),
(28, 6, 1),
(29, 6, 5),
(30, 6, 2),
(31, 6, 3),
(32, 6, 4),
(33, 7, 1),
(34, 7, 2),
(35, 7, 8),
(36, 7, 16),
(37, 8, 1),
(38, 8, 2),
(39, 8, 8),
(40, 8, 16),
(41, 9, 1),
(42, 9, 2),
(43, 9, 3),
(44, 9, 4),
(45, 9, 5),
(46, 9, 6),
(47, 10, 9),
(48, 10, 10),
(49, 10, 11),
(51, 11, 13),
(52, 11, 12),
(53, 12, 1),
(54, 12, 7),
(55, 13, 7),
(56, 13, 5),
(57, 13, 6),
(58, 14, 7),
(59, 14, 5),
(60, 14, 6),
(61, 15, 2),
(62, 15, 7),
(63, 16, 1),
(64, 16, 14),
(65, 17, 1),
(66, 17, 15),
(67, 17, 16),
(68, 17, 17),
(69, 17, 4),
(70, 18, 1),
(71, 18, 15),
(72, 18, 16),
(73, 18, 17),
(74, 18, 4),
(75, 19, 1),
(76, 19, 15),
(77, 19, 16),
(78, 19, 17),
(79, 19, 4),
(80, 20, 1),
(81, 20, 15),
(82, 20, 16),
(83, 20, 2),
(84, 20, 4),
(85, 21, 16),
(86, 21, 2),
(87, 22, 1),
(88, 22, 15),
(89, 22, 16),
(90, 22, 2),
(91, 23, 1),
(92, 23, 15),
(93, 23, 16),
(94, 24, 1),
(95, 24, 15),
(96, 24, 16),
(97, 24, 18),
(98, 25, 1),
(99, 25, 2),
(100, 25, 3),
(101, 25, 4),
(102, 25, 5),
(103, 25, 6),
(104, 26, 1),
(105, 26, 15),
(106, 26, 16),
(107, 26, 18),
(108, 27, 1),
(109, 27, 15),
(110, 27, 16),
(111, 27, 17),
(112, 27, 4),
(113, 28, 1),
(114, 28, 15),
(115, 28, 16),
(116, 28, 2),
(117, 29, 1),
(118, 29, 19),
(119, 29, 16),
(123, 31, 1),
(124, 31, 19),
(125, 31, 16),
(126, 32, 1),
(127, 32, 19),
(128, 32, 16),
(165, 30, 1),
(166, 30, 16),
(167, 30, 19);

-- --------------------------------------------------------

--
-- Table structure for table `activity_levels`
--

CREATE TABLE `activity_levels` (
  `level_id` int(11) NOT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `level_name` varchar(255) NOT NULL,
  `points` int(11) NOT NULL,
  `period_id` int(11) DEFAULT NULL,
  `system_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activity_levels`
--

INSERT INTO `activity_levels` (`level_id`, `activity_id`, `level_name`, `points`, `period_id`, `system_id`) VALUES
(1, 1, 'Internasional', 25, 1, 1),
(2, 1, 'Nasional', 20, 1, 1),
(3, 1, 'Regional/Daerah', 15, 1, 1),
(4, 1, 'Internal UNPAM', 10, 1, 1),
(5, 2, 'Internasional', 25, 4, 1),
(6, 2, 'Terakreditasi Nasional', 20, 4, 1),
(7, 2, 'Belum Terakreditasi', 15, 4, 1),
(8, 2, 'Internal UNPAM', 10, 4, 1),
(9, 3, 'Internasional', 15, 5, 2),
(10, 3, 'Nasional', 10, 5, 2),
(11, 3, 'Regional/Daerah', 5, 5, 2),
(12, 6, 'Prestasi internasional (Juara I, II, III)', 30, 1, 3),
(13, 6, 'Peserta Kejuaraan Internasional', 10, 1, 3),
(14, 6, 'Prestasi Nasional (Juara I, II, III)', 25, 1, 3),
(15, 6, 'Peserta Kejuaraan Nasional', 5, 1, 3),
(16, 6, 'Prestasi Internal UNPAM (Juara I,II,III)', 5, 1, 3),
(17, 6, 'Peserta', 1, 1, 3),
(35, 7, 'Koordinator Asisten', 15, 2, 2),
(36, 7, 'Asisten', 10, 2, 2),
(37, 7, 'Studi Group/Research Group', 7, 2, 2),
(38, 8, 'Internasional', 20, 1, 2),
(39, 8, 'Nasional', 15, 1, 2),
(40, 8, 'Regional/Daerah', 5, 1, 2),
(41, 9, 'Proyek Riset UNPAM', 20, 1, 1),
(42, 9, 'Proyek Riset Fakultas', 15, 1, 1),
(43, 9, 'Proyek Riset Lembaga', 10, 1, 1),
(44, 9, 'Proyek Riset Program Studi', 7, 1, 1),
(45, 10, 'Status Sebagai Pegawai Tetap Perusahaan', 15, 2, 2),
(46, 10, 'Wirausaha/Enterpreneur', 15, 2, 2),
(47, 10, 'Part Time (Jam/Minggu)', 7, 2, 2),
(48, 11, 'Paten Nasional', 100, 3, 2),
(49, 12, 'Ber-ISBN', 30, 2, 2),
(50, 13, 'Lokal/Wilayah/Nasional', 30, 2, 2),
(51, 14, 'Lokal/Wilayah/Nasional', 30, 2, 2),
(52, 15, 'Nasional', 20, 2, 2),
(53, 16, 'Ketua/Wakil', 15, 3, 2),
(54, 16, 'Sekretaris/Bendahara/Kabid/Steering Committee', 10, 3, 2),
(55, 16, 'Anggota', 5, 3, 2),
(56, 17, 'Ketua/Wakil', 10, 1, 2),
(57, 17, 'Sekretaris/Bendahara/Kabid/Steering Committee', 7, 1, 2),
(58, 17, 'Anggota', 5, 1, 2),
(59, 18, 'Ketua/Wakil', 15, 1, 2),
(60, 18, 'Sekretaris/Bendahara/Kabid/Steering Committee', 10, 1, 2),
(61, 18, 'Anggota', 5, 1, 2),
(62, 19, 'Ketua/Wakil', 20, 1, 2),
(63, 19, 'Sekretaris/Bendahara/Kabid/Steering Committee', 15, 1, 2),
(64, 19, 'Anggota', 10, 1, 2),
(65, 20, 'LKMM Pra-Dasar/Dasar/Menengah/Lanjutan', 25, 1, 2),
(66, 20, 'LDKO', 5, 1, 2),
(67, 21, 'PKKMB Universitas', 10, 1, 2),
(68, 21, 'PKKMB Program Studi', 7, 1, 2),
(69, 22, 'Kegiatan Promosi', 7, 1, 2),
(70, 23, 'Petugas', 4, 1, 4),
(71, 23, 'Peserta', 2, 1, 4),
(72, 24, 'Pengembang Sistem', 20, 2, 2),
(73, 24, 'Tim Magang', 15, 2, 2),
(74, 25, 'Sebagai Ketua', 10, 3, 1),
(75, 25, 'Sebagai Anggota', 5, 3, 1),
(76, 26, 'Sebagai Ketua', 10, 3, 2),
(77, 26, 'Sebagai Anggota', 5, 3, 2),
(78, 27, 'Sebagai Ketua', 15, 1, 2),
(79, 27, 'Sebagai Anggota', 10, 1, 2),
(80, 28, 'Juri/Pelatih Regional/Nasional/Internasional', 15, 1, 2),
(81, 29, 'Sebagai Ketua/Sekretaris/Bendahara/Kabid', 25, 2, 2),
(82, 29, 'Sebagai Anggota Biasa', 5, 2, 2),
(85, 31, 'Sebagai Ketua/Sekretaris/Bendahara/Kabid', 15, 2, 2),
(86, 31, 'Sebagai Anggota Biasa', 5, 2, 2),
(87, 32, 'Sebagai Ketua/Sekretaris/Bendahara/Kabid', 10, 2, 2),
(88, 32, 'Sebagai Anggota Biasa', 5, 2, 2),
(89, 4, 'Internasional', 7, 1, 1),
(90, 4, 'Nasional', 5, 1, 1),
(91, 4, 'Regional/Daerah', 2, 1, 1),
(92, 5, 'Internasional', 25, 1, 1),
(93, 5, 'Nasional', 20, 1, 1),
(94, 5, 'Regional/Daerah', 10, 1, 1),
(95, 5, 'Internal UNPAM', 5, 1, 1),
(125, 30, 'Sebagai Ketua/Sekretaris/Bendahara/Kabid', 20, 2, 2),
(126, 30, 'Sebagai Anggota Biasa', 5, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL,
  `nidn` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`admin_id`, `nidn`, `full_name`, `password`, `created_at`) VALUES
(1, 2011700337, 'Lukman Hakim', '$2y$10$fdLC0/2mM5nO9.x7MYyx9O9j/w8TmQrtE7RY0ldnmqvoPsKkMnjg.', '2024-12-26 09:50:00'),
(2, 2147483647, 'Lukman Muludin', '$2y$10$MWjyajaCE/Y9cnBwWAreG.mVXEFCWgRE7aldnmsVL3RbOZp5OIyhe', '2024-12-26 15:31:29'),
(3, 2111700337, 'Lorem Ipsum', '$2y$10$GizqU/oi2sFl.x8QEldYrOPx5HTcouUq08vPhRgc.0NtC344OcxCa', '2024-12-26 15:37:48'),
(4, 2011700355, 'Lukman Muludin', '$2y$10$lL/FNkQVqfkFuu1lFiYaRug3zUrcKUUsFeiJtgMSMViXJFewzWiqO', '2024-12-29 12:06:58'),
(5, 2011700300, 'Lukman Muludin', '$2y$10$ANtaBuqtmKq3ww0f9MYxjOC/.x0jCIj48oH/FpyHOdb3LOaUIgj/m', '2024-12-29 12:10:23'),
(7, 221011700, 'Lukman Muludin', '$2y$10$w5klsLO4Vx/l.3YFNGhYfOeEHWRArRoikSsrSWGDrf3VC6pMrnCKy', '2024-12-29 12:29:58'),
(8, 401039204, 'Tri Prasetyo', '$2y$10$mseLBIq6MeWLzBQylvI7suHTZlPZSZ7dPbWmp2zBomx.tGHYI.A6u', '2025-01-02 06:58:07');

-- --------------------------------------------------------

--
-- Table structure for table `assessment_periods`
--

CREATE TABLE `assessment_periods` (
  `period_id` int(11) NOT NULL,
  `period_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment_periods`
--

INSERT INTO `assessment_periods` (`period_id`, `period_name`) VALUES
(1, 'Tiap Kegiatan'),
(2, 'Tiap Semester'),
(3, 'Tiap Tahun'),
(4, 'Tiap Jurnal'),
(5, 'Tiap Tulisan');

-- --------------------------------------------------------

--
-- Table structure for table `assessment_systems`
--

CREATE TABLE `assessment_systems` (
  `system_id` int(11) NOT NULL,
  `system_name` varchar(50) NOT NULL,
  `system_code` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `assessment_systems`
--

INSERT INTO `assessment_systems` (`system_id`, `system_name`, `system_code`) VALUES
(1, 'LKA (SISPEMA)', 'KEGIATAN ILMIAH'),
(2, 'LKA (SISPEMA)', 'REKOGNISI'),
(3, 'LKA (SISPEMA)', 'PRESTASI'),
(4, 'LKA (SISPEMA)', 'MENTAL KEBANGSAAN');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`) VALUES
(1, 'TRI DHARMA PERGURUAN TINGGI'),
(2, 'ORGANISASI & KEGIATAN KEMAHASISWAAN INTERNAL KAMPUS'),
(3, 'ORGANISASI EKTRA KAMPUS');

-- --------------------------------------------------------

--
-- Table structure for table `document_types`
--

CREATE TABLE `document_types` (
  `doc_type_id` int(11) NOT NULL,
  `doc_type_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_types`
--

INSERT INTO `document_types` (`doc_type_id`, `doc_type_name`) VALUES
(1, 'Foto Kegiatan'),
(2, 'Sertifikat'),
(3, 'Surat Tugas'),
(4, 'Surat Undangan'),
(5, 'URL Kegiatan/Link OJS/Link Publikasi'),
(6, 'Makalah/Jurnal/Artikel/Laporan'),
(7, 'Naskah Artikel'),
(8, 'SK/Surat Tugas/Surat Undangan'),
(9, 'Surat Keterangan Sebagai Pegawai'),
(10, 'Scan ID Card'),
(11, 'Surat Izin Usaha'),
(12, 'Bukti Pendaftaran/Hak Paten'),
(13, 'Naskah Spesifikasi Produk'),
(14, 'SK Pengukuhan'),
(15, 'ST Kegiatan'),
(16, 'URL Penyelenggara'),
(17, 'Sertifikat Panitia'),
(18, 'Laporan Hasil Kegiatan'),
(19, 'SK Penetapan'),
(20, 'Naskah Buku'),
(21, 'Foto Buku'),
(22, 'Naskah Lagu'),
(23, 'Link Publikasi'),
(24, 'Video Rekaman Lagu'),
(25, 'Video Rekaman Tari'),
(26, 'Naskah Tari'),
(27, 'Sertifikat HKI'),
(28, 'Naskah yang di HKI-kan'),
(29, 'Foto Produk'),
(30, 'Dokumentasi Kegiatan'),
(31, 'Link Pemberitaan'),
(32, 'Sertifikat Penghargaan'),
(33, 'Surat Tugas Delegasi'),
(34, 'Flyer Kejuaraan'),
(35, 'Sertifikat Juri'),
(36, 'Foto Pelanggaran'),
(37, 'Foto Surat Pernyataan');

-- --------------------------------------------------------

--
-- Table structure for table `document_uploads`
--

CREATE TABLE `document_uploads` (
  `upload_id` int(11) NOT NULL,
  `submission_id` int(11) DEFAULT NULL,
  `doc_type_id` int(11) DEFAULT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `upload_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `file_size` int(11) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `document_uploads`
--

INSERT INTO `document_uploads` (`upload_id`, `submission_id`, `doc_type_id`, `file_name`, `file_path`, `upload_date`, `file_size`, `file_type`) VALUES
(146, 50, 16, 'URL Document', 'https://gfd', '2024-12-29 13:16:04', NULL, 'url'),
(147, 50, 1, 'FB_IMG_1735467689708.jpg', 'uploads/67714b944716b_FB_IMG_1735467689708.jpg', '2024-12-29 13:16:04', 67611, 'image/jpeg'),
(148, 50, 2, 'Screenshot_2024-12-29-08-54-58-897_com.google.android.apps.docs.png', 'uploads/67714b94522f5_Screenshot_2024-12-29-08-54-58-897_com.google.android.apps.docs.png', '2024-12-29 13:16:04', 167293, 'image/png'),
(149, 50, 8, 'FB_IMG_1735313759313.jpg', 'uploads/67714b9456147_FB_IMG_1735313759313.jpg', '2024-12-29 13:16:04', 10168, 'image/jpeg'),
(177, 78, 16, 'URL Document', 'https://lorem.ipsum', '2024-12-29 18:00:40', NULL, 'url'),
(178, 78, 2, '1735272607278.jpeg', 'uploads/67718e4840450_1735272607278.jpeg', '2024-12-29 18:00:40', 405644, 'image/jpeg'),
(179, 87, 1, 'foto_lomba_programming.jpg', 'uploads/foto_lomba_programming.jpg', '2025-01-02 04:17:29', 250000, 'image/jpeg'),
(180, 87, 2, 'sertifikat_juara_gemastik.pdf', 'uploads/sertifikat_juara_gemastik.pdf', '2025-01-02 04:17:29', 500000, 'application/pdf'),
(181, 87, 5, 'URL Document', 'https://gemastik2024.id', '2025-01-02 04:17:29', NULL, 'url'),
(182, 88, 3, 'sk_asisten_lab.pdf', 'uploads/sk_asisten_lab.pdf', '2025-01-02 04:17:29', 300000, 'application/pdf'),
(183, 88, 1, 'foto_mengajar_lab.jpg', 'uploads/foto_mengajar_lab.jpg', '2025-01-02 04:17:29', 200000, 'image/jpeg'),
(184, 89, 6, 'paper_sistem_informasi.pdf', 'uploads/paper_sistem_informasi.pdf', '2025-01-02 04:17:29', 800000, 'application/pdf'),
(185, 89, 5, 'URL Document', 'https://jurnal.unpam.ac.id', '2025-01-02 04:17:29', NULL, 'url'),
(186, 126, 16, 'URL Document', 'https://asdasda.com', '2025-01-02 04:32:22', NULL, 'url'),
(187, 126, 2, 'cropped-BPR-SS-Logo-Lingkaran-New (1).png', 'uploads/677616d6539dc_cropped-BPR-SS-Logo-Lingkaran-New (1).png', '2025-01-02 04:32:22', 23517, 'image/png'),
(188, 127, 2, 'screencapture-127-0-0-1-5500-data-pribadi-html-2024-12-27-21_04_22.png', 'uploads/677618cef2b38_screencapture-127-0-0-1-5500-data-pribadi-html-2024-12-27-21_04_22.png', '2025-01-02 04:40:46', 1034293, 'image/png'),
(189, 127, 7, '360_F_92535664_IvFsQeHjBzfE6sD4VHdO8u5OHUSc6yHF.jpg', 'uploads/677618cf00d2f_360_F_92535664_IvFsQeHjBzfE6sD4VHdO8u5OHUSc6yHF.jpg', '2025-01-02 04:40:47', 87087, 'image/jpeg'),
(190, 128, 5, 'URL Document', 'https://ojs.unpam.ac.id', '2025-01-02 07:10:45', NULL, 'url'),
(191, 128, 1, 'Screenshot 2024-06-13 221343.png', 'uploads/67763bf5ea1db_Screenshot 2024-06-13 221343.png', '2025-01-02 07:10:45', 1186258, 'image/png'),
(192, 128, 7, 'Screenshot 2024-04-17 220604.png', 'uploads/67763bf5ee669_Screenshot 2024-04-17 220604.png', '2025-01-02 07:10:45', 163322, 'image/png'),
(193, 129, 5, 'URL Document', 'https://hasdhahsdha', '2025-01-02 07:45:20', NULL, 'url'),
(194, 129, 1, 'screencapture-127-0-0-1-5500-kampus-html-2024-12-27-21_04_49.png', 'uploads/67764410c1a6d_screencapture-127-0-0-1-5500-kampus-html-2024-12-27-21_04_49.png', '2025-01-02 07:45:20', 1190420, 'image/png'),
(195, 129, 7, '360_F_92535664_IvFsQeHjBzfE6sD4VHdO8u5OHUSc6yHF.jpg', 'uploads/67764410c5505_360_F_92535664_IvFsQeHjBzfE6sD4VHdO8u5OHUSc6yHF.jpg', '2025-01-02 07:45:20', 87087, 'image/jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `fakultas`
--

CREATE TABLE `fakultas` (
  `fakultas_id` int(11) NOT NULL,
  `nama_fakultas` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fakultas`
--

INSERT INTO `fakultas` (`fakultas_id`, `nama_fakultas`, `created_at`) VALUES
(1, 'Fakultas Ekonomi dan Bisnis', '2024-12-24 15:31:59'),
(2, 'Fakultas Hukum', '2024-12-24 15:31:59'),
(3, 'Fakultas Sastra', '2024-12-24 15:31:59'),
(4, 'Fakultas Teknik', '2024-12-24 15:31:59'),
(5, 'Fakultas Ilmu Komputer', '2024-12-24 15:31:59'),
(6, 'Fakultas Keguruan dan Ilmu Pendidikan', '2024-12-24 15:31:59'),
(7, 'Fakultas Matematika dan Ilmu Pengetahuan Alam', '2024-12-24 15:31:59'),
(8, 'Fakultas Agama Islam', '2024-12-24 15:31:59'),
(9, 'Fakultas Ilmu Komunikasi', '2024-12-24 15:31:59'),
(10, 'Fakultas Ilmu Sosial dan Ilmu Politik', '2024-12-24 15:31:59'),
(11, 'Program Pascasarjana', '2024-12-24 15:34:52');

-- --------------------------------------------------------

--
-- Table structure for table `prodi`
--

CREATE TABLE `prodi` (
  `prodi_id` int(11) NOT NULL,
  `fakultas_id` int(11) NOT NULL,
  `nama_prodi` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prodi`
--

INSERT INTO `prodi` (`prodi_id`, `fakultas_id`, `nama_prodi`, `created_at`) VALUES
(133, 1, 'Manajemen (S-1)', '2024-12-24 15:35:02'),
(134, 1, 'Akuntansi (S-1)', '2024-12-24 15:35:02'),
(135, 1, 'Administrasi Perkantoran (D-3)', '2024-12-24 15:35:02'),
(136, 1, 'Perpajakan (D-4)', '2024-12-24 15:35:02'),
(137, 1, 'Manajemen (PSDKU, Serang)', '2024-12-24 15:35:02'),
(138, 1, 'Akuntansi (PSDKU, Serang)', '2024-12-24 15:35:02'),
(139, 2, 'Ilmu Hukum (S-1)', '2024-12-24 15:35:02'),
(140, 2, 'Ilmu Hukum (PSDKU, Serang)', '2024-12-24 15:35:02'),
(141, 3, 'Sastra Inggris (S-1)', '2024-12-24 15:35:02'),
(142, 3, 'Sastra Indonesia (S-1)', '2024-12-24 15:35:02'),
(143, 4, 'Teknik Mesin (S-1)', '2024-12-24 15:35:02'),
(144, 4, 'Teknik Industri (S-1)', '2024-12-24 15:35:02'),
(145, 4, 'Teknik Elektro (S-1)', '2024-12-24 15:35:02'),
(146, 4, 'Teknik Kimia (S-1)', '2024-12-24 15:35:02'),
(147, 4, 'Teknik Mesin (PSDKU, Serang)', '2024-12-24 15:35:02'),
(148, 4, 'Teknik Elektro (PSDKU, Serang)', '2024-12-24 15:35:02'),
(149, 5, 'Teknik Informatika (S-1)', '2024-12-24 15:35:02'),
(150, 5, 'Sistem Informasi (S-1)', '2024-12-24 15:35:02'),
(151, 5, 'Sistem Komputer (S-1)', '2024-12-24 15:35:02'),
(152, 5, 'Teknik Informatika (PSDKU, Serang)', '2024-12-24 15:35:02'),
(153, 5, 'Sistem Informasi (PSDKU, Serang)', '2024-12-24 15:35:02'),
(154, 6, 'Pendidikan Pancasila dan Kewarganegaraan (S-1)', '2024-12-24 15:35:02'),
(155, 6, 'Pendidikan Ekonomi (S-1)', '2024-12-24 15:35:02'),
(156, 6, 'Pendidikan Jasmani (S-1)', '2024-12-24 15:35:02'),
(157, 6, 'Pendidikan Guru Sekolah Dasar (S-1)', '2024-12-24 15:35:02'),
(158, 6, 'Pendidikan Profesi Guru', '2024-12-24 15:35:02'),
(159, 7, 'Matematika (S-1)', '2024-12-24 15:35:02'),
(160, 7, 'Matematika (PSDKU, Serang)', '2024-12-24 15:35:02'),
(161, 7, 'Biologi (S-1)', '2024-12-24 15:35:02'),
(162, 7, 'Biologi (PSDKU, Serang)', '2024-12-24 15:35:02'),
(163, 7, 'Kimia (S-1)', '2024-12-24 15:35:02'),
(164, 7, 'Kimia (PSDKU, Serang)', '2024-12-24 15:35:02'),
(165, 8, 'Manajemen Pendidikan Islam (S-1)', '2024-12-24 15:35:02'),
(166, 8, 'Ekonomi Syariah (S-1)', '2024-12-24 15:35:02'),
(167, 9, 'Ilmu Komunikasi (S-1)', '2024-12-24 15:35:02'),
(168, 10, 'Administrasi Negara (S-1)', '2024-12-24 15:35:02'),
(169, 10, 'Administrasi Negara (PSDKU, Serang)', '2024-12-24 15:35:02'),
(170, 10, 'Ilmu Pemerintahan (S-1)', '2024-12-24 15:35:02'),
(171, 10, 'Ilmu Pemerintahan (PSDKU, Serang)', '2024-12-24 15:35:02'),
(172, 11, 'Ilmu Hukum (S-2)', '2024-12-24 15:35:02'),
(173, 11, 'Manajemen (S-2)', '2024-12-24 15:35:02'),
(174, 11, 'Teknik Informatika (S-2)', '2024-12-24 15:35:02'),
(175, 11, 'Akuntansi (S-2)', '2024-12-24 15:35:02'),
(176, 11, 'Manajemen Pendidikan (S-2)', '2024-12-24 15:35:02');

-- --------------------------------------------------------

--
-- Stand-in structure for view `prodi_summary`
-- (See below for the actual view)
--
CREATE TABLE `prodi_summary` (
`nama_prodi` varchar(255)
,`nama_fakultas` varchar(255)
,`system_id` int(11)
,`total_aktivitas` bigint(21)
,`total_points` decimal(32,0)
);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `nim` varchar(20) NOT NULL,
  `prodi_id` int(11) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `nim`, `prodi_id`, `full_name`, `password`, `created_at`) VALUES
(1, '221011700337', 149, 'Lukman Muludin', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 12:17:25'),
(2, '221011700050', 150, 'Satriyo Rizkyansah', '$2y$10$9orO0Qats0jUfDXn4AcdauCNwVhL/zQRIxGaxV9.EzpSIjFc6FRti', '2024-12-21 14:53:09'),
(9, '221011700336', 150, 'Imam Samudra Fasha', '$2y$10$ZLqF4j/uKfQG1hGdIWaEhuBvz9AuRBzbG64toMZ0rg8/B/GY8RX32', '2024-12-23 08:56:10'),
(10, '221011700334', 149, 'Faizal Yusuf Rifaldy', '$2y$10$Gdbo0exuK8nG3ZSdaLsVYeVZN275/rKnQgducsfEwSecARxUkywui', '2024-12-23 08:58:39'),
(11, '221011700471', 149, 'Muhammad Rangga Pramudya', '$2y$10$y///pc15.bILelCnICAv7.8bAW5EhBbvBaNscA1w9Pj2XdPKXptHm', '2024-12-23 11:52:45'),
(12, '221011700333', 149, 'Ega Stinka', '$2y$10$pe/dzVht..5FnL57UsaFAeOqlq7M3JQM9f7Ma0hkBmILe5NaNOOUG', '2024-12-23 11:52:49'),
(14, '241011400217', 149, 'Muhammad Rizki Fajri', '$2y$10$md.V7bmgJuH.MeQFalYece250d.EjEI8hPZrr0iVqxag.OgIilH3i', '2024-12-23 12:11:43'),
(15, '221011300023', 167, 'Ratu Alycia Zahra', '$2y$10$NeuvPmKzimi0v6zu4Ol2ZuHSUwqgN2knIXJ5sDucRepQcHUdxagzq', '2024-12-23 12:13:26'),
(16, '221010503959', 133, 'Zidna Zaena Nikhal', '$2y$10$IpOx.Ehk71S4g8H17v0EEufLH.3sRPNdWKDubMl2W0UlCWlzYKFE.', '2024-12-23 14:36:24'),
(17, '221011700338', 145, 'Lukman Hakim', '$2y$10$npGYnhJEt5md4FVEIb9XceC5U53xlhP5o2vcyXqfj6wuC43nl.WH6', '2024-12-24 15:45:28'),
(18, '231011400041', 149, 'Selvita Suci Noviyanti', '$2y$10$VV/jiPpuzKpIuHQZfBuHvu3aM2QL1qi4zwP3ziZBXgK7zQ2kjRJdy', '2024-12-29 12:59:23'),
(19, '241011701320', 150, 'Kanaya Rapasha', '$2y$10$4X7IIZijuwihtNfd.VRCAexr1Zquff45C25SuTkB0hPfRfvPMwyM6', '2024-12-29 13:41:40'),
(20, '221011400123', 150, 'Ahmad Fauzi', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(21, '221011500234', 151, 'Sarah Amalia', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(22, '221010300345', 133, 'Muhammad Rizki', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(23, '221010400456', 134, 'Annisa Putri', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(24, '221010600567', 136, 'Dimas Pratama', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(25, '221010700678', 137, 'Putri Wulandari', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(26, '221011200789', 142, 'Fajar Ramadhan', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(27, '221011300890', 143, 'Siti Nurhaliza', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(28, '221011800901', 158, 'Budi Santoso', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(29, '221011900012', 159, 'Rina Melati', '$2y$10$PsVwYOBNTvQoWz5V/zkyp.anv10NLmZUtEjNgLwtR5Wa08pc4bL0i', '2024-12-21 05:17:25'),
(31, '2012140289', 149, 'Tri Prasetyo', '$2y$10$f4jVlNq3CBZuIrbqCPpRbeV06xdvuHy.SPjJpG8VA6UMaoX9bPIMu', '2025-01-02 07:06:56');

-- --------------------------------------------------------

--
-- Table structure for table `user_activities`
--

CREATE TABLE `user_activities` (
  `submission_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `activity_id` int(11) DEFAULT NULL,
  `activity_level_id` int(11) DEFAULT NULL,
  `activity_desc` text NOT NULL,
  `submission_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_activities`
--

INSERT INTO `user_activities` (`submission_id`, `user_id`, `activity_id`, `activity_level_id`, `activity_desc`, `submission_date`, `status`, `reviewed_by`, `reviewed_at`, `feedback`, `created_at`) VALUES
(50, 1, 8, 38, 'Peserta pameran nasional CILPACASTRA FEST 2.0', '2024-12-29 13:16:04', 'approved', 1, '2025-01-02 04:11:00', 'ok', '2024-12-29 13:16:04'),
(78, 19, 21, 67, 'Lomba Puisi Tingkat Nasional', '2024-12-29 18:00:40', 'approved', 1, '2024-12-29 18:01:39', '👌 ', '2024-12-29 18:00:40'),
(79, 1, 21, 67, 'Peserta PPKMB universitas', '2025-01-01 20:31:56', 'approved', 1, '2025-01-01 20:43:28', 'mantap', '2025-01-01 20:31:56'),
(80, 1, 6, 14, 'Lomba Desain Poster Nasional 2024', '2025-01-01 20:33:01', 'approved', 1, '2025-01-01 20:43:37', 'keren', '2025-01-01 20:33:01'),
(81, 1, 4, 89, 'Peserta Seminar Internasional Teknologi Informasi 2024', '2024-12-14 20:30:00', 'approved', 1, '2025-01-01 20:43:58', 'ok', '2025-01-01 20:40:49'),
(82, 1, 7, 35, 'Koordinator Asisten Praktikum Pemrograman Web', '2024-12-20 00:15:00', 'approved', 1, '2025-01-01 20:45:50', 'ok', '2025-01-01 20:40:49'),
(83, 2, 16, 53, 'Ketua Himpunan Mahasiswa Teknik Informatika', '2024-12-09 19:00:00', 'approved', 1, '2025-01-01 20:45:59', 'ok', '2025-01-01 20:40:49'),
(84, 2, 5, 93, 'Ketua Program Bakti Sosial di Tangerang Selatan', '2024-12-21 21:45:00', 'approved', 1, '2025-01-01 14:15:23', 'Great work!', '2025-01-01 20:40:49'),
(85, 2, 2, 6, 'Publikasi Jurnal Nasional Terakreditasi Sinta 2', '2024-12-17 23:20:00', 'approved', 1, '2025-01-01 14:17:45', 'Excellent achievement', '2025-01-01 20:40:49'),
(86, 9, 10, 45, 'Karyawan Tetap di PT Solusi Digital Indonesia', '2024-12-25 02:30:00', 'approved', 1, '2025-01-01 14:22:12', 'Well done', '2025-01-01 20:40:49'),
(87, 10, 3, 10, 'Penulis Artikel di Majalah Tekno Indonesia', '2024-12-11 18:45:00', 'approved', 1, '2025-01-01 14:25:38', 'Keep up the good work!', '2025-01-01 20:40:49'),
(88, 10, 17, 56, 'Ketua Panitia Pameran Teknologi Fakultas', '2024-12-28 01:10:00', 'approved', 1, '2025-01-01 14:28:55', 'Very impressive', '2025-01-01 20:40:49'),
(89, 11, 6, 14, 'Juara 3 Lomba Programming Tingkat Nasional', '2024-12-13 20:20:00', 'approved', 1, '2025-01-01 14:32:17', 'Outstanding performance', '2025-01-01 20:40:49'),
(90, 11, 24, 72, 'Magang sebagai Pengembang Sistem di UNPAM', '2024-12-27 00:40:00', 'approved', 1, '2025-01-01 14:35:42', 'Good initiative', '2025-01-01 20:40:49'),
(91, 12, 21, 67, 'Peserta PKKMB Universitas 2024', '2024-12-15 19:15:00', 'approved', 1, '2025-01-01 14:38:19', 'Nice work', '2025-01-01 20:40:49'),
(92, 12, 4, 90, 'Peserta Seminar Nasional Kecerdasan Buatan', '2024-12-22 23:30:00', 'approved', 1, '2025-01-01 14:42:33', 'Excellent participation', '2025-01-01 20:40:49'),
(95, 14, 7, 36, 'Asisten Praktikum Basis Data', '2024-12-16 20:50:00', 'approved', 1, '2025-01-01 14:52:14', 'Good performance', '2025-01-01 20:40:49'),
(96, 14, 22, 69, 'Tim Promosi UNPAM di SMA Negeri 1 Pamulang', '2024-12-24 00:20:00', 'approved', 1, '2025-01-01 14:55:39', 'Excellent work ethic', '2025-01-01 20:40:49'),
(97, 15, 20, 65, 'Peserta LKMM-TD 2024', '2024-12-12 18:30:00', 'approved', 1, '2025-01-01 14:58:45', 'Great participation', '2025-01-01 20:40:49'),
(98, 15, 5, 94, 'Koordinator Bakti Sosial Korban Banjir', '2024-12-20 22:40:00', 'approved', 1, '2025-01-01 15:02:18', 'Outstanding leadership', '2025-01-01 20:40:49'),
(99, 1, 4, 89, 'Peserta Seminar Internasional Teknologi Informasi 2024', '2024-12-14 20:30:00', 'approved', 1, '2025-01-01 15:05:43', 'Well organized', '2025-01-01 20:41:34'),
(100, 1, 7, 35, 'Koordinator Asisten Praktikum Pemrograman Web', '2024-12-20 00:15:00', 'approved', 1, '2025-01-01 15:08:29', 'Excellent coordination', '2025-01-01 20:41:34'),
(101, 2, 16, 53, 'Ketua Himpunan Mahasiswa Teknik Informatika', '2024-12-09 19:00:00', 'approved', 1, '2025-01-01 15:12:54', 'Great leadership skills', '2025-01-01 20:41:34'),
(102, 2, 5, 93, 'Ketua Program Bakti Sosial di Tangerang Selatan', '2024-12-21 21:45:00', 'approved', 1, '2025-01-01 15:15:22', 'Impressive initiative', '2025-01-01 20:41:34'),
(103, 9, 2, 6, 'Publikasi Jurnal Nasional Terakreditasi Sinta 2', '2024-12-17 23:20:00', 'approved', 1, '2025-01-01 15:18:47', 'High quality work', '2025-01-01 20:41:34'),
(104, 9, 10, 45, 'Karyawan Tetap di PT Solusi Digital Indonesia', '2024-12-25 02:30:00', 'approved', 1, '2025-01-01 15:22:15', 'Professional performance', '2025-01-01 20:41:34'),
(105, 1, 11, 48, 'Mendaftarkan produk inovasi', '2025-01-01 20:52:06', 'approved', 1, '2025-01-01 20:52:33', 'kereen', '2025-01-01 20:52:06'),
(106, 20, 6, 14, 'Juara 2 Lomba Programming Nasional GEMASTIK', '2024-12-01 02:00:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(107, 20, 7, 35, 'Koordinator Asisten Lab Programming', '2024-12-05 07:30:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(108, 21, 2, 6, 'Publikasi Paper di Jurnal Sistem Informasi Indonesia', '2024-12-02 03:15:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(109, 21, 16, 53, 'Ketua Himpunan Mahasiswa Sistem Informasi', '2024-12-06 04:45:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(110, 22, 9, 41, 'Proyek Riset Pengembangan Mobil Listrik UNPAM', '2024-12-03 06:20:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(111, 22, 8, 38, 'Pameran Inovasi Teknologi Otomotif Internasional', '2024-12-07 08:00:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(112, 23, 6, 12, 'Juara 1 Kompetisi Robot Internasional', '2024-12-04 01:45:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(113, 23, 10, 45, 'Pegawai Tetap PT PLN Indonesia', '2024-12-08 09:30:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(114, 24, 5, 92, 'Program Pengabdian Masyarakat di Desa Binaan', '2024-12-09 02:30:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(115, 24, 24, 72, 'Magang di Departemen Production Planning', '2024-12-13 03:45:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(116, 25, 2, 5, 'Publikasi Penelitian di International Journal of Chemical Engineering', '2024-12-10 04:15:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(117, 25, 7, 36, 'Asisten Laboratorium Kimia Dasar', '2024-12-14 06:30:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(118, 26, 3, 10, 'Penulis Artikel di Majalah Ekonomi Nasional', '2024-12-11 07:20:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(119, 26, 10, 46, 'Wirausaha Online Shop dengan Omset 50jt/bulan', '2024-12-15 08:45:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(120, 27, 16, 54, 'Bendahara HIMA Manajemen', '2024-12-12 09:00:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(121, 27, 22, 69, 'Tim Promosi UNPAM di Job Fair Jakarta', '2024-12-16 02:15:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(122, 28, 4, 89, 'Delegasi International Law Conference 2024', '2024-12-17 03:30:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(123, 28, 5, 93, 'Koordinator Bantuan Hukum Masyarakat', '2024-12-19 04:45:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(124, 29, 13, 50, 'Pencipta Lagu untuk Drama Musical Kampus', '2024-12-18 06:00:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(125, 29, 3, 9, 'Penulis Artikel Bahasa di Media Internasional', '2024-12-20 07:15:00', 'approved', 1, '2025-01-02 04:22:16', 'Aktivitas telah disetujui. Terima kasih atas kontribusinya!', '2025-01-02 04:17:29'),
(126, 1, 21, 67, 'asdasd', '2025-01-02 04:32:22', 'approved', 1, '2025-01-02 04:32:45', 'asd', '2025-01-02 04:32:22'),
(127, 1, 15, 52, 'test', '2025-01-02 04:40:46', 'approved', 1, '2025-01-02 04:41:09', 'keren', '2025-01-02 04:40:46'),
(128, 31, 3, 10, 'penulisan media massa pada aktivitas pengabdian kepada masyarakat', '2025-01-02 07:10:45', 'approved', 1, '2025-01-02 07:13:22', 'terima', '2025-01-02 07:10:45'),
(129, 1, 3, 9, 'Menulis artikel di kompasiana', '2025-01-02 07:45:20', 'approved', 1, '2025-01-02 07:47:56', 'oke', '2025-01-02 07:45:20');

-- --------------------------------------------------------

--
-- Table structure for table `user_violations`
--

CREATE TABLE `user_violations` (
  `violation_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `violation_type_id` int(11) DEFAULT NULL,
  `violation_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `statement` varchar(100) NOT NULL,
  `violation_photo` varchar(100) NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `reviewed_by` int(11) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_violations`
--

INSERT INTO `user_violations` (`violation_id`, `user_id`, `violation_type_id`, `violation_date`, `description`, `statement`, `violation_photo`, `status`, `reviewed_by`, `reviewed_at`, `created_at`) VALUES
(51, 1, 2, '2024-12-29', 'Rambut panjang', '67714e54e2c52_FB_IMG_1735365886382.jpg', '67714e54e268b_FB_IMG_1735313759313.jpg', 'approved', 1, '2024-12-29 18:07:11', '2024-12-29 13:27:48'),
(53, 1, 1, '2024-12-15', 'Terlambat masuk kelas', 'dummy_statement_1.jpg', 'dummy_photo_1.jpg', 'approved', 1, '2025-01-01 14:13:45', '2025-01-01 20:40:49'),
(54, 2, 2, '2024-12-16', 'Menggunakan sandal ke kampus', 'dummy_statement_2.jpg', 'dummy_photo_2.jpg', 'approved', 1, '2025-01-01 14:16:32', '2025-01-01 20:40:49'),
(55, 9, 1, '2024-12-17', 'Tidak memakai kartu identitas', 'dummy_statement_3.jpg', 'dummy_photo_3.jpg', 'approved', 1, '2025-01-01 14:19:18', '2025-01-01 20:40:49'),
(56, 10, 2, '2024-12-18', 'Rambut tidak sesuai ketentuan', 'dummy_statement_4.jpg', 'dummy_photo_4.jpg', 'approved', 1, '2025-01-01 14:22:47', '2025-01-01 20:40:49'),
(57, 11, 1, '2024-12-19', 'Parkir tidak pada tempatnya', 'dummy_statement_5.jpg', 'dummy_photo_5.jpg', 'approved', 1, '2025-01-01 14:25:55', '2025-01-01 20:40:49'),
(58, 12, 2, '2024-12-20', 'Memakai celana jeans robek', 'dummy_statement_6.jpg', 'dummy_photo_6.jpg', 'approved', 1, '2025-01-01 14:28:14', '2025-01-01 20:40:49'),
(60, 14, 3, '2024-12-22', 'Membuang sampah sembarangan', 'dummy_statement_8.jpg', 'dummy_photo_8.jpg', 'approved', 1, '2025-01-01 14:34:22', '2025-01-01 20:40:49'),
(61, 15, 2, '2024-12-23', 'Bermain kartu di lingkungan kampus', 'dummy_statement_9.jpg', 'dummy_photo_9.jpg', 'approved', 1, '2025-01-01 14:37:48', '2025-01-01 20:40:49'),
(62, 1, 1, '2024-12-15', 'Terlambat masuk kelas', 'statement_terlambat_001.jpg', 'foto_terlambat_001.jpg', 'approved', 1, '2025-01-01 14:40:15', '2025-01-01 20:41:34'),
(63, 2, 2, '2024-12-16', 'Menggunakan sandal ke kampus', 'statement_sandal_001.jpg', 'foto_sandal_001.jpg', 'approved', 1, '2025-01-01 14:43:33', '2025-01-01 20:41:34'),
(64, 9, 1, '2024-12-17', 'Tidak memakai kartu identitas', 'statement_kartu_001.jpg', 'foto_kartu_001.jpg', 'approved', 1, '2025-01-01 14:46:59', '2025-01-01 20:41:34'),
(65, 10, 2, '2024-12-18', 'Rambut tidak sesuai ketentuan', 'statement_rambut_001.jpg', 'foto_rambut_001.jpg', 'approved', 1, '2025-01-01 14:49:27', '2025-01-01 20:41:34'),
(66, 11, 1, '2024-12-19', 'Parkir tidak pada tempatnya', 'statement_parkir_001.jpg', 'foto_parkir_001.jpg', 'approved', 1, '2025-01-01 14:52:44', '2025-01-01 20:41:34'),
(67, 20, 1, '2024-12-01', 'Terlambat masuk kelas - Pelanggaran telah ditinjau dan disetujui.', 'statement_001.jpg', 'violation_001.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(68, 21, 2, '2024-12-02', 'Memakai sendal ke kampus - Pelanggaran telah ditinjau dan disetujui.', 'statement_002.jpg', 'violation_002.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(69, 22, 1, '2024-12-03', 'Tidak memakai helm di area kampus - Pelanggaran telah ditinjau dan disetujui.', 'statement_003.jpg', 'violation_003.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(70, 23, 2, '2024-12-04', 'Memakai kaos oblong - Pelanggaran telah ditinjau dan disetujui.', 'statement_004.jpg', 'violation_004.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(71, 24, 1, '2024-12-05', 'Parkir tidak pada tempatnya - Pelanggaran telah ditinjau dan disetujui.', 'statement_005.jpg', 'violation_005.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(72, 25, 3, '2024-12-06', 'Merusak properti laboratorium - Pelanggaran telah ditinjau dan disetujui.', 'statement_006.jpg', 'violation_006.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(73, 26, 1, '2024-12-07', 'Tidak membawa kartu mahasiswa - Pelanggaran telah ditinjau dan disetujui.', 'statement_007.jpg', 'violation_007.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(74, 27, 2, '2024-12-08', 'Rambut diwarnai - Pelanggaran telah ditinjau dan disetujui.', 'statement_008.jpg', 'violation_008.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(75, 28, 1, '2024-12-09', 'Makan di ruang kelas - Pelanggaran telah ditinjau dan disetujui.', 'statement_009.jpg', 'violation_009.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(76, 29, 2, '2024-12-10', 'Bermain kartu di kampus - Pelanggaran telah ditinjau dan disetujui.', 'statement_010.jpg', 'violation_010.jpg', 'approved', 1, '2025-01-02 04:23:03', '2025-01-02 04:17:29'),
(77, 1, 2, '2025-01-02', 'Rambut panjang', '677619047a463_67692c4f82a3a_bpr.png', '677619047a1ca_screencapture-127-0-0-1-5500-data-pribadi-html-2024-12-27-21_04_22.png', 'approved', 1, '2025-01-02 04:42:00', '2025-01-02 04:41:40'),
(79, 1, 1, '2025-01-02', 'asd', '67763a3c13091_Feed Filkom Desember (4).png', '67763a3c12629_screencapture-127-0-0-1-5500-data-pribadi-html-2024-12-27-21_04_22.png', 'approved', 1, '2025-01-02 07:52:13', '2025-01-02 07:03:24'),
(80, 31, 1, '2025-01-02', 'rambut panjang', '67763b9b9ecec_Screenshot 2024-06-13 221343.png', '67763b9b9e5a0_Screenshot 2024-06-13 221343.png', 'approved', 1, '2025-01-02 07:12:57', '2025-01-02 07:09:15'),
(81, 1, 3, '2025-01-02', 'Merokok di area kampus', '6776444e80f26_67692c4f82a3a_bpr.png', '6776444e80c21_screencapture-127-0-0-1-5500-data-pribadi-html-2024-12-27-21_04_22.png', 'approved', 1, '2025-01-02 07:53:16', '2025-01-02 07:46:22'),
(82, 1, 2, '2025-01-02', 'rambut panjang', '6776461de3301_Feed Filkom Desember (4).png', '6776461de2e8f_screencapture-127-0-0-1-5500-data-pribadi-html-2024-12-27-21_04_22.png', 'pending', NULL, NULL, '2025-01-02 07:54:05');

-- --------------------------------------------------------

--
-- Table structure for table `violation_types`
--

CREATE TABLE `violation_types` (
  `violation_type_id` int(11) NOT NULL,
  `type_name` varchar(50) NOT NULL,
  `points` int(11) NOT NULL,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `violation_types`
--

INSERT INTO `violation_types` (`violation_type_id`, `type_name`, `points`, `description`) VALUES
(1, 'Ringan', 10, 'Pelanggaran kategori ringan'),
(2, 'Sedang', 15, 'Pelanggaran kategori sedang'),
(3, 'Berat', 20, 'Pelanggaran kategori berat');

-- --------------------------------------------------------

--
-- Structure for view `prodi_summary`
--
DROP TABLE IF EXISTS `prodi_summary`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `prodi_summary`  AS SELECT `p`.`nama_prodi` AS `nama_prodi`, `f`.`nama_fakultas` AS `nama_fakultas`, `al`.`system_id` AS `system_id`, count(`ua`.`submission_id`) AS `total_aktivitas`, sum(`al`.`points`) AS `total_points` FROM ((((`prodi` `p` left join `fakultas` `f` on(`p`.`fakultas_id` = `f`.`fakultas_id`)) left join `users` `u` on(`p`.`prodi_id` = `u`.`prodi_id`)) left join `user_activities` `ua` on(`u`.`user_id` = `ua`.`user_id`)) left join `activity_levels` `al` on(`ua`.`activity_level_id` = `al`.`level_id`)) GROUP BY `p`.`prodi_id`, `f`.`nama_fakultas`, `al`.`system_id` ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`activity_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `activity_documents`
--
ALTER TABLE `activity_documents`
  ADD PRIMARY KEY (`doc_id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `doc_type_id` (`doc_type_id`);

--
-- Indexes for table `activity_levels`
--
ALTER TABLE `activity_levels`
  ADD PRIMARY KEY (`level_id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `period_id` (`period_id`),
  ADD KEY `system_id` (`system_id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`admin_id`),
  ADD UNIQUE KEY `nidn` (`nidn`);

--
-- Indexes for table `assessment_periods`
--
ALTER TABLE `assessment_periods`
  ADD PRIMARY KEY (`period_id`);

--
-- Indexes for table `assessment_systems`
--
ALTER TABLE `assessment_systems`
  ADD PRIMARY KEY (`system_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `document_types`
--
ALTER TABLE `document_types`
  ADD PRIMARY KEY (`doc_type_id`);

--
-- Indexes for table `document_uploads`
--
ALTER TABLE `document_uploads`
  ADD PRIMARY KEY (`upload_id`),
  ADD KEY `submission_id` (`submission_id`),
  ADD KEY `doc_type_id` (`doc_type_id`);

--
-- Indexes for table `fakultas`
--
ALTER TABLE `fakultas`
  ADD PRIMARY KEY (`fakultas_id`);

--
-- Indexes for table `prodi`
--
ALTER TABLE `prodi`
  ADD PRIMARY KEY (`prodi_id`),
  ADD KEY `fakultas_id` (`fakultas_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `nim` (`nim`),
  ADD KEY `prodi_id` (`prodi_id`);

--
-- Indexes for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD PRIMARY KEY (`submission_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `activity_id` (`activity_id`),
  ADD KEY `activity_level_id` (`activity_level_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `user_violations`
--
ALTER TABLE `user_violations`
  ADD PRIMARY KEY (`violation_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `violation_type_id` (`violation_type_id`),
  ADD KEY `reviewed_by` (`reviewed_by`);

--
-- Indexes for table `violation_types`
--
ALTER TABLE `violation_types`
  ADD PRIMARY KEY (`violation_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `activity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `activity_documents`
--
ALTER TABLE `activity_documents`
  MODIFY `doc_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `activity_levels`
--
ALTER TABLE `activity_levels`
  MODIFY `level_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=131;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `admin_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `assessment_periods`
--
ALTER TABLE `assessment_periods`
  MODIFY `period_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `assessment_systems`
--
ALTER TABLE `assessment_systems`
  MODIFY `system_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `document_types`
--
ALTER TABLE `document_types`
  MODIFY `doc_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `document_uploads`
--
ALTER TABLE `document_uploads`
  MODIFY `upload_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=196;

--
-- AUTO_INCREMENT for table `fakultas`
--
ALTER TABLE `fakultas`
  MODIFY `fakultas_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `prodi`
--
ALTER TABLE `prodi`
  MODIFY `prodi_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=177;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `user_activities`
--
ALTER TABLE `user_activities`
  MODIFY `submission_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=130;

--
-- AUTO_INCREMENT for table `user_violations`
--
ALTER TABLE `user_violations`
  MODIFY `violation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=83;

--
-- AUTO_INCREMENT for table `violation_types`
--
ALTER TABLE `violation_types`
  MODIFY `violation_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`);

--
-- Constraints for table `activity_documents`
--
ALTER TABLE `activity_documents`
  ADD CONSTRAINT `activity_documents_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `activity_documents_ibfk_2` FOREIGN KEY (`doc_type_id`) REFERENCES `document_types` (`doc_type_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `activity_levels`
--
ALTER TABLE `activity_levels`
  ADD CONSTRAINT `activity_levels_ibfk_1` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`),
  ADD CONSTRAINT `activity_levels_ibfk_2` FOREIGN KEY (`period_id`) REFERENCES `assessment_periods` (`period_id`),
  ADD CONSTRAINT `activity_levels_ibfk_3` FOREIGN KEY (`system_id`) REFERENCES `assessment_systems` (`system_id`);

--
-- Constraints for table `document_uploads`
--
ALTER TABLE `document_uploads`
  ADD CONSTRAINT `document_uploads_ibfk_1` FOREIGN KEY (`submission_id`) REFERENCES `user_activities` (`submission_id`),
  ADD CONSTRAINT `document_uploads_ibfk_2` FOREIGN KEY (`doc_type_id`) REFERENCES `document_types` (`doc_type_id`);

--
-- Constraints for table `prodi`
--
ALTER TABLE `prodi`
  ADD CONSTRAINT `prodi_ibfk_1` FOREIGN KEY (`fakultas_id`) REFERENCES `fakultas` (`fakultas_id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`prodi_id`) REFERENCES `prodi` (`prodi_id`) ON DELETE SET NULL;

--
-- Constraints for table `user_activities`
--
ALTER TABLE `user_activities`
  ADD CONSTRAINT `user_activities_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_activities_ibfk_2` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`activity_id`),
  ADD CONSTRAINT `user_activities_ibfk_3` FOREIGN KEY (`activity_level_id`) REFERENCES `activity_levels` (`level_id`),
  ADD CONSTRAINT `user_activities_ibfk_4` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`admin_id`);

--
-- Constraints for table `user_violations`
--
ALTER TABLE `user_violations`
  ADD CONSTRAINT `user_violations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `user_violations_ibfk_2` FOREIGN KEY (`violation_type_id`) REFERENCES `violation_types` (`violation_type_id`),
  ADD CONSTRAINT `user_violations_ibfk_3` FOREIGN KEY (`reviewed_by`) REFERENCES `admins` (`admin_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
