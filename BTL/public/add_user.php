<?php

session_start();

require_once "../config/database.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: index.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $full_name = trim($_POST["full_name"] ?? "");
    $password = $_POST["password"] ?? "";
    $role = $_POST["role"] ?? "";
    
    $allowed_roles = ["admin", "teacher", "student"];

    if ($username === "" || $full_name === "" || $password === "" || $role === "") {

        $message = "Vui lòng nhập đầy đủ thông tin.";

    } elseif (!preg_match('/^[A-Za-z0-9_.-]{3,50}$/', $username)) {

        $message = "Tên đăng nhập phải từ 3-50 ký tự và chỉ gồm chữ, số, dấu chấm, gạch dưới hoặc gạch ngang.";

    } elseif (strlen($full_name) < 2 || strlen($full_name) > 100) {

        $message = "Họ và tên phải từ 2-100 ký tự.";

    } elseif (!preg_match('/^[\\p{L}\\p{M} .\'-]+$/u', $full_name)) {

        $message = "Họ và tên chứa ký tự không hợp lệ.";

    } elseif (strlen($password) < 6 || strlen($password) > 100) {

        $message = "Mật khẩu phải từ 6-100 ký tự.";

    } elseif (!in_array($role, $allowed_roles, true)) {

        $message = "Vai trò không hợp lệ.";

    } else {

        // Kiểm tra tài khoản đã tồn tại
        $check = $pdo->prepare(
            "SELECT id FROM users WHERE username = :username"
        );

        $check->execute([
            ":username" => $username
        ]);

        if ($check->fetch()) {

            $message = "Tên đăng nhập đã tồn tại.";

        } else {

            // Mã hóa mật khẩu
            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            // Thêm tài khoản
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

            header("Location: users.php");
            exit;
        }
    }
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <title>Thêm tài khoản</title>

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

        .container {
            width: 500px;
            margin: 60px auto;
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 5px 20px rgba(80,130,190,0.08);
        }

        h1 {
            text-align: center;
            color: #6f9bd4;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
            color: #6485ad;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #d8e5f5;
            border-radius: 8px;
            background: #f8fbff;
        }

        button {
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            background: #94b4dc;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #7fa5d2;
        }

        .back {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #6f9bd4;
            text-decoration: none;
        }

        .error {
            background: #fff1f1;
            color: #c98282;
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 6px;
        }

    </style>

</head>

<body>

<div class="container">

    <h1>Thêm tài khoản</h1>

    <?php if ($message !== ""): ?>

        <div class="error">
            <?php echo htmlspecialchars($message); ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <label>Tên đăng nhập</label>

        <input
            type="text"
            name="username"
            placeholder="Nhập tên đăng nhập"
            required
        >

        <label>Họ và tên</label>

        <input
            type="text"
            name="full_name"
            placeholder="Nhập họ và tên"
            required
        >

        <label>Mật khẩu</label>

        <input
            type="password"
            name="password"
            placeholder="Nhập mật khẩu"
            required
        >

        <label>Vai trò</label>

        <select name="role" required>

            <option value="">-- Chọn vai trò --</option>

            <option value="admin">Admin</option>

            <option value="teacher">Giảng viên</option>

            <option value="student">Sinh viên</option>

        </select>

        <button type="submit">
            THÊM TÀI KHOẢN
        </button>

    </form>

    <a href="users.php" class="back">
        ← Quay lại danh sách
    </a>

</div>

</body>

</html>