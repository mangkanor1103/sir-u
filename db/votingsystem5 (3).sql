-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2025 at 10:38 AM
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
  `partylist_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `candidates`
--

INSERT INTO `candidates` (`id`, `election_id`, `position_id`, `firstname`, `lastname`, `photo`, `platform`, `partylist_id`) VALUES
(33, 18, 5, '3y43yy', '3y43y3', 'uploads/67ca5259514bb.png', 'sqwdqwq', 8),
(35, 25, 7, 'Kian', 'Rodriguez ', 'uploads/67d6254eb8aac.jpeg', 'Ieuruhdj', 10),
(36, 25, 7, 'Pangi', '2', 'uploads/67d6256f9d55d.jpg', 'Iridjdjd', 10),
(37, 25, 8, 'Pangit', '3', 'uploads/67d62591cbe70.jpg', 'Pandyfusisi', 10),
(38, 25, 8, 'Pangit', '4', 'uploads/67d625b2650d6.jpg', 'Idjdjdjjs', 10),
(39, 25, 9, 'Idkdkdid', 'Tifijd', 'uploads/67d625d0c30e1.jpg', 'Ieidjd', 10),
(40, 25, 9, 'I3irjej', 'Rjjrjej', 'uploads/67d625e4f2add.jpg', 'Eiidjsjs', 10),
(41, 25, 10, 'Iridirid', 'Krkrkrk', 'uploads/67d625fbce025.jpeg', 'Krkrk', 10),
(42, 25, 10, '2', 'Ididdiis', 'uploads/67d62624ea217.jpg', 'Ridjdjjd', 10),
(43, 25, 10, '3', 'Kdkrkds', 'uploads/67d6263bd120e.jpg', 'Iriidr', 10),
(44, 25, 10, '4', 'Rodriguez ', 'uploads/67d626552f422.jpg', 'Fjdje', 10),
(45, 25, 10, '5', 'Rodriguez ', 'uploads/67d6266a152dd.jpg', 'Rikrkeke', 10),
(46, 25, 10, '6', 'Rodriguez ', 'uploads/67d6267dbe9fe.jpg', 'Jejeje', 10),
(47, 25, 10, '7', 'Rodriguez ', 'uploads/67d62690280c5.jpg', 'Ieieiejej', 10),
(48, 25, 10, '8', 'Ieissiis', 'uploads/67d626aabc58e.jpg', 'Idkdkd', 10),
(49, 25, 10, '9', 'Iririrr', 'uploads/67d626bec5118.jpeg', 'Fkjdjs', 10),
(50, 25, 10, '10', 'Rodriguez ', 'uploads/67d626d7bd9ef.jpeg', 'Ejjeje', 10),
(51, 26, 11, 'aa', 'aa', 'uploads/67d8b7e0d57ce.jpg', 'yy', 11),
(52, 26, 12, 'Alpha', 'Valdez', 'uploads/67df6d6335253.jpg', 'ww', 11),
(53, 29, 13, 'Kian', 'Rodriguez ', 'uploads/67eddbb9e8ec7.jpg', 'Kejdjdjd', 12),
(62, 38, 0, '', '', 'uploads/67f0de8362517.png', '0', NULL),
(63, 38, 18, 'Rodriguez', 'Kian A.', 'uploads/67f0df6914457.png', 'fwefwf', 17);

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
(5, 'BSIT', '3F1');

-- --------------------------------------------------------

--
-- Table structure for table `elections`
--

CREATE TABLE `elections` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `election_code` varchar(60) NOT NULL,
  `status` tinyint(1) DEFAULT 0,
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `elections`
--

INSERT INTO `elections` (`id`, `name`, `election_code`, `status`, `end_time`) VALUES
(18, 'its', 'QKJ4tE7aR9', 1, NULL),
(19, 'itsa', 'mJx2qrt1Hi', 1, NULL),
(22, 'ITS ', '8MTfn1iYbH', 1, NULL),
(23, 'USG', 'n9frUgkVAH', 1, NULL),
(24, 'ite2fwefwdvss', 'psyG46hMRc', 1, NULL),
(25, 'Usg', 'vWQbMjJkuS', 1, NULL),
(26, 'Hahaha', 'vX76qzRUwI', 1, NULL),
(27, 'it2', 'RuXYn8eyEk', 1, NULL),
(29, 'Crim', 'dFy94QmRCS', 1, NULL),
(34, 'USG ELECTION 2025', 'BikA4bpTos', 1, '2025-04-05 10:02:30'),
(37, 'fsgwsg', '8sSZOTPq5r', 0, NULL),
(38, 'USG Election 2025', 'BxofsLFJbQ', 0, NULL);

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
(9, 18, 'affa', '2025-03-07 02:37:13'),
(11, 25, 'Jgggg', '2025-03-16 01:20:57');

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
(6, 'it', '2025-03-07 10:37:31', NULL, '191|RTRQI92NZ;192|RTR43RN6D;193|RTRITFT36;194|RTR67JGE9;195|RTRE51EAL;196|RTRR4U8FR;197|RTRGGUYCM;198|RTRTQWK0M;199|RTR84MJLW;200|RTR9VO60G', NULL, NULL, '6|Republic Corp.;7|Independent'),
(7, 'it', '2025-03-10 05:17:52', '34|6|Rodriguez|Kian, Anthony|uploads/67caeac97f0f3.jpg|rt343t|9', '211|RTR6IPI3D;212|RTR6BC3RD;213|RTRFJGH4N;214|RTRKT7M73;215|RTRYNJMYF;216|RTRTVZP6B;217|RTR79CJWU;218|RTRJHZSJE;219|RTRJXWH3X;220|RTRV5EC8G', '101|220|34|6|2025-03-07 12:48:19', '6|pres|1', '9|Republic Corp.'),
(8, 'ite2fwefw', '2025-03-13 12:15:52', NULL, '221|BSIT3F1-Y3WWMOA;222|BSIT3F1-RQCTWCO;223|BSIT3F1-AKPCDPL;224|BSIT3F1-T34S60K;225|BSIT3F1-4S197Y4;226|BSIT3F1-MH74OU9;227|BSIT3F1-0AVJQZO;228|BSIT3F1-4CGJTXG;229|BSIT3F1-72FN1MC;230|BSIT3F1-RW9M9JG', NULL, NULL, NULL),
(9, 'USG ELECTION 2025', '2025-04-04 07:58:02', '54|14|Uriel|Melendres|uploads/67ef8c03b0ecb.jpg|EDFSDFDS|13;55|14|XYZ|ABC|uploads/67ef8cab99897.png|DFDSD|14;56|15|ERYTT|DFSDSD|uploads/67ef8d5734ea2.png|FSFS|13;57|15|TYTYU|GHDFHD|uploads/67ef8d89df8cf.png|SFSDFS|14;58|16|FSDFSD|SFDSD|uploads/67ef8db2113ea.jpg|FDSFS|14;59|16|DFGF|FDGD|uploads/67ef8ebd3e38e.png|ERERWE|13', '312|USG25MVC7;313|USG25JI37;314|USG25Z5D5;315|USG25F3IB;316|USG25F3VX;317|USG2584LB;318|USG25OKO3;319|USG254L3O;320|USG25TDXN;321|USG258N0H;322|USG254JH5;323|USG256TQ0;324|USG25KPWF;325|USG25MTHD;326|USG25NF3Y;327|USG25EAI7;328|USG25XN38;329|USG25ST9O;330|USG25IT8I;331|USG25VUAU;332|USG25JY30;333|USG25OOK6;334|USG25UVW4;335|USG25EERW;336|USG25IOKN;337|USG25JD17;338|USG25PMMD;339|USG25XRF3;340|USG25GPBR;341|USG25HO0P;342|USG25A4CN;343|USG255USK;344|USG254XJ1;345|USG25YB33;346|USG255VM2;347|USG2581A5;348|USG252KCD;349|USG250GY1;350|USG253IBN;351|USG25ASJK;352|USG25VMGK;353|USG25CSE9;354|USG257XFX;355|USG256DRY;356|USG25OR4N;357|USG25EJME;358|USG258XG6;359|USG255NV3;360|USG251SWU;361|USG25VF8F;362|USG256MC8;363|USG25CNT6;364|USG25ON8P;365|USG25V210;366|USG258OHD;367|USG25FFIC;368|USG25WRJT;369|USG259TXU;370|USG25ZWOQ;371|USG258EHQ;372|USG25WD0D;373|USG25L5MW;374|USG258KK8;375|USG25Z72Y;376|USG25K0YT;377|USG25L3NA;378|USG25KDW7;379|USG25J6TE;380|USG25A1H5;381|USG25NH2H;382|USG25WZH6;383|USG255REC;384|USG25AW8S;385|USG25PTZ9;386|USG25255K;387|USG25TLD3;388|USG25C50W;389|USG25248H;390|USG25BOZQ;391|USG25J7G7;392|USG25P1I1;393|USG25IWRC;394|USG2512PF;395|USG25007E;396|USG253XOE;397|USG25EK17;398|USG25OTFA;399|USG25WZQN;400|USG25O1G6;401|USG25AYKT;402|USG25MZQZ;403|USG25VWPJ;404|USG25MQ46;405|USG25C390;406|USG258IRG;407|USG25NSTS;408|USG25NVFG;409|USG25WD0E;410|USG25IRY1;411|USG25F3HA', '114|403|54|14|2025-04-04 07:53:23;115|403|57|15|2025-04-04 07:53:23;116|403|58|16|2025-04-04 07:53:23;117|410|55|14|2025-04-04 07:55:02;119|410|58|16|2025-04-04 07:55:02;120|402|54|14|2025-04-04 07:55:45;121|402|57|15|2025-04-04 07:55:45;122|402|58|16|2025-04-04 07:55:45', '14|PRESIDENT|1;15|V. PRESIDENT|1;16|SENATORS|8', '13|LAKAS;14|LIBERAL'),
(10, 'usg election', '2025-04-05 14:32:35', '60|17|asas|kian1103|uploads/67f0c08d33543.png|sdfddndfn|15;61|17|votingsystem5|Kian A.|uploads/67f0c114f2c18.png|fwfwfwfwf|15', NULL, NULL, '17|treasurer|1', '15|nuketown'),
(11, 'USG ELECTION 2025', '2025-04-05 14:34:10', NULL, NULL, NULL, NULL, NULL),
(12, 'USG ELECTION 2025', '2025-04-05 14:38:56', NULL, NULL, NULL, NULL, NULL),
(13, 'USG ELECTION 2025', '2025-04-05 14:46:56', NULL, NULL, NULL, NULL, NULL),
(14, 'USG ELECTION 2025', '2025-04-05 14:57:39', NULL, NULL, NULL, NULL, NULL),
(15, 'USG ELECTION 2025', '2025-04-05 14:58:49', NULL, NULL, NULL, NULL, NULL);

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
(8, 18, 'Republic Corp.'),
(10, 25, 'Pangit party list '),
(11, 26, 'MATATAG'),
(12, 29, 'Bbm'),
(17, 38, 'gfsgrsgsfwefw');

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
(5, 18, 'vpres', 1),
(7, 25, 'president', 1),
(8, 25, 'vice president', 1),
(9, 25, 'secretary', 1),
(10, 25, 'Senator ', 8),
(12, 26, 'President', 1),
(13, 29, 'president', 1),
(18, 38, 'treasurerer', 1);

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
  `photo` varchar(255) DEFAULT NULL,
  `election_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `voters_id`, `name`, `year_section`, `course`, `photo`, `election_id`) VALUES
(16, 281, 'Rea Rodriguez', '4f1', 'it', 'pics/students/17420879706697898274083496114994.jpg', 25),
(17, 302, 'Alpha', '3F1', 'BSIT', 'pics/students/17437421920748928579657440557269.jpg', 29);

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
(1, 38, 'BSIT348XIBU'),
(2, 38, 'BSIT3CKOMAP'),
(3, 38, 'BSIT32PL17Y'),
(4, 38, 'BSIT3YN0Y92'),
(5, 38, 'BSIT3WEKSLP'),
(6, 38, 'BSIT3FUQCGV'),
(7, 38, 'BSIT3KGIGKH'),
(8, 38, 'BSIT3KRNJEV'),
(9, 38, 'BSIT3N0MDQ7'),
(10, 38, 'BSIT3JD8ADF'),
(11, 38, 'THRHRPF3JXG'),
(12, 38, 'THRHRL7JRF1'),
(13, 38, 'THRHRTVMO9V'),
(14, 38, 'THRHRAFWCHL'),
(15, 38, 'THRHRL2BUKQ'),
(16, 38, 'THRHR8CX85B'),
(17, 38, 'THRHR3IIZMI'),
(18, 38, 'THRHR7KKMN6'),
(19, 38, 'THRHR7UYMH9'),
(20, 38, 'THRHR48ZX5C'),
(21, 38, 'THRHREJI3PY'),
(22, 38, 'THRHR2O4AO8'),
(23, 38, 'THRHR9YCKJX'),
(24, 38, 'THRHRMOSF9X'),
(25, 38, 'THRHREG6LLX'),
(26, 38, 'THRHRHTD3YI'),
(27, 38, 'THRHRRYO4Z2'),
(28, 38, 'THRHRY4QZGF'),
(29, 38, 'THRHR78HVK9'),
(30, 38, 'THRHRWI4N25');

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
(102, 25, 281, 35, 7, '2025-03-16 01:20:52'),
(103, 25, 281, 37, 8, '2025-03-16 01:20:52'),
(104, 25, 281, NULL, 9, '2025-03-16 01:20:52'),
(105, 25, 281, 41, 10, '2025-03-16 01:20:52'),
(106, 25, 281, 42, 10, '2025-03-16 01:20:52'),
(107, 25, 281, 43, 10, '2025-03-16 01:20:52'),
(108, 25, 281, 44, 10, '2025-03-16 01:20:52'),
(109, 25, 281, 45, 10, '2025-03-16 01:20:52'),
(110, 25, 281, 46, 10, '2025-03-16 01:20:52'),
(111, 25, 281, 47, 10, '2025-03-16 01:20:52'),
(112, 25, 281, 48, 10, '2025-03-16 01:20:52'),
(113, 29, 302, NULL, 13, '2025-04-04 04:51:07');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=64;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `elections`
--
ALTER TABLE `elections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

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
  MODIFY `partylist_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `positions`
--
ALTER TABLE `positions`
  MODIFY `position_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `voters`
--
ALTER TABLE `voters`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `votes`
--
ALTER TABLE `votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

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
