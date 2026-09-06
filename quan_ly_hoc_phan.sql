-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 06, 2026 at 02:58 AM
-- Server version: 8.4.7
-- PHP Version: 8.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `quan_ly_hoc_phan`
--

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

DROP TABLE IF EXISTS `classes`;
CREATE TABLE IF NOT EXISTS `classes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_code` varchar(50) NOT NULL,
  `course_id` int NOT NULL,
  `semester_id` int NOT NULL,
  `lecturer_id` int NOT NULL,
  `room` varchar(50) NOT NULL,
  `max_students` smallint UNSIGNED NOT NULL,
  `weekday` tinyint UNSIGNED NOT NULL,
  `start_period` tinyint UNSIGNED NOT NULL,
  `end_period` tinyint UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_class_semester_code` (`semester_id`,`class_code`),
  KEY `idx_class_schedule` (`semester_id`,`weekday`,`start_period`,`end_period`),
  KEY `course_id` (`course_id`),
  KEY `lecturer_id` (`lecturer_id`)
) ;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_code`, `course_id`, `semester_id`, `lecturer_id`, `room`, `max_students`, `weekday`, `start_period`, `end_period`, `status`) VALUES
(1, 'WEB123-01', 1, 1, 4, 'A101', 17, 2, 1, 3, 1),
(2, 'WEB123-02', 1, 1, 2, 'A102', 25, 3, 4, 6, 1),
(3, 'HTTT-01', 2, 1, 2, 'A201', 35, 4, 1, 3, 1),
(4, 'CSDL-01', 3, 1, 2, 'A202', 40, 5, 4, 6, 1),
(5, 'TA01', 4, 1, 2, 'B101', 50, 6, 1, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `class_schedules`
--

DROP TABLE IF EXISTS `class_schedules`;
CREATE TABLE IF NOT EXISTS `class_schedules` (
  `id` int NOT NULL AUTO_INCREMENT,
  `class_id` int NOT NULL,
  `weekday` tinyint NOT NULL,
  `start_period` int NOT NULL,
  `end_period` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `room` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_class_schedules_class` (`class_id`)
) ;

--
-- Dumping data for table `class_schedules`
--

INSERT INTO `class_schedules` (`id`, `class_id`, `weekday`, `start_period`, `end_period`, `start_date`, `end_date`, `room`) VALUES
(1, 4, 2, 1, 3, '2026-08-10', '2026-09-17', 'CS1-A1-210'),
(2, 3, 6, 1, 4, '2026-08-10', '2026-09-10', 'CS1-A1-211'),
(6, 5, 4, 1, 3, '2026-09-10', '2026-12-31', 'CS1-A2-405'),
(4, 1, 2, 7, 9, '2026-08-10', '2026-09-17', 'CS2-A1-303'),
(5, 2, 2, 4, 6, '2026-08-10', '2026-09-17', 'CS2-A1-303');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(150) NOT NULL,
  `credits` tinyint UNSIGNED NOT NULL,
  `description` text,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `code`, `name`, `credits`, `description`, `status`) VALUES
(1, 'WEB123', 'Lập trình Web', 3, 'PHP, MySQL và phát triển ứng dụng Web.', 1),
(2, 'HTTT', 'Hệ thống thông tin', 3, 'Phân tích và thiết kế hệ thống thông tin.', 1),
(3, 'CSDL', 'Cơ sở dữ liệu', 3, 'Thiết kế và quản lý cơ sở dữ liệu.', 1),
(4, 'TA01', 'Tiếng Anh 1', 3, 'Học phần tiếng Anh cơ bản dùng chung.', 1),
(5, 'LTN', 'Lập trình nhúng', 1, '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `course_faculties`
--

DROP TABLE IF EXISTS `course_faculties`;
CREATE TABLE IF NOT EXISTS `course_faculties` (
  `course_id` int NOT NULL,
  `faculty_id` int NOT NULL,
  PRIMARY KEY (`course_id`,`faculty_id`),
  KEY `faculty_id` (`faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `course_faculties`
--

INSERT INTO `course_faculties` (`course_id`, `faculty_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(4, 2),
(4, 3);

-- --------------------------------------------------------

--
-- Table structure for table `course_lecturers`
--

DROP TABLE IF EXISTS `course_lecturers`;
CREATE TABLE IF NOT EXISTS `course_lecturers` (
  `course_id` int NOT NULL,
  `lecturer_id` int NOT NULL,
  PRIMARY KEY (`course_id`,`lecturer_id`),
  KEY `lecturer_id` (`lecturer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `course_lecturers`
--

INSERT INTO `course_lecturers` (`course_id`, `lecturer_id`) VALUES
(1, 2),
(2, 2),
(3, 2),
(4, 2),
(5, 2),
(1, 4),
(3, 4),
(4, 4),
(3, 6),
(4, 6);

-- --------------------------------------------------------

--
-- Table structure for table `course_semesters`
--

DROP TABLE IF EXISTS `course_semesters`;
CREATE TABLE IF NOT EXISTS `course_semesters` (
  `course_id` int NOT NULL,
  `semester_id` int NOT NULL,
  PRIMARY KEY (`course_id`,`semester_id`),
  KEY `semester_id` (`semester_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `course_semesters`
--

INSERT INTO `course_semesters` (`course_id`, `semester_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(1, 2),
(3, 2),
(4, 2),
(5, 2);

-- --------------------------------------------------------

--
-- Table structure for table `edit_requests`
--

DROP TABLE IF EXISTS `edit_requests`;
CREATE TABLE IF NOT EXISTS `edit_requests` (
  `id` int NOT NULL AUTO_INCREMENT,
  `teacher_id` int NOT NULL,
  `class_id` int NOT NULL,
  `registration_id` int NOT NULL,
  `content` text NOT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `teacher_id` (`teacher_id`),
  KEY `class_id` (`class_id`),
  KEY `registration_id` (`registration_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faculties`
--

DROP TABLE IF EXISTS `faculties`;
CREATE TABLE IF NOT EXISTS `faculties` (
  `id` int NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `faculties`
--

INSERT INTO `faculties` (`id`, `code`, `name`) VALUES
(1, 'CNTT', 'Khoa Công nghệ thông tin'),
(2, 'KT', 'Khoa Kinh tế'),
(3, 'NN', 'Khoa Ngoại ngữ'),
(4, 'SP', 'Khoa Sư phạm '),
(5, 'QLDT', 'Quản lý & Đô thị'),
(6, 'KHXH', 'Khoa KHXH&NV');

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
CREATE TABLE IF NOT EXISTS `grades` (
  `id` int NOT NULL AUTO_INCREMENT,
  `registration_id` int NOT NULL,
  `midterm` decimal(4,2) DEFAULT NULL,
  `final_exam` decimal(4,2) DEFAULT NULL,
  `total` decimal(4,2) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `registration_id` (`registration_id`)
) ;

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

DROP TABLE IF EXISTS `lecturers`;
CREATE TABLE IF NOT EXISTS `lecturers` (
  `user_id` int NOT NULL,
  `lecturer_code` varchar(30) NOT NULL,
  `faculty_id` int NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `lecturer_code` (`lecturer_code`),
  KEY `faculty_id` (`faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`user_id`, `lecturer_code`, `faculty_id`) VALUES
(2, 'GV01', 1),
(4, 'GV02', 2),
(6, 'GV03', 1);

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

DROP TABLE IF EXISTS `registrations`;
CREATE TABLE IF NOT EXISTS `registrations` (
  `id` int NOT NULL AUTO_INCREMENT,
  `student_id` int NOT NULL,
  `class_id` int NOT NULL,
  `registered_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_student_class` (`student_id`,`class_id`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `student_id`, `class_id`, `registered_at`) VALUES
(3, 5, 5, '2026-09-03 13:36:14'),
(5, 3, 4, '2026-09-04 04:57:00'),
(6, 3, 1, '2026-09-04 05:43:43'),
(7, 3, 5, '2026-09-04 05:43:48'),
(8, 3, 3, '2026-09-04 05:43:55'),
(9, 7, 3, '2026-09-05 06:15:13'),
(10, 7, 5, '2026-09-05 06:15:16'),
(11, 7, 2, '2026-09-05 06:15:18'),
(12, 7, 4, '2026-09-05 06:15:25');

-- --------------------------------------------------------

--
-- Table structure for table `semesters`
--

DROP TABLE IF EXISTS `semesters`;
CREATE TABLE IF NOT EXISTS `semesters` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `academic_year` varchar(9) NOT NULL,
  `term` enum('1','2','3') NOT NULL,
  `study_start` date NOT NULL,
  `study_end` date NOT NULL,
  `registration_start` date NOT NULL,
  `registration_end` date NOT NULL,
  `registration_open` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ;

--
-- Dumping data for table `semesters`
--

INSERT INTO `semesters` (`id`, `name`, `academic_year`, `term`, `study_start`, `study_end`, `registration_start`, `registration_end`, `registration_open`, `status`) VALUES
(1, 'Học kỳ 1 năm học 2026-2027', '2026-2027', '1', '2026-08-10', '2026-12-31', '2026-09-01', '2026-09-15', 0, 1),
(2, 'Học kỳ 2 năm học 2026-2027', '2026-2027', '2', '2027-02-01', '2027-06-15', '2027-01-15', '2027-01-30', 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `user_id` int NOT NULL,
  `student_code` varchar(30) NOT NULL,
  `faculty_id` int NOT NULL,
  `class_name` varchar(80) NOT NULL,
  `cohort` varchar(20) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `student_code` (`student_code`),
  KEY `faculty_id` (`faculty_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`user_id`, `student_code`, `faculty_id`, `class_name`, `cohort`) VALUES
(3, 'SV01', 1, 'CNTT-K21', 'K21'),
(5, 'SV02', 2, 'KT-K21', 'K21'),
(7, 'SV03', 1, 'CNTT-K21', 'K21'),
(8, 'SV04', 3, 'NN-K21', 'K21'),
(9, 'SV05', 2, 'KT-K21', 'K21'),
(10, 'SV06', 4, 'SP-K21', 'K21'),
(11, 'SV07', 6, 'KHXH-K21', 'K21'),
(12, 'SV08', 1, 'CNTT-K21', 'K21'),
(13, 'SV09', 6, 'KHXH-K21', 'K21');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','teacher','student') NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `full_name`, `role`, `status`, `created_at`) VALUES
(1, 'admin', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Quản trị viên', 'admin', 1, '2026-09-03 13:36:14'),
(2, 'teacher01', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Nguyễn Văn An', 'teacher', 1, '2026-09-03 13:36:14'),
(3, 'student01', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Nguyễn Thị Hoa', 'student', 1, '2026-09-03 13:36:14'),
(4, 'teacher02', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Trần Văn Bình', 'teacher', 1, '2026-09-03 13:36:14'),
(5, 'student02', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Lê Thị Mai', 'student', 1, '2026-09-03 13:36:14'),
(6, 'teacher03', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Phạm Minh Đức', 'teacher', 1, '2026-09-03 13:36:14'),
(7, 'student03', '$2y$10$MLsi6jl/BwKg4O9Ecp44BOOINUQUeVnCRYdPkUa/Sf5YiedYZScra', 'Nguyễn Thị Trang', 'student', 1, '2026-09-04 12:56:59'),
(8, 'student04', '$2y$10$lqCqMzfTfyBPVT2SvErG3u/40bahuu.RY0FXbj24d8IfsRgTm5UUW', 'Nguyễn Mai Hoa', 'student', 1, '2026-09-04 12:57:39'),
(9, 'student05', '$2y$10$wNNFBbaL/V961vGdXMoXrull1p/5D0pQXsbiVOAMSfFeDPm2w4lTq', 'Trần Thị Mai', 'student', 1, '2026-09-04 12:58:36'),
(10, 'student06', '$2y$10$TIARaXYJVHKjW4zOCxmNC.pR1ktP/KSHTaG05ep9pPjtoYd.EDP.q', 'Trần Thu Thảo', 'student', 1, '2026-09-06 02:48:03'),
(11, 'student07', '$2y$10$WRuKZHsV6VJrU657zc4gPO1sBRDoNLRgquIjvIo495cZkGYSo/HX.', 'Nguyễn Thanh Thảo', 'student', 1, '2026-09-06 02:48:49'),
(12, 'student08', '$2y$10$1y6CFluYBkf6/3elz9wh7uKlOi36GWbQxeGc5WQQF33raFn5ieAuG', 'Phùng Văn Hải', 'student', 1, '2026-09-06 02:49:53'),
(13, 'student09', '$2y$10$fyZTDQCj4EWrLpNmOiZR1uvNdegvxHSxcyzEfbOPnKK.u6XD11s76', 'Phùng Hà Hải', 'student', 1, '2026-09-06 02:50:50');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `classes_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `classes_ibfk_3` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`user_id`) ON DELETE RESTRICT;

--
-- Constraints for table `course_faculties`
--
ALTER TABLE `course_faculties`
  ADD CONSTRAINT `course_faculties_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_faculties_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `course_lecturers`
--
ALTER TABLE `course_lecturers`
  ADD CONSTRAINT `course_lecturers_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_lecturers_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`user_id`) ON DELETE RESTRICT;

--
-- Constraints for table `course_semesters`
--
ALTER TABLE `course_semesters`
  ADD CONSTRAINT `course_semesters_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `course_semesters_ibfk_2` FOREIGN KEY (`semester_id`) REFERENCES `semesters` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `edit_requests`
--
ALTER TABLE `edit_requests`
  ADD CONSTRAINT `edit_requests_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `lecturers` (`user_id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `edit_requests_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `edit_requests_ibfk_3` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `grades`
--
ALTER TABLE `grades`
  ADD CONSTRAINT `grades_ibfk_1` FOREIGN KEY (`registration_id`) REFERENCES `registrations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD CONSTRAINT `lecturers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `lecturers_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `registrations_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`user_id`) ON DELETE RESTRICT,
  ADD CONSTRAINT `registrations_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`) ON DELETE RESTRICT;

--
-- Constraints for table `students`
--
ALTER TABLE `students`
  ADD CONSTRAINT `students_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `students_ibfk_2` FOREIGN KEY (`faculty_id`) REFERENCES `faculties` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
