<?php

session_start();

// Kiểm tra đã đăng nhập chưa
if (!isset($_SESSION["user_id"])) {
    header("Location: index.php");
    exit;
}

// Chỉ Admin mới được vào
if ($_SESSION["role"] !== "admin") {
    die("Bạn không có quyền truy cập trang Admin.");
}

?>

<!DOCTYPE html>
<html lang="vi">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Trang Admin</title>

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
                0 2px 10px rgba(80, 130, 190, 0.08);

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

        .container {
            max-width: 1100px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .welcome {
            background: white;
            padding: 25px;
            border-radius: 14px;

            margin-bottom: 25px;

            box-shadow:
                0 5px 20px rgba(80, 130, 190, 0.08);
        }

        .welcome h2 {
            margin-top: 0;
            color: #6f9bd4;
        }

        .menu {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }

        .menu-item {
            background: white;
            padding: 25px;
            border-radius: 14px;

            box-shadow:
                0 5px 20px rgba(80, 130, 190, 0.08);

            text-align: center;
        }

        .menu-item h3 {
            color: #6f9bd4;
            margin-top: 0;
        }

        .menu-item p {
            font-size: 14px;
            line-height: 1.5;
            color: #8199b5;
            min-height: 45px;
        }

        .btn {
            display: inline-block;

            padding: 10px 18px;

            background: #94b4dc;
            color: white;

            text-decoration: none;
            border-radius: 7px;

            font-size: 14px;
        }

        .btn:hover {
            background: #7fa5d2;
        }

        .logout {
            color: #c98282;
            text-decoration: none;
            margin-left: 15px;
        }

        @media (max-width: 800px) {

            .menu {
                grid-template-columns: 1fr;
            }

            .header {
                padding: 20px;
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


    <div class="welcome">

        <h2>
            Trang quản trị Admin
        </h2>

        <p>
            Chào mừng bạn đến với hệ thống quản lý khóa học
            và đăng ký học phần.
        </p>

        <p>
            Bạn đang đăng nhập với quyền:
            <strong>Quản trị viên</strong>
        </p>

    </div>


    <div class="menu">


        <!-- =========================
             QUẢN LÝ TÀI KHOẢN
        ========================== -->

        <div class="menu-item">

            <h3>
                Quản lý tài khoản
            </h3>

            <p>
                Quản lý tài khoản và vai trò
                của người dùng trong hệ thống.
            </p>

            <a
                href="users.php"
                class="btn"
            >
                Quản lý
            </a>

        </div>


        <!-- =========================
             QUẢN LÝ HỌC KỲ
        ========================== -->

        <div class="menu-item">

            <h3>
                Quản lý học kỳ
            </h3>

            <p>
                Thêm, sửa, xóa và quản lý
                các học kỳ.
            </p>

            <a
                href="semesters.php"
                class="btn"
            >
                Quản lý
            </a>

        </div>


        <!-- =========================
             QUẢN LÝ HỌC PHẦN
        ========================== -->

        <div class="menu-item">

            <h3>
                Quản lý học phần
            </h3>

            <p>
                Quản lý thông tin các học phần
                trong hệ thống.
            </p>

            <a
                href="courses.php"
                class="btn"
            >
                Quản lý
            </a>

        </div>


        <!-- =========================
             QUẢN LÝ LỚP HỌC PHẦN
        ========================== -->

        <div class="menu-item">

            <h3>
                Quản lý lớp học phần
            </h3>

            <p>
                Tạo lớp, gán giảng viên,
                thay đổi sĩ số và trạng thái lớp.
            </p>

            <a
                href="classes.php"
                class="btn"
            >
                Quản lý
            </a>

        </div>


        <!-- =========================
             TÌM KIẾM & LỌC
        ========================== -->

        <div class="menu-item">

            <h3>
                Tìm kiếm & lọc
            </h3>

            <p>
                Tìm kiếm và lọc lớp học phần
                theo các tiêu chí.
            </p>

            <a
                href="classes.php"
                class="btn"
            >
                Tìm kiếm
            </a>

        </div>


        <!-- =========================
             DUYỆT YÊU CẦU
        ========================== -->

        <div class="menu-item">

            <h3>
                Quản lý đăng ký
            </h3>

            <p>
                Xem và quản lý tình trạng
                đăng ký học phần của sinh viên.
            </p>

            <a
                href="registrations.php"
                class="btn"
            >
                Xem đăng ký
            </a>

        </div>


    </div>

</div>


</body>

</html>