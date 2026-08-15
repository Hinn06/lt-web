# HỆ THỐNG QUẢN LÝ KHÓA HỌC VÀ ĐĂNG KÝ HỌC PHẦN

## 1. Giới thiệu đề tài

**Hệ thống quản lý khóa học và đăng ký học phần** là một website được xây dựng nhằm hỗ trợ sinh viên trong việc tìm kiếm, xem thông tin và đăng ký các học phần.

Hệ thống đồng thời hỗ trợ quản trị viên quản lý khóa học, học phần, sinh viên và thông tin đăng ký học phần.

Đề tài được xây dựng với mục tiêu áp dụng kiến thức về lập trình web, cơ sở dữ liệu và mô hình MVC vào việc xây dựng một hệ thống quản lý thực tế.

---
## 2. Thành viên và phân công

| STT | Thành viên   | Phân công                                                  |
| --- | ------------ | ---------------------------------------------------------- |
| 1   | Tô Thị Thu Hiền|ADMIN: Đăng nhập & phân quyền, quản lý tài khoản, quản lý học kỳ, quản lý học phần, quản lý lớp học phần, thay đổi sĩ số, mở/khóa lớp, duyệt yêu cầu chỉnh sửa, tìm kiếm/lọc, phân trang, Endpoint JSON.                |
| 2   | Nguyễn Thị Trang |SINH VIÊN: Quản lý thông tin cá nhân, đăng ký/hủy đăng ký học phần, lịch sử đăng ký, kiểm tra điều kiện đăng ký, xem kết quả học tập.              |
| 3   | Hồ Mai Chi |GIẢNG VIÊN: Trang giảng viên, xem lớp được phân công, xem chi tiết lớp, xem danh sách sinh viên, nhập/cập nhật điểm, yêu cầu chỉnh sửa thông tin.

## 3. Các đối tượng dữ liệu chính

* Sinh viên
* Giảng viên
* Tài khoản
* Học phần
* Lớp học phần
* Đăng ký học phần
* Kết quả học tập

## 4. Các chức năng dự kiến

### Sinh viên

* Đăng nhập/đăng xuất.
* Xem danh sách học phần.
* Xem thông tin lớp học phần.
* Đăng ký học phần.
* Hủy đăng ký học phần.
* Xem lịch học.
* Xem kết quả học tập.

### Giảng viên

* Đăng nhập.
* Xem lớp học phần phụ trách.
* Xem danh sách sinh viên.
* Nhập và cập nhật điểm.

### Quản trị viên

* Quản lý sinh viên.
* Quản lý giảng viên.
* Quản lý học phần.
* Quản lý lớp học phần.
* Quản lý tài khoản.
* Quản lý đăng ký học phần.
* Quản lý kết quả học tập.

 ### Chức năng đã thực hiện đến hết Buổi 2

* Thống nhất tên và phạm vi đề tài.
* Xác định các đối tượng dữ liệu chính.
* Xác định các chức năng chính của hệ thống.
* Phân công nhiệm vụ cho 3 thành viên.
* Xây dựng form Quản lý học phần , Đăng ký học phần

### Quy tắc xếp loại điểm

| Điểm   | Xếp loại   |
| ------ | ---------- |
| >= 8   | Giỏi       |
| >= 6.5 | Khá        |
| >= 5   | Trung bình |
| < 5    | Chưa đạt   |

Ví dụ:

```text
Điểm: 8.5
Xếp loại: Giỏi
```

## 5. Công nghệ sử dụng

- **Ngôn ngữ:** PHP
- **Cơ sở dữ liệu:** MySQL
- **Kết nối cơ sở dữ liệu:** PDO
- **Frontend:** HTML5, CSS3, JavaScript
- **Kiến trúc:** MVC (Model - View - Controller)
- **Web Server:** Apache
- **Môi trường phát triển:** WampServer
- **Trình soạn thảo:** Visual Studio Code
- **Quản lý mã nguồn:** Git và GitHub

---

## 6. Cấu trúc thư mục

```text
quan-ly-khoa-hoc/
│
├── app/
│   ├── controllers/
│   │   └── .gitkeep
│   │
│   ├── models/
│   │   └── .gitkeep
│   │
│   └── views/
│       └── .gitkeep
│
├── config/
│   └── .gitkeep
│
├── public/
│   ├── css/
│   │   └── .gitkeep
│   │
│   ├── js/
│   │   └── .gitkeep
│   │
│   └── images/
│       └── .gitkeep
│
├── database/
│   └── database.sql
│
├── index.php
├── about.php
├── .gitignore
└── README.md
```

### Ý nghĩa các thư mục

| Thư mục / File | Mô tả |
|---|---|
| `app/controllers/` | Chứa các Controller xử lý yêu cầu từ người dùng |
| `app/models/` | Chứa các Model xử lý dữ liệu và tương tác với Database |
| `app/views/` | Chứa giao diện hiển thị cho người dùng |
| `config/` | Chứa các file cấu hình hệ thống và kết nối Database |
| `public/css/` | Chứa các file CSS |
| `public/js/` | Chứa các file JavaScript |
| `public/images/` | Chứa hình ảnh sử dụng trong website |
| `database/` | Chứa file SQL của cơ sở dữ liệu |
| `index.php` | File khởi chạy chính của website |
| `about.php` | Trang giới thiệu đề tài và thành viên nhóm |
| `.gitignore` | Các file/thư mục không đưa lên GitHub |
| `README.md` | Tài liệu giới thiệu và hướng dẫn sử dụng project |

---

# 7. Yêu cầu môi trường

Để chạy project trên máy tính cá nhân, cần cài đặt:
- WampServer
- PHP
- MySQL
- Apache
- Trình duyệt web như Chrome, Microsoft Edge hoặc Firefox
- Visual Studio Code 
- Git 

---

# 8. Hướng dẫn cài đặt và chạy project trên Local

## Bước 1: Cài đặt WampServer

Tải và cài đặt WampServer từ trang web:

https://www.wampserver.com/

Sau khi cài đặt, khởi động **WampServer**.

Kiểm tra biểu tượng WampServer trên thanh taskbar.

Nếu biểu tượng chuyển sang **màu xanh**, Apache và MySQL đã hoạt động bình thường.

---

## Bước 2: Đưa project vào WampServer

Copy hoặc clone project vào thư mục:

```text
C:\wamp64\www\
```

Ví dụ:

```text
C:\wamp64\www\qly-khoa-hoc\
```

Sau khi đặt project, cấu trúc thư mục có dạng:

```text
C:\wamp64\www\qly-khoa-hoc\
│
├── app\
├── config\
├── public\
├── database\
├── index.php
├── about.php
├── .gitignore
└── README.md
```

---

# 9. Tạo cơ sở dữ liệu

## Bước 1
Mở trình duyệt và truy cập:
```text
http://localhost/phpmyadmin
```
## Bước 2
Chọn **New** để tạo Database mới.
Đặt tên Database:
```text
quan_ly_khoa_hoc
```
Nên sử dụng mã hóa:
```text
utf8mb4
```
---
## Bước 3
Import cơ sở dữ liệu.
Trong phpMyAdmin:
```text
Import
→ Choose File
→ Chọn database.sql
→ Import
```
File SQL nằm trong thư mục:
```text
database/database.sql
```
Sau khi import thành công, Database sẽ chứa các bảng cần thiết cho hệ thống.
---
# 10. Cấu hình kết nối Database

Mở file cấu hình Database trong thư mục:

```text
config/
```

Thông tin kết nối MySQL trên WampServer thường có dạng:

```php
<?php

$host = "localhost";
$dbname = "quan_ly_khoa_hoc";
$username = "root";
$password = "";

try {
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $conn->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    die("Kết nối Database thất bại: " . $e->getMessage());
}
```
### Thông tin mặc định của WampServer

```text
Host: localhost
Username: root
Password: 
Database: quan_ly_khoa_hoc
```

# 11. Chạy website

Sau khi:

- WampServer đã chạy.
- Apache đang hoạt động.
- MySQL đang hoạt động.
- Database đã được tạo.
- File SQL đã được import.
- Thông tin kết nối Database đã chính xác.

Mở trình duyệt và truy cập:

```text
http://localhost/quan-ly-khoa-hoc/
```

Nếu project có file `index.php` ở thư mục gốc, website sẽ được khởi chạy từ file này.

---

# 12. Truy cập trang giới thiệu

Trang giới thiệu nhóm và đề tài được đặt tại:

```text
about.php
```
Truy cập:
```text
http://localhost/qly-khoa-hoc/about.php
```
Trang About bao gồm:

- Giới thiệu nhóm
- Giới thiệu đề tài.
- Mục tiêu của hệ thống.
- Công nghệ sử dụng.
- Chức năng chính.
---

# 13. Mục tiêu của đề tài

Hệ thống được xây dựng nhằm:

- Tin học hóa quá trình quản lý khóa học và học phần.
- Giúp sinh viên dễ dàng tìm kiếm và đăng ký học phần.
- Giúp quản trị viên quản lý thông tin một cách thuận tiện.
- Hạn chế việc quản lý dữ liệu thủ công.
- Đảm bảo dữ liệu được lưu trữ và quản lý tập trung.
- Áp dụng mô hình MVC trong xây dựng ứng dụng web.
- Áp dụng PHP và MySQL vào một bài toán quản lý thực tế.

---

# 14. Quy trình hoạt động cơ bản

### Đối với sinh viên

```text
Đăng nhập
    ↓
Xem danh sách khóa học
    ↓
Chọn khóa học
    ↓
Xem học phần
    ↓
Đăng ký học phần
    ↓
Xem danh sách học phần đã đăng ký
```

### Đối với quản trị viên

```text
Đăng nhập Admin
    ↓
Trang quản trị
    ↓
Quản lý khóa học
    ↓
Quản lý học phần
    ↓
Quản lý sinh viên
    ↓
Quản lý đăng ký học phần
```

---

# 15. Ghi chú

Project được xây dựng phục vụ mục đích học tập và thực hành lập trình web.

Khi chạy project trên máy khác, cần đảm bảo:

1. Đã cài đặt WampServer.
2. Apache và MySQL đang hoạt động.
3. Project nằm trong thư mục `C:\wamp64\www\`.
4. Đã tạo Database.
5. Đã import file `database.sql`.
6. Đã cấu hình thông tin kết nối Database.
7. Truy cập đúng đường dẫn `http://localhost/...`.

---

# 16. Bản quyền

© 2026 - Nhóm 6

**Hệ thống quản lý khóa học và đăng ký học phần**

**Nguyễn Thị Trang - Tô Thị Thu Hiền - Hồ Mai Chi**
