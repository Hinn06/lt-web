# HỆ THỐNG QUẢN LÝ KHÓA HỌC VÀ ĐĂNG KÝ HỌC PHẦN

## 1. Giới thiệu đề tài

**Hệ thống quản lý khóa học và đăng ký học phần** là một website được xây dựng nhằm hỗ trợ sinh viên trong việc tìm kiếm, xem thông tin và đăng ký các học phần.

Hệ thống đồng thời hỗ trợ quản trị viên quản lý khóa học, học phần, sinh viên và thông tin đăng ký học phần.

Đề tài được xây dựng với mục tiêu áp dụng kiến thức về lập trình web, cơ sở dữ liệu và mô hình MVC vào việc xây dựng một hệ thống quản lý thực tế.

---

## 2. Thành viên nhóm

| STT | Họ và tên |
| 1 | Nguyễn Thị Trang |
| 2 | Tô Thị Thu Hiền |
| 3 | Hồ Mai Chi |

---

## 3. Công nghệ sử dụng

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

## 4. Chức năng của hệ thống

### 4.1. Chức năng dành cho sinh viên

- Đăng ký tài khoản.
- Đăng nhập hệ thống.
- Đăng xuất.
- Xem danh sách khóa học.
- Tìm kiếm khóa học.
- Xem thông tin chi tiết khóa học.
- Xem danh sách học phần.
- Xem thông tin chi tiết học phần.
- Đăng ký học phần.
- Xem các học phần đã đăng ký.
- Quản lý thông tin cá nhân.

### 4.2. Chức năng dành cho quản trị viên

- Đăng nhập trang quản trị.
- Quản lý khóa học.
- Thêm khóa học.
- Sửa thông tin khóa học.
- Xóa khóa học.
- Quản lý học phần.
- Thêm học phần.
- Sửa thông tin học phần.
- Xóa học phần.
- Quản lý sinh viên.
- Quản lý danh sách đăng ký học phần.
- Xem và quản lý dữ liệu hệ thống.

---

## 5. Cấu trúc thư mục

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

# 6. Yêu cầu môi trường

Để chạy project trên máy tính cá nhân, cần cài đặt:
- WampServer
- PHP
- MySQL
- Apache
- Trình duyệt web như Chrome, Microsoft Edge hoặc Firefox
- Visual Studio Code 
- Git 

---

# 7. Hướng dẫn cài đặt và chạy project trên Local

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

# 8. Tạo cơ sở dữ liệu

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
# 9. Cấu hình kết nối Database

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

# 10. Chạy website

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

# 11. Truy cập trang giới thiệu

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

# 12. Mục tiêu của đề tài

Hệ thống được xây dựng nhằm:

- Tin học hóa quá trình quản lý khóa học và học phần.
- Giúp sinh viên dễ dàng tìm kiếm và đăng ký học phần.
- Giúp quản trị viên quản lý thông tin một cách thuận tiện.
- Hạn chế việc quản lý dữ liệu thủ công.
- Đảm bảo dữ liệu được lưu trữ và quản lý tập trung.
- Áp dụng mô hình MVC trong xây dựng ứng dụng web.
- Áp dụng PHP và MySQL vào một bài toán quản lý thực tế.

---

# 13. Quy trình hoạt động cơ bản

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

# 14. Ghi chú

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

# 15. Bản quyền

© 2026 - Nhóm 6

**Hệ thống quản lý khóa học và đăng ký học phần**

**Nguyễn Thị Trang - Tô Thị Thu Hiền - Hồ Mai Chi**
