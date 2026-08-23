USE `quan_ly_hoc_phan`;
-- =====================================================
-- 1. USERS
-- Mật khẩu mẫu: 123456
-- =====================================================
INSERT INTO `users`
(`id`, `username`, `password`, `full_name`, `role`, `status`)
VALUES
(1, 'admin', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Quản trị viên', 'admin', 1),
(2, 'teacher01', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Nguyễn Văn An', 'teacher', 1),
(3, 'student01', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Nguyễn Thị Hoa', 'student', 1),
(4, 'teacher02', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Trần Văn Bình', 'teacher', 1),
(5, 'student02', '$2y$12$eDI.Hak4yImuoiMuLaUoqe.MBCV7Q7lFMgliJZuA8J/e.NXItc6gC', 'Lê Thị Mai', 'student', 1);
-- =====================================================
-- 2. SEMESTERS
-- =====================================================
INSERT INTO `semesters`
(`id`, `name`, `start_date`, `end_date`, `status`)
VALUES
(1, 'Học kỳ 1 năm học 2026-2027', '2026-08-10', '2027-01-15', 1),
(2, 'Học kỳ 2 năm học 2026-2027', '2027-02-01', '2027-06-15', 1);
-- =====================================================
-- 3. COURSES
-- =====================================================
INSERT INTO `courses`
(`id`, `code`, `name`, `credits`, `description`, `status`)
VALUES
(1, 'WEB123', 'Lập trình Web', 3,
 'Học phần lập trình Web với PHP và MySQL', 1),
(2, 'HTTT', 'Hệ thống thông tin', 3,
 'Phân tích và thiết kế hệ thống thông tin', 1),
(3, 'CSDL', 'Cơ sở dữ liệu', 3,
 'Thiết kế và quản lý cơ sở dữ liệu', 1);
-- =====================================================
-- 4. CLASSES
-- =====================================================
INSERT INTO `classes`
(`id`, `class_code`, `course_id`, `semester_id`,
 `teacher_id`, `max_students`, `status`)
VALUES
(1, 'WEB123-01', 1, 1, 2, 40, 1),
(2, 'WEB123-02', 1, 1, 4, 40, 1),
(3, 'HTTT-01', 2, 1, 2, 40, 1),
(4, 'CSDL-01', 3, 2, 4, 40, 1);
-- =====================================================
-- 5. REGISTRATIONS
-- =====================================================
INSERT INTO `registrations`
(`id`, `student_id`, `class_id`)
VALUES
(1, 3, 1),
(2, 3, 3),
(3, 5, 1),
(4, 5, 2);
-- =====================================================
-- 6. GRADES
-- =====================================================
INSERT INTO `grades`
(`id`, `registration_id`, `midterm`, `final_exam`, `total`)
VALUES
(1, 1, 8.00, 8.50, 8.30),
(2, 2, 7.50, 8.00, 7.80);
-- =====================================================
-- 7. EDIT REQUESTS
-- =====================================================
INSERT INTO `edit_requests`
(`id`, `teacher_id`, `class_id`, `registration_id`,
 `content`, `status`)
VALUES
(1, 2, 1, 1,
 'Đề nghị kiểm tra lại điểm giữa kỳ của sinh viên.',
 'pending'),
(2, 2, 3, 2,
'Đề nghị cập nhật điểm cuối kỳ.',
 'approved'),
(3, 4, 2, 4,
 'Đề nghị kiểm tra lại điểm của sinh viên.',
 'rejected');
