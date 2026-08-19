<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Đăng nhập - Quản lý học phần</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            background: #eef5ff;

            display: flex;
            justify-content: center;
            align-items: center;
        }

        .login-container {
            width: 420px;
            background: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 25px rgba(80, 130, 190, 0.12);
        }

        .logo {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo h1 {
            margin: 0;
            color: #6f9bd4;
            font-size: 27px;
        }

        .logo p {
            margin-top: 8px;
            color: #8ba4c5;
            font-size: 14px;
        }

        .title {
            color: #6f9bd4;
            font-size: 20px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            color: #6485ad;
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #d8e5f5;
            border-radius: 8px;
            outline: none;
            font-size: 14px;
            background: #f8fbff;
        }

        input:focus {
            border-color: #91b2dc;
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 8px;
            background: #94b4dc;
            color: white;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        .btn-login:hover {
            background: #7fa5d2;
        }

        .note {
            margin-top: 20px;
            padding: 12px;
            background: #f1f6fd;
            border-left: 3px solid #94b4dc;
            border-radius: 6px;
            color: #7692b4;
            font-size: 13px;
            line-height: 1.5;
        }
    </style>
</head>

<body>

    <div class="login-container">

        <div class="logo">
            <h1>QUẢN LÝ HỌC PHẦN</h1>
            <p>Hệ thống quản lý khóa học và đăng ký học phần</p>
        </div>

        <h2 class="title">Đăng nhập</h2>

        <form method="POST">

            <div class="form-group">
                <label for="username">Tên đăng nhập</label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Nhập tên đăng nhập"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Nhập mật khẩu"
                    required
                >
            </div>

            <button type="submit" class="btn-login">
                ĐĂNG NHẬP
            </button>

        </form>

        <div class="note">
            Vui lòng đăng nhập bằng tài khoản được cấp để sử dụng hệ thống.
        </div>

    </div>

</body>
</html>