-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Feb 17, 2026 at 08:57 AM
-- Server version: 8.0.44
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prime_bank_db_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `agents`
--

CREATE TABLE `agents` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `department` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('free','busy','offline') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'offline',
  `last_seen` timestamp NULL DEFAULT NULL,
  `total_calls` int NOT NULL DEFAULT '0',
  `total_duration` int NOT NULL DEFAULT '0',
  `average_rating` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `agents`
--

INSERT INTO `agents` (`id`, `user_id`, `name`, `email`, `phone`, `department`, `status`, `last_seen`, `total_calls`, `total_duration`, `average_rating`, `created_at`, `updated_at`) VALUES
(7, 16, 'Test Staff Agent', 'teststaff_1771055233@example.com', NULL, 'Customer Support', 'free', '2026-02-17 07:00:31', 5, -186640, 0, '2026-02-14 07:47:14', '2026-02-17 07:00:31'),
(8, 1, 'Receptionist', 'ashrafulinstasure@gmail.com', NULL, 'Customer Support', 'free', '2026-02-14 08:11:35', 0, 0, 0, '2026-02-14 07:49:50', '2026-02-14 08:11:35');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `call_feedback`
--

CREATE TABLE `call_feedback` (
  `id` bigint UNSIGNED NOT NULL,
  `call_session_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `agent_id` bigint UNSIGNED DEFAULT NULL,
  `rating` int NOT NULL COMMENT '1-5 stars',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `customer_name` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `call_feedback`
--

INSERT INTO `call_feedback` (`id`, `call_session_id`, `user_id`, `agent_id`, `rating`, `comment`, `customer_name`, `created_at`, `updated_at`) VALUES
(3, 12, 17, 7, 5, 'Excellent service! The agent was very helpful and professional.', 'Test Visitor Customer', '2026-02-14 07:47:16', '2026-02-14 07:47:16'),
(4, 13, 4, 7, 5, NULL, 'Visitor', '2026-02-14 07:50:55', '2026-02-14 07:50:55'),
(5, 15, 4, 7, 3, NULL, 'Visitor', '2026-02-14 07:52:35', '2026-02-14 07:52:35'),
(6, 16, 4, 7, 3, 'jkdfjafj', 'Visitor', '2026-02-16 11:54:50', '2026-02-16 11:54:50'),
(7, 17, 4, 7, 3, NULL, 'Visitor', '2026-02-17 07:04:45', '2026-02-17 07:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `call_metrics`
--

CREATE TABLE `call_metrics` (
  `id` bigint UNSIGNED NOT NULL,
  `date` date NOT NULL DEFAULT (curdate()),
  `total_calls` int NOT NULL DEFAULT '0',
  `connected_calls` int NOT NULL DEFAULT '0',
  `missed_calls` int NOT NULL DEFAULT '0',
  `total_duration` int NOT NULL DEFAULT '0',
  `total_wait_time` int NOT NULL DEFAULT '0',
  `average_wait_time` double NOT NULL DEFAULT '0',
  `average_rating` double NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `call_metrics`
--

INSERT INTO `call_metrics` (`id`, `date`, `total_calls`, `connected_calls`, `missed_calls`, `total_duration`, `total_wait_time`, `average_wait_time`, `average_rating`, `created_at`, `updated_at`) VALUES
(1, '2026-02-14', 6, 0, 6, -175, 0, 0, 0, '2026-02-14 04:46:53', '2026-02-14 07:52:20'),
(2, '2026-02-16', 1, 0, 1, -186135, 0, 0, 0, '2026-02-16 11:54:38', '2026-02-16 11:54:38'),
(3, '2026-02-17', 1, 0, 1, -330, 0, 0, 0, '2026-02-17 07:00:31', '2026-02-17 07:00:31');

-- --------------------------------------------------------

--
-- Table structure for table `call_queue`
--

CREATE TABLE `call_queue` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `customer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('waiting','connected','cancelled','timeout','ended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `position` int NOT NULL DEFAULT '0',
  `joined_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `connected_at` timestamp NULL DEFAULT NULL,
  `ended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `call_queue`
--

INSERT INTO `call_queue` (`id`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `status`, `position`, `joined_at`, `connected_at`, `ended_at`, `created_at`, `updated_at`) VALUES
(14, 17, 'Test Visitor Customer', '', 'testvisitor_1771055233@example.com', 'ended', 0, '2026-02-14 07:47:15', '2026-02-14 07:47:15', '2026-02-14 07:47:16', '2026-02-14 07:47:15', '2026-02-14 07:47:16'),
(15, 18, 'Second Visitor', '', 'visitor2_1771055233@example.com', 'connected', 1, '2026-02-14 07:47:15', '2026-02-14 07:49:59', NULL, '2026-02-14 07:47:15', '2026-02-14 07:49:59'),
(16, 4, 'Visitor', '', 'kali1212hit@gmail.com', 'ended', 0, '2026-02-14 07:49:15', '2026-02-14 07:49:15', '2026-02-14 07:50:50', '2026-02-14 07:49:15', '2026-02-14 07:50:50'),
(17, 4, 'Visitor', '', 'kali1212hit@gmail.com', 'ended', 0, '2026-02-14 07:51:00', '2026-02-14 07:51:00', '2026-02-14 07:52:20', '2026-02-14 07:51:00', '2026-02-14 07:52:20'),
(18, 4, 'Visitor', '', 'kali1212hit@gmail.com', 'ended', 0, '2026-02-14 08:12:23', '2026-02-14 08:12:23', '2026-02-16 11:54:38', '2026-02-14 08:12:23', '2026-02-16 11:54:38'),
(19, 4, 'Visitor', '', 'kali1212hit@gmail.com', 'ended', 0, '2026-02-17 06:55:01', '2026-02-17 06:55:01', '2026-02-17 07:00:31', '2026-02-17 06:55:01', '2026-02-17 07:00:31');

-- --------------------------------------------------------

--
-- Table structure for table `call_sessions`
--

CREATE TABLE `call_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `channel_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `agora_uid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `agent_id` bigint UNSIGNED DEFAULT NULL,
  `call_queue_id` bigint UNSIGNED DEFAULT NULL,
  `status` enum('ringing','connected','ended') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ringing',
  `started_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ended_at` timestamp NULL DEFAULT NULL,
  `duration` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `call_sessions`
--

INSERT INTO `call_sessions` (`id`, `channel_name`, `agora_uid`, `user_id`, `agent_id`, `call_queue_id`, `status`, `started_at`, `ended_at`, `duration`, `created_at`, `updated_at`) VALUES
(12, 'call_6990288326735_1771055235', NULL, 17, 7, 14, 'ended', '2026-02-14 07:47:15', '2026-02-14 07:47:15', 0, '2026-02-14 07:47:15', '2026-02-14 07:47:15'),
(13, 'call_699028fba1cf6_1771055355', NULL, 4, 7, 16, 'ended', '2026-02-14 07:49:15', '2026-02-14 07:50:50', -95, '2026-02-14 07:49:15', '2026-02-14 07:50:50'),
(14, 'call_699029273b614_1771055399', NULL, 18, 8, 15, 'ringing', '2026-02-14 07:49:59', NULL, 0, '2026-02-14 07:49:59', '2026-02-14 07:49:59'),
(15, 'call_69902964683c6_1771055460', NULL, 4, 7, 17, 'ended', '2026-02-14 07:51:00', '2026-02-14 07:52:20', -80, '2026-02-14 07:51:00', '2026-02-14 07:52:20'),
(16, 'call_69902e67ba00e_1771056743', NULL, 4, 7, 18, 'ended', '2026-02-14 08:12:23', '2026-02-16 11:54:38', -186135, '2026-02-14 08:12:23', '2026-02-16 11:54:38'),
(17, 'call_699410c511115_1771311301', NULL, 4, 7, 19, 'ended', '2026-02-17 06:55:01', '2026-02-17 07:00:31', -331, '2026-02-17 06:55:01', '2026-02-17 07:00:31');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `insurance_package_id` bigint UNSIGNED NOT NULL,
  `order_id` bigint UNSIGNED DEFAULT NULL,
  `claim_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `claim_amount` decimal(12,2) NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` enum('submitted','under_review','approved','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'submitted',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `failed_jobs`
--

INSERT INTO `failed_jobs` (`id`, `uuid`, `connection`, `queue`, `payload`, `exception`, `failed_at`) VALUES
(1, 'f67b8db4-cad1-48b7-b1b2-eb1dd80fc788', 'redis', 'default', '{\"uuid\":\"f67b8db4-cad1-48b7-b1b2-eb1dd80fc788\",\"displayName\":\"App\\\\Notifications\\\\VisitorRegistered\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Visitor\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:35:\\\"App\\\\Notifications\\\\VisitorRegistered\\\":3:{s:10:\\\"\\u0000*\\u0000visitor\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:18:\\\"App\\\\Models\\\\Visitor\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:8:\\\"\\u0000*\\u0000visit\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:16:\\\"App\\\\Models\\\\Visit\\\";s:2:\\\"id\\\";i:1;s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"00a68a37-73ae-42a9-a0d3-d9e2bf895374\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1769095333,\"id\":\"L0CEhANtY2S3xIi4atjPcLrgClBIssxT\",\"attempts\":0,\"delay\":null}', 'Illuminate\\Database\\Eloquent\\ModelNotFoundException: No query results for model [App\\Models\\Visitor]. in /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Database/Eloquent/Builder.php:780\nStack trace:\n#0 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/SerializesAndRestoresModelIdentifiers.php(110): Illuminate\\Database\\Eloquent\\Builder->firstOrFail()\n#1 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/SerializesAndRestoresModelIdentifiers.php(63): Illuminate\\Notifications\\Notification->restoreModel()\n#2 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/SerializesModels.php(97): Illuminate\\Notifications\\Notification->getRestoredPropertyValue()\n#3 [internal function]: Illuminate\\Notifications\\Notification->__unserialize()\n#4 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(95): unserialize()\n#5 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/CallQueuedHandler.php(62): Illuminate\\Queue\\CallQueuedHandler->getCommand()\n#6 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/Jobs/Job.php(102): Illuminate\\Queue\\CallQueuedHandler->call()\n#7 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(485): Illuminate\\Queue\\Jobs\\Job->fire()\n#8 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(435): Illuminate\\Queue\\Worker->process()\n#9 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/Worker.php(201): Illuminate\\Queue\\Worker->runJob()\n#10 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(148): Illuminate\\Queue\\Worker->daemon()\n#11 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Queue/Console/WorkCommand.php(131): Illuminate\\Queue\\Console\\WorkCommand->runWorker()\n#12 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(36): Illuminate\\Queue\\Console\\WorkCommand->handle()\n#13 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Container/Util.php(43): Illuminate\\Container\\BoundMethod::{closure:Illuminate\\Container\\BoundMethod::call():35}()\n#14 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(96): Illuminate\\Container\\Util::unwrapIfClosure()\n#15 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Container/BoundMethod.php(35): Illuminate\\Container\\BoundMethod::callBoundMethod()\n#16 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Container/Container.php(799): Illuminate\\Container\\BoundMethod::call()\n#17 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Console/Command.php(211): Illuminate\\Container\\Container->call()\n#18 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/symfony/console/Command/Command.php(341): Illuminate\\Console\\Command->execute()\n#19 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Console/Command.php(180): Symfony\\Component\\Console\\Command\\Command->run()\n#20 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/symfony/console/Application.php(1102): Illuminate\\Console\\Command->run()\n#21 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/symfony/console/Application.php(356): Symfony\\Component\\Console\\Application->doRunCommand()\n#22 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/symfony/console/Application.php(195): Symfony\\Component\\Console\\Application->doRun()\n#23 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Foundation/Console/Kernel.php(198): Symfony\\Component\\Console\\Application->run()\n#24 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/vendor/laravel/framework/src/Illuminate/Foundation/Application.php(1235): Illuminate\\Foundation\\Console\\Kernel->handle()\n#25 /home/ashraful/Unisoft/VMSUCBL/VMSUCBL/vms-ucbl/artisan(16): Illuminate\\Foundation\\Application->handleCommand()\n#26 {main}', '2026-01-22 16:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `insurance_packages`
--

CREATE TABLE `insurance_packages` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `price` decimal(10,2) NOT NULL,
  `coverage_amount` decimal(12,2) NOT NULL,
  `duration_months` int NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_20_050527_add_two_factor_columns_to_users_table', 1),
(5, '2026_01_20_050539_create_personal_access_tokens_table', 1),
(6, '2026_01_20_101151_create_permission_tables', 1),
(7, '2026_01_21_075910_create_visitors_table', 1),
(8, '2026_01_21_080315_create_visitor_blocks_table', 1),
(9, '2026_01_21_080400_create_visit_types_table', 1),
(10, '2026_01_21_080600_create_visits_table', 1),
(11, '2026_01_21_081745_create_rfids_table', 1),
(12, '2026_01_21_083711_create_visit_logs_table', 1),
(13, '2026_01_21_093741_create_notifications_table', 1),
(14, '2026_01_21_094133_create_visitor__otps_table', 1),
(16, '2026_01_23_120000_add_visit_id_to_rfids_table', 2),
(17, '2026_01_23_185341_update_visit_status_enum_only', 3),
(18, '2026_01_24_000000_add_checkin_checkout_columns_to_visits_table', 4),
(19, '2026_01_22_041040_create_user_infos_table', 5),
(20, '2026_01_22_045605_create_studentloginfroms_table', 5),
(21, '2026_01_27_090741_create_insurance_pacages_table', 5),
(22, '2026_01_27_091027_create_orders_table', 5),
(23, '2026_01_27_091158_create_claims_table', 5),
(24, '2026_02_14_100000_create_video_call_tables', 6),
(25, '2026_02_14_065214_fix_call_queue_status_enum', 7);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_permissions`
--

INSERT INTO `model_has_permissions` (`permission_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 1),
(3, 'App\\Models\\User', 1),
(4, 'App\\Models\\User', 1),
(5, 'App\\Models\\User', 1),
(6, 'App\\Models\\User', 1),
(7, 'App\\Models\\User', 1),
(8, 'App\\Models\\User', 1),
(9, 'App\\Models\\User', 1),
(10, 'App\\Models\\User', 1),
(11, 'App\\Models\\User', 1),
(1, 'App\\Models\\User', 2),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 2),
(4, 'App\\Models\\User', 2),
(5, 'App\\Models\\User', 2),
(6, 'App\\Models\\User', 2),
(7, 'App\\Models\\User', 2),
(8, 'App\\Models\\User', 2),
(9, 'App\\Models\\User', 2),
(10, 'App\\Models\\User', 2),
(11, 'App\\Models\\User', 2);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(3, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(1, 'App\\Models\\User', 3),
(4, 'App\\Models\\User', 4),
(2, 'App\\Models\\User', 5),
(4, 'App\\Models\\User', 6),
(2, 'App\\Models\\User', 7),
(4, 'App\\Models\\User', 8),
(4, 'App\\Models\\User', 9),
(2, 'App\\Models\\User', 10),
(4, 'App\\Models\\User', 11),
(4, 'App\\Models\\User', 12),
(2, 'App\\Models\\User', 13),
(4, 'App\\Models\\User', 14),
(2, 'App\\Models\\User', 16),
(4, 'App\\Models\\User', 17),
(4, 'App\\Models\\User', 18);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `visit_id` bigint UNSIGNED NOT NULL,
  `channel` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `sent_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `insurance_package_id` bigint UNSIGNED NOT NULL,
  `policy_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('pending','active','expired','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'view visitors', 'web', '2026-01-22 19:15:03', '2026-01-22 19:15:03'),
(2, 'create visitors', 'web', '2026-01-22 19:15:03', '2026-01-22 19:15:03'),
(3, 'edit visitors', 'web', '2026-01-22 19:15:03', '2026-01-22 19:15:03'),
(4, 'delete visitors', 'web', '2026-01-22 19:15:03', '2026-01-22 19:15:03'),
(5, 'create visit', 'web', '2026-01-23 18:54:52', '2026-01-23 18:54:52'),
(6, 'verify visit otp', 'web', '2026-01-23 18:54:52', '2026-01-23 18:54:52'),
(7, 'approve visit', 'web', '2026-01-23 18:54:52', '2026-01-23 18:54:52'),
(8, 'reject visit', 'web', '2026-01-23 18:54:52', '2026-01-23 18:54:52'),
(9, 'checkin visit', 'web', '2026-01-23 18:54:52', '2026-01-23 18:54:52'),
(10, 'checkout visit', 'web', '2026-01-23 18:54:52', '2026-01-23 18:54:52'),
(11, 'view live dashboard', 'web', '2026-01-23 18:54:52', '2026-01-23 18:54:52');

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rfids`
--

CREATE TABLE `rfids` (
  `id` bigint UNSIGNED NOT NULL,
  `tag_uid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `visit_id` bigint UNSIGNED DEFAULT NULL,
  `generated_by` bigint UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'admin', 'web', '2026-01-22 04:47:18', '2026-01-22 04:47:18'),
(2, 'staff', 'web', '2026-01-22 04:47:18', '2026-01-22 04:47:18'),
(3, 'receptionist', 'web', '2026-01-22 04:47:18', '2026-01-22 04:47:18'),
(4, 'visitor', 'web', '2026-01-22 04:47:18', '2026-01-22 04:47:18'),
(5, 'Manager', 'web', '2026-01-22 08:32:57', '2026-01-22 08:32:57'),
(12, 'play', 'web', '2026-01-22 08:54:36', '2026-01-22 08:54:36');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(1, 2),
(7, 2),
(8, 2),
(9, 2),
(10, 2),
(11, 2),
(1, 3),
(5, 3),
(6, 3),
(9, 3),
(10, 3),
(11, 3),
(1, 4);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('4sNcZ0sgF79KLz9M9N2nVcQTvG5aJJSgxSv2pv0q', 3, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64; rv:140.0) Gecko/20100101 Firefox/140.0', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiMFk0YzRYTWtzM1JMMGc1cXFoSlJlYmlqZHh0a2lPNFJCUDRzWGljWSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9maWxlIjtzOjU6InJvdXRlIjtzOjc6InByb2ZpbGUiO31zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mzt9', 1769078417),
('gTo8Jzn1r6YmgbTdVdRCFv3aDgCJLyEVZDWn4djk', 3, '127.0.0.1', 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiTFhaUmxlNEtBekFPd3d4cnpNNGk1cldpVTJzc0lra0dIVGVScnpMeiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mzc6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9hZG1pbi9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6MTU6ImFkbWluLmRhc2hib2FyZCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjA6e31zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aTozO30=', 1769070951);

-- --------------------------------------------------------

--
-- Table structure for table `studentloginfroms`
--

CREATE TABLE `studentloginfroms` (
  `id` bigint UNSIGNED NOT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `two_factor_secret` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_recovery_codes` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_team_id` bigint UNSIGNED DEFAULT NULL,
  `profile_photo_path` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `current_team_id`, `profile_photo_path`, `created_at`, `updated_at`) VALUES
(1, 'Receptionist', 'ashrafulinstasure@gmail.com', NULL, '$2y$12$iGysvL.hNGbGlCzB8z/uHOHSRB/yIXeaJgfGzFtmXb8ipZXU3x3/K', NULL, NULL, NULL, 'PZh29QlItlq380t97psrGXkoBCtEf7yfH717J4DH5nvCzSBYF292PY7ffaYY', NULL, NULL, '2026-01-22 03:50:39', '2026-01-22 03:50:39'),
(2, 'Staff', 'ashrafulunisoft@gmail.com', NULL, '$2y$12$cYI2OLGJ7cLuNJkz1tLYIO8l6e4UGO2BLjdZUK9NJCqFVSGvq1d.u', NULL, NULL, NULL, '1VR5RfTHCJuJoXAQxGZbUdX0lyRD5XI8v4yXYMSY7CTT0GnYQenp5OrdxDw4', NULL, NULL, '2026-01-22 03:50:39', '2026-01-22 07:25:39'),
(3, 'Admin', 'amshuvo64@gmail.com', NULL, '$2y$12$iGysvL.hNGbGlCzB8z/uHOHSRB/yIXeaJgfGzFtmXb8ipZXU3x3/K', NULL, NULL, NULL, 'wp5qQMozFBJwz2TTgbHgKfWFNvEbYSXfSGPgmWhaTbH6owv0XRmBv5OImGWm', NULL, NULL, '2026-01-22 03:50:39', '2026-01-22 03:50:39'),
(4, 'Visitor', 'kali1212hit@gmail.com', NULL, '$2y$12$iGysvL.hNGbGlCzB8z/uHOHSRB/yIXeaJgfGzFtmXb8ipZXU3x3/K', NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-22 03:50:39', '2026-01-22 03:50:39'),
(7, 'Customer Care Agent', 'staff@test.com', NULL, '$2y$12$YN7hIcj7JF6S4JCC16J8a.DKLMHMaYRB.DE3QUPOFdO4lhuavu3Tm', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-14 06:53:11', '2026-02-14 06:53:11'),
(8, 'Test Customer', 'customer@test.com', NULL, '$2y$12$7oQloK8EolDtafacqwPvVugKAfLUoxJoglksmk1WXvvrg6qI/pJjW', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-14 06:53:11', '2026-02-14 06:53:11'),
(9, 'Second Customer', 'customer2@test.com', NULL, '$2y$12$cvOYX6hGT6TNc5jzKN1qAOSLrzJ3cAKle95is3/iEVQCwJrNjp1pm', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-14 06:53:15', '2026-02-14 06:53:15'),
(12, 'Second Visitor', 'visitor2@example.com', NULL, '$2y$12$lMTdCVX4zPCXjpBzPSDY8uHtU9rAfT0wCSJbSeoMyPovy1YTs3kqy', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-14 07:43:45', '2026-02-14 07:43:45'),
(16, 'Test Staff Agent', 'teststaff_1771055233@example.com', NULL, '$2y$12$BCqGRH0qGfr7Mm2AOLt/gO9qZsWcCu8AiAFhOE2ENAVDZHuCzT1Eu', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-14 07:47:14', '2026-02-14 07:47:14'),
(17, 'Test Visitor Customer', 'testvisitor_1771055233@example.com', NULL, '$2y$12$sF.w1qeasfSsqiuJnJIRoey9B0vBQ9aWDt2NQiGQy6Ng/H4pP8/hK', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-14 07:47:14', '2026-02-14 07:47:14'),
(18, 'Second Visitor', 'visitor2_1771055233@example.com', NULL, '$2y$12$jaq1oIB96oD/C9INU66UTeZbhJV38EOhqikI6iRg4TYG/vN8iEppC', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-14 07:47:15', '2026-02-14 07:47:15');

-- --------------------------------------------------------

--
-- Table structure for table `user_infos`
--

CREATE TABLE `user_infos` (
  `id` bigint UNSIGNED NOT NULL,
  `first name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitors`
--

CREATE TABLE `visitors` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `is_blocked` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visitors`
--

INSERT INTO `visitors` (`id`, `name`, `phone`, `email`, `address`, `is_blocked`, `created_at`, `updated_at`, `deleted_at`) VALUES
(24, 'Kessie Davis', '+1 (837) 683-2037', 'geluwe@mailinator.com', 'Flores and Foreman Trading', 0, '2026-01-23 10:38:11', '2026-01-23 10:38:11', NULL),
(25, 'ashraful', '01859385787', 'ashrafulunisoft@gmail.com', 'Flores and Foreman Trading', 0, '2026-01-23 10:40:51', '2026-01-23 10:40:51', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `visitor_blocks`
--

CREATE TABLE `visitor_blocks` (
  `id` bigint UNSIGNED NOT NULL,
  `visitor_id` bigint UNSIGNED NOT NULL,
  `block_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `blocked_by` bigint UNSIGNED NOT NULL,
  `blocked_at` timestamp NOT NULL,
  `unblocked_by` bigint UNSIGNED DEFAULT NULL,
  `unblocked_at` timestamp NULL DEFAULT NULL,
  `status` enum('blocked','unblocked') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'blocked',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visitor__otps`
--

CREATE TABLE `visitor__otps` (
  `id` bigint UNSIGNED NOT NULL,
  `visitor_id` bigint UNSIGNED NOT NULL,
  `otp_hash` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `channel` enum('email','sms','both') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'both',
  `expires_at` timestamp NOT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `attempts` tinyint UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE `visits` (
  `id` bigint UNSIGNED NOT NULL,
  `visitor_id` bigint UNSIGNED NOT NULL,
  `meeting_user_id` bigint UNSIGNED NOT NULL,
  `visit_type_id` bigint UNSIGNED NOT NULL,
  `purpose` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `schedule_time` datetime NOT NULL,
  `otp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `otp_verified_at` timestamp NULL DEFAULT NULL,
  `status` enum('pending_otp','pending_host','approved','rejected','checked_in','completed','pending','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `rfid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `checkin_time` timestamp NULL DEFAULT NULL,
  `checkout_time` timestamp NULL DEFAULT NULL,
  `rejected_reason` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visits`
--

INSERT INTO `visits` (`id`, `visitor_id`, `meeting_user_id`, `visit_type_id`, `purpose`, `schedule_time`, `otp`, `otp_verified_at`, `status`, `rfid`, `approved_at`, `checkin_time`, `checkout_time`, `rejected_reason`, `created_at`, `updated_at`) VALUES
(26, 24, 1, 2, 'Rerum quia laboriosa', '2026-01-23 00:00:00', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-23 10:38:11', '2026-01-23 10:38:11'),
(27, 25, 2, 2, 'Metting', '2026-01-23 00:00:00', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-23 10:40:51', '2026-01-23 10:40:51'),
(28, 25, 2, 1, 'meeting', '2026-01-23 00:00:00', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-23 10:49:31', '2026-01-23 10:49:31'),
(29, 25, 2, 2, 'urgent', '2026-01-23 00:00:00', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-23 10:55:04', '2026-01-23 10:55:04'),
(30, 25, 2, 2, 'urgent', '2026-01-23 00:00:00', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-23 11:02:32', '2026-01-23 11:02:32'),
(31, 25, 2, 1, 'urgent', '2026-01-23 00:00:00', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-23 12:54:17', '2026-01-23 12:54:17'),
(32, 25, 2, 1, 'urgent', '2026-01-23 00:00:00', NULL, NULL, 'pending_otp', NULL, NULL, NULL, NULL, NULL, '2026-01-23 19:18:36', '2026-01-23 19:18:36'),
(33, 25, 2, 1, 'urgent', '2026-01-23 00:00:00', NULL, NULL, 'pending_otp', NULL, NULL, NULL, NULL, NULL, '2026-01-23 19:22:26', '2026-01-23 19:22:26'),
(34, 25, 2, 1, 'urgent', '2026-01-23 00:00:00', NULL, NULL, 'pending_otp', NULL, NULL, NULL, NULL, NULL, '2026-01-23 19:26:04', '2026-01-23 19:26:04'),
(35, 25, 2, 1, 'urgent', '2026-01-23 00:00:00', NULL, '2026-01-23 19:31:21', 'pending', NULL, NULL, NULL, NULL, NULL, '2026-01-23 19:30:55', '2026-01-23 19:31:21'),
(36, 25, 2, 1, 'urgent', '2026-01-23 00:00:00', NULL, '2026-01-23 19:41:56', 'approved', 'RFID-HWFDAAHX', '2026-01-23 19:43:37', NULL, NULL, NULL, '2026-01-23 19:41:30', '2026-01-23 19:43:37'),
(37, 25, 2, 1, 'urgent', '2026-01-23 00:00:00', NULL, '2026-01-23 19:49:15', 'completed', 'RFID-TUKXPKMR', '2026-01-23 19:49:33', '2026-01-23 20:10:19', '2026-01-23 20:10:31', NULL, '2026-01-23 19:48:54', '2026-01-23 20:10:31');

-- --------------------------------------------------------

--
-- Table structure for table `visit_logs`
--

CREATE TABLE `visit_logs` (
  `id` bigint UNSIGNED NOT NULL,
  `visit_id` bigint UNSIGNED NOT NULL,
  `rfid_id` bigint UNSIGNED NOT NULL,
  `checkin_time` timestamp NULL DEFAULT NULL,
  `checkout_time` timestamp NULL DEFAULT NULL,
  `total_minutes` int DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `visit_types`
--

CREATE TABLE `visit_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `visit_types`
--

INSERT INTO `visit_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Meeting', NULL, NULL),
(2, 'Interview', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `agents`
--
ALTER TABLE `agents`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `agents_email_unique` (`email`),
  ADD KEY `agents_user_id_foreign` (`user_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `call_feedback`
--
ALTER TABLE `call_feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `call_feedback_call_session_id_foreign` (`call_session_id`),
  ADD KEY `call_feedback_user_id_foreign` (`user_id`),
  ADD KEY `call_feedback_agent_id_foreign` (`agent_id`);

--
-- Indexes for table `call_metrics`
--
ALTER TABLE `call_metrics`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `call_queue`
--
ALTER TABLE `call_queue`
  ADD PRIMARY KEY (`id`),
  ADD KEY `call_queue_user_id_foreign` (`user_id`);

--
-- Indexes for table `call_sessions`
--
ALTER TABLE `call_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `call_sessions_channel_name_unique` (`channel_name`),
  ADD KEY `call_sessions_user_id_foreign` (`user_id`),
  ADD KEY `call_sessions_agent_id_foreign` (`agent_id`),
  ADD KEY `call_sessions_call_queue_id_foreign` (`call_queue_id`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `claims_claim_number_unique` (`claim_number`),
  ADD KEY `claims_user_id_foreign` (`user_id`),
  ADD KEY `claims_insurance_package_id_foreign` (`insurance_package_id`),
  ADD KEY `claims_order_id_foreign` (`order_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `insurance_packages`
--
ALTER TABLE `insurance_packages`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_visit_id_foreign` (`visit_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `orders_policy_number_unique` (`policy_number`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_insurance_package_id_foreign` (`insurance_package_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

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
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `rfids`
--
ALTER TABLE `rfids`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rfids_tag_uid_unique` (`tag_uid`),
  ADD KEY `rfids_visit_id_foreign` (`visit_id`),
  ADD KEY `rfids_generated_by_foreign` (`generated_by`);

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
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `studentloginfroms`
--
ALTER TABLE `studentloginfroms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `studentloginfroms_email_unique` (`email`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_infos`
--
ALTER TABLE `user_infos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_infos_email_unique` (`email`);

--
-- Indexes for table `visitors`
--
ALTER TABLE `visitors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitors_phone_index` (`phone`),
  ADD KEY `visitors_email_index` (`email`);

--
-- Indexes for table `visitor_blocks`
--
ALTER TABLE `visitor_blocks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor_blocks_visitor_id_foreign` (`visitor_id`),
  ADD KEY `visitor_blocks_blocked_by_foreign` (`blocked_by`),
  ADD KEY `visitor_blocks_unblocked_by_foreign` (`unblocked_by`);

--
-- Indexes for table `visitor__otps`
--
ALTER TABLE `visitor__otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visitor__otps_visitor_id_is_active_index` (`visitor_id`,`is_active`);

--
-- Indexes for table `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visits_visitor_id_foreign` (`visitor_id`),
  ADD KEY `visits_meeting_user_id_foreign` (`meeting_user_id`),
  ADD KEY `visits_visit_type_id_foreign` (`visit_type_id`);

--
-- Indexes for table `visit_logs`
--
ALTER TABLE `visit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `visit_logs_visit_id_foreign` (`visit_id`),
  ADD KEY `visit_logs_rfid_id_foreign` (`rfid_id`);

--
-- Indexes for table `visit_types`
--
ALTER TABLE `visit_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `visit_types_name_unique` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `agents`
--
ALTER TABLE `agents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `call_feedback`
--
ALTER TABLE `call_feedback`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `call_metrics`
--
ALTER TABLE `call_metrics`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `call_queue`
--
ALTER TABLE `call_queue`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `call_sessions`
--
ALTER TABLE `call_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `insurance_packages`
--
ALTER TABLE `insurance_packages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rfids`
--
ALTER TABLE `rfids`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `studentloginfroms`
--
ALTER TABLE `studentloginfroms`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `user_infos`
--
ALTER TABLE `user_infos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitors`
--
ALTER TABLE `visitors`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `visitor_blocks`
--
ALTER TABLE `visitor_blocks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visitor__otps`
--
ALTER TABLE `visitor__otps`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visits`
--
ALTER TABLE `visits`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `visit_logs`
--
ALTER TABLE `visit_logs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `visit_types`
--
ALTER TABLE `visit_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `agents`
--
ALTER TABLE `agents`
  ADD CONSTRAINT `agents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `call_feedback`
--
ALTER TABLE `call_feedback`
  ADD CONSTRAINT `call_feedback_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `call_feedback_call_session_id_foreign` FOREIGN KEY (`call_session_id`) REFERENCES `call_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `call_feedback_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `call_queue`
--
ALTER TABLE `call_queue`
  ADD CONSTRAINT `call_queue_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `call_sessions`
--
ALTER TABLE `call_sessions`
  ADD CONSTRAINT `call_sessions_agent_id_foreign` FOREIGN KEY (`agent_id`) REFERENCES `agents` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `call_sessions_call_queue_id_foreign` FOREIGN KEY (`call_queue_id`) REFERENCES `call_queue` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `call_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_insurance_package_id_foreign` FOREIGN KEY (`insurance_package_id`) REFERENCES `insurance_packages` (`id`),
  ADD CONSTRAINT `claims_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `claims_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_insurance_package_id_foreign` FOREIGN KEY (`insurance_package_id`) REFERENCES `insurance_packages` (`id`),
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rfids`
--
ALTER TABLE `rfids`
  ADD CONSTRAINT `rfids_generated_by_foreign` FOREIGN KEY (`generated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `rfids_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visitor_blocks`
--
ALTER TABLE `visitor_blocks`
  ADD CONSTRAINT `visitor_blocks_blocked_by_foreign` FOREIGN KEY (`blocked_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `visitor_blocks_unblocked_by_foreign` FOREIGN KEY (`unblocked_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `visitor_blocks_visitor_id_foreign` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visitor__otps`
--
ALTER TABLE `visitor__otps`
  ADD CONSTRAINT `visitor__otps_visitor_id_foreign` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visits`
--
ALTER TABLE `visits`
  ADD CONSTRAINT `visits_meeting_user_id_foreign` FOREIGN KEY (`meeting_user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `visits_visit_type_id_foreign` FOREIGN KEY (`visit_type_id`) REFERENCES `visit_types` (`id`),
  ADD CONSTRAINT `visits_visitor_id_foreign` FOREIGN KEY (`visitor_id`) REFERENCES `visitors` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `visit_logs`
--
ALTER TABLE `visit_logs`
  ADD CONSTRAINT `visit_logs_rfid_id_foreign` FOREIGN KEY (`rfid_id`) REFERENCES `rfids` (`id`),
  ADD CONSTRAINT `visit_logs_visit_id_foreign` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
