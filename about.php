<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu Nhóm 6</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            background: #eaf4ff;
            color: #334155;
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
        }

        h1 {
            color: #3b82c4;
            font-size: 32px;
            margin-bottom: 25px;
        }

        h2 {
            color: #5b9bd5;
            margin-top: 25px;
        }

        li {
            margin: 10px 0;
            line-height: 1.5;
        }

        .members {
            background: #ffffff;
            padding: 20px 25px;
            border-radius: 14px;
            border: 1px solid #d7eafb;
            box-shadow: 0 5px 18px rgba(91, 155, 213, 0.10);
        }

        .topic-title {
            margin-top: 35px;
            margin-bottom: 15px;
            color: #4f8fc4;
        }

        .topic-card {
            background: linear-gradient(135deg, #ffffff, #f4faff);
            border-radius: 16px;
            padding: 30px;
            border: 1px solid #d7eafb;
            box-shadow: 0 8px 25px rgba(91, 155, 213, 0.15);
        }

        .topic-card h3 {
            color: #3f82b7;
            font-size: 23px;
            margin: 0 0 15px;
            line-height: 1.4;
        }

        .intro {
            color: #64748b;
            line-height: 1.7;
            margin-bottom: 25px;
        }

        .features {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .feature {
            flex: 1;
            min-width: 200px;
            background: #edf7ff;
            padding: 20px;
            border-radius: 12px;
            border: 1px solid #dceefa;
        }

        .icon {
            font-size: 30px;
            margin-bottom: 10px;
        }

        .feature h4 {
            color: #4f8fc4;
            margin: 0 0 8px;
        }

        .feature p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.6;
            margin: 0;
        }

        .back {
            display: inline-block;
            margin-top: 25px;
            color: #4f91c9;
            text-decoration: none;
            font-weight: bold;
        }

        .back:hover {
            color: #256da5;
            text-decoration: underline;
        }
    </style>
</head>

<body>

    <h1>Giới thiệu Nhóm 6</h1>

    <h2>Thành viên nhóm</h2>

    <div class="members">
        <ul>
            <li>Nguyễn Thị Trang - 224001836</li>
            <li>Tô Thị Thu Hiền - 224001785</li>
            <li>Hồ Mai Chi - 224001776</li>
        </ul>
    </div>

    <h2 class="topic-title">Đề tài 1</h2>

    <div class="topic-card">

        <h3>
            Hệ thống quản lý khóa học<br>
            và đăng ký học phần
        </h3>

        <p class="intro">
            Hệ thống được xây dựng nhằm hỗ trợ sinh viên
            tra cứu lớp học phần, đăng ký học phần và theo dõi
            lịch sử đăng ký một cách thuận tiện, nhanh chóng.
        </p>

        <div class="features">

            <div class="feature">
                <div class="icon">📚</div>

                <h4>Quản lý học phần</h4>

                <p>
                    Quản lý học kỳ, học phần và các lớp học phần.
                </p>
            </div>

            <div class="feature">
                <div class="icon">📝</div>

                <h4>Đăng ký học phần</h4>

                <p>
                    Sinh viên có thể đăng ký, hủy đăng ký
                    và xem lịch sử đăng ký.
                </p>
            </div>

            <div class="feature">
                <div class="icon">👥</div>

                <h4>Quản lý người dùng</h4>

                <p>
                    Phân quyền và quản lý sinh viên,
                    giảng viên và quản trị viên.
                </p>
            </div>

        </div>
    </div>

    <a class="back" href="index.php">← Quay lại trang chủ</a>

</body>
</html>