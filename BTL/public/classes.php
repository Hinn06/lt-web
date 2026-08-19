<?php

session_start();

require_once "../config/database.php";

// =========================
// KIỂM TRA ĐĂNG NHẬP
// =========================

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

// Chỉ Admin được quản lý lớp
if ($_SESSION["role"] !== "admin") {
    die("Bạn không có quyền truy cập trang này.");
}

$message = "";


// =========================
// THÊM LỚP HỌC PHẦN
// =========================

if (isset($_POST["add_class"])) {

    $class_code = trim($_POST["class_code"] ?? "");
    $course_id_raw = trim($_POST["course_id"] ?? "");
    $course_id = filter_var($course_id_raw, FILTER_VALIDATE_INT);
    $semester_id_raw = trim($_POST["semester_id"] ?? "");
    $semester_id = filter_var($semester_id_raw, FILTER_VALIDATE_INT);
    $teacher_id_raw = trim($_POST["teacher_id"] ?? "");
    $teacher_id = filter_var($teacher_id_raw, FILTER_VALIDATE_INT);
    $max_students_raw = trim($_POST["max_students"] ?? "");
    $max_students = filter_var($max_students_raw, FILTER_VALIDATE_INT);

    if (
        $class_code === "" ||
        $course_id_raw === "" ||
        $semester_id_raw === "" ||
        $teacher_id_raw === "" ||
        $max_students_raw === ""
    ) {

        $message = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (!preg_match('/^[A-Za-z0-9_-]{2,30}$/', $class_code)) {

        $message = "Mã lớp phải từ 2-30 ký tự và chỉ gồm chữ, số, gạch dưới hoặc gạch ngang.";

    } elseif (
        $course_id === false || $course_id <= 0 ||
        $semester_id === false || $semester_id <= 0 ||
        $teacher_id === false || $teacher_id <= 0 ||
        $max_students === false || $max_students < 1 || $max_students > 500
    ) {

        $message = "Thông tin học phần, học kỳ, giảng viên hoặc sĩ số không hợp lệ.";

    } else {

        // Kiểm tra các khóa ngoại có tồn tại và đúng trạng thái
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = :id AND status = 1 LIMIT 1");
        $stmt->execute([":id" => $course_id]);
        $course_exists = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT id FROM semesters WHERE id = :id AND status = 1 LIMIT 1");
        $stmt->execute([":id" => $semester_id]);
        $semester_exists = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = :id AND role = 'teacher' AND status = 1 LIMIT 1");
        $stmt->execute([":id" => $teacher_id]);
        $teacher_exists = $stmt->fetch();

        if (!$course_exists || !$semester_exists || !$teacher_exists) {

            $message = "Học phần, học kỳ hoặc giảng viên không hợp lệ.";

        } else {

        try {

            $stmt = $pdo->prepare(
                "INSERT INTO classes
                (
                    class_code,
                    course_id,
                    semester_id,
                    teacher_id,
                    max_students,
                    status
                )
                VALUES
                (
                    :class_code,
                    :course_id,
                    :semester_id,
                    :teacher_id,
                    :max_students,
                    1
                )"
            );

            $stmt->execute([
                ":class_code" => $class_code,
                ":course_id" => $course_id,
                ":semester_id" => $semester_id,
                ":teacher_id" => $teacher_id,
                ":max_students" => $max_students
            ]);

            $message = "Thêm lớp học phần thành công.";

        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                $message = "Mã lớp học phần đã tồn tại.";
            } else {
                $message = "Không thể thêm lớp học phần.";
            }
        }
        }
    }
}


// =========================
// SỬA LỚP HỌC PHẦN
// =========================

if (isset($_POST["edit_class"])) {

    $id = (int) ($_POST["id"] ?? 0);

    $class_code = trim($_POST["class_code"] ?? "");
    $course_id_raw = trim($_POST["course_id"] ?? "");
    $course_id = filter_var($course_id_raw, FILTER_VALIDATE_INT);
    $semester_id_raw = trim($_POST["semester_id"] ?? "");
    $semester_id = filter_var($semester_id_raw, FILTER_VALIDATE_INT);
    $teacher_id_raw = trim($_POST["teacher_id"] ?? "");
    $teacher_id = filter_var($teacher_id_raw, FILTER_VALIDATE_INT);
    $max_students_raw = trim($_POST["max_students"] ?? "");
    $max_students = filter_var($max_students_raw, FILTER_VALIDATE_INT);

    if (
        $id <= 0 ||
        $class_code === "" ||
        $course_id_raw === "" ||
        $semester_id_raw === "" ||
        $teacher_id_raw === "" ||
        $max_students_raw === ""
    ) {

        $message = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (!preg_match('/^[A-Za-z0-9_-]{2,30}$/', $class_code)) {

        $message = "Mã lớp phải từ 2-30 ký tự và chỉ gồm chữ, số, gạch dưới hoặc gạch ngang.";

    } elseif (
        $course_id === false || $course_id <= 0 ||
        $semester_id === false || $semester_id <= 0 ||
        $teacher_id === false || $teacher_id <= 0 ||
        $max_students === false || $max_students < 1 || $max_students > 500
    ) {

        $message = "Thông tin học phần, học kỳ, giảng viên hoặc sĩ số không hợp lệ.";

    } else {

        // Kiểm tra các khóa ngoại
        $stmt = $pdo->prepare("SELECT id FROM courses WHERE id = :id AND status = 1 LIMIT 1");
        $stmt->execute([":id" => $course_id]);
        $course_exists = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT id FROM semesters WHERE id = :id AND status = 1 LIMIT 1");
        $stmt->execute([":id" => $semester_id]);
        $semester_exists = $stmt->fetch();

        $stmt = $pdo->prepare("SELECT id FROM users WHERE id = :id AND role = 'teacher' AND status = 1 LIMIT 1");
        $stmt->execute([":id" => $teacher_id]);
        $teacher_exists = $stmt->fetch();

        if (!$course_exists || !$semester_exists || !$teacher_exists) {

            $message = "Học phần, học kỳ hoặc giảng viên không hợp lệ.";

        } else {

        try {

            $stmt = $pdo->prepare(
                "UPDATE classes
                 SET
                    class_code = :class_code,
                    course_id = :course_id,
                    semester_id = :semester_id,
                    teacher_id = :teacher_id,
                    max_students = :max_students
                 WHERE id = :id"
            );

            $stmt->execute([
                ":class_code" => $class_code,
                ":course_id" => $course_id,
                ":semester_id" => $semester_id,
                ":teacher_id" => $teacher_id,
                ":max_students" => $max_students,
                ":id" => $id
            ]);

            $message = "Cập nhật lớp học phần thành công.";

        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                $message = "Mã lớp học phần đã tồn tại.";
            } else {
                $message = "Không thể cập nhật lớp học phần.";
            }
        }
        }
    }
}


// =========================
// KHÓA / MỞ LỚP
// =========================

if (isset($_GET["toggle"])) {

    $id = (int) $_GET["toggle"];

    if ($id > 0) {

        $stmt = $pdo->prepare(
            "UPDATE classes
             SET status = IF(status = 1, 0, 1)
             WHERE id = :id"
        );

        $stmt->execute([
            ":id" => $id
        ]);
    }

    header("Location: classes.php");
    exit;
}


// =========================
// XÓA LỚP
// =========================

if (isset($_GET["delete"])) {

    $id = (int) $_GET["delete"];

    if ($id > 0) {

        $stmt = $pdo->prepare(
            "DELETE FROM classes
             WHERE id = :id"
        );

        $stmt->execute([
            ":id" => $id
        ]);
    }

    header("Location: classes.php");
    exit;
}


// =========================
// LẤY LỚP ĐANG SỬA
// =========================

$editClass = null;

if (isset($_GET["edit"])) {

    $id = (int) $_GET["edit"];

    $stmt = $pdo->prepare(
        "SELECT *
         FROM classes
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $editClass = $stmt->fetch();
}


// =========================
// LẤY DANH SÁCH HỌC PHẦN
// =========================

$stmt = $pdo->query(
    "SELECT id, code, name
     FROM courses
     WHERE status = 1
     ORDER BY name"
);

$courses = $stmt->fetchAll();


// =========================
// LẤY DANH SÁCH HỌC KỲ
// =========================

$stmt = $pdo->query(
    "SELECT id, name
     FROM semesters
     WHERE status = 1
     ORDER BY id DESC"
);

$semesters = $stmt->fetchAll();


// =========================
// LẤY DANH SÁCH GIẢNG VIÊN
// =========================

$stmt = $pdo->query(
    "SELECT id, username, full_name
     FROM users
     WHERE role = 'teacher'
     ORDER BY full_name"
);

$teachers = $stmt->fetchAll();


// =====================================================
// TÌM KIẾM & LỌC
// =====================================================

$search = trim($_GET["search"] ?? "");

$filter_teacher = (int) ($_GET["teacher"] ?? 0);

$filter_semester = (int) ($_GET["semester"] ?? 0);

$filter_status = $_GET["status"] ?? "";


// =========================
// TẠO QUERY DANH SÁCH LỚP
// =========================

$sql = "
    SELECT
        classes.*,
        courses.code AS course_code,
        courses.name AS course_name,
        semesters.name AS semester_name,
        users.full_name AS teacher_name

    FROM classes

    INNER JOIN courses
        ON classes.course_id = courses.id

    INNER JOIN semesters
        ON classes.semester_id = semesters.id

    INNER JOIN users
        ON classes.teacher_id = users.id

    WHERE 1 = 1
";

$params = [];


// =========================
// TÌM KIẾM
// =========================

if ($search !== "") {

    $sql .= "
        AND (
            classes.class_code LIKE :search
            OR courses.code LIKE :search
            OR courses.name LIKE :search
        )
    ";

    $params[":search"] = "%" . $search . "%";
}


// =========================
// LỌC GIẢNG VIÊN
// =========================

if ($filter_teacher > 0) {

    $sql .= "
        AND classes.teacher_id = :teacher
    ";

    $params[":teacher"] = $filter_teacher;
}


// =========================
// LỌC HỌC KỲ
// =========================

if ($filter_semester > 0) {

    $sql .= "
        AND classes.semester_id = :semester
    ";

    $params[":semester"] = $filter_semester;
}


// =========================
// LỌC TRẠNG THÁI
// =========================

if ($filter_status === "open") {

    $sql .= "
        AND classes.status = 1
    ";

}

if ($filter_status === "locked") {

    $sql .= "
        AND classes.status = 0
    ";

}


$sql .= "
    ORDER BY classes.id DESC
";


// =========================
// THỰC THI
// =========================

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$classes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Quản lý lớp học phần</title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;

            font-family: Arial, sans-serif;

            background: #eef5ff;

            color: #536d8c;
        }

        .header {
            background: white;

            padding: 20px 40px;

            display: flex;

            justify-content: space-between;

            align-items: center;

            box-shadow:
                0 2px 10px rgba(80,130,190,.08);
        }

        .header h1 {
            margin: 0;

            color: #6f9bd4;

            font-size: 23px;
        }

        .back {
            color: #6f9bd4;

            text-decoration: none;
        }

        .container {
            max-width: 1300px;

            margin: 40px auto;

            padding: 0 20px;
        }

        .box {
            background: white;

            padding: 25px;

            border-radius: 14px;

            box-shadow:
                0 5px 20px rgba(80,130,190,.08);

            margin-bottom: 25px;
        }

        h2 {
            margin-top: 0;

            color: #6f9bd4;
        }

        .message {
            padding: 12px;

            margin-bottom: 20px;

            background: #eef7f2;

            color: #5e9878;

            border-radius: 7px;
        }

        /* =========================
           FORM THÊM / SỬA
        ========================== */

        .form-grid {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 15px;
        }

        .form-group {
            display: flex;

            flex-direction: column;
        }

        label {
            margin-bottom: 7px;

            font-weight: bold;

            color: #6485ad;

            font-size: 14px;
        }

        input,
        select {
            padding: 11px;

            border: 1px solid #d8e5f5;

            border-radius: 7px;

            outline: none;

            background: #f8fbff;
        }

        input:focus,
        select:focus {
            border-color: #94b4dc;
        }

        /* =========================
           BUTTON
        ========================== */

        .btn {
            display: inline-block;

            padding: 8px 12px;

            margin-right: 5px;

            border-radius: 6px;

            border: none;

            text-decoration: none;

            font-size: 12px;

            cursor: pointer;
        }

        .btn-main {
            margin-top: 18px;

            padding: 11px 20px;

            background: #94b4dc;

            color: white;

            font-weight: bold;
        }

        .btn-main:hover {
            background: #7fa5d2;
        }

        .btn-edit {
            background: #e8f1fc;

            color: #6388b5;
        }

        .btn-toggle {
            background: #edf5fd;

            color: #6388b5;
        }

        .btn-delete {
            background: #fff0f0;

            color: #c98282;
        }

        .btn-cancel {
            background: #f1f3f6;

            color: #718096;
        }

        /* =========================
           BỘ LỌC
        ========================== */

        .filter-grid {
            display: grid;

            grid-template-columns:
                2fr
                1fr
                1fr
                1fr
                auto
                auto;

            gap: 10px;

            align-items: end;
        }

        .filter-group {
            display: flex;

            flex-direction: column;
        }

        .filter-group label {
            margin-bottom: 6px;
        }

        .btn-search {
            padding: 11px 18px;

            background: #94b4dc;

            color: white;

            border: none;

            border-radius: 7px;

            cursor: pointer;

            font-weight: bold;
        }

        .btn-search:hover {
            background: #7fa5d2;
        }

        .btn-reset {
            display: inline-block;

            padding: 11px 18px;

            background: #f1f3f6;

            color: #718096;

            text-decoration: none;

            border-radius: 7px;

            text-align: center;
        }

        .result-info {
            margin-top: 15px;

            color: #8199b5;

            font-size: 14px;
        }

        /* =========================
           TABLE
        ========================== */

        table {
            width: 100%;

            border-collapse: collapse;

            margin-top: 20px;
        }

        th {
            background: #e5effc;

            color: #6485ad;

            padding: 13px;

            text-align: left;
        }

        td {
            padding: 13px;

            border-bottom: 1px solid #edf2f8;
        }

        tr:hover {
            background: #f8fbff;
        }

        .status {
            color: #65a681;

            font-weight: bold;
        }

        .locked {
            color: #c98282;

            font-weight: bold;
        }

        .empty {
            text-align: center;

            padding: 25px;

            color: #8199b5;
        }

        @media (max-width: 1000px) {

            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 900px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 20px;
            }

            table {
                font-size: 11px;
            }

        }

    </style>

</head>

<body>


<header class="header">

    <h1>
        QUẢN LÝ LỚP HỌC PHẦN
    </h1>

    <a
        href="admin.php"
        class="back"
    >
        ← Quay lại Admin
    </a>

</header>


<div class="container">


    <?php if ($message !== ""): ?>

        <div class="message">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <!-- =========================
         FORM THÊM / SỬA
    ========================== -->

    <div class="box">

        <?php if ($editClass): ?>

            <h2>
                ✏️ Sửa lớp học phần
            </h2>

        <?php else: ?>

            <h2>
                + Thêm lớp học phần
            </h2>

        <?php endif; ?>


        <form method="POST">

            <?php if ($editClass): ?>

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $editClass["id"]; ?>"
                >

            <?php endif; ?>


            <div class="form-grid">


                <div class="form-group">

                    <label>
                        Mã lớp học phần
                    </label>

                    <input
                        type="text"
                        name="class_code"
                        placeholder="VD: WEB101-01"

                        value="<?php
                            echo $editClass
                                ? htmlspecialchars(
                                    $editClass["class_code"]
                                )
                                : "";
                        ?>"

                        required
                    >

                </div>


                <div class="form-group">

                    <label>
                        Học phần
                    </label>

                    <select
                        name="course_id"
                        required
                    >

                        <option value="">
                            -- Chọn học phần --
                        </option>

                        <?php foreach ($courses as $course): ?>

                            <option
                                value="<?php echo $course["id"]; ?>"

                                <?php

                                if (
                                    $editClass &&
                                    $editClass["course_id"]
                                    == $course["id"]
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $course["code"]
                                );
                                ?>

                                -

                                <?php
                                echo htmlspecialchars(
                                    $course["name"]
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Học kỳ
                    </label>

                    <select
                        name="semester_id"
                        required
                    >

                        <option value="">
                            -- Chọn học kỳ --
                        </option>

                        <?php foreach ($semesters as $semester): ?>

                            <option
                                value="<?php echo $semester["id"]; ?>"

                                <?php

                                if (
                                    $editClass &&
                                    $editClass["semester_id"]
                                    == $semester["id"]
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $semester["name"]
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Giảng viên phụ trách
                    </label>

                    <select
                        name="teacher_id"
                        required
                    >

                        <option value="">
                            -- Chọn giảng viên --
                        </option>

                        <?php foreach ($teachers as $teacher): ?>

                            <option
                                value="<?php echo $teacher["id"]; ?>"

                                <?php

                                if (
                                    $editClass &&
                                    $editClass["teacher_id"]
                                    == $teacher["id"]
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $teacher["full_name"]
                                );
                                ?>

                                -

                                <?php
                                echo htmlspecialchars(
                                    $teacher["username"]
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="form-group">

                    <label>
                        Sĩ số tối đa
                    </label>

                    <input
                        type="number"
                        name="max_students"
                        min="1"

                        value="<?php
                            echo $editClass
                                ? $editClass["max_students"]
                                : 50;
                        ?>"

                        required
                    >

                </div>


            </div>


            <?php if ($editClass): ?>

                <button
                    type="submit"
                    name="edit_class"
                    class="btn btn-main"
                >
                    LƯU THAY ĐỔI
                </button>

                <a
                    href="classes.php"
                    class="btn btn-cancel"
                >
                    HỦY
                </a>

            <?php else: ?>

                <button
                    type="submit"
                    name="add_class"
                    class="btn btn-main"
                >
                    THÊM LỚP
                </button>

            <?php endif; ?>


        </form>

    </div>


    <!-- =========================
         TÌM KIẾM & LỌC
    ========================== -->

    <div class="box">

        <h2>
            🔎 Tìm kiếm & lọc lớp học phần
        </h2>


        <form
            method="GET"
            action="classes.php"
        >

            <div class="filter-grid">


                <!-- Tìm kiếm -->

                <div class="filter-group">

                    <label>
                        Tìm kiếm
                    </label>

                    <input
                        type="text"
                        name="search"

                        placeholder="Mã lớp, mã học phần hoặc tên học phần"

                        value="<?php
                            echo htmlspecialchars($search);
                        ?>"
                    >

                </div>


                <!-- Giảng viên -->

                <div class="filter-group">

                    <label>
                        Giảng viên
                    </label>

                    <select name="teacher">

                        <option value="0">
                            Tất cả giảng viên
                        </option>

                        <?php foreach ($teachers as $teacher): ?>

                            <option
                                value="<?php echo $teacher["id"]; ?>"

                                <?php

                                if (
                                    $filter_teacher
                                    == $teacher["id"]
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $teacher["full_name"]
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Học kỳ -->

                <div class="filter-group">

                    <label>
                        Học kỳ
                    </label>

                    <select name="semester">

                        <option value="0">
                            Tất cả học kỳ
                        </option>

                        <?php foreach ($semesters as $semester): ?>

                            <option
                                value="<?php echo $semester["id"]; ?>"

                                <?php

                                if (
                                    $filter_semester
                                    == $semester["id"]
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $semester["name"]
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- Trạng thái -->

                <div class="filter-group">

                    <label>
                        Trạng thái
                    </label>

                    <select name="status">

                        <option value="">
                            Tất cả
                        </option>

                        <option
                            value="open"

                            <?php
                            echo $filter_status === "open"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Đang mở
                        </option>

                        <option
                            value="locked"

                            <?php
                            echo $filter_status === "locked"
                                ? "selected"
                                : "";
                            ?>
                        >
                            Đã khóa
                        </option>

                    </select>

                </div>


                <!-- Nút tìm -->

                <div>

                    <button
                        type="submit"
                        class="btn-search"
                    >
                        Tìm kiếm
                    </button>

                </div>


                <!-- Nút reset -->

                <div>

                    <a
                        href="classes.php"
                        class="btn-reset"
                    >
                        Xóa lọc
                    </a>

                </div>


            </div>

        </form>


        <div class="result-info">

            Tìm thấy
            <strong>
                <?php echo count($classes); ?>
            </strong>
            lớp học phần.

        </div>

    </div>


    <!-- =========================
         DANH SÁCH LỚP
    ========================== -->

    <div class="box">

        <h2>
            Danh sách lớp học phần
        </h2>


        <table>

            <thead>

                <tr>

                    <th>
                        ID
                    </th>

                    <th>
                        Mã lớp
                    </th>

                    <th>
                        Học phần
                    </th>

                    <th>
                        Học kỳ
                    </th>

                    <th>
                        Giảng viên
                    </th>

                    <th>
                        Sĩ số tối đa
                    </th>

                    <th>
                        Trạng thái
                    </th>

                    <th>
                        Thao tác
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php if (count($classes) > 0): ?>


                <?php foreach ($classes as $class): ?>


                    <tr>


                        <td>

                            <?php
                            echo $class["id"];
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $class["class_code"]
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $class["course_code"]
                            );
                            ?>

                            -

                            <?php
                            echo htmlspecialchars(
                                $class["course_name"]
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $class["semester_name"]
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo htmlspecialchars(
                                $class["teacher_name"]
                            );
                            ?>

                        </td>


                        <td>

                            <?php
                            echo $class["max_students"];
                            ?>

                        </td>


                        <td>


                            <?php if ($class["status"] == 1): ?>

                                <span class="status">
                                    Đang mở
                                </span>

                            <?php else: ?>

                                <span class="locked">
                                    Đã khóa
                                </span>

                            <?php endif; ?>


                        </td>


                        <td>


                            <a
                                href="classes.php?edit=<?php echo $class["id"]; ?>"
                                class="btn btn-edit"
                            >
                                Sửa
                            </a>


                            <a
                                href="classes.php?toggle=<?php echo $class["id"]; ?>"
                                class="btn btn-toggle"

                                onclick="return confirm(
                                    'Bạn có chắc muốn thay đổi trạng thái lớp này?'
                                );"
                            >

                                <?php

                                echo $class["status"] == 1
                                    ? "Khóa"
                                    : "Mở";

                                ?>

                            </a>


                            <a
                                href="classes.php?delete=<?php echo $class["id"]; ?>"
                                class="btn btn-delete"

                                onclick="return confirm(
                                    'Bạn có chắc muốn xóa lớp này?'
                                );"
                            >
                                Xóa
                            </a>


                        </td>


                    </tr>


                <?php endforeach; ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="8"
                        class="empty"
                    >

                        Không tìm thấy lớp học phần phù hợp.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>

    </div>


</div>


</body>

</html>