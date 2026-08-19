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

// Chỉ sinh viên được vào
if ($_SESSION["role"] !== "student") {
    die("Bạn không có quyền truy cập trang sinh viên.");
}

$student_id = $_SESSION["user_id"];

$message = "";
$message_type = "";


// =========================
// ĐĂNG KÝ LỚP
// =========================

if (isset($_POST["register"])) {

    $class_id = (int) ($_POST["class_id"] ?? 0);

    if ($class_id <= 0) {

        $message = "Lớp học phần không hợp lệ.";
        $message_type = "error";

    } else {

        // Kiểm tra lớp có tồn tại và đang mở
        $stmt = $pdo->prepare(
            "SELECT
                classes.*,
                courses.code AS course_code,
                courses.name AS course_name

             FROM classes

             INNER JOIN courses
                ON classes.course_id = courses.id

             INNER JOIN semesters
                ON classes.semester_id = semesters.id

             WHERE classes.id = :class_id
             AND classes.status = 1
             AND courses.status = 1
             AND semesters.status = 1"
        );

        $stmt->execute([
            ":class_id" => $class_id
        ]);

        $class = $stmt->fetch();


        if (!$class) {

            $message = "Lớp không tồn tại hoặc đã bị khóa.";
            $message_type = "error";

        } else {

            // Kiểm tra đã đăng ký chưa
            $stmt = $pdo->prepare(
                "SELECT id
                 FROM registrations
                 WHERE student_id = :student_id
                 AND class_id = :class_id"
            );

            $stmt->execute([
                ":student_id" => $student_id,
                ":class_id" => $class_id
            ]);

            $exists = $stmt->fetch();


            if ($exists) {

                $message = "Bạn đã đăng ký lớp này rồi.";
                $message_type = "error";

            } else {

                // Đếm số sinh viên hiện tại
                $stmt = $pdo->prepare(
                    "SELECT COUNT(*) AS total
                     FROM registrations
                     WHERE class_id = :class_id"
                );

                $stmt->execute([
                    ":class_id" => $class_id
                ]);

                $total = $stmt->fetch()["total"];


                // Kiểm tra sĩ số
                if ($total >= $class["max_students"]) {

                    $message = "Lớp đã đủ sĩ số.";
                    $message_type = "error";

                } else {

                    // Đăng ký
                    $stmt = $pdo->prepare(
                        "INSERT INTO registrations
                        (student_id, class_id)
                        VALUES
                        (:student_id, :class_id)"
                    );

                    $stmt->execute([
                        ":student_id" => $student_id,
                        ":class_id" => $class_id
                    ]);

                    $message = "Đăng ký học phần thành công.";
                    $message_type = "success";
                }
            }
        }
    }
}


// =========================
// HỦY ĐĂNG KÝ
// =========================

if (isset($_POST["cancel"])) {

    $registration_id = (int) ($_POST["registration_id"] ?? 0);

    if ($registration_id <= 0) {

        $message = "Mã đăng ký không hợp lệ.";
        $message_type = "error";

    } else {

        // Chỉ được hủy đăng ký của chính mình
        $stmt = $pdo->prepare(
            "DELETE FROM registrations
             WHERE id = :id
             AND student_id = :student_id"
        );

        $stmt->execute([
            ":id" => $registration_id,
            ":student_id" => $student_id
        ]);

        if ($stmt->rowCount() > 0) {

            $message = "Hủy đăng ký thành công.";
            $message_type = "success";

        } else {

            $message = "Không thể hủy đăng ký.";
            $message_type = "error";
        }
    }
}


// =========================
// LẤY DANH SÁCH LỚP
// =========================

$stmt = $pdo->query(
    "SELECT
        classes.id,
        classes.class_code,
        classes.max_students,
        classes.status,

        courses.code AS course_code,
        courses.name AS course_name,

        semesters.name AS semester_name,

        users.full_name AS teacher_name,

        (
            SELECT COUNT(*)
            FROM registrations
            WHERE registrations.class_id = classes.id
        ) AS registered_count

     FROM classes

     INNER JOIN courses
        ON classes.course_id = courses.id

     INNER JOIN semesters
        ON classes.semester_id = semesters.id

     INNER JOIN users
        ON classes.teacher_id = users.id

     WHERE classes.status = 1
     AND courses.status = 1

     ORDER BY classes.id DESC"
);

$classes = $stmt->fetchAll();


// =========================
// LẤY LỊCH SỬ ĐĂNG KÝ
// =========================

$stmt = $pdo->prepare(
    "SELECT
        registrations.id,
        registrations.registered_at,

        classes.class_code,

        courses.code AS course_code,
        courses.name AS course_name,

        semesters.name AS semester_name,

        users.full_name AS teacher_name

     FROM registrations

     INNER JOIN classes
        ON registrations.class_id = classes.id

     INNER JOIN courses
        ON classes.course_id = courses.id

     INNER JOIN semesters
        ON classes.semester_id = semesters.id

     INNER JOIN users
        ON classes.teacher_id = users.id

     WHERE registrations.student_id = :student_id

     ORDER BY registrations.id DESC"
);

$stmt->execute([
    ":student_id" => $student_id
]);

$registrations = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Trang sinh viên</title>

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

            box-shadow:
                0 2px 10px rgba(80,130,190,.08);

            display: flex;

            justify-content: space-between;

            align-items: center;
        }

        .header h1 {

            margin: 0;

            color: #6f9bd4;

            font-size: 23px;
        }

        .user-info {

            color: #7692b4;
        }

        .logout {

            color: #c98282;

            text-decoration: none;

            margin-left: 15px;
        }

        .container {

            max-width: 1200px;

            margin: 40px auto;

            padding: 0 20px;
        }

        .welcome {

            background: white;

            padding: 25px;

            border-radius: 14px;

            margin-bottom: 25px;

            box-shadow:
                0 5px 20px rgba(80,130,190,.08);
        }

        .welcome h2 {

            margin-top: 0;

            color: #6f9bd4;
        }

        .box {

            background: white;

            padding: 25px;

            border-radius: 14px;

            margin-bottom: 25px;

            box-shadow:
                0 5px 20px rgba(80,130,190,.08);
        }

        .box h2 {

            margin-top: 0;

            color: #6f9bd4;
        }

        .message {

            padding: 13px;

            border-radius: 7px;

            margin-bottom: 20px;
        }

        .success {

            background: #eef7f2;

            color: #5e9878;
        }

        .error {

            background: #fff1f1;

            color: #c96f6f;
        }

        table {

            width: 100%;

            border-collapse: collapse;
        }

        th {

            background: #e5effc;

            color: #6485ad;

            padding: 13px;

            text-align: left;
        }

        td {

            padding: 13px;

            border-bottom:
                1px solid #edf2f8;
        }

        tr:hover {

            background: #f8fbff;
        }

        .btn {

            padding: 8px 12px;

            border: none;

            border-radius: 6px;

            cursor: pointer;

            font-size: 12px;
        }

        .btn-register {

            background: #94b4dc;

            color: white;
        }

        .btn-register:hover {

            background: #7fa5d2;
        }

        .btn-cancel {

            background: #fff0f0;

            color: #c98282;
        }

        .available {

            color: #5e9878;

            font-weight: bold;
        }

        .full {

            color: #c98282;

            font-weight: bold;
        }

        @media (max-width: 900px) {

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
        QUẢN LÝ HỌC PHẦN
    </h1>


    <div class="user-info">

        Xin chào,

        <strong>
            <?php
            echo htmlspecialchars($_SESSION["full_name"]);
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


    <!-- =========================
         WELCOME
    ========================== -->

    <div class="welcome">

        <h2>
            Trang sinh viên
        </h2>

        <p>
            Tra cứu và đăng ký các lớp học phần.
        </p>

    </div>


    <!-- =========================
         THÔNG BÁO
    ========================== -->

    <?php if ($message !== ""): ?>

        <div class="message <?php echo $message_type; ?>">

            <?php
            echo htmlspecialchars($message);
            ?>

        </div>

    <?php endif; ?>


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

                    <th>Mã lớp</th>

                    <th>Học phần</th>

                    <th>Học kỳ</th>

                    <th>Giảng viên</th>

                    <th>Số chỗ</th>

                    <th>Thao tác</th>

                </tr>

            </thead>


            <tbody>


            <?php if (count($classes) > 0): ?>


                <?php foreach ($classes as $class): ?>


                    <?php

                    $remaining =
                        $class["max_students"]
                        - $class["registered_count"];

                    ?>


                    <tr>


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


                            <?php if ($remaining > 0): ?>


                                <span class="available">

                                    Còn
                                    <?php echo $remaining; ?>
                                    chỗ

                                </span>


                            <?php else: ?>


                                <span class="full">

                                    Đã đầy

                                </span>


                            <?php endif; ?>


                        </td>


                        <td>


                            <?php if ($remaining > 0): ?>


                                <form
                                    method="POST"
                                    style="display:inline;"
                                >

                                    <input
                                        type="hidden"
                                        name="class_id"
                                        value="<?php echo $class["id"]; ?>"
                                    >


                                    <button
                                        type="submit"
                                        name="register"
                                        class="btn btn-register"
                                    >
                                        Đăng ký
                                    </button>

                                </form>


                            <?php else: ?>


                                <span class="full">
                                    Hết chỗ
                                </span>


                            <?php endif; ?>


                        </td>


                    </tr>


                <?php endforeach; ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="6"
                        style="text-align:center;"
                    >
                        Chưa có lớp học phần.
                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>

    </div>


    <!-- =========================
         LỊCH SỬ ĐĂNG KÝ
    ========================== -->

    <div class="box">

        <h2>
            Lịch sử đăng ký
        </h2>


        <table>

            <thead>

                <tr>

                    <th>Mã lớp</th>

                    <th>Học phần</th>

                    <th>Học kỳ</th>

                    <th>Giảng viên</th>

                    <th>Ngày đăng ký</th>

                    <th>Thao tác</th>

                </tr>

            </thead>


            <tbody>


            <?php if (count($registrations) > 0): ?>


                <?php foreach ($registrations as $registration): ?>


                    <tr>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $registration["class_code"]
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $registration["course_code"]
                            );

                            ?>

                            -

                            <?php

                            echo htmlspecialchars(
                                $registration["course_name"]
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $registration["semester_name"]
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $registration["teacher_name"]
                            );

                            ?>

                        </td>


                        <td>

                            <?php

                            echo htmlspecialchars(
                                $registration["registered_at"]
                            );

                            ?>

                        </td>


                        <td>


                            <form
                                method="POST"
                                style="display:inline;"
                            >

                                <input
                                    type="hidden"
                                    name="registration_id"
                                    value="<?php echo $registration["id"]; ?>"
                                >


                                <button
                                    type="submit"
                                    name="cancel"
                                    class="btn btn-cancel"

                                    onclick="return confirm('Bạn có chắc muốn hủy đăng ký lớp này?');"
                                >

                                    Hủy đăng ký

                                </button>

                            </form>


                        </td>


                    </tr>


                <?php endforeach; ?>


            <?php else: ?>


                <tr>

                    <td
                        colspan="6"
                        style="text-align:center;"
                    >

                        Bạn chưa đăng ký lớp học phần nào.

                    </td>

                </tr>


            <?php endif; ?>


            </tbody>

        </table>

    </div>


</div>


</body>

</html>