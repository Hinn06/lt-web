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

// Chỉ Admin được quản lý học phần
if ($_SESSION["role"] !== "admin") {
    die("Bạn không có quyền truy cập trang này.");
}

$message = "";


// =========================
// THÊM HỌC PHẦN
// =========================

if (isset($_POST["add_course"])) {

    $code = trim($_POST["code"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $credits_raw = trim($_POST["credits"] ?? "");
    $credits = filter_var($credits_raw, FILTER_VALIDATE_INT);
    $description = trim($_POST["description"] ?? "");

    if ($code === "" || $name === "" || $credits_raw === "") {

        $message = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (!preg_match('/^[A-Za-z0-9_-]{2,20}$/', $code)) {

        $message = "Mã học phần phải từ 2-20 ký tự và chỉ gồm chữ, số, gạch dưới hoặc gạch ngang.";

    } elseif (strlen($name) < 2 || strlen($name) > 150) {

        $message = "Tên học phần phải từ 2-150 ký tự.";

    } elseif ($credits === false || $credits < 1 || $credits > 6) {

        $message = "Số tín chỉ phải là số nguyên từ 1 đến 6.";

    } else {

        try {

            $stmt = $pdo->prepare(
                "INSERT INTO courses
                (code, name, credits, description, status)
                VALUES
                (:code, :name, :credits, :description, 1)"
            );

            $stmt->execute([
                ":code" => $code,
                ":name" => $name,
                ":credits" => $credits,
                ":description" => $description
            ]);

            $message = "Thêm học phần thành công.";

        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                $message = "Mã học phần đã tồn tại.";
            } else {
                $message = "Không thể thêm học phần.";
            }
        }
    }
}


// =========================
// SỬA HỌC PHẦN
// =========================

if (isset($_POST["edit_course"])) {

    $id = (int) ($_POST["id"] ?? 0);

    $code = trim($_POST["code"] ?? "");
    $name = trim($_POST["name"] ?? "");
    $credits_raw = trim($_POST["credits"] ?? "");
    $credits = filter_var($credits_raw, FILTER_VALIDATE_INT);
    $description = trim($_POST["description"] ?? "");

    if (
        $id <= 0 ||
        $code === "" ||
        $name === "" ||
        $credits_raw === ""
    ) {

        $message = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (!preg_match('/^[A-Za-z0-9_-]{2,20}$/', $code)) {

        $message = "Mã học phần phải từ 2-20 ký tự và chỉ gồm chữ, số, gạch dưới hoặc gạch ngang.";

    } elseif (strlen($name) < 2 || strlen($name) > 150) {

        $message = "Tên học phần phải từ 2-150 ký tự.";

    } elseif ($credits === false || $credits < 1 || $credits > 6) {

        $message = "Số tín chỉ phải là số nguyên từ 1 đến 6.";

    } else {

        try {

            $stmt = $pdo->prepare(
                "UPDATE courses
                 SET code = :code,
                     name = :name,
                     credits = :credits,
                     description = :description
                 WHERE id = :id"
            );

            $stmt->execute([
                ":code" => $code,
                ":name" => $name,
                ":credits" => $credits,
                ":description" => $description,
                ":id" => $id
            ]);

            $message = "Cập nhật học phần thành công.";

        } catch (PDOException $e) {

            if ($e->getCode() == 23000) {
                $message = "Mã học phần đã tồn tại.";
            } else {
                $message = "Không thể cập nhật học phần.";
            }
        }
    }
}


// =========================
// KHÓA / MỞ HỌC PHẦN
// =========================

if (isset($_GET["toggle"])) {

    $id = (int) $_GET["toggle"];

    if ($id > 0) {

        $stmt = $pdo->prepare(
            "UPDATE courses
             SET status = IF(status = 1, 0, 1)
             WHERE id = :id"
        );

        $stmt->execute([
            ":id" => $id
        ]);
    }

    header("Location: courses.php");
    exit;
}


// =========================
// XÓA HỌC PHẦN
// =========================

if (isset($_GET["delete"])) {

    $id = (int) $_GET["delete"];

    if ($id > 0) {

        $stmt = $pdo->prepare(
            "DELETE FROM courses
             WHERE id = :id"
        );

        $stmt->execute([
            ":id" => $id
        ]);
    }

    header("Location: courses.php");
    exit;
}


// =========================
// LẤY HỌC PHẦN ĐANG SỬA
// =========================

$editCourse = null;

if (isset($_GET["edit"])) {

    $id = (int) $_GET["edit"];

    $stmt = $pdo->prepare(
        "SELECT *
         FROM courses
         WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $editCourse = $stmt->fetch();
}


// =========================
// LẤY DANH SÁCH HỌC PHẦN
// =========================

$stmt = $pdo->prepare(
    "SELECT *
     FROM courses
     ORDER BY id DESC"
);

$stmt->execute();

$courses = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý học phần</title>

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
            max-width: 1200px;
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

        .full {
            grid-column: 1 / -1;
        }

        label {
            margin-bottom: 7px;

            font-weight: bold;
            color: #6485ad;
            font-size: 14px;
        }

        input,
        textarea {
            padding: 11px;

            border: 1px solid #d8e5f5;
            border-radius: 7px;

            outline: none;

            background: #f8fbff;

            font-family: Arial, sans-serif;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        input:focus,
        textarea:focus {
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

        .description {
            max-width: 250px;
        }

        @media (max-width: 800px) {

            .form-grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: auto;
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
        QUẢN LÝ HỌC PHẦN
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

        <?php if ($editCourse): ?>

            <h2>
                ✏️ Sửa học phần
            </h2>

            <form method="POST">

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $editCourse["id"]; ?>"
                >

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Mã học phần
                        </label>

                        <input
                            type="text"
                            name="code"
                            value="<?php echo htmlspecialchars($editCourse["code"]); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Tên học phần
                        </label>

                        <input
                            type="text"
                            name="name"
                            value="<?php echo htmlspecialchars($editCourse["name"]); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Số tín chỉ
                        </label>

                        <input
                            type="number"
                            name="credits"
                            min="1"
                            value="<?php echo $editCourse["credits"]; ?>"
                            required
                        >

                    </div>


                    <div class="form-group full">

                        <label>
                            Mô tả
                        </label>

                        <textarea
                            name="description"
                            placeholder="Nhập mô tả học phần..."
                        ><?php echo htmlspecialchars($editCourse["description"] ?? ""); ?></textarea>

                    </div>

                </div>


                <button
                    type="submit"
                    name="edit_course"
                    class="btn btn-main"
                >
                    LƯU THAY ĐỔI
                </button>


                <a
                    href="courses.php"
                    class="btn btn-cancel"
                >
                    HỦY
                </a>

            </form>


        <?php else: ?>


            <h2>
                + Thêm học phần
            </h2>

            <form method="POST">

                <div class="form-grid">

                    <div class="form-group">

                        <label>
                            Mã học phần
                        </label>

                        <input
                            type="text"
                            name="code"
                            placeholder="VD: WEB101"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Tên học phần
                        </label>

                        <input
                            type="text"
                            name="name"
                            placeholder="VD: Lập trình Web"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Số tín chỉ
                        </label>

                        <input
                            type="number"
                            name="credits"
                            min="1"
                            placeholder="VD: 3"
                            required
                        >

                    </div>


                    <div class="form-group full">

                        <label>
                            Mô tả
                        </label>

                        <textarea
                            name="description"
                            placeholder="Nhập mô tả học phần..."
                        ></textarea>

                    </div>

                </div>


                <button
                    type="submit"
                    name="add_course"
                    class="btn btn-main"
                >
                    THÊM HỌC PHẦN
                </button>

            </form>

        <?php endif; ?>

    </div>


    <!-- =========================
         DANH SÁCH HỌC PHẦN
    ========================== -->

    <div class="box">

        <h2>
            Danh sách học phần
        </h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>

                    <th>Mã HP</th>

                    <th>Tên học phần</th>

                    <th>Tín chỉ</th>

                    <th>Mô tả</th>

                    <th>Trạng thái</th>

                    <th>Thao tác</th>

                </tr>

            </thead>


            <tbody>

            <?php if (count($courses) > 0): ?>

                <?php foreach ($courses as $course): ?>

                    <tr>

                        <td>
                            <?php echo $course["id"]; ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $course["code"]
                            );
                            ?>
                        </td>


                        <td>
                            <?php
                            echo htmlspecialchars(
                                $course["name"]
                            );
                            ?>
                        </td>


                        <td>
                            <?php echo $course["credits"]; ?>
                        </td>


                        <td class="description">

                            <?php

                            echo htmlspecialchars(
                                $course["description"] ?? ""
                            );

                            ?>

                        </td>


                        <td>

                            <?php if ($course["status"] == 1): ?>

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
                                href="courses.php?edit=<?php echo $course["id"]; ?>"
                                class="btn btn-edit"
                            >
                                Sửa
                            </a>


                            <a
                                href="courses.php?toggle=<?php echo $course["id"]; ?>"
                                class="btn btn-toggle"
                                onclick="return confirm('Bạn có chắc muốn thay đổi trạng thái học phần này?');"
                            >

                                <?php

                                echo $course["status"] == 1
                                    ? "Khóa"
                                    : "Mở";

                                ?>

                            </a>


                            <a
                                href="courses.php?delete=<?php echo $course["id"]; ?>"
                                class="btn btn-delete"
                                onclick="return confirm('Bạn có chắc muốn xóa học phần này?');"
                            >
                                Xóa
                            </a>

                        </td>

                    </tr>

                <?php endforeach; ?>

            <?php else: ?>

                <tr>

                    <td
                        colspan="7"
                        style="text-align:center;"
                    >
                        Chưa có học phần nào.
                    </td>

                </tr>

            <?php endif; ?>

            </tbody>

        </table>

    </div>


</div>

</body>

</html>