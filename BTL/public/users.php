<?php

session_start();

require_once "../config/database.php";

// =========================
// KIỂM TRA QUYỀN
// =========================

if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

if ($_SESSION["role"] !== "admin") {
    die("Bạn không có quyền truy cập.");
}

$message = "";


// =========================
// THÊM TÀI KHOẢN
// =========================

if (isset($_POST["add_user"])) {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $full_name = trim($_POST["full_name"] ?? "");
    $role = $_POST["role"] ?? "student";

    $allowed_roles = ["admin", "teacher", "student"];

    if ($username === "" || $password === "" || $full_name === "") {

        $message = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {

        $message = "Tên đăng nhập phải từ 3-50 ký tự và chỉ gồm chữ, số, dấu chấm, gạch dưới hoặc gạch ngang.";

    } elseif (strlen($full_name) < 2 || strlen($full_name) > 100) {

        $message = "Họ và tên phải từ 2-100 ký tự.";

    } elseif (!preg_match('/^[\p{L}\p{M} .\'-]+$/u', $full_name)) {

        $message = "Họ và tên chứa ký tự không hợp lệ.";

    } elseif (strlen($password) < 6 || strlen($password) > 100) {

        $message = "Mật khẩu phải từ 6-100 ký tự.";

    } elseif (!in_array($role, $allowed_roles, true)) {

        $message = "Vai trò không hợp lệ.";

    } else {

        $check = $pdo->prepare(
            "SELECT id FROM users WHERE username = :username"
        );

        $check->execute([
            ":username" => $username
        ]);

        if ($check->fetch()) {

            $message = "Tên đăng nhập đã tồn tại.";

        } else {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql = "INSERT INTO users
                    (username, password, full_name, role, status)
                    VALUES
                    (:username, :password, :full_name, :role, 1)";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":username" => $username,
                ":password" => $hashedPassword,
                ":full_name" => $full_name,
                ":role" => $role
            ]);

            $message = "Thêm tài khoản thành công.";
        }
    }
}


// =========================
// SỬA TÀI KHOẢN
// =========================

if (isset($_POST["edit_user"])) {

    $id = (int) ($_POST["id"] ?? 0);

    $full_name = trim($_POST["full_name"] ?? "");
    $role = $_POST["role"] ?? "student";
    $password = $_POST["password"] ?? "";

    $allowed_roles = ["admin", "teacher", "student"];

    if ($id <= 0 || $full_name === "") {

        $message = "Thông tin tài khoản không hợp lệ.";

    } elseif (strlen($full_name) < 2 || strlen($full_name) > 100) {

        $message = "Họ và tên phải từ 2-100 ký tự.";

    } elseif (!preg_match('/^[\p{L}\p{M} .\'-]+$/u', $full_name)) {

        $message = "Họ và tên chứa ký tự không hợp lệ.";

    } elseif (!in_array($role, $allowed_roles, true)) {

        $message = "Vai trò không hợp lệ.";

    } elseif ($password !== "" && (strlen($password) < 6 || strlen($password) > 100)) {

        $message = "Mật khẩu mới phải từ 6-100 ký tự.";

    } else {

        // Nếu nhập mật khẩu mới
        if ($password !== "") {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $sql = "UPDATE users
                    SET full_name = :full_name,
                        role = :role,
                        password = :password
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":full_name" => $full_name,
                ":role" => $role,
                ":password" => $hashedPassword,
                ":id" => $id
            ]);

        } else {

            // Không nhập mật khẩu thì giữ mật khẩu cũ
            $sql = "UPDATE users
                    SET full_name = :full_name,
                        role = :role
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);

            $stmt->execute([
                ":full_name" => $full_name,
                ":role" => $role,
                ":id" => $id
            ]);
        }

        // Nếu sửa chính tài khoản đang đăng nhập
        if ($id == $_SESSION["user_id"]) {

            $_SESSION["full_name"] = $full_name;
            $_SESSION["role"] = $role;
        }

        $message = "Cập nhật tài khoản thành công.";
    }
}


// =========================
// KHÓA / MỞ TÀI KHOẢN
// =========================

if (isset($_GET["toggle"])) {

    $id = (int) $_GET["toggle"];

    // Không cho tự khóa mình
    if ($id != $_SESSION["user_id"]) {

        $stmt = $pdo->prepare(
            "UPDATE users
             SET status = IF(status = 1, 0, 1)
             WHERE id = :id"
        );

        $stmt->execute([
            ":id" => $id
        ]);
    }

    header("Location: users.php");
    exit;
}


// =========================
// XÓA TÀI KHOẢN
// =========================

if (isset($_GET["delete"])) {

    $id = (int) $_GET["delete"];

    // Không cho tự xóa mình
    if ($id != $_SESSION["user_id"]) {

        $stmt = $pdo->prepare(
            "DELETE FROM users
             WHERE id = :id"
        );

        $stmt->execute([
            ":id" => $id
        ]);
    }

    header("Location: users.php");
    exit;
}


// =========================
// LẤY TÀI KHOẢN ĐANG SỬA
// =========================

$editUser = null;

if (isset($_GET["edit"])) {

    $id = (int) $_GET["edit"];

    $stmt = $pdo->prepare(
        "SELECT * FROM users WHERE id = :id"
    );

    $stmt->execute([
        ":id" => $id
    ]);

    $editUser = $stmt->fetch();
}


// =========================
// LẤY DANH SÁCH
// =========================

$stmt = $pdo->prepare(
    "SELECT * FROM users ORDER BY id DESC"
);

$stmt->execute();

$users = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Quản lý tài khoản</title>

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

            box-shadow: 0 5px 20px rgba(80,130,190,.08);

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

        .btn {
            display: inline-block;

            padding: 8px 12px;

            border-radius: 6px;

            border: none;

            text-decoration: none;

            font-size: 12px;

            cursor: pointer;
        }

        .btn-add {
            margin-top: 18px;

            padding: 11px 20px;

            border: none;
            border-radius: 7px;

            background: #94b4dc;
            color: white;

            cursor: pointer;
            font-weight: bold;
        }

        .btn-add:hover {
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

        .self {
            color: #9aaabd;
            font-size: 12px;
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
        QUẢN LÝ TÀI KHOẢN
    </h1>

    <a href="admin.php" class="back">
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

        <?php if ($editUser): ?>

            <h2>
                ✏️ Sửa tài khoản
            </h2>

            <form method="POST">

                <input
                    type="hidden"
                    name="id"
                    value="<?php echo $editUser["id"]; ?>"
                >


                <div class="form-grid">


                    <div class="form-group">

                        <label>
                            Tên đăng nhập
                        </label>

                        <input
                            type="text"
                            value="<?php echo htmlspecialchars($editUser["username"]); ?>"
                            disabled
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Họ và tên
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            value="<?php echo htmlspecialchars($editUser["full_name"]); ?>"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Mật khẩu mới
                        </label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Để trống nếu không đổi"
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Vai trò
                        </label>

                        <select name="role">

                            <option
                                value="student"
                                <?php echo $editUser["role"] === "student" ? "selected" : ""; ?>
                            >
                                Sinh viên
                            </option>

                            <option
                                value="teacher"
                                <?php echo $editUser["role"] === "teacher" ? "selected" : ""; ?>
                            >
                                Giảng viên
                            </option>

                            <option
                                value="admin"
                                <?php echo $editUser["role"] === "admin" ? "selected" : ""; ?>
                            >
                                Admin
                            </option>

                        </select>

                    </div>


                </div>


                <button
                    type="submit"
                    name="edit_user"
                    class="btn-add"
                >
                    LƯU THAY ĐỔI
                </button>


                <a
                    href="users.php"
                    class="btn btn-cancel"
                >
                    HỦY
                </a>

            </form>


        <?php else: ?>


            <h2>
                + Thêm tài khoản
            </h2>

            <form method="POST">

                <div class="form-grid">


                    <div class="form-group">

                        <label>
                            Tên đăng nhập
                        </label>

                        <input
                            type="text"
                            name="username"
                            placeholder="Nhập tên đăng nhập"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Mật khẩu
                        </label>

                        <input
                            type="password"
                            name="password"
                            placeholder="Nhập mật khẩu"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Họ và tên
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            placeholder="Nhập họ và tên"
                            required
                        >

                    </div>


                    <div class="form-group">

                        <label>
                            Vai trò
                        </label>

                        <select name="role">

                            <option value="student">
                                Sinh viên
                            </option>

                            <option value="teacher">
                                Giảng viên
                            </option>

                            <option value="admin">
                                Admin
                            </option>

                        </select>

                    </div>


                </div>


                <button
                    type="submit"
                    name="add_user"
                    class="btn-add"
                >
                    THÊM TÀI KHOẢN
                </button>

            </form>

        <?php endif; ?>

    </div>


    <!-- =========================
         DANH SÁCH
    ========================== -->

    <div class="box">

        <h2>
            Danh sách tài khoản
        </h2>

        <table>

            <thead>

                <tr>

                    <th>ID</th>
                    <th>Tên đăng nhập</th>
                    <th>Họ và tên</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>

                </tr>

            </thead>


            <tbody>

            <?php foreach ($users as $user): ?>

                <tr>

                    <td>
                        <?php echo $user["id"]; ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars($user["username"]);
                        ?>
                    </td>


                    <td>
                        <?php
                        echo htmlspecialchars($user["full_name"]);
                        ?>
                    </td>


                    <td>

                        <?php

                        if ($user["role"] === "admin") {

                            echo "Admin";

                        } elseif ($user["role"] === "teacher") {

                            echo "Giảng viên";

                        } else {

                            echo "Sinh viên";

                        }

                        ?>

                    </td>


                    <td>

                        <?php if ($user["status"] == 1): ?>

                            <span class="status">
                                Hoạt động
                            </span>

                        <?php else: ?>

                            <span class="locked">
                                Đã khóa
                            </span>

                        <?php endif; ?>

                    </td>


                    <td>

                        <?php if ($user["id"] == $_SESSION["user_id"]): ?>

                            <a
                                href="users.php?edit=<?php echo $user["id"]; ?>"
                                class="btn btn-edit"
                            >
                                Sửa
                            </a>

                            <span class="self">
                                Tài khoản hiện tại
                            </span>

                        <?php else: ?>


                            <a
                                href="users.php?edit=<?php echo $user["id"]; ?>"
                                class="btn btn-edit"
                            >
                                Sửa
                            </a>


                            <a
                                href="users.php?toggle=<?php echo $user["id"]; ?>"
                                class="btn btn-toggle"
                                onclick="return confirm('Bạn có chắc muốn thay đổi trạng thái tài khoản này?');"
                            >
                                <?php
                                echo $user["status"] == 1
                                    ? "Khóa"
                                    : "Mở khóa";
                                ?>
                            </a>


                            <a
                                href="users.php?delete=<?php echo $user["id"]; ?>"
                                class="btn btn-delete"
                                onclick="return confirm('Bạn có chắc muốn xóa tài khoản này?');"
                            >
                                Xóa
                            </a>


                        <?php endif; ?>

                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>

        </table>

    </div>


</div>

</body>

</html>