CREATE DATABASE IF NOT EXISTS `quan_ly_hoc_phan`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `quan_ly_hoc_phan`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `edit_requests`;
DROP TABLE IF EXISTS `grades`;
DROP TABLE IF EXISTS `registrations`;
DROP TABLE IF EXISTS `classes`;
DROP TABLE IF EXISTS `courses`;
DROP TABLE IF EXISTS `semesters`;
DROP TABLE IF EXISTS `users`;

SET FOREIGN_KEY_CHECKS = 1;


-- =====================================================
-- 1. BẢNG USERS
-- =====================================================

CREATE TABLE `users` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `password` VARCHAR(255) NOT NULL,
    `full_name` VARCHAR(100) NOT NULL,
    `role` ENUM('student', 'teacher', 'admin') NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_users_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- 2. BẢNG COURSES
-- =====================================================

CREATE TABLE `courses` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `credits` INT NOT NULL,
    `description` TEXT,
    `status` TINYINT(1) NOT NULL DEFAULT 1,

    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_courses_code` (`code`),

    CONSTRAINT `chk_courses_credits`
        CHECK (`credits` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- 3. BẢNG SEMESTERS
-- =====================================================

CREATE TABLE `semesters` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100) NOT NULL,
    `start_date` DATE NOT NULL,
    `end_date` DATE NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,

    PRIMARY KEY (`id`),

    CONSTRAINT `chk_semesters_date`
        CHECK (`start_date` <= `end_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- 4. BẢNG CLASSES
-- =====================================================

CREATE TABLE `classes` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `class_code` VARCHAR(50) NOT NULL,
    `course_id` INT NOT NULL,
    `semester_id` INT NOT NULL,
    `teacher_id` INT NOT NULL,
    `max_students` INT NOT NULL,
    `status` TINYINT(1) NOT NULL DEFAULT 1,

    PRIMARY KEY (`id`),

    UNIQUE KEY `uk_classes_class_code` (`class_code`),

    KEY `idx_classes_course` (`course_id`),
    KEY `idx_classes_semester` (`semester_id`),
    KEY `idx_classes_teacher` (`teacher_id`),

    CONSTRAINT `fk_classes_course`
        FOREIGN KEY (`course_id`)
        REFERENCES `courses` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT `fk_classes_semester`
        FOREIGN KEY (`semester_id`)
        REFERENCES `semesters` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,
CONSTRAINT `fk_classes_teacher`
        FOREIGN KEY (`teacher_id`)
        REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT `chk_classes_max_students`
        CHECK (`max_students` > 0)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- 5. BẢNG REGISTRATIONS
-- =====================================================

CREATE TABLE `registrations` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `student_id` INT NOT NULL,
    `class_id` INT NOT NULL,
    `registered_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    -- Một sinh viên không được đăng ký trùng một lớp
    UNIQUE KEY `uk_registrations_student_class`
        (`student_id`, `class_id`),

    KEY `idx_registrations_student` (`student_id`),
    KEY `idx_registrations_class` (`class_id`),

    CONSTRAINT `fk_registrations_student`
        FOREIGN KEY (`student_id`)
        REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `fk_registrations_class`
        FOREIGN KEY (`class_id`)
        REFERENCES `classes` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- 6. BẢNG GRADES
-- =====================================================

CREATE TABLE `grades` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `registration_id` INT NOT NULL,
    `midterm` DECIMAL(4,2) DEFAULT NULL,
    `final_exam` DECIMAL(4,2) DEFAULT NULL,
    `total` DECIMAL(4,2) DEFAULT NULL,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    -- Một đăng ký chỉ có một bảng điểm
    UNIQUE KEY `uk_grades_registration`
        (`registration_id`),

    CONSTRAINT `fk_grades_registration`
        FOREIGN KEY (`registration_id`)
        REFERENCES `registrations` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE,

    CONSTRAINT `chk_grades_midterm`
        CHECK (`midterm` IS NULL OR (`midterm` >= 0 AND `midterm` <= 10)),

    CONSTRAINT `chk_grades_final`
        CHECK (`final_exam` IS NULL OR (`final_exam` >= 0 AND `final_exam` <= 10)),

    CONSTRAINT `chk_grades_total`
        CHECK (`total` IS NULL OR (`total` >= 0 AND `total` <= 10))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- =====================================================
-- 7. BẢNG EDIT_REQUESTS
-- =====================================================

CREATE TABLE `edit_requests` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `teacher_id` INT NOT NULL,
    `class_id` INT NOT NULL,
    `registration_id` INT NOT NULL,
    `content` TEXT,
    `status` ENUM('pending', 'approved', 'rejected')
        NOT NULL DEFAULT 'pending',
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (`id`),

    KEY `idx_edit_requests_teacher` (`teacher_id`),
KEY `idx_edit_requests_class` (`class_id`),
    KEY `idx_edit_requests_registration` (`registration_id`),

    CONSTRAINT `fk_edit_requests_teacher`
        FOREIGN KEY (`teacher_id`)
        REFERENCES `users` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT `fk_edit_requests_class`
        FOREIGN KEY (`class_id`)
        REFERENCES `classes` (`id`)
        ON UPDATE CASCADE
        ON DELETE RESTRICT,

    CONSTRAINT `fk_edit_requests_registration`
        FOREIGN KEY (`registration_id`)
        REFERENCES `registrations` (`id`)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;