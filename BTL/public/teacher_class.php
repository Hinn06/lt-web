<?php

session_start();

require_once "../config/database.php";

// =====================================================
// KIỂM TRA ĐĂNG NHẬP
// =====================================================

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SESSION["role"] !== "teacher") {
    die("Bạn không có quyền truy cập trang này.");
}

$teacher_id = (int) $_SESSION["user_id"];

$message = "";
$error = "";


// =====================================================
// THÔNG BÁO SAU KHI REDIRECT
// =====================================================

if (isset($_GET["saved"])) {
    $message = "Cập nhật điểm thành công.";
}

if (isset($_GET["request_sent"])) {
    $message = "Đã gửi yêu cầu chỉnh sửa thành công.";
}


// =====================================================
// CSRF
// =====================================================

if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

$csrf_token = $_SESSION["csrf_token"];


// =====================================================
// CẬP NHẬT ĐIỂM
// =====================================================

if (isset($_POST["save_grade"])) {

    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals(
            $_SESSION["csrf_token"],
            $_POST["csrf_token"]
        )
    ) {
        die("CSRF token không hợp lệ.");
    }

    $registration_id = (int) ($_POST["registration_id"] ?? 0);

    $midterm_raw = trim($_POST["midterm"] ?? "");
    $final_raw = trim($_POST["final_exam"] ?? "");

    $midterm = null;
    $final_exam = null;
    $total = null;


    // =================================================
    // KIỂM TRA ĐIỂM GIỮA KỲ
    // =================================================

    if ($midterm_raw !== "") {

        if (!is_numeric($midterm_raw)) {

            $error = "Điểm giữa kỳ không hợp lệ.";

        } else {

            $midterm = (float) $midterm_raw;

            if ($midterm < 0 || $midterm > 10) {
                $error = "Điểm giữa kỳ phải từ 0 đến 10.";
            }
        }
    }


    // =================================================
    // KIỂM TRA ĐIỂM CUỐI KỲ
    // =================================================

    if ($error === "" && $final_raw !== "") {

        if (!is_numeric($final_raw)) {

            $error = "Điểm cuối kỳ không hợp lệ.";

        } else {

            $final_exam = (float) $final_raw;

            if ($final_exam < 0 || $final_exam > 10) {
                $error = "Điểm cuối kỳ phải từ 0 đến 10.";
            }
        }
    }


    // =================================================
    // CẬP NHẬT
    // =================================================

    if ($error === "") {

        if ($registration_id <= 0) {

            $error = "Không xác định được sinh viên.";

        } else {

            // Kiểm tra sinh viên thuộc lớp của giảng viên

            $stmt = $pdo->prepare("
                SELECT
                    registrations.id,
                    registrations.class_id
                FROM registrations
                INNER JOIN classes
                    ON registrations.class_id = classes.id
                WHERE registrations.id = :registration_id
                AND classes.teacher_id = :teacher_id
                LIMIT 1
            ");

            $stmt->execute([
                ":registration_id" => $registration_id,
                ":teacher_id" => $teacher_id
            ]);

            $registration = $stmt->fetch(PDO::FETCH_ASSOC);


            if (!$registration) {

                $error =
                    "Bạn không có quyền cập nhật điểm sinh viên này.";

            } else {

                // 40% giữa kỳ + 60% cuối kỳ

                if (
                    $midterm !== null &&
                    $final_exam !== null
                ) {

                    $total = round(
                        $midterm * 0.4 +
                        $final_exam * 0.6,
                        2
                    );
                }


                // Kiểm tra đã có điểm chưa

                $stmt = $pdo->prepare("
                    SELECT id
                    FROM grades
                    WHERE registration_id = :registration_id
                    LIMIT 1
                ");

                $stmt->execute([
                    ":registration_id" => $registration_id
                ]);

                $grade = $stmt->fetch(PDO::FETCH_ASSOC);


                if ($grade) {

                    // UPDATE

                    $stmt = $pdo->prepare("
                        UPDATE grades
                        SET
                            midterm = :midterm,
                            final_exam = :final_exam,
                            total = :total
                        WHERE registration_id = :registration_id
                    ");

                    $stmt->execute([
                        ":midterm" => $midterm,
                        ":final_exam" => $final_exam,
                        ":total" => $total,
                        ":registration_id" => $registration_id
                    ]);

                } else {

                    // INSERT

                    $stmt = $pdo->prepare("
                        INSERT INTO grades
                        (
                            registration_id,
                            midterm,
                            final_exam,
                            total
                        )
                        VALUES
                        (
                            :registration_id,
                            :midterm,
                            :final_exam,
                            :total
                        )
                    ");

                    $stmt->execute([
                        ":registration_id" => $registration_id,
                        ":midterm" => $midterm,
                        ":final_exam" => $final_exam,
                        ":total" => $total
                    ]);
                }


                // Giữ nguyên lớp đang xem sau khi lưu

                header(
                    "Location: teacher_class.php?class_id="
                    . (int) $registration["class_id"]
                    . "&saved=1"
                );

                exit;
            }
        }
    }
}


// =====================================================
// GỬI YÊU CẦU CHỈNH SỬA
// =====================================================

if (isset($_POST["send_request"])) {

    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals(
            $_SESSION["csrf_token"],
            $_POST["csrf_token"]
        )
    ) {
        die("CSRF token không hợp lệ.");
    }


    $class_id = (int) ($_POST["class_id"] ?? 0);

    $registration_id =
        (int) ($_POST["registration_id"] ?? 0);

    $title = trim(
        $_POST["title"] ?? ""
    );

    $content = trim(
        $_POST["content"] ?? ""
    );


    if ($class_id <= 0) {

        $error = "Không xác định được lớp.";

    } elseif ($title === "") {

        $error = "Vui lòng nhập tiêu đề yêu cầu.";

    } elseif (strlen($title) > 150) {

        $error = "Tiêu đề yêu cầu không được vượt quá 150 ký tự.";

    } elseif ($content === "") {

        $error = "Vui lòng nhập nội dung yêu cầu.";

    } elseif (strlen($content) > 2000) {

        $error = "Nội dung yêu cầu không được vượt quá 2000 ký tự.";

    } else {

        // Kiểm tra lớp thuộc giảng viên

        $stmt = $pdo->prepare("
            SELECT id
            FROM classes
            WHERE id = :class_id
            AND teacher_id = :teacher_id
            LIMIT 1
        ");

        $stmt->execute([
            ":class_id" => $class_id,
            ":teacher_id" => $teacher_id
        ]);

        $class_check =
            $stmt->fetch(PDO::FETCH_ASSOC);


        if (!$class_check) {

            $error =
                "Bạn không có quyền gửi yêu cầu cho lớp này.";

        } else {

            // Nếu chọn sinh viên thì kiểm tra sinh viên thuộc lớp

            if ($registration_id > 0) {

                $stmt = $pdo->prepare("
                    SELECT id
                    FROM registrations
                    WHERE id = :registration_id
                    AND class_id = :class_id
                    LIMIT 1
                ");

                $stmt->execute([
                    ":registration_id" => $registration_id,
                    ":class_id" => $class_id
                ]);

                $registration_check =
                    $stmt->fetch(PDO::FETCH_ASSOC);


                if (!$registration_check) {

                    $error =
                        "Sinh viên không thuộc lớp này.";
                }
            }


            if ($error === "") {

                try {

                    /*
                     * edit_requests của database hiện tại
                     * không có cột title.
                     *
                     * Vì vậy đưa tiêu đề vào content.
                     */

                    $request_content =
                        "TIÊU ĐỀ: "
                        . $title
                        . "\n\n"
                        . $content;


                    $stmt = $pdo->prepare("
                        INSERT INTO edit_requests
                        (
                            teacher_id,
                            class_id,
                            registration_id,
                            content,
                            status
                        )
                        VALUES
                        (
                            :teacher_id,
                            :class_id,
                            :registration_id,
                            :content,
                            'pending'
                        )
                    ");


                    $stmt->execute([

                        ":teacher_id" =>
                            $teacher_id,

                        ":class_id" =>
                            $class_id,

                        ":registration_id" =>
                            $registration_id > 0
                                ? $registration_id
                                : null,

                        ":content" =>
                            $request_content
                    ]);


                    header(
                        "Location: teacher_class.php?class_id="
                        . $class_id
                        . "&request_sent=1"
                    );

                    exit;


                } catch (PDOException $e) {

                    $error =
                        "Không thể gửi yêu cầu chỉnh sửa: "
                        . $e->getMessage();
                }
            }
        }
    }
}


// =====================================================
// LẤY THÔNG TIN GIẢNG VIÊN
// =====================================================

$stmt = $pdo->prepare("
    SELECT
        id,
        username,
        full_name
    FROM users
    WHERE id = :id
    LIMIT 1
");

$stmt->execute([
    ":id" => $teacher_id
]);

$teacher =
    $stmt->fetch(PDO::FETCH_ASSOC);


// =====================================================
// LẤY CÁC LỚP
// =====================================================

$stmt = $pdo->prepare("
    SELECT

        classes.id,
        classes.class_code,
        classes.max_students,
        classes.status,

        courses.code AS course_code,
        courses.name AS course_name,

        semesters.name AS semester_name,

        COUNT(registrations.id) AS student_count

    FROM classes

    INNER JOIN courses
        ON classes.course_id = courses.id

    INNER JOIN semesters
        ON classes.semester_id = semesters.id

    LEFT JOIN registrations
        ON classes.id = registrations.class_id

    WHERE classes.teacher_id = :teacher_id

    GROUP BY
        classes.id,
        classes.class_code,
        classes.max_students,
        classes.status,
        courses.code,
        courses.name,
        semesters.name

    ORDER BY classes.id DESC
");

$stmt->execute([
    ":teacher_id" => $teacher_id
]);

$classes =
    $stmt->fetchAll(PDO::FETCH_ASSOC);


// =====================================================
// XEM CHI TIẾT LỚP
// =====================================================

$selected_class = null;
$students = [];

$class_id =
    (int) ($_GET["class_id"] ?? 0);


if ($class_id > 0) {

    $stmt = $pdo->prepare("
        SELECT

            classes.id,
            classes.class_code,
            classes.max_students,
            classes.status,

            courses.code AS course_code,
            courses.name AS course_name,

            semesters.name AS semester_name

        FROM classes

        INNER JOIN courses
            ON classes.course_id = courses.id

        INNER JOIN semesters
            ON classes.semester_id = semesters.id

        WHERE classes.id = :class_id
        AND classes.teacher_id = :teacher_id

        LIMIT 1
    ");

    $stmt->execute([
        ":class_id" => $class_id,
        ":teacher_id" => $teacher_id
    ]);

    $selected_class =
        $stmt->fetch(PDO::FETCH_ASSOC);


    if ($selected_class) {

        $stmt = $pdo->prepare("
            SELECT

                registrations.id AS registration_id,

                users.id AS student_id,
                users.username,
                users.full_name,

                grades.midterm,
                grades.final_exam,
                grades.total

            FROM registrations

            INNER JOIN users
                ON registrations.student_id = users.id

            LEFT JOIN grades
                ON grades.registration_id =
                   registrations.id

            WHERE registrations.class_id = :class_id

            ORDER BY users.full_name
        ");

        $stmt->execute([
            ":class_id" => $class_id
        ]);

        $students =
            $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

?>


<!DOCTYPE html>

<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Trang giảng viên</title>


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


        .user {
            color: #7692b4;
        }


        .logout {

            color: #c98282;

            text-decoration: none;

            margin-left: 15px;
        }


        .container {

            max-width: 1250px;

            margin: 35px auto;

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

            background: #eef7f2;

            color: #5e9878;

            padding: 12px;

            border-radius: 8px;

            margin-bottom: 20px;
        }


        .error {

            background: #fff0f0;

            color: #c98282;

            padding: 12px;

            border-radius: 8px;

            margin-bottom: 20px;
        }


        table {

            width: 100%;

            border-collapse: collapse;

            margin-top: 15px;
        }


        th {

            background: #e5effc;

            color: #6485ad;

            padding: 12px;

            text-align: left;

            white-space: nowrap;
        }


        td {

            padding: 12px;

            border-bottom:
                1px solid #edf2f8;

            vertical-align: middle;
        }


        tr:hover {

            background: #f8fbff;
        }


        .btn {

            display: inline-block;

            padding: 8px 13px;

            border-radius: 6px;

            text-decoration: none;

            border: none;

            cursor: pointer;

            font-size: 13px;

            white-space: nowrap;
        }


        .btn-main {

            background: #94b4dc;

            color: white;
        }


        .btn-main:hover {

            background: #7fa5d2;
        }


        .btn-back {

            background: #edf5fd;

            color: #6388b5;
        }


        .status-open {

            color: #65a681;

            font-weight: bold;
        }


        .status-lock {

            color: #c98282;

            font-weight: bold;
        }


        /* =================================================
           Ô ĐIỂM
        ================================================= */

        .grade-input {

            width: 90px;

            height: 40px;

            padding: 8px 10px;

            border: 1px solid #d8e5f5;

            border-radius: 7px;

            background: #f8fbff;

            font-family: Arial, sans-serif;

            font-size: 15px;

            color: #536d8c;
        }


        .grade-input:focus {

            outline: none;

            border-color: #94b4dc;

            background: white;
        }


        .total-grade {

            display: inline-block;

            min-width: 45px;

            color: #536d8c;

            font-size: 15px;

            text-align: center;
        }


        .no-grade {

            color: #9aabc0;

            white-space: nowrap;
        }


        td form {

            margin: 0;
        }


        /* =================================================
           FORM YÊU CẦU
        ================================================= */

        .request-grid {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 15px;
        }


        .form-group {

            display: flex;

            flex-direction: column;
        }


        .full {

            grid-column: 1 / -1;
        }


        label {

            margin-bottom: 7px;

            font-weight: bold;

            color: #6485ad;
        }


        input,
        select,
        textarea {

            width: 100%;

            padding: 10px;

            border: 1px solid #d8e5f5;

            border-radius: 7px;

            background: #f8fbff;

            font-family: Arial, sans-serif;

            font-size: 14px;
        }


        input:focus,
        select:focus,
        textarea:focus {

            outline: none;

            border-color: #94b4dc;

            background: white;
        }


        textarea {

            min-height: 120px;

            resize: vertical;
        }


        /* =================================================
           RESPONSIVE
        ================================================= */

        @media (max-width: 800px) {

            .header {

                padding: 20px;

                flex-direction: column;

                align-items: flex-start;

                gap: 10px;
            }


            .request-grid {

                grid-template-columns: 1fr;
            }


            .full {

                grid-column: auto;
            }


            table {

                font-size: 12px;

                display: block;

                overflow-x: auto;
            }


            .grade-input {

                width: 75px;
            }

        }

    </style>

</head>


<body>


<header class="header">

    <h1>
        QUẢN LÝ LỚP HỌC PHẦN
    </h1>


    <div class="user">

        Xin chào,

        <strong>
            <?php
            echo htmlspecialchars(
                $teacher["full_name"] ?? "Giảng viên"
            );
            ?>
        </strong>


        <a
            href="logout.php"
            class="logout"
        >
            Đăng xuất
        </a>

    </div>

</header>


<div class="container">


    <!-- =================================================
         THÔNG BÁO
    ================================================== -->

    <?php if ($message !== ""): ?>

        <div class="message">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


    <?php if ($error !== ""): ?>

        <div class="error">

            <?php
            echo htmlspecialchars($error);
            ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         DANH SÁCH LỚP
    ================================================== -->

    <div class="box">

        <h2>
            Lớp học phần được phân công
        </h2>


        <?php if (count($classes) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>Mã lớp</th>

                        <th>Học phần</th>

                        <th>Học kỳ</th>

                        <th>Sĩ số</th>

                        <th>Trạng thái</th>

                        <th>Thao tác</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($classes as $class): ?>

                    <tr>

                        <td>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $class["class_code"]
                                );
                                ?>

                            </strong>

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
                            echo $class["student_count"];
                            ?>

                            /

                            <?php
                            echo $class["max_students"];
                            ?>

                        </td>


                        <td>

                            <?php if ($class["status"] == 1): ?>

                                <span class="status-open">
                                    Đang mở
                                </span>

                            <?php else: ?>

                                <span class="status-lock">
                                    Đã khóa
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <a
                                href="teacher_class.php?class_id=<?php echo $class["id"]; ?>"
                                class="btn btn-main"
                            >
                                Xem chi tiết
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p>
                Bạn chưa được phân công lớp học phần nào.
            </p>

        <?php endif; ?>

    </div>


    <?php if ($selected_class): ?>


    <!-- =================================================
         CHI TIẾT LỚP
    ================================================== -->

    <div class="box">

        <h2>
            Chi tiết lớp học phần
        </h2>


        <p>

            <strong>Mã lớp:</strong>

            <?php
            echo htmlspecialchars(
                $selected_class["class_code"]
            );
            ?>

        </p>


        <p>

            <strong>Học phần:</strong>

            <?php
            echo htmlspecialchars(
                $selected_class["course_code"]
            );
            ?>

            -

            <?php
            echo htmlspecialchars(
                $selected_class["course_name"]
            );
            ?>

        </p>


        <p>

            <strong>Học kỳ:</strong>

            <?php
            echo htmlspecialchars(
                $selected_class["semester_name"]
            );
            ?>

        </p>


        <p>

            <strong>Sĩ số:</strong>

            <?php
            echo count($students);
            ?>

            /

            <?php
            echo $selected_class["max_students"];
            ?>

        </p>


        <a
            href="teacher_class.php"
            class="btn btn-back"
        >
            ← Quay lại danh sách lớp
        </a>

    </div>


    <!-- =================================================
         DANH SÁCH SINH VIÊN + ĐIỂM
    ================================================== -->

    <div class="box">

        <h2>
            Danh sách sinh viên và điểm
        </h2>


        <?php if (count($students) > 0): ?>

            <table>

                <thead>

                    <tr>

                        <th>STT</th>

                        <th>Mã sinh viên</th>

                        <th>Họ và tên</th>

                        <th>Giữa kỳ</th>

                        <th>Cuối kỳ</th>

                        <th>Tổng</th>

                        <th>Cập nhật</th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $students as $index => $student
                ): ?>

                    <?php

                    /*
                     * Mỗi sinh viên có một form riêng.
                     * Dùng form="" để input cuối kỳ và nút
                     * vẫn thuộc đúng form mà không phá bố cục table.
                     */

                    $form_id =
                        "grade_form_"
                        . $student["registration_id"];

                    ?>


                    <tr>

                        <!-- STT -->

                        <td>

                            <?php
                            echo $index + 1;
                            ?>

                        </td>


                        <!-- MÃ SINH VIÊN -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $student["username"]
                            );
                            ?>

                        </td>


                        <!-- HỌ VÀ TÊN -->

                        <td>

                            <?php
                            echo htmlspecialchars(
                                $student["full_name"]
                            );
                            ?>

                        </td>


                        <!-- GIỮA KỲ -->

                        <td>

                            <form
                                id="<?php echo $form_id; ?>"
                                method="POST"
                            >

                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?php
                                    echo htmlspecialchars(
                                        $csrf_token
                                    );
                                    ?>"
                                >


                                <input
                                    type="hidden"
                                    name="registration_id"
                                    value="<?php
                                    echo $student[
                                        "registration_id"
                                    ];
                                    ?>"
                                >


                                <input
                                    type="hidden"
                                    name="class_id"
                                    value="<?php
                                    echo $selected_class["id"];
                                    ?>"
                                >


                                <input
                                    type="number"
                                    name="midterm"
                                    class="grade-input"
                                    min="0"
                                    max="10"
                                    step="0.01"
                                    placeholder="GK"
                                    value="<?php
                                    echo $student["midterm"] !== null
                                        ? htmlspecialchars(
                                            $student["midterm"]
                                        )
                                        : "";
                                    ?>"
                                >

                            </form>

                        </td>


                        <!-- CUỐI KỲ -->

                        <td>

                            <input
                                type="number"
                                name="final_exam"
                                form="<?php echo $form_id; ?>"
                                class="grade-input"
                                min="0"
                                max="10"
                                step="0.01"
                                placeholder="CK"
                                value="<?php
                                echo $student["final_exam"] !== null
                                    ? htmlspecialchars(
                                        $student["final_exam"]
                                    )
                                    : "";
                                ?>"
                            >

                        </td>


                        <!-- TỔNG -->

                        <td>

                            <?php if (
                                $student["total"] !== null
                            ): ?>

                                <strong class="total-grade">

                                    <?php
                                    echo htmlspecialchars(
                                        $student["total"]
                                    );
                                    ?>

                                </strong>

                            <?php else: ?>

                                <span class="no-grade">
                                    Chưa có
                                </span>

                            <?php endif; ?>

                        </td>


                        <!-- CẬP NHẬT -->

                        <td>

                            <button
                                type="submit"
                                name="save_grade"
                                form="<?php echo $form_id; ?>"
                                class="btn btn-main"
                            >
                                Lưu điểm
                            </button>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <p>
                Lớp này chưa có sinh viên đăng ký.
            </p>

        <?php endif; ?>

    </div>


    <!-- =================================================
         GỬI YÊU CẦU CHỈNH SỬA
    ================================================== -->

    <div class="box">

        <h2>
            Yêu cầu chỉnh sửa thông tin
        </h2>


        <p>
            Nếu thông tin lớp học phần chưa chính xác,
            giảng viên có thể gửi yêu cầu cho quản trị viên.
        </p>


        <form method="POST">

            <!-- CSRF -->

            <input
                type="hidden"
                name="csrf_token"
                value="<?php
                echo htmlspecialchars(
                    $csrf_token
                );
                ?>"
            >


            <!-- CLASS ID -->

            <input
                type="hidden"
                name="class_id"
                value="<?php
                echo $selected_class["id"];
                ?>"
            >


            <div class="request-grid">


                <!-- TIÊU ĐỀ -->

                <div class="form-group full">

                    <label>
                        Tiêu đề yêu cầu
                    </label>


                    <input
                        type="text"
                        name="title"
                        placeholder="VD: Yêu cầu sửa sĩ số lớp"
                        required
                    >

                </div>


                <!-- SINH VIÊN -->

                <div class="form-group full">

                    <label>
                        Sinh viên liên quan
                    </label>


                    <select name="registration_id">

                        <option value="0">
                            -- Yêu cầu chung cho lớp --
                        </option>


                        <?php foreach (
                            $students as $student
                        ): ?>

                            <option
                                value="<?php
                                echo $student[
                                    "registration_id"
                                ];
                                ?>"
                            >

                                <?php
                                echo htmlspecialchars(
                                    $student["username"]
                                );
                                ?>

                                -

                                <?php
                                echo htmlspecialchars(
                                    $student["full_name"]
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <!-- NỘI DUNG -->

                <div class="form-group full">

                    <label>
                        Nội dung
                    </label>


                    <textarea
                        name="content"
                        placeholder="Nhập nội dung cần chỉnh sửa..."
                        required
                    ></textarea>

                </div>


            </div>


            <br>


            <button
                type="submit"
                name="send_request"
                class="btn btn-main"
            >
                Gửi yêu cầu
            </button>

        </form>

    </div>


    <?php endif; ?>


</div>


</body>

</html>