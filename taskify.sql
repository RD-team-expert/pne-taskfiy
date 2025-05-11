-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 30, 2025 at 02:52 PM
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
-- Database: `taskify_test_1.1.3`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED DEFAULT NULL,
  `actor_id` bigint(20) NOT NULL,
  `actor_type` varchar(56) NOT NULL,
  `type_id` bigint(20) NOT NULL,
  `type` varchar(56) NOT NULL,
  `parent_type_id` bigint(20) DEFAULT NULL,
  `parent_type` varchar(56) DEFAULT NULL,
  `activity` varchar(56) NOT NULL,
  `message` varchar(512) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `allowances`
--

CREATE TABLE `allowances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `amount` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `allowance_payslip`
--

CREATE TABLE `allowance_payslip` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `allowance_id` bigint(20) UNSIGNED NOT NULL,
  `payslip_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ch_favorites`
--

CREATE TABLE `ch_favorites` (
  `id` char(36) NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `favorite_id` bigint(20) NOT NULL,
  `type_id` bigint(20) DEFAULT NULL,
  `type` varchar(28) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ch_messages`
--

CREATE TABLE `ch_messages` (
  `id` char(36) NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `from_id` bigint(20) NOT NULL,
  `to_id` bigint(20) NOT NULL,
  `type_id` bigint(20) DEFAULT NULL,
  `type` varchar(28) DEFAULT NULL,
  `body` varchar(5000) DEFAULT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `company` varchar(255) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `country_code` varchar(28) DEFAULT NULL,
  `country_iso_code` varchar(28) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `doj` date DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `zip` varchar(255) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0,
  `lang` varchar(28) NOT NULL DEFAULT 'en',
  `remember_token` text DEFAULT NULL,
  `email_verification_mail_sent` tinyint(4) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `acct_create_mail_sent` tinyint(4) NOT NULL DEFAULT 1,
  `internal_purpose` tinyint(4) NOT NULL DEFAULT 0,
  `default_workspace_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_meeting`
--

CREATE TABLE `client_meeting` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_notifications`
--

CREATE TABLE `client_notifications` (
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `is_push` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_project`
--

CREATE TABLE `client_project` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `client_workspace`
--

CREATE TABLE `client_workspace` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `commenter_id` bigint(20) UNSIGNED DEFAULT NULL,
  `commenter_type` varchar(255) DEFAULT 'App\\Models\\User',
  `commentable_type` varchar(255) NOT NULL,
  `commentable_id` bigint(20) UNSIGNED NOT NULL,
  `content` longtext NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `comment_attachments`
--

CREATE TABLE `comment_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `comment_id` bigint(20) UNSIGNED NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contracts`
--

CREATE TABLE `contracts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `contract_type_id` bigint(20) UNSIGNED NOT NULL,
  `description` text DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `promisor_sign` text DEFAULT NULL,
  `promisee_sign` text DEFAULT NULL,
  `signed_pdf` text DEFAULT NULL,
  `created_by` varchar(56) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contract_types`
--

CREATE TABLE `contract_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `contract_types`
--

INSERT INTO `contract_types` (`id`, `workspace_id`, `type`, `created_at`, `updated_at`) VALUES
(0, NULL, 'Default', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `deductions`
--

CREATE TABLE `deductions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(512) NOT NULL DEFAULT 'amount',
  `percentage` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `deduction_payslip`
--

CREATE TABLE `deduction_payslip` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `deduction_id` bigint(20) UNSIGNED NOT NULL,
  `payslip_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `estimates_invoices`
--

CREATE TABLE `estimates_invoices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `zip_code` varchar(50) DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `note` longtext DEFAULT NULL,
  `personal_note` longtext DEFAULT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `total` double NOT NULL,
  `tax_amount` double DEFAULT NULL,
  `final_total` double NOT NULL,
  `created_by` varchar(56) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `estimates_invoice_item`
--

CREATE TABLE `estimates_invoice_item` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `estimates_invoice_id` bigint(20) UNSIGNED NOT NULL,
  `qty` double NOT NULL,
  `unit_id` bigint(20) DEFAULT NULL,
  `rate` double NOT NULL,
  `tax_id` bigint(20) DEFAULT NULL,
  `amount` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `expense_type_id` bigint(20) UNSIGNED NOT NULL,
  `amount` double NOT NULL,
  `note` text DEFAULT NULL,
  `expense_date` date NOT NULL,
  `created_by` varchar(56) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_types`
--

CREATE TABLE `expense_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_types`
--

INSERT INTO `expense_types` (`id`, `workspace_id`, `title`, `description`, `created_at`, `updated_at`) VALUES
(0, NULL, 'Default', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `favoritable_type` varchar(255) NOT NULL,
  `favoritable_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fcm_tokens`
--

CREATE TABLE `fcm_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `fcm_token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `price` double NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`id`, `name`, `code`, `created_at`, `updated_at`) VALUES
(4, 'English', 'en', '2023-06-29 09:37:47', '2023-06-29 09:37:47'),
(30, 'Hindi', 'hn', '2024-04-09 04:12:36', '2024-04-09 04:12:36'),
(31, 'Amharic', 'am', '2024-04-10 01:32:04', '2024-04-10 01:32:04'),
(32, 'Korean', 'ko', '2024-04-10 04:00:08', '2024-04-10 04:00:08'),
(33, 'Vietnamese', 'vn', '2024-04-10 04:45:26', '2024-04-10 04:45:26'),
(34, 'Portuguese', 'pt', '2024-04-10 23:11:05', '2024-04-10 23:11:05'),
(35, 'Español', 'es', '2024-04-11 05:03:18', '2024-04-11 05:03:18'),
(36, 'Français', 'fr', '2024-04-11 05:07:52', '2024-04-11 05:07:52'),
(37, 'Arabic', 'ar', '2024-04-11 05:37:27', '2024-04-11 05:37:27'),
(38, 'Dutch', 'nl', '2024-04-11 05:44:31', '2024-04-11 05:44:31'),
(39, 'Turkish', 'tr', '2024-04-11 05:52:41', '2024-04-11 05:52:41'),
(40, 'Indonesia', 'Ina', '2024-04-11 05:57:20', '2024-04-11 05:57:20'),
(41, 'Thai', 'TH', '2024-04-11 06:01:59', '2024-04-11 06:01:59'),
(42, 'Hrvatski', 'hr', '2024-04-11 06:06:25', '2024-04-11 06:06:25'),
(43, 'Italian', 'it', '2024-04-11 06:16:08', '2024-04-11 06:16:08');

-- --------------------------------------------------------

--
-- Table structure for table `leave_editors`
--

CREATE TABLE `leave_editors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_requests`
--

CREATE TABLE `leave_requests` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `from_date` date NOT NULL,
  `to_date` date NOT NULL,
  `from_time` time DEFAULT NULL,
  `to_time` time DEFAULT NULL,
  `reason` text NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `visible_to_all` tinyint(4) NOT NULL DEFAULT 0,
  `action_by` bigint(20) UNSIGNED DEFAULT NULL,
  `comment` text DEFAULT NULL COMMENT 'For admin and leave editor',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leave_request_visibility`
--

CREATE TABLE `leave_request_visibility` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `leave_request_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `media`
--

CREATE TABLE `media` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL,
  `uuid` char(36) DEFAULT NULL,
  `collection_name` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(255) DEFAULT NULL,
  `disk` varchar(255) NOT NULL,
  `conversions_disk` varchar(255) DEFAULT NULL,
  `size` bigint(20) UNSIGNED NOT NULL,
  `manipulations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `custom_properties` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `generated_conversions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `responsive_images` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `order_column` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meetings`
--

CREATE TABLE `meetings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `start_date_time` timestamp NULL DEFAULT NULL,
  `end_date_time` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meeting_user`
--

CREATE TABLE `meeting_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `meeting_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu_orders`
--

CREATE TABLE `menu_orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `menu_order` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`menu_order`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_resets_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(5, '2023_01_05_044027_create_clients_table', 2),
(6, '2023_01_09_041709_create_projects_table', 3),
(7, '2023_01_12_095035_create_tasks_table', 4),
(8, '2023_01_25_061517_create_project_user_table', 5),
(9, '2023_01_27_044336_create_project_client_table', 6),
(10, '2023_01_30_050742_create_task_user_table', 7),
(11, '2023_01_30_060853_create_task_user_table', 8),
(12, '2023_03_02_051733_create_todo_table', 9),
(13, '2023_03_06_043616_create_status_table', 9),
(14, '2023_03_14_045106_create_permission_tables', 10),
(15, '2023_06_14_074411_create_settings_table', 11),
(16, '2023_06_15_074941_create_meetings_table', 12),
(19, '2023_06_15_073618_create_meeting_users_table', 13),
(21, '2023_06_15_074054_create_meeting_clients_table', 14),
(22, '2023_06_15_111007_create_meetings_table', 15),
(23, '2023_06_15_121717_create_meeting_user_table', 16),
(24, '2023_06_15_121752_create_client_meeting_table', 16),
(25, '2023_06_16_999999_add_active_status_to_users', 17),
(26, '2023_06_16_999999_add_avatar_to_users', 17),
(27, '2023_06_16_999999_add_dark_mode_to_users', 17),
(28, '2023_06_16_999999_add_messenger_color_to_users', 17),
(29, '2023_06_16_999999_create_chatify_favorites_table', 17),
(30, '2023_06_16_999999_create_chatify_messages_table', 17),
(34, '2023_06_16_132714_create_workspaces_table', 18),
(36, '2023_06_16_133749_create_workspace_client_table', 18),
(37, '2023_06_16_133505_create_workspace_user_table', 19),
(39, '2023_06_19_100748_alter_projects_table_add_column_workspace_id', 20),
(40, '2023_06_19_111816_alter_projects_table_add_column_created_by', 21),
(41, '2023_06_19_124749_alter_tasks_table_add_columns', 22),
(42, '2023_06_19_134537_alter_meeting_and_todo_tables_add_columns', 23),
(44, '2023_06_20_152434_alter_ch_messages_add_column_workspace_id', 24),
(45, '2023_06_24_162358_alter_ch_messages_add_column_workspace_id', 25),
(46, '2023_06_28_101921_alter_ch_favorites_table_add_column_workspace_id', 25),
(47, '2023_06_29_105758_create_languages_table', 26),
(48, '2023_08_22_124156_create_tags_table', 27),
(49, '2023_08_22_175355_create_project_tags_table', 28),
(50, '2023_08_22_180431_create_project_tag_table', 29),
(51, '2023_08_22_181203_create_project_tag_table', 30),
(52, '2023_08_24_050550_create_project_tag_table', 31),
(53, '2023_09_11_140028_create_todos_table', 32),
(54, '2023_09_11_085224_create_todos_table', 33),
(55, '2023_09_19_063727_create_leave_requests_table', 34),
(56, '2023_09_19_134017_create_leave_editors_table', 35),
(59, '2023_09_20_124013_create_contract_types_table', 36),
(60, '2023_09_20_123424_create_contracts_table', 37),
(61, '2023_09_25_084705_create_payment_methods_table', 38),
(62, '2023_09_25_070729_create_payslips_table', 39),
(63, '2023_09_25_094532_create_allowances_table', 40),
(64, '2023_09_25_094632_create_deductions_table', 40),
(65, '2023_09_27_124345_create_allowance_payslip_table', 41),
(66, '2023_09_27_124747_create_deduction_payslip_table', 41),
(67, '2023_09_29_131131_create_notes_table', 42),
(68, '2023_10_03_115025_create_updates_table', 43),
(69, '2023_10_03_115438_create_updates_table', 44),
(70, '2023_12_18_100056_create_time_trackers_table', 45),
(71, '2023_12_19_044413_create_time_trackers_table', 46),
(74, '2023_12_19_065522_create_time_trackers_table', 47),
(75, '2024_01_17_121328_create_media_table', 48),
(76, '2024_01_24_135513_create_activity_logs_table', 49),
(77, '2024_01_29_135126_create_taxes_table', 50),
(78, '2024_01_30_070848_create_units_table', 51),
(79, '2024_01_31_045129_create_items_table', 52),
(80, '2024_02_02_142029_create_estimates_invoices_table', 53),
(81, '2024_02_02_162343_create_estimates_invoice_item_table', 53),
(82, '2024_02_12_093738_create_expense_types_table', 54),
(83, '2024_02_12_094118_create_expenses_table', 54),
(84, '2024_02_16_043457_create_payments_table', 55),
(85, '2024_02_26_101512_create_milestones_table', 56),
(87, '2024_03_05_074505_create_notifications_table', 57),
(88, '2024_03_05_092120_create_client_notifications_table', 57),
(89, '2024_03_05_092139_create_notification_user_table', 57),
(91, '2024_03_11_120312_create_templates_table', 58),
(92, '2024_04_13_142557_create_priorities_table', 59),
(93, '2024_04_29_122911_create_role_status_table', 60),
(94, '2024_05_02_074714_create_leave_request_visibility_table', 61),
(95, '2024_05_17_054155_create_user_client_preferences_table', 62),
(96, '2024_07_09_100000_create_comments_table', 63),
(97, '2024_08_09_090054_create_comment_attachments_table', 63),
(98, '2024_10_09_050915_create_menu_orders_table', 63),
(99, '2024_10_21_132437_create_fcm_tokens_table', 63),
(100, '2024_11_28_055136_create_favorites_table', 64),
(101, '2024_11_30_044132_create_pinned_table', 64),
(102, '2025_01_16_085659_create_recuring_tasks', 65),
(103, '2025_01_24_042906_create_status_timelines_table', 65),
(104, '2025_01_25_055631_create_reminders_table', 65);

-- --------------------------------------------------------

--
-- Table structure for table `milestones`
--

CREATE TABLE `milestones` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `cost` double NOT NULL,
  `progress` double NOT NULL DEFAULT 0,
  `description` longtext DEFAULT NULL,
  `created_by` varchar(56) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(191) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notes`
--

CREATE TABLE `notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `creator_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `from_id` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification_user`
--

CREATE TABLE `notification_user` (
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `is_system` tinyint(1) DEFAULT 1,
  `is_push` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `invoice_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` double NOT NULL,
  `payment_date` date NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` varchar(56) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_methods`
--

CREATE TABLE `payment_methods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_methods`
--

INSERT INTO `payment_methods` (`id`, `workspace_id`, `title`, `created_at`, `updated_at`) VALUES
(0, NULL, 'Default', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `payslips`
--

CREATE TABLE `payslips` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `payment_method_id` bigint(20) UNSIGNED DEFAULT NULL,
  `month` varchar(128) NOT NULL,
  `working_days` double NOT NULL,
  `lop_days` double NOT NULL,
  `paid_days` double NOT NULL,
  `basic_salary` double NOT NULL,
  `leave_deduction` double NOT NULL,
  `ot_hours` double NOT NULL DEFAULT 0,
  `ot_rate` double NOT NULL DEFAULT 0,
  `ot_payment` double NOT NULL DEFAULT 0,
  `total_allowance` double NOT NULL DEFAULT 0,
  `incentives` double NOT NULL DEFAULT 0,
  `bonus` double NOT NULL DEFAULT 0,
  `total_earnings` double NOT NULL,
  `total_deductions` double NOT NULL DEFAULT 0,
  `net_pay` double NOT NULL,
  `payment_date` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `note` text DEFAULT NULL,
  `created_by` varchar(56) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'edit_tasks', 'web', '2023-03-14 00:32:57', '2023-03-14 00:32:57'),
(3, 'delete_tasks', 'web', '2023-03-14 01:09:18', '2023-03-14 01:09:18'),
(4, 'edit_projects', 'web', '2023-03-14 03:22:52', '2023-03-14 03:22:52'),
(5, 'delete_projects', 'web', '2023-03-14 03:23:43', '2023-03-14 03:23:43'),
(6, 'edit_clients', 'web', '2023-03-14 03:23:53', '2023-03-14 03:23:53'),
(7, 'delete_clients', 'web', '2023-03-14 03:24:01', '2023-03-14 03:24:01'),
(8, 'create_projects', 'web', '2023-03-14 23:12:29', '2023-03-14 23:12:29'),
(9, 'create_tasks', 'web', '2023-03-14 23:12:34', '2023-03-14 23:12:34'),
(10, 'create_clients', 'web', '2023-03-14 23:12:38', '2023-03-14 23:12:38'),
(11, 'edit_users', 'web', '2023-03-17 00:51:15', '2023-03-17 00:51:15'),
(12, 'delete_users', 'web', '2023-03-17 00:57:41', '2023-03-17 00:57:41'),
(13, 'manage_users', 'web', '2023-03-27 00:04:49', '2023-03-27 00:04:49'),
(14, 'manage_projects', 'web', '2023-03-27 00:04:54', '2023-03-27 00:04:54'),
(15, 'manage_tasks', 'web', '2023-03-27 00:04:58', '2023-03-27 00:04:58'),
(16, 'manage_clients', 'web', '2023-03-27 00:05:02', '2023-03-27 00:05:02'),
(17, 'create_users', 'web', '2023-03-27 00:19:25', '2023-03-27 00:19:25'),
(22, 'create_workspaces', 'web', '2023-08-12 10:48:04', '2023-08-12 10:48:04'),
(23, 'manage_workspaces', 'web', '2023-08-12 10:48:04', '2023-08-12 10:48:04'),
(24, 'edit_workspaces', 'web', '2023-08-12 10:48:04', '2023-08-12 10:48:04'),
(25, 'delete_workspaces', 'web', '2023-08-12 10:48:04', '2023-08-12 10:48:04'),
(26, 'create_meetings', 'web', '2023-08-12 10:50:22', '2023-08-12 10:50:22'),
(27, 'manage_meetings', 'web', '2023-08-12 10:50:22', '2023-08-12 10:50:22'),
(28, 'edit_meetings', 'web', '2023-08-12 10:50:22', '2023-08-12 10:50:22'),
(29, 'delete_meetings', 'web', '2023-08-12 10:50:22', '2023-08-12 10:50:22'),
(37, 'delete_projects', 'client', '2023-09-08 11:57:55', '2023-09-08 11:57:55'),
(38, 'manage_projects', 'client', '2023-09-08 11:57:55', '2023-09-08 11:57:55'),
(39, 'create_tasks', 'client', '2023-09-08 12:32:02', '2023-09-08 12:32:02'),
(40, 'create_projects', 'client', '2023-09-08 12:33:36', '2023-09-08 12:33:36'),
(41, 'manage_tasks', 'client', '2023-09-08 12:33:48', '2023-09-08 12:33:48'),
(42, 'edit_tasks', 'client', '2023-09-08 12:33:59', '2023-09-08 12:33:59'),
(43, 'delete_tasks', 'client', '2023-09-08 12:34:13', '2023-09-08 12:34:13'),
(44, 'create_users', 'client', '2023-09-08 12:34:25', '2023-09-08 12:34:25'),
(45, 'manage_users', 'client', '2023-09-08 12:34:37', '2023-09-08 12:34:37'),
(46, 'edit_users', 'client', '2023-09-08 12:34:56', '2023-09-08 12:34:56'),
(47, 'delete_users', 'client', '2023-09-08 12:35:08', '2023-09-08 12:35:08'),
(48, 'create_clients', 'client', '2023-09-08 12:35:25', '2023-09-08 12:35:25'),
(49, 'manage_clients', 'client', '2023-09-08 12:35:37', '2023-09-08 12:35:37'),
(50, 'edit_clients', 'client', '2023-09-08 12:35:49', '2023-09-08 12:35:49'),
(51, 'delete_clients', 'client', '2023-09-08 12:36:01', '2023-09-08 12:36:01'),
(52, 'create_workspaces', 'client', '2023-09-08 12:36:17', '2023-09-08 12:36:17'),
(53, 'manage_workspaces', 'client', '2023-09-08 12:36:34', '2023-09-08 12:36:34'),
(54, 'edit_workspaces', 'client', '2023-09-08 12:36:45', '2023-09-08 12:36:45'),
(55, 'delete_workspaces', 'client', '2023-09-08 12:37:01', '2023-09-08 12:37:01'),
(56, 'create_meetings', 'client', '2023-09-08 12:37:16', '2023-09-08 12:37:16'),
(57, 'manage_meetings', 'client', '2023-09-08 12:37:29', '2023-09-08 12:37:29'),
(58, 'edit_meetings', 'client', '2023-09-08 12:37:42', '2023-09-08 12:37:42'),
(59, 'delete_meetings', 'client', '2023-09-08 12:37:55', '2023-09-08 12:37:55'),
(60, 'edit_projects', 'client', '2023-09-08 13:10:48', '2023-09-08 13:10:48'),
(61, 'create_contracts', 'client', '2023-09-23 23:32:01', '2023-09-23 23:32:01'),
(62, 'manage_contracts', 'client', '2023-09-23 23:32:44', '2023-09-23 23:32:44'),
(63, 'edit_contracts', 'client', '2023-09-23 23:32:56', '2023-09-23 23:32:56'),
(64, 'delete_contracts', 'client', '2023-09-23 23:33:04', '2023-09-23 23:33:04'),
(65, 'delete_contracts', 'web', '2023-09-23 23:33:16', '2023-09-23 23:33:16'),
(66, 'edit_contracts', 'web', '2023-09-23 23:33:26', '2023-09-23 23:33:26'),
(67, 'manage_contracts', 'web', '2023-09-23 23:33:37', '2023-09-23 23:33:37'),
(68, 'create_contracts', 'web', '2023-09-23 23:33:50', '2023-09-23 23:33:50'),
(69, 'create_timesheet', 'web', '2023-12-19 07:13:52', '2023-12-19 07:13:52'),
(70, 'delete_timesheet', 'web', '2023-12-19 07:13:55', '2023-12-19 07:13:55'),
(71, 'manage_timesheet', 'web', '2023-12-19 07:13:55', '2023-12-19 07:13:55'),
(72, 'create_timesheet', 'client', '2023-12-19 07:15:11', '2023-12-19 07:15:11'),
(73, 'delete_timesheet', 'client', '2023-12-19 07:15:11', '2023-12-19 07:15:11'),
(74, 'manage_timesheet', 'client', '2023-12-19 07:15:11', '2023-12-19 07:15:11'),
(75, 'create_payslips', 'client', '2023-12-22 05:46:34', '2023-12-22 05:46:34'),
(76, 'edit_payslips', 'client', '2023-12-22 05:46:35', '2023-12-22 05:46:35'),
(77, 'delete_payslips', 'client', '2023-12-22 05:46:35', '2023-12-22 05:46:35'),
(78, 'manage_payslips', 'client', '2023-12-22 05:46:35', '2023-12-22 05:46:35'),
(79, 'create_payslips', 'web', '2023-12-22 05:46:52', '2023-12-22 05:46:52'),
(80, 'edit_payslips', 'web', '2023-12-22 05:46:53', '2023-12-22 05:46:53'),
(81, 'delete_payslips', 'web', '2023-12-22 05:46:53', '2023-12-22 05:46:53'),
(82, 'manage_payslips', 'web', '2023-12-22 05:46:53', '2023-12-22 05:46:53'),
(83, 'access_all_data', 'web', '2024-01-08 07:22:04', '2024-01-08 13:47:12'),
(85, 'access_all_data', 'client', '2024-01-08 07:22:04', '2024-01-08 13:47:12'),
(86, 'manage_activity_log', 'web', '2024-01-15 13:46:23', '2024-01-15 13:46:23'),
(87, 'delete_activity_log', 'web', '2024-01-15 13:46:23', '2024-01-15 13:46:23'),
(88, 'manage_activity_log', 'client', '2024-01-15 13:46:23', '2024-01-15 13:46:23'),
(89, 'delete_activity_log', 'client', '2024-01-15 13:46:23', '2024-01-15 13:46:23'),
(90, 'create_estimates_invoices', 'web', '2024-02-07 06:15:12', '2024-02-07 06:15:12'),
(91, 'manage_estimates_invoices', 'web', '2024-02-07 06:15:12', '2024-02-07 06:15:12'),
(92, 'edit_estimates_invoices', 'web', '2024-02-07 06:15:12', '2024-02-07 06:15:12'),
(93, 'delete_estimates_invoices', 'web', '2024-02-07 06:15:13', '2024-02-07 06:15:13'),
(94, 'create_estimates_invoices', 'client', '2024-02-07 06:15:30', '2024-02-07 06:15:30'),
(95, 'manage_estimates_invoices', 'client', '2024-02-07 06:15:30', '2024-02-07 06:15:30'),
(96, 'edit_estimates_invoices', 'client', '2024-02-07 06:15:31', '2024-02-07 06:15:31'),
(97, 'delete_estimates_invoices', 'client', '2024-02-07 06:15:31', '2024-02-07 06:15:31'),
(98, 'create_expenses', 'client', '2024-02-13 01:06:26', '2024-02-13 01:06:26'),
(99, 'manage_expenses', 'client', '2024-02-13 01:06:27', '2024-02-13 01:06:27'),
(100, 'edit_expenses', 'client', '2024-02-13 01:06:27', '2024-02-13 01:06:27'),
(101, 'delete_expenses', 'client', '2024-02-13 01:06:27', '2024-02-13 01:06:27'),
(102, 'create_expenses', 'web', '2024-02-13 01:11:45', '2024-02-13 01:11:45'),
(103, 'manage_expenses', 'web', '2024-02-13 01:11:45', '2024-02-13 01:11:45'),
(104, 'edit_expenses', 'web', '2024-02-13 01:11:45', '2024-02-13 01:11:45'),
(105, 'delete_expenses', 'web', '2024-02-13 01:11:45', '2024-02-13 01:11:45'),
(106, 'create_milestones', 'web', '2024-02-26 04:28:09', '2024-02-26 04:28:09'),
(107, 'manage_milestones', 'web', '2024-02-26 04:28:09', '2024-02-26 04:28:09'),
(108, 'edit_milestones', 'web', '2024-02-26 04:28:09', '2024-02-26 04:28:09'),
(109, 'delete_milestones', 'web', '2024-02-26 04:28:09', '2024-02-26 04:28:09'),
(110, 'create_milestones', 'client', '2024-02-26 04:28:35', '2024-02-26 04:28:35'),
(111, 'manage_milestones', 'client', '2024-02-26 04:28:35', '2024-02-26 04:28:35'),
(112, 'edit_milestones', 'client', '2024-02-26 04:28:36', '2024-02-26 04:28:36'),
(113, 'delete_milestones', 'client', '2024-02-26 04:28:36', '2024-02-26 04:28:36'),
(114, 'manage_system_notifications', 'client', '2024-03-05 06:57:10', '2024-03-05 06:57:10'),
(115, 'delete_system_notifications', 'client', '2024-03-05 06:57:10', '2024-03-05 06:57:10'),
(116, 'manage_system_notifications', 'web', '2024-03-05 06:57:21', '2024-03-05 06:57:21'),
(117, 'delete_system_notifications', 'web', '2024-03-05 06:57:21', '2024-03-05 06:57:21'),
(118, 'create_statuses', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(119, 'manage_statuses', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(120, 'edit_statuses', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(121, 'delete_statuses', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(122, 'create_statuses', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(123, 'manage_statuses', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(124, 'edit_statuses', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(125, 'delete_statuses', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(126, 'create_priorities', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(127, 'manage_priorities', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(128, 'edit_priorities', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(129, 'delete_priorities', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(130, 'create_priorities', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(131, 'manage_priorities', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(132, 'edit_priorities', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(133, 'delete_priorities', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(134, 'create_tags', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(135, 'manage_tags', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(136, 'edit_tags', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(137, 'delete_tags', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(138, 'create_tags', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(139, 'manage_tags', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(140, 'edit_tags', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(141, 'delete_tags', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(142, 'create_contract_types', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(143, 'manage_contract_types', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(144, 'edit_contract_types', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(145, 'delete_contract_types', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(146, 'create_contract_types', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(147, 'manage_contract_types', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(148, 'edit_contract_types', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(149, 'delete_contract_types', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(150, 'create_expense_types', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(151, 'manage_expense_types', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(152, 'edit_expense_types', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(153, 'delete_expense_types', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(154, 'create_expense_types', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(155, 'manage_expense_types', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(156, 'edit_expense_types', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(157, 'delete_expense_types', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(158, 'create_allowances', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(159, 'manage_allowances', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(160, 'edit_allowances', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(161, 'delete_allowances', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(162, 'create_allowances', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(163, 'manage_allowances', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(164, 'edit_allowances', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(165, 'delete_allowances', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(166, 'create_deductions', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(167, 'manage_deductions', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(168, 'edit_deductions', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(169, 'delete_deductions', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(170, 'create_deductions', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(171, 'manage_deductions', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(172, 'edit_deductions', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(173, 'delete_deductions', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(174, 'create_payment_methods', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(175, 'manage_payment_methods', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(176, 'edit_payment_methods', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(177, 'delete_payment_methods', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(178, 'create_payment_methods', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(179, 'manage_payment_methods', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(180, 'edit_payment_methods', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(181, 'delete_payment_methods', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(182, 'create_payments', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(183, 'manage_payments', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(184, 'edit_payments', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(185, 'delete_payments', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(186, 'create_payments', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(187, 'manage_payments', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(188, 'edit_payments', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(189, 'delete_payments', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(190, 'create_taxes', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(191, 'manage_taxes', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(192, 'edit_taxes', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(193, 'delete_taxes', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(194, 'create_taxes', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(195, 'manage_taxes', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(196, 'edit_taxes', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(197, 'delete_taxes', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(198, 'create_units', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(199, 'manage_units', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(200, 'edit_units', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(201, 'delete_units', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(202, 'create_units', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(203, 'manage_units', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(204, 'edit_units', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(205, 'delete_units', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(206, 'create_items', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(207, 'manage_items', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(208, 'edit_items', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(209, 'delete_items', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(210, 'create_items', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(211, 'manage_items', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(212, 'edit_items', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(213, 'delete_items', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(214, 'create_media', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(215, 'manage_media', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(216, 'delete_media', 'web', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(217, 'create_media', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(218, 'manage_media', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01'),
(219, 'delete_media', 'client', '2024-09-17 12:47:01', '2024-09-17 12:47:01');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pinned`
--

CREATE TABLE `pinned` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `pinnable_type` varchar(255) NOT NULL,
  `pinnable_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `priorities`
--

CREATE TABLE `priorities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `status_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `priority_id` bigint(20) DEFAULT NULL,
  `budget` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `is_favorite` tinyint(4) NOT NULL DEFAULT 0,
  `task_accessibility` varchar(28) NOT NULL DEFAULT 'assigned_users',
  `note` longtext DEFAULT NULL,
  `client_can_discuss` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_tag`
--

CREATE TABLE `project_tag` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `tag_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_user`
--

CREATE TABLE `project_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recurring_tasks`
--

CREATE TABLE `recurring_tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `frequency` enum('daily','weekly','monthly','yearly') NOT NULL,
  `day_of_week` text DEFAULT NULL,
  `day_of_month` text DEFAULT NULL,
  `month_of_year` text DEFAULT NULL,
  `starts_from` date DEFAULT NULL,
  `number_of_occurrences` text DEFAULT NULL,
  `completed_occurrences` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_created_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reminders`
--

CREATE TABLE `reminders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `remindable_type` varchar(255) NOT NULL,
  `remindable_id` bigint(20) UNSIGNED NOT NULL,
  `frequency_type` enum('daily','weekly','monthly') NOT NULL,
  `frequency_value` int(11) DEFAULT NULL,
  `day_of_week` int(11) DEFAULT NULL,
  `day_of_month` int(11) DEFAULT NULL,
  `time_of_day` time NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_sent_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) NOT NULL,
  `guard_name` varchar(191) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2023-03-13 23:28:49', '2023-09-24 06:53:41'),
(9, 'member', 'web', '2023-03-31 06:21:47', '2023-03-31 06:21:47'),
(21, 'client', 'client', '2023-12-22 07:45:49', '2023-12-22 07:45:49');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `role_status`
--

CREATE TABLE `role_status` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `status_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `variable` varchar(255) DEFAULT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `variable`, `value`, `created_at`, `updated_at`) VALUES
(5, 'general_settings', '{\"company_title\":\"Taskify\",\"currency_full_form\":\"Indian Rupee\",\"currency_symbol\":\"\\u20b9\",\"currency_code\":\"INR\",\"timezone\":\"Asia\\/Kolkata\",\"date_format\":\"DD-MM-YYYY|d-m-Y\",\"footer_text\":\"<p>made with \\u2764\\ufe0f by <a href=\\\"https:\\/\\/www.infinitietech.com\\/\\\" target=\\\"_blank\\\" rel=\\\"noopener\\\">Infinitie Technologies<\\/a><\\/p>\",\"full_logo\":\"\",\"half_logo\":\"\",\"favicon\":\"\"}', '2023-06-14 07:48:25', '2024-04-02 00:24:45'),
(9, 'pusher_settings', NULL, '2023-06-21 05:33:13', '2023-10-09 01:09:20'),
(10, 'email_settings', NULL, '2023-06-21 08:43:07', '2025-01-30 06:32:47'),
(11, 'media_storage_settings', NULL, '2024-01-22 07:03:48', '2024-02-12 01:07:32'),
(12, 'sms_gateway_settings', NULL, '2024-03-29 01:21:39', '2024-04-02 04:45:13'),
(13, 'privacy_policy', '{\"privacy_policy\":\"<p><strong>Privacy Policy<\\/strong><\\/p>\"}', '2024-10-14 17:32:09', '2024-10-14 17:32:09'),
(14, 'about_us', '{\"about_us\":\"<p><strong>About Us<\\/strong><\\/p>\"}', '2024-10-14 17:32:09', '2024-10-14 17:32:09'),
(15, 'terms_conditions', '{\"terms_conditions\":\"<p><strong>Terms and Conditions<\\/strong><\\/p>\"}', '2024-10-14 17:32:09', '2024-10-14 17:32:09'),
(16, 'slack_settings', NULL, '2025-01-30 06:31:27', '2025-01-30 06:31:27');

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp(),
  `is_default` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `title`, `slug`, `color`, `created_at`, `updated_at`, `is_default`) VALUES
(0, 'Default', 'default', 'secondary', '2023-09-16 05:14:30', '2023-09-16 05:14:30', 0),
(71, 'Open', 'open', 'success', '2025-01-30 11:47:57', '2025-01-30 11:47:57', 1),
(72, 'Closed', 'closed', 'danger', '2025-01-30 11:47:57', '2025-01-30 11:47:57', 1);

-- --------------------------------------------------------

--
-- Table structure for table `status_timelines`
--

CREATE TABLE `status_timelines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `entity_id` bigint(20) UNSIGNED NOT NULL,
  `entity_type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `previous_status` varchar(255) DEFAULT NULL,
  `new_color` varchar(255) DEFAULT NULL,
  `old_color` varchar(255) DEFAULT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `color` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tasks`
--

CREATE TABLE `tasks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `project_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `status_id` bigint(20) NOT NULL DEFAULT 1,
  `priority_id` bigint(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `note` longtext DEFAULT NULL,
  `client_can_discuss` tinyint(1) DEFAULT 0,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_user`
--

CREATE TABLE `task_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `task_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `taxes`
--

CREATE TABLE `taxes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `type` varchar(512) NOT NULL,
  `percentage` double DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `templates`
--

CREATE TABLE `templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `subject` text DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `templates`
--

INSERT INTO `templates` (`id`, `type`, `name`, `subject`, `content`, `status`, `created_at`, `updated_at`) VALUES
(1, 'email', 'task_reminder', 'New Task Reminder', '<div style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100%!important;\">\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\" role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" align=\"center\">\r\n<table style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; margin: 0px; padding: 0px; width: 100%; height: 659.844px;\" role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr style=\"height: 77.0938px;\">\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; padding: 25px 0px; height: 77.0938px; text-align: center;\"><span style=\"font-size: 18px;\">{COMPANY_LOGO}</span></td>\r\n</tr>\r\n<tr style=\"height: 486.25px;\">\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; background-color: rgb(237, 242, 247); margin: 0px; padding: 0px; width: 100%; border: hidden !important; height: 486.25px;\" width=\"100%\">\r\n<table class=\"m_2051327272198114809inner-body\" style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; margin: 0 auto; padding: 0; width: 570px;\" role=\"presentation\" width=\"570\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; max-width: 100vw; padding: 32px;\">\r\n<p style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;\">You have a task reminder for Task #<strong>{TASK_ID} - {TASK_TITLE}</strong>. You can view the task here: {TASK_URL}.</p>\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; margin: 30px auto; padding: 0; text-align: center; width: 100%;\" role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" align=\"center\">\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" align=\"center\">\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" role=\"presentation\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\"><a class=\"m_2051327272198114809button\" style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; border-radius: 4px; color: #fff; display: inline-block; overflow: hidden; text-decoration: none; background-color: #2d3748; border-bottom: 8px solid #2d3748; border-left: 18px solid #2d3748; border-right: 18px solid #2d3748; border-top: 8px solid #2d3748;\" href=\"{TASK_URL}\" target=\"_blank\" rel=\"noopener\" data-saferedirecturl=\"https://www.google.com/url?q=http://127.0.0.1:8000/email/verify/129/9e323bcc5df5fdff3fe6ff960fd6576204a510ca?expires%3D1710157052%26signature%3D2df5df03c59d0dcd960500989168d0bb4b064035d572bc4195ca5a912b0302e8&amp;source=gmail&amp;ust=1710333202290000&amp;usg=AOvVaw2RJnO33Iiu4v5j5tJBiQWH\">View Task Details</a></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;\">Regards,</p>\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; border-top: 1px solid #e8e5ef; margin-top: 25px; padding-top: 25px;\" role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\">\r\n<p style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; line-height: 1.5em; margin-top: 0; text-align: left; font-size: 14px;\">If you\'re having trouble clicking the \"View Task Details\" button, copy and paste the URL below into your web browser: {TASK_URL}</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; max-width: 100vw; padding: 32px;\">\r\n<h1 style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0; text-align: left;\">&nbsp;</h1>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n<tr style=\"height: 96.5px;\">\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; height: 96.5px;\">\r\n<table class=\"m_2051327272198114809footer\" style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; margin: 0 auto; padding: 0; text-align: center; width: 570px;\" role=\"presentation\" width=\"570\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; max-width: 100vw; padding: 32px;\" align=\"center\">\r\n<p style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">&copy; {CURRENT_YEAR} {COMPANY_TITLE}. All rights reserved.</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div class=\"yj6qo\">&nbsp;</div>\r\n<div class=\"adL\">&nbsp;</div>\r\n</div>', 1, '2025-01-30 06:25:02', '2025-01-30 06:25:02'),
(2, 'email', 'recurring_task', 'New Recurring Task is Executed', '<div style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; background-color: #ffffff; color: #718096; height: 100%; line-height: 1.4; margin: 0; padding: 0; width: 100%!important;\">\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; background-color: #edf2f7; margin: 0; padding: 0; width: 100%;\" role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" align=\"center\">\r\n<table style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; margin: 0px; padding: 0px; width: 100%; height: 659.844px;\" role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr style=\"height: 77.0938px;\">\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; padding: 25px 0px; height: 77.0938px; text-align: center;\"><span style=\"font-size: 18px;\">{COMPANY_LOGO}</span></td>\r\n</tr>\r\n<tr style=\"height: 486.25px;\">\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; background-color: rgb(237, 242, 247); margin: 0px; padding: 0px; width: 100%; border: hidden !important; height: 486.25px;\" width=\"100%\">\r\n<table class=\"m_2051327272198114809inner-body\" style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; background-color: #ffffff; border-color: #e8e5ef; border-radius: 2px; border-width: 1px; margin: 0 auto; padding: 0; width: 570px;\" role=\"presentation\" width=\"570\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; max-width: 100vw; padding: 32px;\">\r\n<p style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;\">The recurring task <strong> #{TASK_ID} - \"{TASK_TITLE}\"</strong> has been executed. You can view the new instance here: {TASK_URL}.</p>\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; margin: 30px auto; padding: 0; text-align: center; width: 100%;\" role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" align=\"center\">\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" role=\"presentation\" border=\"0\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" align=\"center\">\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\" role=\"presentation\" border=\"0\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\"><a class=\"m_2051327272198114809button\" style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; border-radius: 4px; color: #fff; display: inline-block; overflow: hidden; text-decoration: none; background-color: #2d3748; border-bottom: 8px solid #2d3748; border-left: 18px solid #2d3748; border-right: 18px solid #2d3748; border-top: 8px solid #2d3748;\" href=\"{TASK_URL}\" target=\"_blank\" rel=\"noopener\" data-saferedirecturl=\"https://www.google.com/url?q=http://127.0.0.1:8000/email/verify/129/9e323bcc5df5fdff3fe6ff960fd6576204a510ca?expires%3D1710157052%26signature%3D2df5df03c59d0dcd960500989168d0bb4b064035d572bc4195ca5a912b0302e8&amp;source=gmail&amp;ust=1710333202290000&amp;usg=AOvVaw2RJnO33Iiu4v5j5tJBiQWH\">View Task Details</a></td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<p style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; font-size: 16px; line-height: 1.5em; margin-top: 0; text-align: left;\">Regards,</p>\r\n<table style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; border-top: 1px solid #e8e5ef; margin-top: 25px; padding-top: 25px;\" role=\"presentation\" width=\"100%\" cellspacing=\"0\" cellpadding=\"0\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\';\">\r\n<p style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; line-height: 1.5em; margin-top: 0; text-align: left; font-size: 14px;\">If you\'re having trouble clicking the \"View Task Details\" button, copy and paste the URL below into your web browser: {TASK_URL}</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; max-width: 100vw; padding: 32px;\">\r\n<h1 style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; color: #3d4852; font-size: 18px; font-weight: bold; margin-top: 0; text-align: left;\">&nbsp;</h1>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n<tr style=\"height: 96.5px;\">\r\n<td style=\"box-sizing: border-box; font-family: -apple-system, BlinkMacSystemFont, \'Segoe UI\', Roboto, Helvetica, Arial, sans-serif, \'Apple Color Emoji\', \'Segoe UI Emoji\', \'Segoe UI Symbol\'; height: 96.5px;\">\r\n<table class=\"m_2051327272198114809footer\" style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; margin: 0 auto; padding: 0; text-align: center; width: 570px;\" role=\"presentation\" width=\"570\" cellspacing=\"0\" cellpadding=\"0\" align=\"center\">\r\n<tbody>\r\n<tr>\r\n<td style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; max-width: 100vw; padding: 32px;\" align=\"center\">\r\n<p style=\"box-sizing: border-box; font-family: -apple-system,BlinkMacSystemFont,\'Segoe UI\',Roboto,Helvetica,Arial,sans-serif,\'Apple Color Emoji\',\'Segoe UI Emoji\',\'Segoe UI Symbol\'; line-height: 1.5em; margin-top: 0; color: #b0adc5; font-size: 12px; text-align: center;\">&copy; {CURRENT_YEAR} {COMPANY_TITLE}. All rights reserved.</p>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n</td>\r\n</tr>\r\n</tbody>\r\n</table>\r\n<div class=\"yj6qo\">&nbsp;</div>\r\n<div class=\"adL\">&nbsp;</div>\r\n</div>', 1, '2025-01-30 06:25:32', '2025-01-30 06:25:32'),
(3, 'sms', 'task_reminder', NULL, 'You have a task reminder for Task #{TASK_ID} - \"{TASK_TITLE}\". You can view the task here: {TASK_URL}', 1, '2025-01-30 06:28:51', '2025-01-30 06:28:51'),
(4, 'sms', 'recurring_task', NULL, 'The recurring task #{TASK_ID} - \"{TASK_TITLE}\" has been executed. You can view the new instance here: {TASK_URL}', 1, '2025-01-30 06:29:00', '2025-01-30 06:29:00'),
(5, 'whatsapp', 'task_reminder', NULL, 'You have a task reminder for Task #{TASK_ID} - \"{TASK_TITLE}\". You can view the task here: {TASK_URL}', 1, '2025-01-30 06:29:24', '2025-01-30 06:29:24'),
(6, 'whatsapp', 'recurring_task', NULL, 'The recurring task #{TASK_ID} - \"{TASK_TITLE}\" has been executed. You can view the new instance here: {TASK_URL}', 1, '2025-01-30 06:29:34', '2025-01-30 06:29:34'),
(7, 'system', 'task_reminder', 'New Task Reminder', 'You have a task reminder for Task #{TASK_ID} - \"{TASK_TITLE}\".', 1, '2025-01-30 06:29:55', '2025-01-30 06:29:55'),
(8, 'system', 'recurring_task', 'New Recurring Task is Executed', 'The recurring task #{TASK_ID} - \"{TASK_TITLE}\" has been executed.', 1, '2025-01-30 06:30:08', '2025-01-30 06:30:08'),
(9, 'push', 'task_reminder', 'New Task Reminder', 'You have a task reminder for Task #{TASK_ID} - \"{TASK_TITLE}\".', 1, '2025-01-30 06:30:26', '2025-01-30 06:30:26'),
(10, 'push', 'recurring_task', 'New Recurring Task is Executed', 'The recurring task #{TASK_ID} - \"{TASK_TITLE}\" has been executed.', 1, '2025-01-30 06:30:38', '2025-01-30 06:30:38'),
(11, 'slack', 'task_assignment', NULL, '*New Task Assigned:* {TASK_TITLE}, ID: #{TASK_ID}. By {ASSIGNEE_FIRST_NAME} {ASSIGNEE_LAST_NAME} You can find the task here : {TASK_URL}', 1, '2025-01-30 06:30:48', '2025-01-30 06:30:48'),
(12, 'slack', 'task_reminder', NULL, 'You have a task reminder for Task #{TASK_ID} - \"{TASK_TITLE}\". You can view the task here: {TASK_URL}.', 1, '2025-01-30 06:30:56', '2025-01-30 06:30:56'),
(13, 'slack', 'recurring_task', NULL, 'The recurring task #{TASK_ID} - \"{TASK_TITLE}\" has been executed. You can view the new instance here: {TASK_URL}.', 1, '2025-01-30 06:31:03', '2025-01-30 06:31:03');

-- --------------------------------------------------------

--
-- Table structure for table `time_trackers`
--

CREATE TABLE `time_trackers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `start_date_time` datetime NOT NULL,
  `end_date_time` datetime DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `todos`
--

CREATE TABLE `todos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `priority` varchar(255) NOT NULL,
  `is_completed` tinyint(1) NOT NULL DEFAULT 0,
  `creator_id` bigint(20) UNSIGNED NOT NULL,
  `creator_type` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `units`
--

CREATE TABLE `units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE `updates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `updates`
--

INSERT INTO `updates` (`id`, `version`, `created_at`, `updated_at`) VALUES
(1, '1.0.0', '2023-10-03 06:26:04', '2023-10-03 06:26:04'),
(2, '1.0.1', '2023-10-30 05:35:10', '2023-10-30 05:35:10'),
(3, '1.0.2', '2024-01-27 04:21:48', '2024-01-27 04:21:53'),
(4, '1.0.3', '2024-01-27 04:22:09', '2024-01-27 04:22:16'),
(5, '1.0.4', '2024-03-01 12:53:15', '2024-03-01 12:53:15'),
(6, '1.0.5', '2024-04-04 17:59:02', '2024-04-04 17:59:02'),
(7, '1.0.6', '2024-04-25 16:16:21', '2024-04-25 16:16:21'),
(8, '1.0.7', '2024-05-07 14:52:04', '2024-05-07 14:52:04'),
(9, '1.0.8', '2024-05-30 08:06:35', '2024-05-30 08:06:35'),
(10, '1.0.9', '2024-06-27 12:10:06', '2024-06-27 12:10:06'),
(11, '1.0.10', '2024-07-10 12:05:41', '2024-07-10 12:05:41'),
(12, '1.1.0', '2024-09-17 18:27:48', '2024-09-17 18:27:48'),
(13, '1.1.1', '2024-10-26 17:26:45', '2024-10-26 17:26:45'),
(14, '1.1.2', '2024-12-20 19:36:16', '2024-12-20 19:36:16'),
(15, '1.1.3', '2025-01-30 06:17:57', '2025-01-30 06:17:57');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `country_code` varchar(28) DEFAULT NULL,
  `country_iso_code` varchar(28) DEFAULT NULL,
  `phone` varchar(56) DEFAULT NULL,
  `email` varchar(191) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `zip` varchar(56) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `dob` date DEFAULT NULL,
  `doj` date DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'avatar.png',
  `active_status` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'For chatify messenger',
  `dark_mode` tinyint(1) NOT NULL DEFAULT 0,
  `messenger_color` varchar(255) DEFAULT NULL,
  `lang` varchar(28) NOT NULL DEFAULT 'en',
  `remember_token` text DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `default_workspace_id` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `status` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_client_preferences`
--

CREATE TABLE `user_client_preferences` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` varchar(56) NOT NULL,
  `table_name` varchar(255) NOT NULL,
  `visible_columns` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`visible_columns`)),
  `enabled_notifications` longtext DEFAULT NULL,
  `default_view` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_workspace`
--

CREATE TABLE `user_workspace` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `workspace_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `workspaces`
--

CREATE TABLE `workspaces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `is_primary` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_workspace_id_index` (`workspace_id`);

--
-- Indexes for table `allowances`
--
ALTER TABLE `allowances`
  ADD PRIMARY KEY (`id`),
  ADD KEY `allowances_workspace_id_foreign` (`workspace_id`);

--
-- Indexes for table `allowance_payslip`
--
ALTER TABLE `allowance_payslip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `allowance_payslip_allowance_id_foreign` (`allowance_id`),
  ADD KEY `allowance_payslip_payslip_id_foreign` (`payslip_id`);

--
-- Indexes for table `ch_favorites`
--
ALTER TABLE `ch_favorites`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ch_messages`
--
ALTER TABLE `ch_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_email_unique` (`email`);

--
-- Indexes for table `client_meeting`
--
ALTER TABLE `client_meeting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_meeting_meeting_id_client_id_unique` (`meeting_id`,`client_id`),
  ADD KEY `client_meeting_client_id_foreign` (`client_id`);

--
-- Indexes for table `client_notifications`
--
ALTER TABLE `client_notifications`
  ADD PRIMARY KEY (`client_id`,`notification_id`),
  ADD KEY `client_notifications_notification_id_foreign` (`notification_id`);

--
-- Indexes for table `client_project`
--
ALTER TABLE `client_project`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_client_project_id_client_id_unique` (`project_id`,`client_id`),
  ADD KEY `project_client_client_id_foreign` (`client_id`);

--
-- Indexes for table `client_workspace`
--
ALTER TABLE `client_workspace`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_workspace_workspace_id_client_id_unique` (`workspace_id`,`client_id`),
  ADD KEY `client_workspace_client_id_foreign` (`client_id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comments_commentable_type_commentable_id_index` (`commentable_type`,`commentable_id`),
  ADD KEY `comments_parent_id_index` (`parent_id`),
  ADD KEY `comments_user_id_index` (`commenter_id`);

--
-- Indexes for table `comment_attachments`
--
ALTER TABLE `comment_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comment_attachments_comment_id_foreign` (`comment_id`);

--
-- Indexes for table `contracts`
--
ALTER TABLE `contracts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contracts_workspace_id_foreign` (`workspace_id`),
  ADD KEY `contracts_project_id_foreign` (`project_id`),
  ADD KEY `contracts_client_id_foreign` (`client_id`),
  ADD KEY `contracts_contract_type_id_foreign` (`contract_type_id`);

--
-- Indexes for table `contract_types`
--
ALTER TABLE `contract_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `contract_types_workspace_id_foreign` (`workspace_id`);

--
-- Indexes for table `deductions`
--
ALTER TABLE `deductions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `deduction_payslip`
--
ALTER TABLE `deduction_payslip`
  ADD PRIMARY KEY (`id`),
  ADD KEY `deduction_payslip_deduction_id_foreign` (`deduction_id`),
  ADD KEY `deduction_payslip_payslip_id_foreign` (`payslip_id`);

--
-- Indexes for table `estimates_invoices`
--
ALTER TABLE `estimates_invoices`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estimates_invoices_client_id_foreign` (`client_id`),
  ADD KEY `estimates_invoices_workspace_id_foreign` (`workspace_id`);

--
-- Indexes for table `estimates_invoice_item`
--
ALTER TABLE `estimates_invoice_item`
  ADD PRIMARY KEY (`id`),
  ADD KEY `estimates_invoices_items_item_id_foreign` (`item_id`),
  ADD KEY `estimates_invoices_items_estimate_invoice_id_foreign` (`estimates_invoice_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expenses_workspace_id_foreign` (`workspace_id`),
  ADD KEY `expenses_user_id_foreign` (`user_id`),
  ADD KEY `expenses_expense_type_id_foreign` (`expense_type_id`);

--
-- Indexes for table `expense_types`
--
ALTER TABLE `expense_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `expense_types_workspace_id_foreign` (`workspace_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `favorites_favoritable_type_favoritable_id_index` (`favoritable_type`,`favoritable_id`),
  ADD KEY `favorites_user_id_foreign` (`user_id`),
  ADD KEY `favorites_client_id_foreign` (`client_id`);

--
-- Indexes for table `fcm_tokens`
--
ALTER TABLE `fcm_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fcm_tokens_user_id_foreign` (`user_id`),
  ADD KEY `fcm_tokens_client_id_foreign` (`client_id`);

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leave_editors`
--
ALTER TABLE `leave_editors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_editors_user_id_foreign` (`user_id`);

--
-- Indexes for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_requests_user_id_foreign` (`user_id`),
  ADD KEY `leave_requests_workspace_id_foreign` (`workspace_id`);

--
-- Indexes for table `leave_request_visibility`
--
ALTER TABLE `leave_request_visibility`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leave_request_id` (`leave_request_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `media_uuid_unique` (`uuid`),
  ADD KEY `media_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `media_order_column_index` (`order_column`);

--
-- Indexes for table `meetings`
--
ALTER TABLE `meetings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `meeting_user`
--
ALTER TABLE `meeting_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `meeting_user_meeting_id_user_id_unique` (`meeting_id`,`user_id`),
  ADD KEY `meeting_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `menu_orders`
--
ALTER TABLE `menu_orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `menu_orders_user_id_foreign` (`user_id`),
  ADD KEY `menu_orders_client_id_foreign` (`client_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `milestones`
--
ALTER TABLE `milestones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `milestones_project_id_foreign` (`project_id`),
  ADD KEY `milestones_workspace_id_foreign` (`workspace_id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notes_workspace_id_foreign` (`workspace_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_workspace_id_foreign` (`workspace_id`);

--
-- Indexes for table `notification_user`
--
ALTER TABLE `notification_user`
  ADD PRIMARY KEY (`user_id`,`notification_id`),
  ADD KEY `notification_user_notification_id_foreign` (`notification_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payment_methods_workspace_id_foreign` (`workspace_id`);

--
-- Indexes for table `payslips`
--
ALTER TABLE `payslips`
  ADD PRIMARY KEY (`id`),
  ADD KEY `payslips_user_id_foreign` (`user_id`),
  ADD KEY `payslips_workspace_id_foreign` (`workspace_id`),
  ADD KEY `payslips_payment_method_id_foreign` (`payment_method_id`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `pinned`
--
ALTER TABLE `pinned`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pinned_pinnable_type_pinnable_id_index` (`pinnable_type`,`pinnable_id`),
  ADD KEY `pinned_user_id_foreign` (`user_id`),
  ADD KEY `pinned_client_id_foreign` (`client_id`);

--
-- Indexes for table `priorities`
--
ALTER TABLE `priorities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_status_id_foreign` (`status_id`);

--
-- Indexes for table `project_tag`
--
ALTER TABLE `project_tag`
  ADD PRIMARY KEY (`id`),
  ADD KEY `project_tag_project_id_foreign` (`project_id`),
  ADD KEY `project_tag_tag_id_foreign` (`tag_id`);

--
-- Indexes for table `project_user`
--
ALTER TABLE `project_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_user_project_id_user_id_unique` (`project_id`,`user_id`),
  ADD KEY `project_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `recurring_tasks`
--
ALTER TABLE `recurring_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recurring_tasks_task_id_foreign` (`task_id`);

--
-- Indexes for table `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reminders_remindable_type_remindable_id_index` (`remindable_type`,`remindable_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `role_status`
--
ALTER TABLE `role_status`
  ADD PRIMARY KEY (`role_id`,`status_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `status_timelines`
--
ALTER TABLE `status_timelines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `status_timelines_entity_id_entity_type_index` (`entity_id`,`entity_type`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tasks_project_id_foreign` (`project_id`);

--
-- Indexes for table `task_user`
--
ALTER TABLE `task_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `task_user_task_id_user_id_unique` (`task_id`,`user_id`),
  ADD KEY `task_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `taxes`
--
ALTER TABLE `taxes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `templates`
--
ALTER TABLE `templates`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `templates_type_name_unique` (`type`,`name`);

--
-- Indexes for table `time_trackers`
--
ALTER TABLE `time_trackers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `time_trackers_workspace_id_foreign` (`workspace_id`),
  ADD KEY `time_trackers_user_id_foreign` (`user_id`);

--
-- Indexes for table `todos`
--
ALTER TABLE `todos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `units`
--
ALTER TABLE `units`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `updates`
--
ALTER TABLE `updates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_client_preferences`
--
ALTER TABLE `user_client_preferences`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_workspace`
--
ALTER TABLE `user_workspace`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_workspace_workspace_id_user_id_unique` (`workspace_id`,`user_id`),
  ADD KEY `user_workspace_user_id_foreign` (`user_id`);

--
-- Indexes for table `workspaces`
--
ALTER TABLE `workspaces`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allowances`
--
ALTER TABLE `allowances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `allowance_payslip`
--
ALTER TABLE `allowance_payslip`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_meeting`
--
ALTER TABLE `client_meeting`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_project`
--
ALTER TABLE `client_project`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `client_workspace`
--
ALTER TABLE `client_workspace`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comment_attachments`
--
ALTER TABLE `comment_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contracts`
--
ALTER TABLE `contracts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contract_types`
--
ALTER TABLE `contract_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `deductions`
--
ALTER TABLE `deductions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `deduction_payslip`
--
ALTER TABLE `deduction_payslip`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `estimates_invoices`
--
ALTER TABLE `estimates_invoices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `estimates_invoice_item`
--
ALTER TABLE `estimates_invoice_item`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_types`
--
ALTER TABLE `expense_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fcm_tokens`
--
ALTER TABLE `fcm_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `leave_editors`
--
ALTER TABLE `leave_editors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_requests`
--
ALTER TABLE `leave_requests`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leave_request_visibility`
--
ALTER TABLE `leave_request_visibility`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `media`
--
ALTER TABLE `media`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meetings`
--
ALTER TABLE `meetings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `meeting_user`
--
ALTER TABLE `meeting_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `menu_orders`
--
ALTER TABLE `menu_orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `milestones`
--
ALTER TABLE `milestones`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payment_methods`
--
ALTER TABLE `payment_methods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `payslips`
--
ALTER TABLE `payslips`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=220;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pinned`
--
ALTER TABLE `pinned`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `priorities`
--
ALTER TABLE `priorities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_tag`
--
ALTER TABLE `project_tag`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_user`
--
ALTER TABLE `project_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recurring_tasks`
--
ALTER TABLE `recurring_tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `status_timelines`
--
ALTER TABLE `status_timelines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_user`
--
ALTER TABLE `task_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `taxes`
--
ALTER TABLE `taxes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `templates`
--
ALTER TABLE `templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `time_trackers`
--
ALTER TABLE `time_trackers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `todos`
--
ALTER TABLE `todos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `units`
--
ALTER TABLE `units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `updates`
--
ALTER TABLE `updates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_client_preferences`
--
ALTER TABLE `user_client_preferences`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_workspace`
--
ALTER TABLE `user_workspace`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `workspaces`
--
ALTER TABLE `workspaces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `allowances`
--
ALTER TABLE `allowances`
  ADD CONSTRAINT `allowances_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `allowance_payslip`
--
ALTER TABLE `allowance_payslip`
  ADD CONSTRAINT `allowance_payslip_allowance_id_foreign` FOREIGN KEY (`allowance_id`) REFERENCES `allowances` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `allowance_payslip_payslip_id_foreign` FOREIGN KEY (`payslip_id`) REFERENCES `payslips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_meeting`
--
ALTER TABLE `client_meeting`
  ADD CONSTRAINT `client_meeting_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_meeting_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_notifications`
--
ALTER TABLE `client_notifications`
  ADD CONSTRAINT `client_notifications_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_notifications_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_project`
--
ALTER TABLE `client_project`
  ADD CONSTRAINT `project_client_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_client_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_workspace`
--
ALTER TABLE `client_workspace`
  ADD CONSTRAINT `client_workspace_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_workspace_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `comments_user_id_foreign` FOREIGN KEY (`commenter_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `comment_attachments`
--
ALTER TABLE `comment_attachments`
  ADD CONSTRAINT `comment_attachments_comment_id_foreign` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contracts`
--
ALTER TABLE `contracts`
  ADD CONSTRAINT `contracts_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_contract_type_id_foreign` FOREIGN KEY (`contract_type_id`) REFERENCES `contract_types` (`id`),
  ADD CONSTRAINT `contracts_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `contracts_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `contract_types`
--
ALTER TABLE `contract_types`
  ADD CONSTRAINT `contract_types_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `deduction_payslip`
--
ALTER TABLE `deduction_payslip`
  ADD CONSTRAINT `deduction_payslip_deduction_id_foreign` FOREIGN KEY (`deduction_id`) REFERENCES `deductions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `deduction_payslip_payslip_id_foreign` FOREIGN KEY (`payslip_id`) REFERENCES `payslips` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `estimates_invoices`
--
ALTER TABLE `estimates_invoices`
  ADD CONSTRAINT `estimates_invoices_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `estimates_invoices_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `estimates_invoice_item`
--
ALTER TABLE `estimates_invoice_item`
  ADD CONSTRAINT `estimates_invoices_items_estimate_invoice_id_foreign` FOREIGN KEY (`estimates_invoice_id`) REFERENCES `estimates_invoices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `estimates_invoices_items_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `expenses_expense_type_id_foreign` FOREIGN KEY (`expense_type_id`) REFERENCES `expense_types` (`id`),
  ADD CONSTRAINT `expenses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `expenses_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `expense_types`
--
ALTER TABLE `expense_types`
  ADD CONSTRAINT `expense_types_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `favorites_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `favorites_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fcm_tokens`
--
ALTER TABLE `fcm_tokens`
  ADD CONSTRAINT `fcm_tokens_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fcm_tokens_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_editors`
--
ALTER TABLE `leave_editors`
  ADD CONSTRAINT `leave_editors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_requests`
--
ALTER TABLE `leave_requests`
  ADD CONSTRAINT `leave_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_requests_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leave_request_visibility`
--
ALTER TABLE `leave_request_visibility`
  ADD CONSTRAINT `leave_request_visibility_ibfk_1` FOREIGN KEY (`leave_request_id`) REFERENCES `leave_requests` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `leave_request_visibility_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `meeting_user`
--
ALTER TABLE `meeting_user`
  ADD CONSTRAINT `meeting_user_meeting_id_foreign` FOREIGN KEY (`meeting_id`) REFERENCES `meetings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `meeting_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu_orders`
--
ALTER TABLE `menu_orders`
  ADD CONSTRAINT `menu_orders_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `menu_orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `milestones`
--
ALTER TABLE `milestones`
  ADD CONSTRAINT `milestones_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `milestones_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notes`
--
ALTER TABLE `notes`
  ADD CONSTRAINT `notes_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notification_user`
--
ALTER TABLE `notification_user`
  ADD CONSTRAINT `notification_user_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payment_methods`
--
ALTER TABLE `payment_methods`
  ADD CONSTRAINT `payment_methods_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payslips`
--
ALTER TABLE `payslips`
  ADD CONSTRAINT `payslips_payment_method_id_foreign` FOREIGN KEY (`payment_method_id`) REFERENCES `payment_methods` (`id`),
  ADD CONSTRAINT `payslips_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `payslips_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pinned`
--
ALTER TABLE `pinned`
  ADD CONSTRAINT `pinned_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `pinned_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_tag`
--
ALTER TABLE `project_tag`
  ADD CONSTRAINT `project_tag_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_tag_tag_id_foreign` FOREIGN KEY (`tag_id`) REFERENCES `tags` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `project_user`
--
ALTER TABLE `project_user`
  ADD CONSTRAINT `project_user_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `project_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recurring_tasks`
--
ALTER TABLE `recurring_tasks`
  ADD CONSTRAINT `recurring_tasks_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_status`
--
ALTER TABLE `role_status`
  ADD CONSTRAINT `role_status_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_status_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_project_id_foreign` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_user`
--
ALTER TABLE `task_user`
  ADD CONSTRAINT `task_user_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `tasks` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `time_trackers`
--
ALTER TABLE `time_trackers`
  ADD CONSTRAINT `time_trackers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `time_trackers_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_workspace`
--
ALTER TABLE `user_workspace`
  ADD CONSTRAINT `user_workspace_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_workspace_workspace_id_foreign` FOREIGN KEY (`workspace_id`) REFERENCES `workspaces` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
