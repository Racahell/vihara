-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2026 at 08:20 PM
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
-- Database: `vihara`
--

-- --------------------------------------------------------

--
-- Table structure for table `activities`
--

CREATE TABLE `activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime DEFAULT NULL,
  `quota` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `registered_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activities`
--

INSERT INTO `activities` (`id`, `title`, `slug`, `description`, `location`, `start_at`, `end_at`, `quota`, `registered_count`, `is_active`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Puja Bakti Mingguan', 'puja-bakti-mingguan-ywls', 'Kegiatan rutin vihara untuk kebaktian dan kebersamaan umat.', 'Aula Utama Vihara', '2026-04-02 09:00:21', '2026-04-02 12:00:21', 150, 2, 1, 1, '2026-03-30 07:00:21', '2026-03-30 09:52:45', NULL),
(2, 'Meditasi Purnama', 'meditasi-purnama-bmww', 'Kegiatan rutin vihara untuk kebaktian dan kebersamaan umat.', 'Aula Utama Vihara', '2026-04-09 09:00:21', '2026-04-09 12:00:21', 120, 0, 1, 1, '2026-03-30 07:00:21', '2026-03-30 07:00:21', NULL),
(3, 'Bakti Sosial Vihara', 'bakti-sosial-vihara-niyt', 'Kegiatan rutin vihara untuk kebaktian dan kebersamaan umat.', 'Aula Utama Vihara', '2026-04-19 09:00:21', '2026-04-19 12:00:21', 80, 0, 1, 1, '2026-03-30 07:00:21', '2026-03-30 07:00:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `target_type` varchar(255) DEFAULT NULL,
  `target_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `target_type`, `target_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'login', 'users', 1, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 07:37:47'),
(2, 1, 'logout', 'users', 1, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 08:04:46'),
(3, 1, 'login', 'users', 1, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 08:09:10'),
(4, 1, 'logout', 'users', 1, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 08:09:38'),
(5, 3, 'login', 'users', 3, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 08:09:59'),
(6, 3, 'logout', 'users', 3, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:35:12'),
(7, 6, 'login', 'users', 6, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:42:46'),
(8, 6, 'register_activity', 'activity_registrations', 1, 'Daftar kegiatan: Puja Bakti Mingguan', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:43:03'),
(9, 6, 'create_donation', 'donations', 1, 'Donasi dibuat #1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:43:59'),
(10, 6, 'logout', 'users', 6, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:44:10'),
(11, 3, 'login', 'users', 3, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:44:32'),
(12, 3, 'logout', 'users', 3, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:45:59'),
(13, 6, 'login', 'users', 6, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:46:18'),
(14, 6, 'logout', 'users', 6, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:46:57'),
(15, 3, 'login', 'users', 3, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:47:09'),
(16, 3, 'checkin', 'activity_registrations', 1, 'Check-in kode: REG-260330-O6TVVV', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:48:08'),
(17, 3, 'walkin', 'activity_registrations', 2, 'Walk-in: bubub tercayangg', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 09:52:45'),
(18, 3, 'logout', 'users', 3, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 10:02:24'),
(19, 6, 'login', 'users', 6, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 10:02:37'),
(20, 6, 'logout', 'users', 6, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 10:04:33'),
(21, 3, 'login', 'users', 3, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 10:43:44'),
(22, 3, 'verify_donation', 'donations', 1, 'Verifikasi donasi #1 (reject)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 10:45:04'),
(23, 3, 'logout', 'users', 3, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 10:45:21'),
(24, 4, 'login', 'users', 4, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 10:56:07'),
(25, 4, 'logout', 'users', 4, 'Pengguna logout', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 10:58:31'),
(26, 1, 'login', 'users', 1, 'Pengguna berhasil login', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-03-30 11:15:28');

-- --------------------------------------------------------

--
-- Table structure for table `activity_registrations`
--

CREATE TABLE `activity_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `participant_name` varchar(255) NOT NULL,
  `participant_phone` varchar(32) DEFAULT NULL,
  `registration_code` varchar(255) NOT NULL,
  `qr_payload` varchar(255) NOT NULL,
  `registration_type` enum('regular','walkin') NOT NULL DEFAULT 'regular',
  `attendance_status` enum('belum','hadir') NOT NULL DEFAULT 'belum',
  `registered_at` datetime NOT NULL,
  `checked_in_at` datetime DEFAULT NULL,
  `checkin_method` enum('kode','manual') DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_registrations`
--

INSERT INTO `activity_registrations` (`id`, `activity_id`, `user_id`, `participant_name`, `participant_phone`, `registration_code`, `qr_payload`, `registration_type`, `attendance_status`, `registered_at`, `checked_in_at`, `checkin_method`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 6, 'umat', '081100000005', 'REG-260330-O6TVVV', 'reg:REG-260330-O6TVVV', 'regular', 'hadir', '2026-03-30 16:43:03', '2026-03-30 16:48:08', 'kode', 6, '2026-03-30 09:43:03', '2026-03-30 09:48:08', NULL),
(2, 1, NULL, 'bubub tercayangg', '085111021574', 'WALK-165245-334', 'walkin:69caaa5d90f53', 'walkin', 'hadir', '2026-03-30 16:52:45', '2026-03-30 16:52:45', 'manual', 3, '2026-03-30 09:52:45', '2026-03-30 09:52:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_logs`
--

CREATE TABLE `attendance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_registration_id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `checked_in_at` datetime NOT NULL,
  `method` enum('kode','manual') NOT NULL,
  `handled_by` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `attendance_logs`
--

INSERT INTO `attendance_logs` (`id`, `activity_registration_id`, `activity_id`, `user_id`, `checked_in_at`, `method`, `handled_by`, `notes`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 6, '2026-03-30 16:48:08', 'kode', 3, 'Check-in dengan kode pendaftaran', '2026-03-30 09:48:08', '2026-03-30 09:48:08'),
(2, 2, 1, NULL, '2026-03-30 16:52:45', 'manual', 3, 'Walk-in langsung hadir', '2026-03-30 09:52:45', '2026-03-30 09:52:45');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `discord_logs`
--

CREATE TABLE `discord_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `event` varchar(255) NOT NULL,
  `status_code` int(11) DEFAULT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `response_body` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donasi`
--

CREATE TABLE `donasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kategori_id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode_donasi` varchar(100) NOT NULL,
  `nama_donatur` varchar(150) NOT NULL,
  `email_donatur` varchar(150) DEFAULT NULL,
  `no_hp_donatur` varchar(30) DEFAULT NULL,
  `nominal` decimal(18,2) NOT NULL DEFAULT 0.00,
  `metode_donasi` enum('tunai','transfer','qris','gateway','manual') NOT NULL DEFAULT 'manual',
  `referensi_pembayaran` varchar(150) DEFAULT NULL,
  `file_bukti` varchar(255) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `status_verifikasi` enum('pending','berhasil','ditolak') NOT NULL DEFAULT 'pending',
  `diverifikasi_pada` datetime DEFAULT NULL,
  `diverifikasi_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `alasan_penolakan` text DEFAULT NULL,
  `nomor_kwitansi` varchar(100) DEFAULT NULL,
  `file_kwitansi` varchar(255) DEFAULT NULL,
  `didonasikan_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `donation_category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_id` bigint(20) UNSIGNED DEFAULT NULL,
  `donor_name` varchar(255) NOT NULL,
  `donor_email` varchar(255) DEFAULT NULL,
  `donor_phone` varchar(32) DEFAULT NULL,
  `amount` bigint(20) UNSIGNED NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `payment_method` enum('midtrans','transfer','cash') NOT NULL DEFAULT 'midtrans',
  `payment_status` enum('pending','paid','failed','expired') NOT NULL DEFAULT 'pending',
  `verification_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `midtrans_order_id` varchar(255) DEFAULT NULL,
  `midtrans_transaction_id` varchar(255) DEFAULT NULL,
  `payment_payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payment_payload`)),
  `bank_transfer_proof_path` varchar(255) DEFAULT NULL,
  `paid_at` datetime DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` datetime DEFAULT NULL,
  `rejection_reason` varchar(255) DEFAULT NULL,
  `receipt_number` varchar(255) DEFAULT NULL,
  `receipt_pdf_path` varchar(255) DEFAULT NULL,
  `donated_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donations`
--

INSERT INTO `donations` (`id`, `user_id`, `donation_category_id`, `activity_id`, `donor_name`, `donor_email`, `donor_phone`, `amount`, `note`, `payment_method`, `payment_status`, `verification_status`, `midtrans_order_id`, `midtrans_transaction_id`, `payment_payload`, `bank_transfer_proof_path`, `paid_at`, `verified_by`, `verified_at`, `rejection_reason`, `receipt_number`, `receipt_pdf_path`, `donated_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 6, 2, NULL, 'umat', 'umat@g', '081100000005', 900000, NULL, 'midtrans', 'pending', 'rejected', 'DON-20260330164359-FYMTXN', NULL, '{\"message\":\"Midtrans belum dikonfigurasi\"}', NULL, NULL, 3, '2026-03-30 17:45:04', 'tidak ada bukti', NULL, NULL, '2026-03-30 16:43:59', '2026-03-30 09:43:59', '2026-03-30 10:45:04', NULL),
(2, NULL, NULL, NULL, 'Anonim', NULL, '0865857567', 90000, NULL, 'transfer', 'pending', 'pending', NULL, NULL, '{\"channel\":\"qris\",\"donor_type\":\"anonymous\",\"bank_name\":\"BCA\",\"account_number\":\"1234567890\",\"account_holder\":\"Vihara Dharma Sejahtera\",\"verification_key\":\"3b180224-80bf-4f56-9f61-75d7888c54d0\",\"qris_payload\":\"VIHARA-DONASI|ID:DON69cab6d56c0d5|NOMINAL:90000|DONATUR:Anonim\"}', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-30 17:45:57', '2026-03-30 10:45:57', '2026-03-30 10:45:57', NULL),
(3, NULL, NULL, NULL, 'Anonim', NULL, '0865857567', 90000, NULL, 'transfer', 'paid', 'pending', NULL, NULL, '{\"channel\":\"qris\",\"donor_type\":\"anonymous\",\"bank_name\":\"BCA\",\"account_number\":\"1234567890\",\"account_holder\":\"Vihara Dharma Sejahtera\",\"verification_key\":\"6dd82063-b345-4d09-8dad-dbd5e68e63a4\",\"qris_payload\":\"VIHARA-DONASI|ID:DON69cab755bb593|NOMINAL:90000|DONATUR:Anonim\"}', 'donation-proofs/X7A3KJgBOzRy8jochKnmtQWMvI2fsJfa9WhV5FKz.jpg', '2026-03-30 17:48:44', NULL, NULL, NULL, NULL, NULL, '2026-03-30 17:48:05', '2026-03-30 10:48:05', '2026-03-30 10:48:44', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `donation_categories`
--

CREATE TABLE `donation_categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donation_categories`
--

INSERT INTO `donation_categories` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Donasi Umum', NULL, 1, '2026-03-30 07:00:21', '2026-03-30 07:30:18'),
(2, 'Donasi Pembangunan', NULL, 1, '2026-03-30 07:00:21', '2026-03-30 07:30:18'),
(3, 'Donasi Acara', NULL, 1, '2026-03-30 07:00:21', '2026-03-30 07:30:18');

-- --------------------------------------------------------

--
-- Table structure for table `donation_verification_logs`
--

CREATE TABLE `donation_verification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `donation_id` bigint(20) UNSIGNED NOT NULL,
  `acted_by` bigint(20) UNSIGNED DEFAULT NULL,
  `action` enum('approve','reject') NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `donation_verification_logs`
--

INSERT INTO `donation_verification_logs` (`id`, `donation_id`, `acted_by`, `action`, `reason`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'reject', 'tidak ada bukti', '2026-03-30 10:45:04', '2026-03-30 10:45:04');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_activities`
--

CREATE TABLE `favorite_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `activity_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorit_kegiatan`
--

CREATE TABLE `favorit_kegiatan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED NOT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `file_upload`
--

CREATE TABLE `file_upload` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_modul` varchar(100) NOT NULL,
  `referensi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_file` varchar(255) NOT NULL,
  `nama_file_asli` varchar(255) NOT NULL,
  `path_file` varchar(255) NOT NULL,
  `ekstensi_file` varchar(20) DEFAULT NULL,
  `ukuran_file` bigint(20) DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hak_akses_pengguna`
--

CREATE TABLE `hak_akses_pengguna` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `bisa_lihat` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_tambah` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_ubah` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_hapus` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_setujui` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_ekspor` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_impor` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_restore` tinyint(1) NOT NULL DEFAULT 0,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hak_akses_role`
--

CREATE TABLE `hak_akses_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` bigint(20) UNSIGNED NOT NULL,
  `bisa_lihat` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_tambah` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_ubah` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_hapus` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_setujui` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_ekspor` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_impor` tinyint(1) NOT NULL DEFAULT 0,
  `bisa_restore` tinyint(1) NOT NULL DEFAULT 0,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_donasi`
--

CREATE TABLE `kategori_donasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_kegiatan`
--

CREATE TABLE `kategori_kegiatan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `warna` varchar(20) DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kategori_konten_dharma`
--

CREATE TABLE `kategori_konten_dharma` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `kegiatan`
--

CREATE TABLE `kegiatan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kategori_id` bigint(20) UNSIGNED DEFAULT NULL,
  `judul_kegiatan` varchar(200) NOT NULL,
  `slug` varchar(220) DEFAULT NULL,
  `deskripsi` longtext DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `mulai_pada` datetime NOT NULL,
  `selesai_pada` datetime NOT NULL,
  `kuota` int(11) DEFAULT NULL,
  `jumlah_terdaftar` int(11) NOT NULL DEFAULT 0,
  `status_kegiatan` enum('draft','open','full','closed','cancelled') NOT NULL DEFAULT 'draft',
  `berulang` tinyint(1) NOT NULL DEFAULT 0,
  `tipe_perulangan` enum('harian','mingguan','bulanan','tahunan') DEFAULT NULL,
  `interval_perulangan` int(11) DEFAULT 1,
  `perulangan_sampai` date DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `perlu_persetujuan` tinyint(1) NOT NULL DEFAULT 0,
  `tayang_pada` datetime DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `successful` tinyint(1) NOT NULL DEFAULT 0,
  `logged_in_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `user_id`, `email`, `ip_address`, `user_agent`, `successful`, `logged_in_at`) VALUES
(1, NULL, 'superadmin@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 0, '2026-03-30 07:36:11'),
(2, 1, 'superadmin@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 07:37:47'),
(3, 1, 'superadmin@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 08:09:10'),
(4, 3, 'admin@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 08:09:59'),
(5, 6, 'umat@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 09:42:46'),
(6, 3, 'admin@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 09:44:32'),
(7, NULL, 'umat@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 0, '2026-03-30 09:46:11'),
(8, 6, 'umat@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 09:46:18'),
(9, 3, 'admin@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 09:47:09'),
(10, 6, 'umat@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 10:02:37'),
(11, 3, 'admin@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 10:43:44'),
(12, 4, 'owner@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 10:56:07'),
(13, 1, 'superadmin@g', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 1, '2026-03-30 11:15:28');

-- --------------------------------------------------------

--
-- Table structure for table `log_aktivitas`
--

CREATE TABLE `log_aktivitas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_modul` varchar(100) NOT NULL,
  `tipe_aksi` varchar(50) NOT NULL,
  `referensi_tabel` varchar(100) DEFAULT NULL,
  `referensi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `data_lama` longtext DEFAULT NULL,
  `data_baru` longtext DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `aktivitas_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_backup`
--

CREATE TABLE `log_backup` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_backup` varchar(200) NOT NULL,
  `tipe_backup` enum('database','file','full','partial') NOT NULL DEFAULT 'database',
  `path_file` varchar(255) DEFAULT NULL,
  `ukuran_file` bigint(20) DEFAULT NULL,
  `mode_backup` enum('manual','otomatis','update_restore') NOT NULL DEFAULT 'manual',
  `status_backup` enum('pending','success','failed') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `direstore_pada` datetime DEFAULT NULL,
  `direstore_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_discord`
--

CREATE TABLE `log_discord` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_event` varchar(100) NOT NULL,
  `payload` longtext DEFAULT NULL,
  `status_kirim` enum('pending','sent','failed') NOT NULL DEFAULT 'pending',
  `response_text` text DEFAULT NULL,
  `dikirim_pada` datetime DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_error`
--

CREATE TABLE `log_error` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode_error` varchar(50) DEFAULT NULL,
  `pesan_error` text NOT NULL,
  `file_error` varchar(255) DEFAULT NULL,
  `baris_error` int(11) DEFAULT NULL,
  `url` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `terjadi_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_impor`
--

CREATE TABLE `log_impor` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tipe_impor` enum('pengguna','barang','peserta','donasi') NOT NULL,
  `nama_file` varchar(255) NOT NULL,
  `total_baris` int(11) NOT NULL DEFAULT 0,
  `baris_berhasil` int(11) NOT NULL DEFAULT 0,
  `baris_gagal` int(11) NOT NULL DEFAULT 0,
  `status_impor` enum('pending','success','failed','partial') NOT NULL DEFAULT 'pending',
  `catatan` text DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_kehadiran`
--

CREATE TABLE `log_kehadiran` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pendaftaran_id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `aksi` enum('checkin','checkout','manual_checkin','scan_gagal') NOT NULL,
  `waktu_aksi` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_login`
--

CREATE TABLE `log_login` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `login_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `logout_pada` datetime DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `status_login` enum('berhasil','gagal') NOT NULL DEFAULT 'berhasil',
  `alasan_gagal` text DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_otp_reset_password`
--

CREATE TABLE `log_otp_reset_password` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED NOT NULL,
  `kode_otp` varchar(20) NOT NULL,
  `dikirim_melalui` enum('email','whatsapp') NOT NULL,
  `kadaluarsa_pada` datetime NOT NULL,
  `diverifikasi_pada` datetime DEFAULT NULL,
  `status_otp` enum('sent','used','expired','failed') NOT NULL DEFAULT 'sent',
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `log_verifikasi_donasi`
--

CREATE TABLE `log_verifikasi_donasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `donasi_id` bigint(20) UNSIGNED NOT NULL,
  `aksi_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `tipe_aksi` enum('approve','reject','update') NOT NULL,
  `catatan_aksi` text DEFAULT NULL,
  `waktu_aksi` datetime NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_aplikasi`
--

CREATE TABLE `menu_aplikasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nama_menu` varchar(100) NOT NULL,
  `kode_menu` varchar(100) NOT NULL,
  `rute` varchar(255) DEFAULT NULL,
  `ikon` varchar(100) DEFAULT NULL,
  `urutan` int(11) NOT NULL DEFAULT 0,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_03_30_000100_create_rbac_tables', 1),
(5, '2026_03_30_000200_create_activity_tables', 1),
(6, '2026_03_30_000300_create_donation_tables', 1),
(7, '2026_03_30_000400_create_audit_tables', 1),
(8, '2026_03_30_001000_add_registration_ip_to_users_table', 2),
(9, '2026_03_31_090000_create_website_settings_table', 3);

-- --------------------------------------------------------

--
-- Table structure for table `notifikasi`
--

CREATE TABLE `notifikasi` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `judul` varchar(200) NOT NULL,
  `pesan` text NOT NULL,
  `tipe_notifikasi` enum('sistem','email','push','discord','whatsapp') NOT NULL DEFAULT 'sistem',
  `referensi_tabel` varchar(100) DEFAULT NULL,
  `referensi_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sudah_dibaca` tinyint(1) NOT NULL DEFAULT 0,
  `dikirim_pada` datetime DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pendaftaran_kegiatan`
--

CREATE TABLE `pendaftaran_kegiatan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kegiatan_id` bigint(20) UNSIGNED NOT NULL,
  `pengguna_id` bigint(20) UNSIGNED DEFAULT NULL,
  `kode_pendaftaran` varchar(100) NOT NULL,
  `nama_peserta` varchar(150) NOT NULL,
  `email_peserta` varchar(150) DEFAULT NULL,
  `no_hp_peserta` varchar(30) DEFAULT NULL,
  `tipe_pendaftaran` enum('online','walkin') NOT NULL DEFAULT 'online',
  `status_kehadiran` enum('belum_hadir','hadir','batal') NOT NULL DEFAULT 'belum_hadir',
  `status_persetujuan` enum('pending','disetujui','ditolak') NOT NULL DEFAULT 'disetujui',
  `qr_code` varchar(255) DEFAULT NULL,
  `checkin_pada` datetime DEFAULT NULL,
  `checkin_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `metode_checkin` enum('qr','manual') DEFAULT NULL,
  `catatan` text DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_captcha`
--

CREATE TABLE `pengaturan_captcha` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mode_captcha` enum('online','offline') NOT NULL DEFAULT 'offline',
  `site_key` varchar(255) DEFAULT NULL,
  `secret_key` varchar(255) DEFAULT NULL,
  `panjang_captcha_offline` int(11) NOT NULL DEFAULT 5,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengaturan_sistem`
--

CREATE TABLE `pengaturan_sistem` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kunci_pengaturan` varchar(100) NOT NULL,
  `nilai_pengaturan` longtext DEFAULT NULL,
  `grup_pengaturan` varchar(100) DEFAULT NULL,
  `deskripsi` text DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `nama_lengkap` varchar(150) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `no_hp` varchar(30) DEFAULT NULL,
  `no_whatsapp` varchar(30) DEFAULT NULL,
  `kata_sandi` varchar(255) NOT NULL,
  `jenis_kelamin` enum('L','P') DEFAULT NULL,
  `tempat_lahir` varchar(100) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `email_terverifikasi_pada` datetime DEFAULT NULL,
  `token_verifikasi_email` varchar(255) DEFAULT NULL,
  `kadaluarsa_token_verifikasi` datetime DEFAULT NULL,
  `token_reset_password` varchar(255) DEFAULT NULL,
  `kadaluarsa_reset_password` datetime DEFAULT NULL,
  `reset_melalui` enum('email','whatsapp') DEFAULT NULL,
  `token_ingat_saya` varchar(255) DEFAULT NULL,
  `login_terakhir_pada` datetime DEFAULT NULL,
  `ip_terakhir` varchar(45) DEFAULT NULL,
  `user_agent_terakhir` text DEFAULT NULL,
  `latitude_terakhir` decimal(10,7) DEFAULT NULL,
  `longitude_terakhir` decimal(10,7) DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `online` tinyint(1) NOT NULL DEFAULT 0,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengurus`
--

CREATE TABLE `pengurus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_pengurus` varchar(150) NOT NULL,
  `jabatan` varchar(100) NOT NULL,
  `role_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `no_hp` varchar(30) DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `tanggal_mulai` date DEFAULT NULL,
  `tanggal_selesai` date DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permission_role`
--

CREATE TABLE `permission_role` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `permission_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `profil_vihara`
--

CREATE TABLE `profil_vihara` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_vihara` varchar(200) NOT NULL,
  `deskripsi_singkat` text DEFAULT NULL,
  `deskripsi_lengkap` longtext DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(30) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `link_google_maps` text DEFAULT NULL,
  `latitude` decimal(10,7) DEFAULT NULL,
  `longitude` decimal(10,7) DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `banner` varchar(255) DEFAULT NULL,
  `nama_manager` varchar(150) DEFAULT NULL,
  `nama_website` varchar(150) DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `riwayat_pengaturan`
--

CREATE TABLE `riwayat_pengaturan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `pengaturan_id` bigint(20) UNSIGNED NOT NULL,
  `nilai_lama` longtext DEFAULT NULL,
  `nilai_baru` longtext DEFAULT NULL,
  `diubah_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_ulang_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_ulang_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_system` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `is_system`, `created_at`, `updated_at`) VALUES
(1, 'Superadmin', 'superadmin', 1, '2026-03-30 07:00:21', '2026-03-30 07:00:21'),
(2, 'Admin', 'admin', 1, '2026-03-30 07:00:21', '2026-03-30 07:00:21'),
(3, 'Owner / Ketua', 'owner', 1, '2026-03-30 07:00:21', '2026-03-30 07:00:21'),
(4, 'Petugas', 'petugas', 1, '2026-03-30 07:00:21', '2026-03-30 07:00:21'),
(5, 'Umat', 'umat', 1, '2026-03-30 07:00:21', '2026-03-30 07:00:21');

-- --------------------------------------------------------

--
-- Table structure for table `role_pengguna`
--

CREATE TABLE `role_pengguna` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nama_role` varchar(100) NOT NULL,
  `kode_role` varchar(50) NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `aktif` tinyint(1) NOT NULL DEFAULT 1,
  `dibuat_pada` datetime NOT NULL DEFAULT current_timestamp(),
  `dibuat_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `diubah_pada` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `diubah_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `dihapus_pada` datetime DEFAULT NULL,
  `dihapus_oleh` bigint(20) UNSIGNED DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`role_id`, `user_id`) VALUES
(1, 1),
(2, 3),
(3, 4),
(4, 5),
(5, 6);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('vfsbcCpBFyp7dzsoDvBfRBb5TgbIo3UX7f2J3Wn8', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVWZnZDhoQUVGZm9DY0xHV1lwMHVBeW9JSEZwTDl0cHpEdzVta0ZWRyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDQ6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi93ZWJzaXRlLXNldHRpbmdzIjtzOjU6InJvdXRlIjtzOjI3OiJhZG1pbi53ZWJzaXRlLXNldHRpbmdzLmVkaXQiO31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO30=', 1774894543);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(32) DEFAULT NULL,
  `registration_ip` varchar(45) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `activated_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `last_login_user_agent` text DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `phone`, `registration_ip`, `email_verified_at`, `password`, `is_active`, `activated_at`, `last_login_at`, `last_login_ip`, `last_login_user_agent`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'superadmin', 'superadmin', 'superadmin@g', '081100000001', NULL, '2026-03-30 07:30:17', '$2y$12$580uPwtviPF5T6S2v5jeHeICHozIH3BW9jyneFK2WgWgEaZspJY8i', 1, '2026-03-30 07:30:17', '2026-03-30 11:15:28', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-03-30 07:00:21', '2026-03-30 11:15:28', NULL),
(3, 'admin', 'admin', 'admin@g', '081100000002', NULL, '2026-03-30 07:30:17', '$2y$12$JuRGELdoPcudUBOKottwaOj4apaNBtvYaC5WMi0ljqwKxCmD2WPQe', 1, '2026-03-30 07:30:17', '2026-03-30 10:43:44', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-03-30 07:30:17', '2026-03-30 10:43:44', NULL),
(4, 'owner', 'owner', 'owner@g', '081100000003', NULL, '2026-03-30 07:30:18', '$2y$12$U1BR/ybdklW0HBtYLsTLleFJeUEKCWcyuwuyus/J0nYASb8IborxS', 1, '2026-03-30 07:30:18', '2026-03-30 10:56:07', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-03-30 07:30:18', '2026-03-30 10:56:07', NULL),
(5, 'petugas', 'petugas', 'petugas@g', '081100000004', NULL, '2026-03-30 07:30:18', '$2y$12$O3W0oWssnvWTHTnA2lpNM.TP.L4/obNhOJSODs2V1llNruFNqqIDe', 1, '2026-03-30 07:30:18', NULL, NULL, NULL, NULL, '2026-03-30 07:30:18', '2026-03-30 07:30:18', NULL),
(6, 'umat', 'umat', 'umat@g', '081100000005', NULL, '2026-03-30 07:30:18', '$2y$12$BVSMLYJUodcEMqclbyraNO9CDD2l5Jyrf2Xcum40x0dB0TyZ4Mu.K', 1, '2026-03-30 07:30:18', '2026-03-30 10:02:37', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', NULL, '2026-03-30 07:30:18', '2026-03-30 10:02:37', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `website_settings`
--

CREATE TABLE `website_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activities`
--
ALTER TABLE `activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `activities_slug_unique` (`slug`),
  ADD KEY `activities_created_by_foreign` (`created_by`);

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `activity_registrations`
--
ALTER TABLE `activity_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `activity_registrations_registration_code_unique` (`registration_code`),
  ADD UNIQUE KEY `activity_registrations_qr_payload_unique` (`qr_payload`),
  ADD UNIQUE KEY `activity_registrations_activity_id_user_id_unique` (`activity_id`,`user_id`),
  ADD KEY `activity_registrations_user_id_foreign` (`user_id`),
  ADD KEY `activity_registrations_created_by_foreign` (`created_by`);

--
-- Indexes for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attendance_logs_activity_registration_id_foreign` (`activity_registration_id`),
  ADD KEY `attendance_logs_activity_id_foreign` (`activity_id`),
  ADD KEY `attendance_logs_user_id_foreign` (`user_id`),
  ADD KEY `attendance_logs_handled_by_foreign` (`handled_by`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `discord_logs`
--
ALTER TABLE `discord_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donasi`
--
ALTER TABLE `donasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_donasi` (`kode_donasi`),
  ADD KEY `fk_donasi_pengguna` (`pengguna_id`),
  ADD KEY `fk_donasi_kategori` (`kategori_id`),
  ADD KEY `fk_donasi_kegiatan` (`kegiatan_id`),
  ADD KEY `fk_donasi_diverifikasi_oleh` (`diverifikasi_oleh`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `donations_midtrans_order_id_unique` (`midtrans_order_id`),
  ADD UNIQUE KEY `donations_receipt_number_unique` (`receipt_number`),
  ADD KEY `donations_user_id_foreign` (`user_id`),
  ADD KEY `donations_donation_category_id_foreign` (`donation_category_id`),
  ADD KEY `donations_activity_id_foreign` (`activity_id`),
  ADD KEY `donations_verified_by_foreign` (`verified_by`);

--
-- Indexes for table `donation_categories`
--
ALTER TABLE `donation_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donation_verification_logs`
--
ALTER TABLE `donation_verification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `donation_verification_logs_donation_id_foreign` (`donation_id`),
  ADD KEY `donation_verification_logs_acted_by_foreign` (`acted_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `favorite_activities`
--
ALTER TABLE `favorite_activities`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `favorite_activities_activity_id_user_id_unique` (`activity_id`,`user_id`),
  ADD KEY `favorite_activities_user_id_foreign` (`user_id`);

--
-- Indexes for table `favorit_kegiatan`
--
ALTER TABLE `favorit_kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_favorit_pengguna_kegiatan` (`pengguna_id`,`kegiatan_id`),
  ADD KEY `fk_favorit_kegiatan_kegiatan` (`kegiatan_id`);

--
-- Indexes for table `file_upload`
--
ALTER TABLE `file_upload`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hak_akses_pengguna`
--
ALTER TABLE `hak_akses_pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pengguna_menu` (`pengguna_id`,`menu_id`),
  ADD KEY `fk_hak_akses_pengguna_menu` (`menu_id`);

--
-- Indexes for table `hak_akses_role`
--
ALTER TABLE `hak_akses_role`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_role_menu` (`role_id`,`menu_id`),
  ADD KEY `fk_hak_akses_role_menu` (`menu_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `kategori_donasi`
--
ALTER TABLE `kategori_donasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `kategori_kegiatan`
--
ALTER TABLE `kategori_kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `kategori_konten_dharma`
--
ALTER TABLE `kategori_konten_dharma`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_kategori` (`nama_kategori`);

--
-- Indexes for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`),
  ADD KEY `fk_kegiatan_kategori` (`kategori_id`);

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `login_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_aktivitas_pengguna` (`pengguna_id`);

--
-- Indexes for table `log_backup`
--
ALTER TABLE `log_backup`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_backup_restore_oleh` (`direstore_oleh`);

--
-- Indexes for table `log_discord`
--
ALTER TABLE `log_discord`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_discord_pengguna` (`pengguna_id`);

--
-- Indexes for table `log_error`
--
ALTER TABLE `log_error`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_error_pengguna` (`pengguna_id`);

--
-- Indexes for table `log_impor`
--
ALTER TABLE `log_impor`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `log_kehadiran`
--
ALTER TABLE `log_kehadiran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_kehadiran_pendaftaran` (`pendaftaran_id`),
  ADD KEY `fk_log_kehadiran_kegiatan` (`kegiatan_id`),
  ADD KEY `fk_log_kehadiran_pengguna` (`pengguna_id`);

--
-- Indexes for table `log_login`
--
ALTER TABLE `log_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_login_pengguna` (`pengguna_id`);

--
-- Indexes for table `log_otp_reset_password`
--
ALTER TABLE `log_otp_reset_password`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_otp_reset_password_pengguna` (`pengguna_id`);

--
-- Indexes for table `log_verifikasi_donasi`
--
ALTER TABLE `log_verifikasi_donasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_log_verifikasi_donasi_donasi` (`donasi_id`),
  ADD KEY `fk_log_verifikasi_donasi_pengguna` (`aksi_oleh`);

--
-- Indexes for table `menu_aplikasi`
--
ALTER TABLE `menu_aplikasi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_menu` (`kode_menu`),
  ADD KEY `fk_menu_parent` (`parent_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_notifikasi_pengguna` (`pengguna_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `pendaftaran_kegiatan`
--
ALTER TABLE `pendaftaran_kegiatan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode_pendaftaran` (`kode_pendaftaran`),
  ADD KEY `fk_pendaftaran_kegiatan_kegiatan` (`kegiatan_id`),
  ADD KEY `fk_pendaftaran_kegiatan_pengguna` (`pengguna_id`),
  ADD KEY `fk_pendaftaran_kegiatan_checkin_oleh` (`checkin_oleh`);

--
-- Indexes for table `pengaturan_captcha`
--
ALTER TABLE `pengaturan_captcha`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pengaturan_sistem`
--
ALTER TABLE `pengaturan_sistem`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kunci_pengaturan` (`kunci_pengaturan`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_pengguna_role` (`role_id`);

--
-- Indexes for table `pengurus`
--
ALTER TABLE `pengurus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_pengurus_role` (`role_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_slug_unique` (`slug`);

--
-- Indexes for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD PRIMARY KEY (`role_id`,`permission_id`),
  ADD KEY `permission_role_permission_id_foreign` (`permission_id`);

--
-- Indexes for table `profil_vihara`
--
ALTER TABLE `profil_vihara`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `riwayat_pengaturan`
--
ALTER TABLE `riwayat_pengaturan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_riwayat_pengaturan_pengaturan` (`pengaturan_id`),
  ADD KEY `fk_riwayat_pengaturan_pengguna` (`diubah_oleh`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_pengguna`
--
ALTER TABLE `role_pengguna`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nama_role` (`nama_role`),
  ADD UNIQUE KEY `kode_role` (`kode_role`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`role_id`,`user_id`),
  ADD KEY `role_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- Indexes for table `website_settings`
--
ALTER TABLE `website_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `website_settings_key_unique` (`key`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activities`
--
ALTER TABLE `activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `activity_registrations`
--
ALTER TABLE `activity_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `discord_logs`
--
ALTER TABLE `discord_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donasi`
--
ALTER TABLE `donasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `donations`
--
ALTER TABLE `donations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `donation_categories`
--
ALTER TABLE `donation_categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `donation_verification_logs`
--
ALTER TABLE `donation_verification_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorite_activities`
--
ALTER TABLE `favorite_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorit_kegiatan`
--
ALTER TABLE `favorit_kegiatan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `file_upload`
--
ALTER TABLE `file_upload`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hak_akses_pengguna`
--
ALTER TABLE `hak_akses_pengguna`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hak_akses_role`
--
ALTER TABLE `hak_akses_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_donasi`
--
ALTER TABLE `kategori_donasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_kegiatan`
--
ALTER TABLE `kategori_kegiatan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kategori_konten_dharma`
--
ALTER TABLE `kategori_konten_dharma`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `kegiatan`
--
ALTER TABLE `kegiatan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_backup`
--
ALTER TABLE `log_backup`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_discord`
--
ALTER TABLE `log_discord`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_error`
--
ALTER TABLE `log_error`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_impor`
--
ALTER TABLE `log_impor`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_kehadiran`
--
ALTER TABLE `log_kehadiran`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_login`
--
ALTER TABLE `log_login`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_otp_reset_password`
--
ALTER TABLE `log_otp_reset_password`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `log_verifikasi_donasi`
--
ALTER TABLE `log_verifikasi_donasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_aplikasi`
--
ALTER TABLE `menu_aplikasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notifikasi`
--
ALTER TABLE `notifikasi`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pendaftaran_kegiatan`
--
ALTER TABLE `pendaftaran_kegiatan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengaturan_captcha`
--
ALTER TABLE `pengaturan_captcha`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengaturan_sistem`
--
ALTER TABLE `pengaturan_sistem`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pengurus`
--
ALTER TABLE `pengurus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `profil_vihara`
--
ALTER TABLE `profil_vihara`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `riwayat_pengaturan`
--
ALTER TABLE `riwayat_pengaturan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_pengguna`
--
ALTER TABLE `role_pengguna`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `website_settings`
--
ALTER TABLE `website_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activities`
--
ALTER TABLE `activities`
  ADD CONSTRAINT `activities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `activity_registrations`
--
ALTER TABLE `activity_registrations`
  ADD CONSTRAINT `activity_registrations_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_registrations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `activity_registrations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `attendance_logs`
--
ALTER TABLE `attendance_logs`
  ADD CONSTRAINT `attendance_logs_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_logs_activity_registration_id_foreign` FOREIGN KEY (`activity_registration_id`) REFERENCES `activity_registrations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attendance_logs_handled_by_foreign` FOREIGN KEY (`handled_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `attendance_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `donasi`
--
ALTER TABLE `donasi`
  ADD CONSTRAINT `fk_donasi_diverifikasi_oleh` FOREIGN KEY (`diverifikasi_oleh`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_donasi_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_donasi` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_donasi_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_donasi_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `donations`
--
ALTER TABLE `donations`
  ADD CONSTRAINT `donations_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `donations_donation_category_id_foreign` FOREIGN KEY (`donation_category_id`) REFERENCES `donation_categories` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `donations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `donations_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `donation_verification_logs`
--
ALTER TABLE `donation_verification_logs`
  ADD CONSTRAINT `donation_verification_logs_acted_by_foreign` FOREIGN KEY (`acted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `donation_verification_logs_donation_id_foreign` FOREIGN KEY (`donation_id`) REFERENCES `donations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorite_activities`
--
ALTER TABLE `favorite_activities`
  ADD CONSTRAINT `favorite_activities_activity_id_foreign` FOREIGN KEY (`activity_id`) REFERENCES `activities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorite_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorit_kegiatan`
--
ALTER TABLE `favorit_kegiatan`
  ADD CONSTRAINT `fk_favorit_kegiatan_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favorit_kegiatan_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hak_akses_pengguna`
--
ALTER TABLE `hak_akses_pengguna`
  ADD CONSTRAINT `fk_hak_akses_pengguna_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu_aplikasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hak_akses_pengguna_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `hak_akses_role`
--
ALTER TABLE `hak_akses_role`
  ADD CONSTRAINT `fk_hak_akses_role_menu` FOREIGN KEY (`menu_id`) REFERENCES `menu_aplikasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_hak_akses_role_role` FOREIGN KEY (`role_id`) REFERENCES `role_pengguna` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kegiatan`
--
ALTER TABLE `kegiatan`
  ADD CONSTRAINT `fk_kegiatan_kategori` FOREIGN KEY (`kategori_id`) REFERENCES `kategori_kegiatan` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD CONSTRAINT `login_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `log_aktivitas`
--
ALTER TABLE `log_aktivitas`
  ADD CONSTRAINT `fk_log_aktivitas_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `log_backup`
--
ALTER TABLE `log_backup`
  ADD CONSTRAINT `fk_log_backup_restore_oleh` FOREIGN KEY (`direstore_oleh`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `log_discord`
--
ALTER TABLE `log_discord`
  ADD CONSTRAINT `fk_log_discord_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `log_error`
--
ALTER TABLE `log_error`
  ADD CONSTRAINT `fk_log_error_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `log_kehadiran`
--
ALTER TABLE `log_kehadiran`
  ADD CONSTRAINT `fk_log_kehadiran_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_log_kehadiran_pendaftaran` FOREIGN KEY (`pendaftaran_id`) REFERENCES `pendaftaran_kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_log_kehadiran_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `log_login`
--
ALTER TABLE `log_login`
  ADD CONSTRAINT `fk_log_login_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `log_otp_reset_password`
--
ALTER TABLE `log_otp_reset_password`
  ADD CONSTRAINT `fk_log_otp_reset_password_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `log_verifikasi_donasi`
--
ALTER TABLE `log_verifikasi_donasi`
  ADD CONSTRAINT `fk_log_verifikasi_donasi_donasi` FOREIGN KEY (`donasi_id`) REFERENCES `donasi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_log_verifikasi_donasi_pengguna` FOREIGN KEY (`aksi_oleh`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `menu_aplikasi`
--
ALTER TABLE `menu_aplikasi`
  ADD CONSTRAINT `fk_menu_parent` FOREIGN KEY (`parent_id`) REFERENCES `menu_aplikasi` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `notifikasi`
--
ALTER TABLE `notifikasi`
  ADD CONSTRAINT `fk_notifikasi_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pendaftaran_kegiatan`
--
ALTER TABLE `pendaftaran_kegiatan`
  ADD CONSTRAINT `fk_pendaftaran_kegiatan_checkin_oleh` FOREIGN KEY (`checkin_oleh`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pendaftaran_kegiatan_kegiatan` FOREIGN KEY (`kegiatan_id`) REFERENCES `kegiatan` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pendaftaran_kegiatan_pengguna` FOREIGN KEY (`pengguna_id`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD CONSTRAINT `fk_pengguna_role` FOREIGN KEY (`role_id`) REFERENCES `role_pengguna` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `pengurus`
--
ALTER TABLE `pengurus`
  ADD CONSTRAINT `fk_pengurus_role` FOREIGN KEY (`role_id`) REFERENCES `role_pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `permission_role`
--
ALTER TABLE `permission_role`
  ADD CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `riwayat_pengaturan`
--
ALTER TABLE `riwayat_pengaturan`
  ADD CONSTRAINT `fk_riwayat_pengaturan_pengaturan` FOREIGN KEY (`pengaturan_id`) REFERENCES `pengaturan_sistem` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_riwayat_pengaturan_pengguna` FOREIGN KEY (`diubah_oleh`) REFERENCES `pengguna` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `role_user`
--
ALTER TABLE `role_user`
  ADD CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
