-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2025 at 04:23 AM
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
-- Database: `votingsystem5`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(60) NOT NULL,
  `firstname` varchar(50) NOT NULL,
  `lastname` varchar(50) NOT NULL,
  `photo` varchar(150) NOT NULL,
  `created_on` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `username`, `password`, `firstname`, `lastname`, `photo`, `created_on`) VALUES
(1, 'admin', '$2y$10$lQf6BzNGLpXmKLqlR0aulOBwCBthltGIMoGGIb5ro3HeHm8gpl8XC', 'kian', 'Rodriguez', '440969875_1482253359391132_4061404540813449474_n.jpg', '2024-04-11');

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
-- Table structure for table `candidates`
--

CREATE TABLE `candidates` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `firstname` varchar(30) NOT NULL,
  `lastname` varchar(30) NOT NULL,
  `photo` varchar(150) NOT NULL,
  `platform` text NOT NULL,
  `partylist_id` int(11) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year_section` varchar(50) DEFAULT NULL,
  `age` int(11) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `info_enabled` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `election_id`, `position_id`, `firstname`, `lastname`, `photo`, `platform`, `partylist_id`, `course`, `year_section`, `age`, `sex`, `address`, `info_enabled`) VALUES
(33, 18, 5, '3y43yy', '3y43y3', 'uploads/67ca5259514bb.png', 'sqwdqwq', 8, 'it', '4f1', 18, 'Male', '231214112efw', 1);

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `course` varchar(100) NOT NULL,
  `year_section` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `course`, `year_section`) VALUES
(4, 'it', '4f1');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `election_code` varchar(60) NOT NULL,
  `status` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`id`, `name`, `election_code`, `status`) VALUES
(18, 'its', 'QKJ4tE7aR9', 1),
(19, 'itsa', 'mJx2qrt1Hi', 0);

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
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `election_id`, `feedback`, `created_at`) VALUES
(5, 18, 'wffafwa', '2025-03-07 02:08:32'),
(6, 18, 'dafaf', '2025-03-07 02:18:22'),
(7, 18, 'fwefw', '2025-03-07 02:23:55'),
(8, 18, 'wgwgwegwg', '2025-03-07 02:29:06'),
(9, 18, 'affa', '2025-03-07 02:37:13');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `election_title` varchar(255) NOT NULL,
  `deleted_at` datetime NOT NULL,
  `candidates` text DEFAULT NULL,
  `voters` text DEFAULT NULL,
  `votes` text DEFAULT NULL,
  `positions` text DEFAULT NULL,
  `partylists` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `election_title`, `deleted_at`, `candidates`, `voters`, `votes`, `positions`, `partylists`) VALUES
(1, 'usg election', '2025-03-05 20:37:34', '28|1|ad|asd|uploads/553838_Hollow Block picture.jpg|wfwwgw|2', '89|s72cwf9U7e;90|dp0Xn5aW3T;91|tCh7HtFfwf;92|4kWdFU8sRN;93|CCWUtuRGGO;94|aqSZlGQuzg;95|owP1VdX8s4;96|Oa97gF47YM;97|p5F1nNk1WF;98|516z4gl8VB;99|tEuoKidF1q;100|JlIiW0IqGk;101|UvnjPSRkyi;102|5otq4bWsmY;103|OZOYI8CyDq;104|pcteMPDJwS;105|48FMtyfsSU;106|y2RLS8g081;107|7nxuw3K8hB;108|DtCLgiLLBZ;109|kbEaaVuFbq;110|RBtDE34sJt;111|6Xd9zM0kD5;112|rNgoCwQfKP;113|JJla2BNGJ4;114|OnzgGxn1Po;115|ouLpXR7wds;116|ijSq3j4grB;117|U0L0zxcGou;118|HbGxM8rStG;119|F8m0oZostk;120|KGy5THJEM3;121|1lRO1Vqi1Y;122|FQmwThED4X;123|nk155DDyLq;124|YXXwF0weUF;125|Rz4z0E1iex;126|Iahd6b9Ygj;127|rbiLdEhpae;128|Ny861fNCbE;129|TEvSDqw5S9;130|oxtlyV8F97;131|6vn9B0huZt;132|hY6gDuIgR7;133|h6X9tlbqyO;134|md9cse88eq;135|dr78SYNchN;136|3yU81BFhr7;137|5yopbkPVuM;138|bZ1RByLKYw;139|YtrxQq6ZIL;140|PP8cM0bPi3;141|ba0540DnGg;142|qF4ynuUgfy;143|KaDgUDgSEc;144|rTbWoqmzLm;145|K0xASt1QvN;146|AwwDoiNyLo;147|ZrZplKnTcK;148|yr4oEJ0ANM;149|VxKcrzmlpw;150|ue1qIuPXLV;151|0tW4Fn5uTF;152|e63gbKxy0w;153|KRqlq6wwUw;154|z9QuNztfpA;155|iA99i10I81', '82|89|24|12|2025-03-02 18:43:25;83|90|25|1|2025-03-04 22:38:33;84|90|26|1|2025-03-04 22:38:33;85|91|25|1|2025-03-04 22:39:45;86|91|26|1|2025-03-04 22:39:45;87|92|25|1|2025-03-04 23:00:20;88|93|25|1|2025-03-04 23:04:55;90|94|25|1|2025-03-04 23:07:50;91|95|25|1|2025-03-04 23:16:35', '1|pres|1', '2|Republic Corp.'),
(2, 'usg election', '2025-03-07 07:57:10', '29|2|ad|asd|uploads/4c1eb3fb-bf37-40f2-a495-f1d061784156.jpg|2r2r2|3', '156|sjtgYnNvX4;157|nyINhVQUM8;158|hvRBVSkn3Z;159|1jTEeGNjeB;160|C4TunqRZQ0;161|RTRFQFIMBXE8R;162|RTRJ9YM3H4LJB;163|RTRSWJ652Y9BY;164|RTR25J0S1NHND;165|RTRYLMJ6DKEO9;166|RTRD66BOI1GCI;167|RTR5A8JIDE2XE;168|RTRGCTNCUARA9;169|RTR3IHF68NNMA;170|RTRW7ASLNOQRU', '93|157|29|2|2025-03-05 20:48:09', '2|pres|1', '3|Republic Corp.'),
(3, 'usg election', '2025-03-07 07:57:25', '30|3|eyey|erheh|uploads/rsz_republic_cement_2.jpg|eryeye|4;31|3|4y54y4|y4y45y|uploads/cheat.jpg|4y4y4|4;32|4|3y43yy|3y43y3|uploads/power_tools.jpg|3y3y43|4', '181|RTRTO8Y8H;182|RTR3K0QLL;183|RTRZYZUZ2;184|RTRPVPHPU;185|RTRB0L3HH;186|RTRZ6NZJN;187|RTRX5UQ39;188|RTRTH9YMN;189|RTR6AELCW;190|RTR2WNICS', '94|190|30|3|2025-03-07 07:56:46;95|190|32|4|2025-03-07 07:56:46', '3|pres|1;4|vpres|1', '4|Kian A. Rodriguez'),
(4, 'usg election', '2025-03-07 08:02:04', NULL, '171|RTRA0BRZ4;172|RTRRD18KN;173|RTRHX7OB8;174|RTR7NBU1V;175|RTRZ7UQ4E;176|RTR3S58DH;177|RTRTF62SE;178|RTR1WRAA7;179|RTR00NTR4;180|RTR2F6W55', NULL, NULL, NULL),
(5, 'usg election', '2025-03-07 09:41:29', NULL, NULL, NULL, NULL, '5|Republic Corp.'),
(6, 'it', '2025-03-07 10:37:31', NULL, '191|RTRQI92NZ;192|RTR43RN6D;193|RTRITFT36;194|RTR67JGE9;195|RTRE51EAL;196|RTRR4U8FR;197|RTRGGUYCM;198|RTRTQWK0M;199|RTR84MJLW;200|RTR9VO60G', NULL, NULL, '6|Republic Corp.;7|Independent');

-- --------------------------------------------------------

--
-- Table structure for table `history_candidates`
--

CREATE TABLE `history_candidates` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `position_id` int(11) NOT NULL,
  `votes` int(11) DEFAULT 0,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_candidates`
--

INSERT INTO `history_candidates` (`id`, `election_id`, `name`, `position_id`, `votes`, `deleted_at`) VALUES
(1, 1, '', 4, 0, '2025-03-02 09:49:14');

-- --------------------------------------------------------

--
-- Table structure for table `history_elections`
--

CREATE TABLE `history_elections` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `election_code` varchar(50) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_elections`
--

INSERT INTO `history_elections` (`id`, `name`, `election_code`, `deleted_at`) VALUES
(1, 'General Election 2024', '$2y$10$samplehashedpassword', '2025-03-02 09:49:14'),
(2, 'General Election 2024', '$2y$10$samplehashedpassword', '2025-03-02 09:50:27');

-- --------------------------------------------------------

--
-- Table structure for table `history_positions`
--

CREATE TABLE `history_positions` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `position_name` varchar(255) NOT NULL,
  `max_vote` int(11) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_positions`
--

INSERT INTO `history_positions` (`id`, `election_id`, `position_name`, `max_vote`, `deleted_at`) VALUES
(1, 1, '', 1, '2025-03-02 09:49:14'),
(2, 1, '', 1, '2025-03-02 09:49:14'),
(3, 1, '', 1, '2025-03-02 09:49:14'),
(4, 1, '', 1, '2025-03-02 09:50:27'),
(5, 1, '', 1, '2025-03-02 09:50:27'),
(6, 1, '', 1, '2025-03-02 09:50:28');

-- --------------------------------------------------------

--
-- Table structure for table `history_votes`
--

CREATE TABLE `history_votes` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `voter_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history_votes`
--

INSERT INTO `history_votes` (`id`, `election_id`, `voter_id`, `candidate_id`, `deleted_at`) VALUES
(1, 1, 0, 21, '2025-03-02 09:49:14'),
(2, 1, 0, 21, '2025-03-02 09:49:14'),
(3, 1, 0, 21, '2025-03-02 09:49:14'),
(4, 1, 0, 21, '2025-03-02 09:49:14'),
(5, 1, 0, 21, '2025-03-02 09:49:14'),
(6, 1, 0, 21, '2025-03-02 09:49:14');

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
(3, '0001_01_01_000002_create_jobs_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `partylists`
--

CREATE TABLE `partylists` (
  `partylist_id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `partylists`
--

INSERT INTO `partylists` (`partylist_id`, `election_id`, `name`) VALUES
(8, 18, 'Republic Corp.');

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
-- Table structure for table `positions`
--

CREATE TABLE `positions` (
  `position_id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `max_vote` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `election_id`, `description`, `max_vote`) VALUES
(5, 18, 'vpres', 1);

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

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id` int(11) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `department` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id`, `student_id`, `name`, `department`) VALUES
(1, '1', 'q', 'q');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `voters_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `year_section` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `election_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `voters_id`, `name`, `year_section`, `course`, `election_id`) VALUES
(7, 210, 'Kian A. Rodriguez', '4f1', 'it', 18),
(9, 209, 'Kian A. Rodriguez1', '4f1', 'it', 18),
(10, 208, 'Kian A. Rodriguez11', '4f1', 'it', 18),
(11, 207, 'Kian A. Rodriguez111', '4f1', 'it', 18),
(12, 206, 'Kian A. Rodriguez1111', '4f1', 'it', 18);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `voters`
--

CREATE TABLE `voters` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `voters_id` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `voters`
--

INSERT INTO `voters` (`id`, `election_id`, `voters_id`) VALUES
(201, 18, 'ITSKOD3CF'),
(202, 18, 'ITSVXMIM8'),
(203, 18, 'ITSGDBK4X'),
(204, 18, 'ITS16917C'),
(205, 18, 'ITSKSBH1P'),
(206, 18, 'ITSC7PITS'),
(207, 18, 'ITS7SBL64'),
(208, 18, 'ITSJZL0H0'),
(209, 18, 'ITSL7Y2Q6'),
(210, 18, 'ITS2KHCFJ');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `election_id` int(11) DEFAULT NULL,
  `voters_id` int(11) DEFAULT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `position_id` int(11) DEFAULT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `election_id`, `voters_id`, `candidate_id`, `position_id`, `timestamp`) VALUES
(96, 18, 210, 33, 5, '2025-03-07 10:08:26'),
(97, 18, 209, 33, 5, '2025-03-07 10:18:19'),
(98, 18, 208, 33, 5, '2025-03-07 10:23:35'),
(99, 18, 207, 33, 5, '2025-03-07 10:29:02'),
(100, 18, 206, 33, 5, '2025-03-07 10:36:57');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `candidates`
--
ALTER TABLE `candidates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`),
  ADD KEY `position_id` (`position_id`),
  ADD KEY `partylist_id` (`partylist_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `elections`
--
ALTER TABLE `elections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history_candidates`
--
ALTER TABLE `history_candidates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history_elections`
--
ALTER TABLE `history_elections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history_positions`
--
ALTER TABLE `history_positions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history_votes`
--
ALTER TABLE `history_votes`
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
-- Indexes for table `partylists`
--
ALTER TABLE `partylists`
  ADD PRIMARY KEY (`partylist_id`),
  ADD KEY `election_id` (`election_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `positions`
--
ALTER TABLE `positions`
  ADD PRIMARY KEY (`position_id`),
  ADD KEY `election_id` (`election_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD KEY `voters_id` (`voters_id`),
  ADD KEY `election_id` (`election_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `voters`
--
ALTER TABLE `voters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`);

--
-- Indexes for table `votes`
--
ALTER TABLE `votes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`),
  ADD KEY `voters_id` (`voters_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `position_id` (`position_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `history_candidates`
--
ALTER TABLE `history_candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `history_elections`
--
ALTER TABLE `history_elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `history_positions`
--
ALTER TABLE `history_positions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `history_votes`
--
ALTER TABLE `history_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `partylists`
--
ALTER TABLE `partylists`
  MODIFY `partylist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=211;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`),
  ADD CONSTRAINT `candidates_ibfk_3` FOREIGN KEY (`partylist_id`) REFERENCES `partylists` (`partylist_id`) ON DELETE SET NULL;

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `partylists`
--
ALTER TABLE `partylists`
  ADD CONSTRAINT `partylists_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`voters_id`) REFERENCES `voters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `voters`
--
ALTER TABLE `voters`
  ADD CONSTRAINT `voters_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`);

--
-- Constraints for table `votes`
--
ALTER TABLE `votes`
  ADD CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`voters_id`) REFERENCES `voters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`candidate_id`) REFERENCES `candidates` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `votes_ibfk_4` FOREIGN KEY (`position_id`) REFERENCES `positions` (`position_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
