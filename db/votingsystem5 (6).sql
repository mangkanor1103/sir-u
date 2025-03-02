-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2025 at 02:19 PM
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
(1, 1, 'admin', '$2y$10$lQf6BzNGLpXmKLqlR0aulOBwCBthltGIMoGGIb5ro3HeHm8gpl8XC', 'kian', 'Rodriguez', '440969875_1482253359391132_4061404540813449474_n.jpg', '2024-04-11');

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
  `platform` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `election_id`, `position_id`, `firstname`, `lastname`, `photo`, `platform`) VALUES
(11, 1, 4, 'asfaf', 'afffafaf', 'uploads/rs logo.jpg', 'faf'),
(13, 1, 3, 'asfaf', 'afffafaf', 'uploads/OIP.jfif', 'htjrykjtfkytfk'),
(14, 1, 6, 'rerhehe', 'rhdhdhs', 'uploads/OIP.jfif', 'wfafafa'),
(15, 1, 3, 'kian', 'reeeyeye', 'uploads/OIP.jfif', 'hdjd'),
(16, 1, 3, 'sfwetwtw', 'ryteyey', 'uploads/458804344_1108143657387022_7368374457459692655_n.jpg', 'ryeyey'),
(17, 4, 7, 'Kian', 'A. Rodriguez', 'uploads/Your paragraph text.jpg', 'fwefwffwf'),
(18, 3, 9, 'Rodriguez', 'Kian, Anthony', 'uploads/Your paragraph text.jpg', 'hj,hk,hk,'),
(19, 3, 9, 'ewfgw', 'rgttegre', 'uploads/rs bg.jpg', 'wefwf'),
(20, 3, 9, 'Rodriguez', 'Kian, Anthony', 'uploads/468220162_1772873700136936_5029136730132378084_n.jpg', 'g');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `election_code` varchar(60) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`id`, `name`, `election_code`) VALUES
(1, 'General Election 2024', '$2y$10$samplehashedpassword'),
(2, 'Student Council Election 2024', '$2y$10$samplehashedpassword2'),
(3, 'usg election', '7xbKFkUZTM'),
(4, 'it', 'rFMZ8CKtgN');

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
  `voter_id` int(11) DEFAULT NULL,
  `election_id` int(11) NOT NULL,
  `feedback` text NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
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
  `description` varchar(50) NOT NULL,
  `max_vote` int(11) NOT NULL,
  `priority` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `positions`
--

INSERT INTO `positions` (`position_id`, `election_id`, `description`, `max_vote`, `priority`) VALUES
(3, 1, 'vice president', 1, 2),
(4, 1, 'president', 1, 1),
(6, 1, 'sec', 1, 1),
(7, 4, 'pres', 1, 100),
(9, 3, 'pres', 1, 100);

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
  `voters_id` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `year_section` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `voters_id`, `name`, `year_section`, `course`, `registered_at`) VALUES
(10, '2', 'Rodriguez, Kian, Anthony', 'gegeg', 'rgege', '2025-02-18 12:41:55'),
(11, '3', 'Rodriguez, Kian, Anthony', 'gegeg', 'rgege', '2025-02-18 12:43:57'),
(12, '13', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-02-23 09:21:33'),
(13, '10', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-02-23 09:47:14'),
(14, '14', 'Kian A. Rodriguez', 'bsit 3-1', 'it', '2025-02-23 09:53:39'),
(15, '15', 'Kian A. Rodriguez', 'bsit 3-1', 'it', '2025-02-23 09:55:31'),
(16, '16', 'Kian A. Rodriguez', 'bsit 3-1', 'it', '2025-02-23 10:02:42'),
(17, '17', 'Kian A. Rodriguez', 'bsit 3-1', 'it', '2025-02-23 10:21:56'),
(18, '5', 'Kian A. Rodriguez', 'bsit 3-1', 'it', '2025-02-26 11:59:12'),
(19, '18', 'Kian A. Rodriguez', 'bsit 3-1', 'it', '2025-02-26 12:04:14'),
(20, '6', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-02-26 12:15:33'),
(21, '4', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-02-26 12:24:47'),
(22, '19', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-02-26 12:25:31'),
(23, '20', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-02-26 12:26:25'),
(24, '21', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-02-26 12:27:29'),
(25, '22', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-03-01 11:30:54'),
(26, '23', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-03-01 11:34:20'),
(27, '24', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-03-01 11:36:22'),
(28, '25', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-03-01 11:37:27'),
(29, '26', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-03-01 11:38:19'),
(30, '27', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-03-01 11:48:11'),
(31, '28', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-03-01 11:51:30'),
(32, '29', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-03-01 11:52:06'),
(33, '30', 'Rodriguez, Kian, Anthony', 'bsit 3-1', 'it', '2025-03-01 11:56:39');

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
(10, 3, 'ZwsLyhfKbN'),
(13, 4, 'Xqzj5tKrFH'),
(14, 3, '4YuRARdYjg'),
(15, 3, '3kFWS4bPMT'),
(16, 3, 'aT7MNztB3v'),
(17, 3, 'szmdlbfuDh'),
(19, 3, 'o4adNYUkPy'),
(20, 3, 'W46Tjde6BE'),
(21, 3, 'aWGhiJ78FE'),
(28, 3, 'W6OTf5QT86');

-- --------------------------------------------------------

--
-- Table structure for table `votes`
--

CREATE TABLE `votes` (
  `id` int(11) NOT NULL,
  `election_id` int(11) NOT NULL,
  `voters_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `position_id` int(11) NOT NULL,
  `timestamp` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `votes`
--

INSERT INTO `votes` (`id`, `election_id`, `voters_id`, `candidate_id`, `position_id`, `timestamp`) VALUES
(31, 3, 10, 18, 9, '2025-02-23 17:51:39'),
(32, 3, 14, 18, 9, '2025-02-23 17:54:41'),
(34, 3, 15, 18, 9, '2025-02-23 18:01:54'),
(41, 3, 19, 18, 9, '2025-02-26 20:25:33'),
(42, 3, 20, 18, 9, '2025-02-26 20:26:28'),
(43, 3, 21, 18, 9, '2025-02-26 20:34:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD KEY `election_id` (`election_id`);

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
  ADD KEY `position_id` (`position_id`);

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
  ADD KEY `voter_id` (`voter_id`),
  ADD KEY `election_id` (`election_id`);

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
  ADD UNIQUE KEY `voters_id` (`voters_id`);

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
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidates`
--
ALTER TABLE `candidates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

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
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`);

--
-- Constraints for table `candidates`
--
ALTER TABLE `candidates`
  ADD CONSTRAINT `candidates_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`);

--
-- Constraints for table `feedback`
--
ALTER TABLE `feedback`
  ADD CONSTRAINT `feedback_ibfk_1` FOREIGN KEY (`voter_id`) REFERENCES `voters` (`id`),
  ADD CONSTRAINT `feedback_ibfk_2` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`);

--
-- Constraints for table `positions`
--
ALTER TABLE `positions`
  ADD CONSTRAINT `positions_ibfk_1` FOREIGN KEY (`election_id`) REFERENCES `elections` (`id`);

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
