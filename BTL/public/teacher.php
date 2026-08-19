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

// Chỉ Giảng viên
if ($_SESSION["role"] !== "teacher") {
    die("Bạn không có quyền truy cập trang này.");
}

$teacher_id = $_SESSION["user_id"];

// =========================
// LẤY CÁC LỚP GIẢNG VIÊN ĐƯỢC PHÂN CÔNG
// =========================

$stmt = $pdo->prepare(
    "SELECT
        classes.id,
        classes.class_code,
        classes.max_students,
        classes.status,

        courses.code AS course_code,
        courses.name AS course_name,

        semesters.name AS semester_name,

        (
            SELECT COUNT(*)
            FROM registrations
            WHERE registrations.class_id = classes.id
        ) AS student_count

    FROM classes

    INNER JOIN courses
        ON classes.course_id = courses.id

    INNER JOIN semesters
        ON classes.semester_id = semesters.id

    WHERE classes.teacher_id = :teacher_id

    ORDER BY classes.id DESC"
);

$stmt->execute([
    ":teacher_id" => $teacher_id
]);

$classes = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

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

            box-shadow: 0 2px 10px rgba(80,130,190,.08);
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
            margin-left: 15px;
            color: #c98282;
            text-decoration: none;
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

            box-shadow:
                0 5px 20px rgba(80,130,190,.08);

            margin-bottom: 25px;
        }

        .welcome h2 {
            margin-top: 0;
            color: #6f9bd4;
        }

        .class-box {
            background: white;
            padding: 25px;

            border-radius: 14px;

            box-shadow:
                0 5px 20px rgba(80,130,190,.08);
        }

        .class-box h2 {
            margin-top: 0;
            color: #6f9bd4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th {
            padding: 13px;
            text-align: left;

            background: #e5effc;
            color: #6485ad;
        }

        td {
            padding: 13px;

            border-bottom: 1px solid #edf2f8;
        }

        tr:hover {
            background: #f8fbff;
        }

        .status-open {
            color: #65a681;
            font-weight: bold;
        }

        .status-locked {
            color: #c98282;
            font-weight: bold;
        }

        .btn {
            display: inline-block;

            padding: 8px 13px;

            background: #94b4dc;
            color: white;

            text-decoration: none;

            border-radius: 7px;

            font-size: 13px;
        }

        .btn:hover {
            background: #7fa5d2;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #8ba4c5;
        }

        @media (max-width: 800px) {

            .header {
                padding: 20px;
            }

            .header h1 {
                font-size: 18px;
            }

            table {
                font-size: 12px;
            }

            th,
            td {
                padding: 8px;
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
            echo htmlspecialchars(
                $_SESSION["full_name"]
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


    <div class="welcome">

        <h2>
            Trang giảng viên
        </h2>

        <p>
            Chào mừng giảng viên đến với hệ thống
            quản lý học phần.
        </p>

        <p>
            Tại đây bạn có thể xem các lớp học phần
            được phân công, danh sách sinh viên
            và quản lý điểm.
        </p>

    </div>


    <div class="class-box">

        <h2>
            Lớp học phần được phân công
        </h2>


        <?php if (count($classes) > 0): ?>

            <table>

                <thead>

                    <tr>

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
                            Sinh viên
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

                                <span class="status-locked">
                                    Đã khóa
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <a
                                href="teacher_class.php?id=<?php echo $class["id"]; ?>"
                                class="btn"
                            >
                                Xem chi tiết
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php else: ?>

            <div class="empty">

                Bạn chưa được phân công lớp học phần nào.

            </div>

        <?php endif; ?>


    </div>

</div>

</body>

</html>