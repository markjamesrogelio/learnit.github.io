-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 07, 2025 at 08:05 PM
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
-- Database: `learnit_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `archive`
--

CREATE TABLE `archive` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `reference_id` int(11) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `archived_at` datetime DEFAULT current_timestamp(),
  `date_archived` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `archive`
--

INSERT INTO `archive` (`id`, `type`, `reference_id`, `name`, `archived_at`, `date_archived`) VALUES
(1, 'department', 9, 'College of Mark James', '2025-05-30 12:58:29', '2025-05-30 13:28:24'),
(2, 'department', 10, 'College of Ian', '2025-05-30 12:59:19', '2025-05-30 13:28:24'),
(3, 'section', 1, 'BSN - 3A', '2025-05-30 13:21:08', '2025-05-30 13:28:24'),
(4, 'section', 6, 'BS in Information Technology - 3A', '2025-06-04 19:54:23', '2025-06-04 19:54:23'),
(5, 'section', 7, 'BS in Information Technology - 1A', '2025-06-04 19:55:46', '2025-06-04 19:55:46'),
(6, 'course', 26, 'BS in ML', '2025-06-05 15:20:45', '2025-06-05 15:20:45'),
(7, 'course', 27, 'Bachelor of Elementary Education', '2025-06-05 15:57:50', '2025-06-05 15:57:50');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `department_id` int(11) NOT NULL,
  `date_created` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `name`, `department_id`, `date_created`) VALUES
(1, 'BS in Accountancy', 1, '2025-05-30 11:48:54'),
(2, 'BS in Accounting Information System', 1, '2025-05-30 11:48:54'),
(3, 'BSBA - Financial Management', 1, '2025-05-30 11:48:54'),
(4, 'BSBA - HR Management', 1, '2025-05-30 11:48:54'),
(5, 'BSBA - Marketing Management', 1, '2025-05-30 11:48:54'),
(6, 'BSBA - Operations Management', 1, '2025-05-30 11:48:54'),
(7, 'BS in Hotel and Restaurant Management', 2, '2025-05-30 11:48:54'),
(8, 'BS in Tourism', 2, '2025-05-30 11:48:54'),
(9, 'Bachelor of Elementary Education', 3, '2025-05-30 11:48:54'),
(10, 'Bachelor of Secondary Education - Biology', 3, '2025-05-30 11:48:54'),
(11, 'Bachelor of Secondary Education - English', 3, '2025-05-30 11:48:54'),
(12, 'Bachelor of Secondary Education - Filipino', 3, '2025-05-30 11:48:54'),
(13, 'Bachelor of Secondary Education - Mathematics', 3, '2025-05-30 11:48:54'),
(14, 'BS in Computer Engineering', 4, '2025-05-30 11:48:54'),
(15, 'BS in Electrical Engineering', 4, '2025-05-30 11:48:54'),
(16, 'BS in Mechanical Engineering', 4, '2025-05-30 11:48:54'),
(17, 'BS in Computer Science', 5, '2025-05-30 11:48:54'),
(18, 'BS in Information Technology', 5, '2025-05-30 11:48:54'),
(19, 'BS in Marine Engineering', 6, '2025-05-30 11:48:54'),
(20, 'BS in Marine Transportation', 6, '2025-05-30 11:48:54'),
(21, 'BS in Nursing', 7, '2025-05-30 11:48:54'),
(22, 'BS in Midwifery', 7, '2025-05-30 11:48:54'),
(23, 'AB in Political Science', 8, '2025-05-30 11:48:54'),
(24, 'AB in Psychology', 8, '2025-05-30 11:48:54');

-- --------------------------------------------------------

--
-- Table structure for table `course_year_section_exam`
--

CREATE TABLE `course_year_section_exam` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `section_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_year_section_exam`
--

INSERT INTO `course_year_section_exam` (`id`, `exam_id`, `course`, `year_level`, `section_name`) VALUES
(10, 16, 'BS in Information Technology', '3rd', '3A'),
(11, 16, 'BS in Information Technology', '3rd', '3B'),
(12, 16, 'BS in Information Technology', '3rd', '3C'),
(13, 17, 'BS in Information Technology', '3rd', '3A'),
(14, 17, 'BS in Information Technology', '3rd', '3B'),
(15, 17, 'BS in Information Technology', '3rd', '3C'),
(16, 17, 'BS in Information Technology', '3rd', '3D'),
(17, 18, 'BS in Information Technology', '3rd', '3A'),
(18, 18, 'BS in Information Technology', '3rd', '3B'),
(19, 18, 'BS in Information Technology', '3rd', '3C'),
(20, 18, 'BS in Information Technology', '3rd', '3D'),
(26, 21, 'BS in Accountancy', '1st', '1A');

-- --------------------------------------------------------

--
-- Table structure for table `course_year_section_quiz`
--

CREATE TABLE `course_year_section_quiz` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year_level` varchar(50) NOT NULL,
  `section_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `course_year_section_quiz`
--

INSERT INTO `course_year_section_quiz` (`id`, `quiz_id`, `course`, `year_level`, `section_name`) VALUES
(1, 56, 'BS in Information Technology', '3rd', '3A'),
(2, 56, 'BS in Information Technology', '3rd', '3B'),
(3, 56, 'BS in Information Technology', '3rd', '3C'),
(15, 60, 'BS in Information Technology', '3rd', '3A'),
(16, 60, 'BS in Information Technology', '3rd', '3B'),
(17, 60, 'BS in Information Technology', '3rd', '3C'),
(18, 60, 'BS in Information Technology', '3rd', '3D'),
(41, 71, 'BS in Information Technology', '3rd', '3A'),
(42, 71, 'BS in Information Technology', '3rd', '3B'),
(43, 71, 'BS in Information Technology', '3rd', '3C'),
(44, 71, 'BS in Information Technology', '3rd', '3D');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'College of Business and Accountancy'),
(2, 'College of Tourism, Hospitality & Culinary Arts'),
(3, 'College of Education'),
(4, 'College of Engineering and Technology'),
(5, 'College of Computer Studies'),
(6, 'College of Maritime Education'),
(7, 'College of Health Sciences'),
(8, 'College of Arts and Sciences');

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Draft','Published') DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `course_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exams`
--

INSERT INTO `exams` (`id`, `teacher_id`, `title`, `description`, `status`, `created_at`, `course_id`, `section_id`) VALUES
(16, 70, 'EXAM PRO PRELIM', '1-20', 'Published', '2025-06-04 20:41:54', 0, 0),
(17, 74, 'ADT 313 PRELIM', '1-5 EXAM', 'Published', '2025-06-05 06:35:58', 0, 0),
(18, 74, 'SOE 303 PRELIM', '1-5', 'Published', '2025-06-05 06:39:37', 0, 0),
(21, 74, 'sdasda', 'sdad', 'Draft', '2025-07-03 14:39:12', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `exam_questions`
--

CREATE TABLE `exam_questions` (
  `id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('identification','multiple_choice','true_false') NOT NULL,
  `correct_answer` varchar(255) NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `question_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_questions`
--

INSERT INTO `exam_questions` (`id`, `exam_id`, `question_text`, `question_type`, `correct_answer`, `options`, `question_number`) VALUES
(11, 16, '1 + 1 =', 'identification', '2', NULL, 1),
(12, 16, 'is orange a fruit or color?', 'identification', 'fruit', NULL, 2),
(13, 16, 'do dogs have 4 limbs', 'identification', 'yes', NULL, 3),
(14, 16, 'chose color', 'multiple_choice', 'c. green', '[\"a. blue\",\"b. orange\",\"c. green\",\"d. grey\"]', 4),
(15, 16, 'am i handsome?', 'true_false', 'True', NULL, 5),
(16, 17, 'The SELECT statement is used to remove records from a table.', 'true_false', 'False', NULL, 1),
(17, 17, 'A FOREIGN KEY is used to ensure data integrity by linking two tables together.', 'identification', 'True', NULL, 2),
(18, 17, 'The AND operator is used in SQL to combine multiple conditions, and only rows that satisfy all conditions are included in the result.', 'true_false', 'True', NULL, 3),
(19, 17, 'The ORDER BY clause can only sort results in ascending order.', 'identification', 'False', NULL, 4),
(20, 17, 'SQL statements are case-insensitive, meaning SELECT and select are treated the same.', 'true_false', 'True', '[]', 5),
(21, 18, 'SQL statements are case-insensitive, meaning SELECT and select are treated the same.', 'true_false', 'True', NULL, 1),
(22, 18, 'The INSERT INTO statement can be used to add new records to an existing table.', 'true_false', 'True', '[]', 2),
(23, 18, 'You can create a table in SQL with the CREATE DATABASE statement.', 'true_false', 'False', '[]', 3),
(24, 18, 'The HAVING clause is used to filter groups based on aggregate functions, whereas WHERE filters individual rows.', 'true_false', 'True', '[]', 4),
(25, 18, 'SQL allows you to update multiple records in a single UPDATE statement.', 'true_false', 'True', '[]', 5);

-- --------------------------------------------------------

--
-- Table structure for table `explorer_items`
--

CREATE TABLE `explorer_items` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('folder','file') NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `path` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `availability` enum('Published','Hidden') DEFAULT 'Published',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `course_id` int(11) DEFAULT NULL,
  `section_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `explorer_items`
--

INSERT INTO `explorer_items` (`id`, `name`, `type`, `parent_id`, `path`, `created_at`, `updated_at`, `description`, `start_date`, `end_date`, `availability`, `deleted_at`, `is_deleted`, `course_id`, `section_id`) VALUES
(1, 'BSIT', 'folder', 0, '{\"color\":\"#87cefa\",\"description\":\"\",\"start_date\":\"2025-06-01\",\"end_date\":\"2025-06-02\",\"availability\":\"Published\"}', '2025-05-31 05:58:59', '2025-05-31 05:58:59', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(2, 'BSN', 'folder', 0, '{\"color\":\"#0da1fd\",\"description\":\"\",\"start_date\":\"2025-01-01\",\"end_date\":\"2025-01-01\",\"availability\":\"Published\"}', '2025-05-31 06:13:52', '2025-05-31 06:13:52', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(3, 'BSIT-1A', 'folder', 1, '{\"color\":\"#87cefa\",\"description\":\"\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-05-31 07:01:18', '2025-05-31 07:01:18', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(4, 'BSIT-1A', 'folder', 1, '{\"color\":\"#87cefa\",\"description\":\"\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-05-31 07:01:18', '2025-05-31 09:58:43', NULL, NULL, NULL, 'Published', '2025-05-31 09:58:43', 0, NULL, NULL),
(5, 'Subject', 'folder', 3, '{\"color\":\"#ffffff\",\"description\":\"\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-05-31 09:59:58', '2025-05-31 09:59:58', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(6, 'OPP', 'folder', 5, '{\"color\":\"#ffffff\",\"description\":\"Major Subject\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-05-31 10:00:42', '2025-05-31 10:00:42', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(9, 'OPP', 'folder', 0, '{\"color\":\"#ffffff\",\"description\":\"\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-05-31 16:29:35', '2025-05-31 16:29:35', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(10, 'opp', 'folder', 0, '{\"color\":\"#ffffff\",\"description\":\"\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-05-31 16:30:04', '2025-05-31 16:30:04', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(11, 'OPP', 'folder', 0, '{\"color\":\"#ffffff\",\"description\":\"\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-05-31 16:53:39', '2025-05-31 16:53:39', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(21, 'OOP', 'folder', 10, '{\"color\":\"#ffffff\",\"description\":\"\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-06-04 06:50:22', '2025-06-04 06:50:22', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(22, 'DSDS', 'folder', 21, '{\"color\":\"#ffffff\",\"description\":\"SDS\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-06-04 06:50:33', '2025-06-04 06:50:33', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(23, 'DSADS', 'folder', 22, '{\"color\":\"#ffffff\",\"description\":\"ASDAD\",\"start_date\":\"\",\"end_date\":\"\",\"availability\":\"Published\"}', '2025-06-04 06:50:45', '2025-06-04 06:50:45', NULL, NULL, NULL, 'Published', NULL, 0, NULL, NULL),
(25, 'OOP', 'folder', 0, NULL, '2025-06-04 11:53:51', '2025-06-04 11:53:51', NULL, NULL, NULL, 'Published', NULL, 0, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `module_id` int(11) NOT NULL,
  `module_name` varchar(255) NOT NULL,
  `module_folder` varchar(255) NOT NULL,
  `module_type` enum('pdf','pptx','docx','other') NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `folder_id` int(11) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`module_id`, `module_name`, `module_folder`, `module_type`, `user_id`, `file_path`, `folder_id`, `uploaded_at`) VALUES
(108, 'Llabore, Ian Angelo E. - Copy (1).jpg', 'uploads/modules/SIA_303', '', 74, 'uploads/modules/SIA_303/Llabore, Ian Angelo E. - Copy (1).jpg', 19, '2025-07-03 15:34:36'),
(109, 'SIA303-Case-Analysis_No.1 (1).pdf', 'uploads/modules/SIA_303', 'pdf', 74, 'uploads/modules/SIA_303/SIA303-Case-Analysis_No.1 (1).pdf', 19, '2025-07-03 15:34:36');

-- --------------------------------------------------------

--
-- Table structure for table `module_folders`
--

CREATE TABLE `module_folders` (
  `id` int(11) NOT NULL,
  `folder_name` varchar(255) NOT NULL,
  `course` varchar(255) NOT NULL,
  `year` varchar(255) NOT NULL,
  `sections` text NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `folder_path` varchar(255) NOT NULL,
  `folder_code` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_folders`
--

INSERT INTO `module_folders` (`id`, `folder_name`, `course`, `year`, `sections`, `user_id`, `folder_path`, `folder_code`) VALUES
(19, 'SIA 303', 'BS in Information Technology', '3rd Year', '3A,3B,3C,3D', 74, 'uploads/modules/SIA 303', 'FEZ-GDI-M5G'),
(20, 'NAC 303', 'BS in Information Technology', '3rd Year', '3A,3B,3C,3D', 74, 'uploads/modules/NAC 303', 'MDB-U3H-2PD'),
(21, 'SOE 303', 'BS in Information Technology', '3rd Year', '3A,3B,3C,3D', 74, 'uploads/modules/SOE 303', '6GD-56J-HTB'),
(22, 'AIA 303', 'BS in Information Technology', '3rd Year', '3A,3B,3C,3D', 74, 'uploads/modules/AIA 303', 'BTH-Q8N-76D'),
(23, 'FL 303', 'BS in Information Technology', '3rd Year', '3A,3B,3C,3D', 74, 'uploads/modules/FL 303', '6HE-FEX-HSA'),
(24, 'HCI 303', 'BS in Information Technology', '3rd Year', '3A,3B,3C,3D', 74, 'uploads/modules/HCI 303', 'R6I-EYU-KZ7');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `teacher_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('Draft','Published') DEFAULT 'Draft',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `course_id` int(11) NOT NULL,
  `section_id` int(11) NOT NULL,
  `year_level` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quizzes`
--

INSERT INTO `quizzes` (`id`, `teacher_id`, `title`, `description`, `status`, `created_at`, `updated_at`, `course_id`, `section_id`, `year_level`) VALUES
(56, 70, 'OOP QUIZ 2', '1-20', 'Published', '2025-06-04 17:31:20', '2025-06-04 17:31:20', 0, 0, NULL),
(60, 74, 'SOE QUIZ 1', '1-2 questions', 'Published', '2025-06-05 09:17:43', '2025-07-03 15:40:29', 0, 0, NULL),
(71, 74, 'AIA QUIZ 1', '1-20', 'Draft', '2025-07-03 15:41:10', '2025-07-03 15:41:10', 0, 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('identification','multiple_choice','true_false') NOT NULL,
  `options` text DEFAULT NULL,
  `correct_answer` text NOT NULL,
  `question_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `quiz_id`, `question_text`, `question_type`, `options`, `correct_answer`, `question_number`) VALUES
(196, 56, 'SDSDS', 'multiple_choice', '[\"A\",\"D\",\"D\",\"A\"]', 'A', 1),
(208, 60, '1 + 1', 'identification', '[]', '2', 1),
(209, 60, '1 - 2', 'multiple_choice', '[\"a. 1\",\"b. 4\",\"c. 6\",\"d. 0\"]', 'a. 2', 2),
(210, 60, '3 + 3', 'identification', NULL, '6', 3),
(226, 71, '1+1 = ?', 'multiple_choice', '[\"1\",\"2\",\"3\",\"4\"]', '2', 1),
(227, 71, 'IS YELLOW A COLOR?', 'true_false', NULL, 'TRUE', 2);

-- --------------------------------------------------------

--
-- Table structure for table `sections`
--

CREATE TABLE `sections` (
  `id` int(11) NOT NULL,
  `course` varchar(100) NOT NULL,
  `section_name` varchar(50) NOT NULL,
  `year_level` varchar(10) NOT NULL,
  `date_created` date NOT NULL DEFAULT curdate(),
  `course_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sections`
--

INSERT INTO `sections` (`id`, `course`, `section_name`, `year_level`, `date_created`, `course_id`) VALUES
(1, 'BS in Computer Science', '2A', '2nd', '2025-06-01', '1'),
(2, 'BS in Marine Engineering', '3A', '3rd', '2025-06-01', '1'),
(3, 'BS in Information Technology', '1B', '1st', '2025-06-01', '2'),
(4, 'BS in Computer Engineering', '4B', '4th', '2025-06-01', '2');

-- --------------------------------------------------------

--
-- Table structure for table `student_answers`
--

CREATE TABLE `student_answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `exam_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `correct_answer` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `quiz_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_answers`
--

INSERT INTO `student_answers` (`id`, `student_id`, `exam_id`, `question_id`, `answer`, `correct_answer`, `is_correct`, `submitted_at`, `quiz_id`) VALUES
(1, 69, 16, 11, '1', '2', 0, '2025-06-04 20:46:34', 0),
(2, 69, 16, 12, 'fruit', 'fruit', 0, '2025-06-04 20:46:34', 0),
(3, 69, 16, 13, 'yes', 'yes', 0, '2025-06-04 20:46:34', 0),
(4, 69, 16, 14, '2', 'c. green', 0, '2025-06-04 20:46:34', 0),
(5, 69, 16, 15, 'True', 'True', 0, '2025-06-04 20:46:34', 0),
(6, 69, 16, 11, '2', '2', 0, '2025-06-04 20:51:01', 0),
(7, 69, 16, 12, 'color', 'fruit', 0, '2025-06-04 20:51:01', 0),
(8, 69, 16, 13, 'no', 'yes', 0, '2025-06-04 20:51:01', 0),
(9, 69, 16, 14, '2', 'c. green', 0, '2025-06-04 20:51:01', 0),
(10, 69, 16, 15, 'True', 'True', 0, '2025-06-04 20:51:01', 0),
(11, 69, 16, 11, '3', '2', 0, '2025-06-04 20:54:27', 0),
(12, 69, 16, 12, 'no', 'fruit', 0, '2025-06-04 20:54:27', 0),
(13, 69, 16, 13, 'yes', 'yes', 0, '2025-06-04 20:54:27', 0),
(14, 69, 16, 14, '2', 'c. green', 0, '2025-06-04 20:54:27', 0),
(15, 69, 16, 15, 'True', 'True', 0, '2025-06-04 20:54:27', 0),
(16, 69, 16, 11, '3', '2', 0, '2025-06-04 20:57:18', 0),
(17, 69, 16, 12, 'no', 'fruit', 0, '2025-06-04 20:57:18', 0),
(18, 69, 16, 13, 'yes', 'yes', 0, '2025-06-04 20:57:18', 0),
(19, 69, 16, 14, '2', 'c. green', 0, '2025-06-04 20:57:18', 0),
(20, 69, 16, 15, 'True', 'True', 0, '2025-06-04 20:57:18', 0),
(21, 69, 16, 11, 's', '2', 0, '2025-06-04 20:57:38', 0),
(22, 69, 16, 12, 'sa', 'fruit', 0, '2025-06-04 20:57:38', 0),
(23, 69, 16, 13, 'a', 'yes', 0, '2025-06-04 20:57:38', 0),
(24, 69, 16, 14, '1', 'c. green', 0, '2025-06-04 20:57:38', 0),
(25, 69, 16, 15, 'True', 'True', 0, '2025-06-04 20:57:38', 0),
(26, 69, 16, 11, 'sd', '2', 0, '2025-06-04 21:44:12', 0),
(27, 69, 16, 12, 'd', 'fruit', 0, '2025-06-04 21:44:12', 0),
(28, 69, 16, 13, 'a', 'yes', 0, '2025-06-04 21:44:12', 0),
(29, 69, 16, 14, '2', 'c. green', 0, '2025-06-04 21:44:13', 0),
(30, 69, 16, 15, 'True', 'True', 0, '2025-06-04 21:44:13', 0),
(32, 69, 16, 11, '2', '2', 0, '2025-06-05 05:53:04', 0),
(33, 69, 16, 12, 'no', 'fruit', 0, '2025-06-05 05:53:04', 0),
(34, 69, 16, 13, 'yes', 'yes', 0, '2025-06-05 05:53:04', 0),
(35, 69, 16, 14, '2', 'c. green', 0, '2025-06-05 05:53:04', 0),
(36, 69, 16, 15, 'False', 'True', 0, '2025-06-05 05:53:04', 0),
(37, 69, 16, 11, '3', '2', 0, '2025-06-05 05:54:11', 0),
(38, 69, 16, 12, '3', 'fruit', 0, '2025-06-05 05:54:11', 0),
(39, 69, 16, 13, 'yes', 'yes', 0, '2025-06-05 05:54:11', 0),
(40, 69, 16, 14, '2', 'c. green', 0, '2025-06-05 05:54:11', 0),
(41, 69, 16, 15, 'True', 'True', 0, '2025-06-05 05:54:11', 0),
(42, 69, 16, 11, '2', '2', 0, '2025-07-03 17:41:34', 0),
(43, 69, 16, 12, 'YES', 'fruit', 0, '2025-07-03 17:41:34', 0),
(44, 69, 16, 13, 'yes', 'yes', 0, '2025-07-03 17:41:35', 0),
(45, 69, 16, 14, '3', 'c. green', 0, '2025-07-03 17:41:35', 0),
(46, 69, 16, 15, 'True', 'True', 0, '2025-07-03 17:41:35', 0),
(47, 69, 17, 16, 'False', 'False', 0, '2025-07-03 17:42:39', 0),
(48, 69, 17, 17, '', 'True', 0, '2025-07-03 17:42:39', 0),
(49, 69, 17, 18, 'False', 'True', 0, '2025-07-03 17:42:39', 0),
(50, 69, 17, 19, '', 'False', 0, '2025-07-03 17:42:39', 0),
(51, 69, 18, 21, 'False', 'True', 0, '2025-07-03 17:56:55', 0),
(52, 69, 18, 22, 'True', 'True', 0, '2025-07-03 17:56:55', 0),
(53, 69, 18, 23, 'True', 'False', 0, '2025-07-03 17:56:55', 0),
(54, 69, 18, 24, 'True', 'True', 0, '2025-07-03 17:56:55', 0),
(55, 69, 18, 25, 'True', 'True', 0, '2025-07-03 17:56:55', 0);

-- --------------------------------------------------------

--
-- Table structure for table `student_modules`
--

CREATE TABLE `student_modules` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `module_folder_id` int(11) NOT NULL,
  `date_added` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_modules`
--

INSERT INTO `student_modules` (`id`, `student_id`, `module_folder_id`, `date_added`) VALUES
(1, 69, 19, '2025-07-04 01:16:03'),
(2, 69, 20, '2025-07-04 01:16:21'),
(3, 69, 21, '2025-07-04 01:16:55'),
(4, 69, 24, '2025-07-04 01:49:25');

-- --------------------------------------------------------

--
-- Table structure for table `student_quiz_answers`
--

CREATE TABLE `student_quiz_answers` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `quiz_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text NOT NULL,
  `correct_answer` text NOT NULL,
  `is_correct` tinyint(1) DEFAULT 0,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_quiz_answers`
--

INSERT INTO `student_quiz_answers` (`id`, `student_id`, `quiz_id`, `question_id`, `answer`, `correct_answer`, `is_correct`, `submitted_at`) VALUES
(1, 69, 56, 196, '0', 'A', 0, '2025-06-05 02:12:31'),
(2, 69, 56, 196, '3', 'A', 0, '2025-06-05 02:12:40'),
(3, 69, 60, 208, '2', '2', 0, '2025-06-05 09:21:53'),
(4, 69, 60, 209, '0', 'a. 2', 0, '2025-06-05 09:21:53'),
(5, 69, 60, 210, '6', '6', 0, '2025-06-05 09:21:53'),
(6, 69, 56, 196, '0', 'A', 0, '2025-07-03 17:44:10'),
(7, 69, 60, 208, '2', '2', 0, '2025-07-03 17:58:55'),
(8, 69, 60, 209, '0', 'a. 2', 0, '2025-07-03 17:58:55'),
(9, 69, 60, 210, '6', '6', 0, '2025-07-03 17:58:55');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `department` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','teacher','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `first_name` varchar(100) DEFAULT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `suffix_name` varchar(20) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `id_number` varchar(50) DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL,
  `otp_code` varchar(10) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL,
  `department_id` int(11) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL,
  `otp_purpose` varchar(20) DEFAULT 'register'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `department`, `password`, `role`, `created_at`, `status`, `first_name`, `middle_name`, `last_name`, `suffix_name`, `course`, `section`, `sex`, `year`, `id_number`, `reset_token`, `reset_expiry`, `otp_code`, `otp_expiry`, `department_id`, `profile_pic`, `otp_purpose`) VALUES
(3, 'admin@learnit.com', NULL, '$2y$10$HlJ1UEY0SITpZo0TM9Qjuuy.zqeE1nbEi.3nYNlsB/5N/vuEJRhVS', 'admin', '2025-05-22 20:42:05', 'approved', 'admin', NULL, 'admin', NULL, NULL, NULL, NULL, NULL, NULL, '663f364e89894385181670639bbe555c825ad059ef080a535abe270dd01ce913', '2025-05-29 17:49:38', NULL, NULL, NULL, NULL, 'register'),
(65, 'markjamesrogelio@gmail.com', NULL, '$2y$10$sc.4kDzDuY4IHapl.wQ8Fu4vVK5qkap1eLUzR8HNmVZP7XG0Z4U3y', 'student', '2025-05-30 12:16:02', 'approved', 'Mark James', 'Jacinto', 'Rogelio', '', 'BS in Information Technology', '1A', 'Male', '1st', '2022-00816', NULL, NULL, NULL, NULL, 5, NULL, 'register'),
(69, 'ian@mail.com', NULL, '$2y$10$Cty5FStNAwlX0OQbvwSJtuoCvQUc9Cbn0G8RfqjyX/ZoHjboWutzm', 'student', '2025-05-31 12:26:39', 'approved', 'Angelo', 'Egmarin', 'Llabore', 'none', 'BS in Information Technology', '3A', 'Male', '3', '2022-00832', NULL, NULL, '460001', '2025-07-04 02:06:35', NULL, 'uploads/profile_pics/69.jpg', 'register'),
(70, 'teach@mail.com', NULL, '$2y$10$4yOYUuZk7ZpwL0uJbvLqs.HEuzbb3TVV8HrsG7BIhaH2Lx.lJsyb6', 'teacher', '2025-05-31 13:05:02', 'approved', 'Michael', 'me', 'Yanga', 'none', NULL, NULL, 'Female', NULL, '2022-000012', NULL, NULL, NULL, NULL, 1, NULL, 'register'),
(74, 'dean@dyci.edu.ph', NULL, '$2y$10$ehinsycj/MZJhEyBMNDvU.CdUr64fH6VzdTMCX/Cswvp.VQrtwTby', 'teacher', '2025-06-05 06:02:27', 'approved', 'Mary Ann', 'Dean', 'Lim', 'none', NULL, NULL, 'Female', NULL, '2022-00001', NULL, NULL, NULL, NULL, 5, NULL, 'register'),
(77, 'cath@dyci.edu.ph', NULL, '$2y$10$cZnG25QrNywg1i.FosfPoOLRMRcJzmCZji5kJUBARlW28VYZMs5KK', 'student', '2025-06-05 08:50:26', 'approved', 'Cath', 'C', 'Bartolome', 'None', 'BS in Computer Science', '3A', 'Female', '3rd', '2022-00832', NULL, NULL, NULL, NULL, NULL, NULL, 'register'),
(78, 'ryanrogelio@gmail.com', NULL, '$2y$10$ZGtgv26fuWi/mohPP5L7X.gaGlRMm/edeNYT.aO9b44iBXIBr29aS', 'student', '2025-07-04 01:15:56', 'approved', 'ryan', 'jacinto', 'rogelio', '', 'BS in Marine Engineering', '4A', 'Male', '4th', '00811', NULL, NULL, NULL, NULL, NULL, NULL, 'register'),
(80, 'markrogelio@dyci.edu.ph', NULL, '$2y$10$TOfP9bwO9hQXczXNEzuyZeEu8xZ2PnVGSbHdHKpSYeB/nNCCeCf42', 'student', '2025-07-04 02:48:19', 'pending', 'Mark James', '', 'Rogelio', '', 'AB in Psychology', '1B', 'Female', '1st', '00004', NULL, NULL, '427716', '2025-07-04 10:58:19', NULL, NULL, 'register'),
(81, 'kulot@dyci.edu.ph', NULL, '$2y$10$zCGwAvdemtfiCqZwndzjAuyJhU.H1wBHcDgSMBNbhSMycG1X7Kkde', 'student', '2025-07-04 02:53:46', 'pending', 'Mark James', '', 'Rogelio', '', 'AB in Psychology', '2C', 'Male', '2nd', '00005', NULL, NULL, '349342', '2025-07-04 11:03:46', NULL, NULL, 'register'),
(82, 'test@dyci.edu.ph', NULL, '$2y$10$3f7twRkjjuRK57MNUC9XxuigM302E.pr.I5Y.qUxxBnzoyqy0cSNy', 'student', '2025-07-04 02:56:44', 'approved', 'Mark James', '', 'Rogelio', '', 'AB in Political Science', '1A', 'Male', '1st', '00006', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 'cathleen@dyci.edu.ph', NULL, '$2y$10$RkCjqr1Aw50qUHfkvVtWEuhSIl7BgetqsIRAsTeV0keNSUf.JMkj2', 'student', '2025-07-04 03:00:34', 'approved', 'cathleen', '', 'porciuncula', '', 'BS in Midwifery', '2B', 'Female', '2nd', '00007', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 'adreitorres@dyci.edu.ph', NULL, '$2y$10$jphKfJ31kZkzfFTDRCjKfuRrwiSSpH5fm0UWd2xIPLXJcF2pJLi.2', 'student', '2025-07-04 03:03:31', 'approved', 'andrei', '', 'torres', '', 'BS in Accounting Information System', '1A', 'Female', '1st', '00008', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 'llabore.ianangelo.00832@dyci.edu.ph', NULL, '$2y$10$8SnLVZ9AZT5EgVgS9BCdmuqNlxoG.0uvEboBYEWqOrY6p/sfVKqou', 'student', '2025-07-04 03:10:58', 'approved', 'Mark James', '', 'Rogelio', '', 'BS in Accountancy', '1A', 'Male', '1st', '00008', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 'teacher@dyci.edu.ph', NULL, '$2y$10$qvuYpyaimBubDP43HIdRiuhpnvflGWao2Ki..kyQ2ih3m.1UIu9.W', 'teacher', '2025-07-04 03:12:05', 'approved', 'Mark James', '', 'Rogelio', '', 'BS in Accounting Information System', '2A', 'Male', '2nd', '00009', NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 'testingteacher@dyci.edu.ph', NULL, '$2y$10$6y.WXEgceMORaZFpX6/gLOspisXM1oG9Crp59E0BoUY3sqkxI6MD2', 'teacher', '2025-07-04 03:31:56', 'approved', 'Mark James', '', 'Rogelio', '', NULL, NULL, 'Female', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `youtube_link` text DEFAULT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `course` varchar(50) DEFAULT NULL,
  `year` varchar(10) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `views`
--

CREATE TABLE `views` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `video_path` varchar(255) NOT NULL,
  `likes` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `course` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `views`
--

INSERT INTO `views` (`id`, `title`, `description`, `category`, `video_path`, `likes`, `created_at`, `course`) VALUES
(12, 'Introduction To Java', 'Brief 14 minutes introduction to learn Java', 'Information Communication Technology', '74_1749105834.mp4', 0, '2025-06-05 06:43:54', 'BS in Information Technology'),
(13, 'Introduction to SQl', 'Discussion about SQL', 'Mathematics', '74_1749106271.mp4', 0, '2025-06-05 06:51:11', 'BS in Information Technology'),
(14, 'Introduction to Python', 'Brief discussion for python', 'Robotics', '74_1749106974.mp4', 0, '2025-06-05 07:02:54', 'BS in Information Technology');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archive`
--
ALTER TABLE `archive`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `department_id` (`department_id`);

--
-- Indexes for table `course_year_section_exam`
--
ALTER TABLE `course_year_section_exam`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `course_year_section_quiz`
--
ALTER TABLE `course_year_section_quiz`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_id` (`exam_id`);

--
-- Indexes for table `explorer_items`
--
ALTER TABLE `explorer_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_explorer_course` (`course_id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`module_id`),
  ADD KEY `folder_id` (`folder_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `module_folders`
--
ALTER TABLE `module_folders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folder_code` (`folder_code`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quiz_id` (`quiz_id`);

--
-- Indexes for table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `exam_id` (`exam_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `student_modules`
--
ALTER TABLE `student_modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_module` (`student_id`,`module_folder_id`),
  ADD KEY `module_folder_id` (`module_folder_id`);

--
-- Indexes for table `student_quiz_answers`
--
ALTER TABLE `student_quiz_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `quiz_id` (`quiz_id`),
  ADD KEY `question_id` (`question_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `fk_department` (`department_id`);

--
-- Indexes for table `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `views`
--
ALTER TABLE `views`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archive`
--
ALTER TABLE `archive`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `course_year_section_exam`
--
ALTER TABLE `course_year_section_exam`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `course_year_section_quiz`
--
ALTER TABLE `course_year_section_quiz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `exam_questions`
--
ALTER TABLE `exam_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `explorer_items`
--
ALTER TABLE `explorer_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `module_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- AUTO_INCREMENT for table `module_folders`
--
ALTER TABLE `module_folders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=228;

--
-- AUTO_INCREMENT for table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `student_answers`
--
ALTER TABLE `student_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `student_modules`
--
ALTER TABLE `student_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `student_quiz_answers`
--
ALTER TABLE `student_quiz_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `views`
--
ALTER TABLE `views`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_year_section_exam`
--
ALTER TABLE `course_year_section_exam`
  ADD CONSTRAINT `course_year_section_exam_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_year_section_quiz`
--
ALTER TABLE `course_year_section_quiz`
  ADD CONSTRAINT `course_year_section_quiz_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_questions`
--
ALTER TABLE `exam_questions`
  ADD CONSTRAINT `exam_questions_ibfk_1` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `explorer_items`
--
ALTER TABLE `explorer_items`
  ADD CONSTRAINT `fk_explorer_course` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `modules`
--
ALTER TABLE `modules`
  ADD CONSTRAINT `modules_ibfk_1` FOREIGN KEY (`folder_id`) REFERENCES `module_folders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `modules_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `module_folders`
--
ALTER TABLE `module_folders`
  ADD CONSTRAINT `module_folders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD CONSTRAINT `quizzes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD CONSTRAINT `quiz_questions_ibfk_1` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_answers`
--
ALTER TABLE `student_answers`
  ADD CONSTRAINT `student_answers_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `student_answers_ibfk_2` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`),
  ADD CONSTRAINT `student_answers_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `exam_questions` (`id`);

--
-- Constraints for table `student_modules`
--
ALTER TABLE `student_modules`
  ADD CONSTRAINT `student_modules_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_modules_ibfk_2` FOREIGN KEY (`module_folder_id`) REFERENCES `module_folders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_quiz_answers`
--
ALTER TABLE `student_quiz_answers`
  ADD CONSTRAINT `student_quiz_answers_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `student_quiz_answers_ibfk_2` FOREIGN KEY (`quiz_id`) REFERENCES `quizzes` (`id`),
  ADD CONSTRAINT `student_quiz_answers_ibfk_3` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_department` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
