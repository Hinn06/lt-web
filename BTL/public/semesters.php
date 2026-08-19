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

// Chỉ Admin được quản lý học kỳ
if ($_SESSION["role"] !== "admin") {
    die("Bạn không có quyền truy cập trang này.");
}

$message = "";


// =========================
// THÊM HỌC KỲ
// =========================

if (isset($_POST["add_semester"])) {

    $name = trim($_POST["name"] ?? "");
    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";

    $start = DateTime::createFromFormat("!Y-m-d", $start_date);
    $end = DateTime::createFromFormat("!Y-m-d", $end_date);
    $valid_start = $start && $start->format("Y-m-d") === $start_date;
    $valid_end = $end && $end->format("Y-m-d") === $end_date;

    if ($name === "" || $start_date === "" || $end_date === "") {

        $message = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (strlen($name) < 2 || strlen($name) > 100) {

        $message = "Tên học kỳ phải từ 2-100 ký tự.";

    } elseif (!$valid_start || !$valid_end) {

        $message = "Ngày bắt đầu hoặc ngày kết thúc không hợp lệ.";

    } elseif ($start > $end) {

        $message = "Ngày bắt đầu không được lớn hơn ngày kết thúc.";

    } else {

        $stmt = $pdo->prepare(
            "INSERT INTO semesters
            (name, start_date, end_date, status)
            VALUES
            (:name, :start_date, :end_date, 1)"
        );

        $stmt->execute([
            ":name" => $name,
            ":start_date" => $start_date,
            ":end_date" => $end_date
        ]);

        $message = "Thêm học kỳ thành công.";
    }
}


// =========================
// SỬA HỌC KỲ
// =========================

if (isset($_POST["edit_semester"])) {

    $id = (int) ($_POST["id"] ?? 0);

    $name = trim($_POST["name"] ?? "");
    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";

    $start = DateTime::createFromFormat("!Y-m-d", $start_date);
    $end = DateTime::createFromFormat("!Y-m-d", $end_date);
    $valid_start = $start && $start->format("Y-m-d") === $start_date;
    $valid_end = $end && $end->format("Y-m-d") === $end_date;

    if ($id <= 0 || $name === "" || $start_date === "" || $end_date === "") {

        $message = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (strlen($name) < 2 || strlen($name) > 100) {

        $message = "Tên học kỳ phải từ 2-100 ký tự.";

    } elseif (!$valid_start || !$valid_end) {

        $message = "Ngày bắt đầu hoặc ngày kết thúc không hợp lệ.";

    } elseif ($start > $end) {

        $message = "Ngày bắt đầu không được lớn hơn ngày kết thúc.";

    } else {

        $stmt = $pdo->prepare(
            "UPDATE semesters
             SET name = :name,
                 start_date = :start_date,
                 end_date = :end_date
             WHERE id = :id"
        );

        $stmt->execute([
            ":name" => $name,
            ":start_date" => $start_date,
            ":end_date" => $end_date,
            ":id" => $id
        ]);

        $message = "Cập nhật học kỳ thành công.";
    }
}


// =========================
// KHÓA / MỞ HỌC KỲ
// =========================

if (isset($_GET["toggle"])) {

    $id = (int) $_GET["toggle"];

    if ($id > 0) {

        $stmt = $pdo->prepare(
            "UPDATE semesters
             SET status = IF(status = 1, 0, 1)
             WHERE id = :id"
        );

        $stmt->execute([
            ":id" => $id
        ]);
    }

    header("Location: semesters.php");
    exit;
}


// =========================
// XÓA HỌC KỲ
// =========================

if (isset($_GET["delete"])) {

    $id = (int) $_GET["delete"];

    if ($id > 0) {

        $stmt = $pdo->prepare(
            "DELETE FROM semesters
             WHERE id = :id"
        );

        $stmt->execute([
            ":id" => $id
        ]);
    }

    header("Location: semesters.php");
    exit;
}


// =========================
// LẤY HỌC KỲ ĐANG SỬA
// =========================

$editSemester = null;

if (isset($_GET["edit"])) {

    $id = (int) $_GET["edit"];

    $stmt = $pdo->prepare(
        "SELECT * FROM semesters
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $editSemester = $stmt->fetch();
}


// =========================
// LẤY DANH SÁCH HỌC KỲ
// =========================

$stmt = $pdo->prepare(
    "SELECT * FROM semesters
     ORDER BY id DESC"
);

$stmt->execute();

$semesters = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý học kỳ</title>

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

        .back {
            color: #6f9bd4;
            text-decoration: none;
        }

        .container {
            max-width: 1150px;
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

        input {
            padding: 11px;

            border: 1px solid #d8e5f5;
            border-radius: 7px;

            outline: none;

            background: #f8fbff;
        }

        input:focus {
            border-color: #94b4dc;
        }

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

        @media (max-width: 700px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 20px;
            }

            table {
                font-size: 12px;
            }

        }

    </style>

</head>

<body>


<header class="header">

    <h1>
        QUẢN LÝ HỌC KỲ
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

            <?php echo htmlspecialchars($message); ?>

        </div>

    <?php endif; ?>


    <!-- =========================
         FORM THÊM / SỬA
    ========================== -->

    <div class="box">

        <?php if ($editSemester): ?>

            <h2>
                ✏️ Sửa học kỳ
            </h2>

            <form method="POST">

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $editSemester["id"]; ?>"
                >

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Tên học kỳ
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="<?php echo htmlspecialchars($editSemester["name"]); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Ngày bắt đầu
                        </label>

                        <input
                            type="date"
                            name="start_date"
                            value="<?php echo $editSemester["start_date"]; ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Ngày kết thúc
                        </label>

                        <input
                            type="date"
                            name="end_date"
                            value="<?php echo $editSemester["end_date"]; ?>"
                            required
                        >

                    </div>

                </div>


                <button
                    type="submit"
                    name="edit_semester"
                    class="btn btn-main"
                >
                    LƯU THAY ĐỔI
                </button>


                <a
                    href="semesters.php"
                    class="btn btn-cancel"
                >
                    HỦY
                </a>

            </form>


        <?php else: ?>


            <h2>
                + Thêm học kỳ
            </h2>

            <form method="POST">

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Tên học kỳ
                        </label>

                        <input
                            type="text"
                            name="name"
                            placeholder="VD: Học kỳ 1 năm 2026-2027"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Ngày bắt đầu
                        </label>

                        <input
                            type="date"
                            name="start_date"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Ngày kết thúc
                        </label>

                        <input
                            type="date"
                            name="end_date"
                            required
                        >

                    </div>

                </div>


                <button
                    type="submit"
                    name="add_semester"
                    class="btn btn-main"
                >
                    THÊM HỌC KỲ
                </button>

            </form>

        <?php endif; ?>

    </div>


    <!-- =========================
         DANH SÁCH HỌC KỲ
    ========================== -->

    <div class="box">

        <h2>
            Danh sách học kỳ
        </h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Tên học kỳ</th>

                    <th>Ngày bắt đầu</th>

                    <th>Ngày kết thúc</th>

                    <th>Trạng thái</th>

                    <th>Thao tác</th>

                </tr>

            </thead>


            <tbody>

            <?php if (count($semesters) > 0): ?>

                <?php foreach ($semesters as $semester): ?>

                    <tr>

                        <td>
                            <?php echo $semester["id"]; ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $semester["name"]
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $semester["start_date"]
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $semester["end_date"]
                            );
                            ?>
                        </td>


                        <td>

                            <?php if ($semester["status"] == 1): ?>

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
                                href="semesters.php?edit=<?php echo $semester["id"]; ?>"
                                class="btn btn-edit"
                            >
                                Sửa
                            </a>


                            <a
                                href="semesters.php?toggle=<?php echo $semester["id"]; ?>"
                                class="btn btn-toggle"
                                onclick="return confirm('Bạn có chắc muốn thay đổi trạng thái học kỳ này?');"
                            >

                                <?php

                                echo $semester["status"] == 1
                                    ? "Khóa"
                                    : "Mở";

                                ?>

                            </a>


                            <a
                                href="semesters.php?delete=<?php echo $semester["id"]; ?>"
                                class="btn btn-delete"
                                onclick="return confirm('Bạn có chắc muốn xóa học kỳ này?');"
                            >
                                Xóa
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td
                        colspan="6"
                        style="text-align:center;"
                    >
                        Chưa có học kỳ nào.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>


</div>

</body>

</html>