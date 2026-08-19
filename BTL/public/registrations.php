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

// Chỉ Admin
if ($_SESSION["role"] !== "admin") {
    die("Bạn không có quyền truy cập trang này.");
}


// =========================
// TÌM KIẾM & LỌC
// =========================

$search = trim($_GET["search"] ?? "");

$filter_class = (int) ($_GET["class_id"] ?? 0);

$filter_semester = (int) ($_GET["semester_id"] ?? 0);


// =========================
// LẤY DANH SÁCH LỚP
// =========================

$stmt = $pdo->query("
    SELECT
        classes.id,
        classes.class_code,
        courses.code AS course_code,
        courses.name AS course_name
    FROM classes
    INNER JOIN courses
        ON classes.course_id = courses.id
    ORDER BY classes.class_code
");

$classes = $stmt->fetchAll();


// =========================
// LẤY DANH SÁCH HỌC KỲ
// =========================

$stmt = $pdo->query("
    SELECT id, name
    FROM semesters
    ORDER BY id DESC
");

$semesters = $stmt->fetchAll();


// =========================
// QUERY ĐĂNG KÝ
// =========================

$sql = "
    SELECT

        registrations.id AS registration_id,

        registrations.registered_at,

        users.username,
        users.full_name,

        classes.class_code,

        courses.code AS course_code,
        courses.name AS course_name,

        semesters.name AS semester_name

    FROM registrations

    INNER JOIN users
        ON registrations.student_id = users.id

    INNER JOIN classes
        ON registrations.class_id = classes.id

    INNER JOIN courses
        ON classes.course_id = courses.id

    INNER JOIN semesters
        ON classes.semester_id = semesters.id

    WHERE users.role = 'student'
";

$params = [];


// =========================
// TÌM KIẾM
// =========================

if ($search !== "") {

    $sql .= "
        AND (
            users.username LIKE :search
            OR users.full_name LIKE :search
            OR classes.class_code LIKE :search
            OR courses.code LIKE :search
            OR courses.name LIKE :search
        )
    ";

    $params[":search"] = "%" . $search . "%";
}


// =========================
// LỌC LỚP
// =========================

if ($filter_class > 0) {

    $sql .= "
        AND classes.id = :class_id
    ";

    $params[":class_id"] = $filter_class;
}


// =========================
// LỌC HỌC KỲ
// =========================

if ($filter_semester > 0) {

    $sql .= "
        AND classes.semester_id = :semester_id
    ";

    $params[":semester_id"] = $filter_semester;
}


// =========================
// SẮP XẾP
// =========================

$sql .= "
    ORDER BY registrations.id DESC
";


// =========================
// CHẠY QUERY
// =========================

$stmt = $pdo->prepare($sql);

$stmt->execute($params);

$registrations = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Quản lý đăng ký học phần</title>

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
            max-width: 1350px;

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

        .filter-grid {
            display: grid;

            grid-template-columns:
                2fr
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

        label {
            margin-bottom: 7px;

            font-weight: bold;

            color: #6485ad;

            font-size: 14px;
        }

        input,
        select {
            width: 100%;

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

        .student {
            font-weight: bold;

            color: #6f9bd4;
        }

        .empty {
            text-align: center;

            padding: 30px;

            color: #8199b5;
        }

        @media (max-width: 1000px) {

            .filter-grid {
                grid-template-columns: 1fr 1fr;
            }

        }

        @media (max-width: 800px) {

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
        QUẢN LÝ ĐĂNG KÝ HỌC PHẦN
    </h1>

    <a
        href="admin.php"
        class="back"
    >
        ← Quay lại Admin
    </a>

</header>


<div class="container">


    <!-- =========================
         TÌM KIẾM
    ========================== -->

    <div class="box">

        <h2>
            🔎 Tìm kiếm đăng ký
        </h2>

        <form
            method="GET"
            action="registrations.php"
        >

            <div class="filter-grid">


                <div class="filter-group">

                    <label>
                        Tìm kiếm
                    </label>

                    <input
                        type="text"
                        name="search"

                        placeholder="MSSV, tên sinh viên, mã lớp, học phần..."

                        value="<?php
                            echo htmlspecialchars($search);
                        ?>"
                    >

                </div>


                <div class="filter-group">

                    <label>
                        Lớp học phần
                    </label>

                    <select name="class_id">

                        <option value="0">
                            Tất cả lớp
                        </option>

                        <?php foreach ($classes as $class): ?>

                            <option
                                value="<?php echo $class["id"]; ?>"

                                <?php

                                if (
                                    $filter_class
                                    == $class["id"]
                                ) {
                                    echo "selected";
                                }

                                ?>
                            >

                                <?php
                                echo htmlspecialchars(
                                    $class["class_code"]
                                );
                                ?>

                                -

                                <?php
                                echo htmlspecialchars(
                                    $class["course_code"]
                                );
                                ?>

                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>


                <div class="filter-group">

                    <label>
                        Học kỳ
                    </label>

                    <select name="semester_id">

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


                <div>

                    <button
                        type="submit"
                        class="btn-search"
                    >
                        Tìm kiếm
                    </button>

                </div>


                <div>

                    <a
                        href="registrations.php"
                        class="btn-reset"
                    >
                        Xóa lọc
                    </a>

                </div>


            </div>

        </form>


        <div class="result-info">

            Có

            <strong>
                <?php echo count($registrations); ?>
            </strong>

            lượt đăng ký.

        </div>

    </div>


    <!-- =========================
         DANH SÁCH
    ========================== -->

    <div class="box">

        <h2>
            Danh sách sinh viên đăng ký
        </h2>


        <table>

            <thead>

                <tr>

                    <th>
                        STT
                    </th>

                    <th>
                        Tài khoản
                    </th>

                    <th>
                        Sinh viên
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
                        Thời gian đăng ký
                    </th>

                </tr>

            </thead>


            <tbody>


            <?php if (count($registrations) > 0): ?>

                <?php $stt = 1; ?>

                <?php foreach ($registrations as $registration): ?>

                    <tr>

                        <td>
                            <?php echo $stt++; ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $registration["username"]
                            );
                            ?>
                        </td>


                        <td>

                            <span class="student">

                                <?php
                                echo htmlspecialchars(
                                    $registration["full_name"]
                                );
                                ?>

                            </span>

                        </td>


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
                                $registration["registered_at"]
                            );
                            ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td
                        colspan="7"
                        class="empty"
                    >

                        Chưa có sinh viên đăng ký học phần.

                    </td>

                </tr>

            <?php endif; ?>


            </tbody>

        </table>

    </div>


</div>


</body>

</html>