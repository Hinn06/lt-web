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
## Quy tắc Validation

### 1.1. Đăng nhập

| Trường dữ liệu | Quy tắc |
|---|---|
| Username | Không được để trống |
| Mật khẩu | Không được để trống |

### 1.2. Tài khoản

| Trường dữ liệu | Quy tắc |
|---|---|
| Username | Bắt buộc, không được trùng |
| Mật khẩu | Bắt buộc khi tạo tài khoản |
| Họ tên | Không được để trống |
| Vai trò | Phải chọn Admin, Giảng viên hoặc Sinh viên |

### 1.3. Học kỳ

| Trường dữ liệu | Quy tắc |
|---|---|
| Tên học kỳ | Không được để trống |
| Ngày bắt đầu | Bắt buộc, đúng định dạng ngày |
| Ngày kết thúc | Bắt buộc, đúng định dạng ngày |
| Thời gian | Ngày bắt đầu không được lớn hơn ngày kết thúc |

### 1.4. Học phần

| Trường dữ liệu | Quy tắc |
|---|---|
| Mã học phần | Bắt buộc, không được trùng |
| Tên học phần | Không được để trống |
| Số tín chỉ | Phải là số nguyên dương |
| Mô tả | Có thể để trống |

### 1.5. Lớp học phần

| Trường dữ liệu | Quy tắc |
|---|---|
| Mã lớp | Bắt buộc, không được trùng |
| Học phần | Phải chọn học phần tồn tại |
| Học kỳ | Phải chọn học kỳ tồn tại |
| Giảng viên | Phải chọn giảng viên tồn tại |
| Sĩ số tối đa | Phải là số nguyên dương |

### 1.6. Điểm

- Điểm phải là số.
- Điểm phải nằm trong khoảng từ `0` đến `10`.
- Không chấp nhận điểm nhỏ hơn `0` hoặc lớn hơn `10`.

Công thức tính điểm tổng kết:

`Điểm tổng kết = Điểm giữa kỳ × 40% + Điểm cuối kỳ × 60%`
## Quy tắc nghiệp vụ

### 2.1. Phân quyền người dùng

- **Admin:** Quản lý tài khoản, học kỳ, học phần, lớp học phần và dữ liệu đăng ký.
- **Giảng viên:** Xem các lớp được phân công, xem danh sách sinh viên và nhập/cập nhật điểm.
- **Sinh viên:** Xem, đăng ký, hủy đăng ký học phần và xem lịch sử đăng ký.

Người dùng không được truy cập các chức năng nằm ngoài quyền hạn của mình.

### 2.2. Quy tắc học phần

- Mỗi học phần phải có mã học phần duy nhất.
- Một học phần có thể có nhiều lớp học phần.
- Không được tạo học phần có mã đã tồn tại.
- Không nên xóa học phần nếu đang có dữ liệu liên quan.

### 2.3. Quy tắc lớp học phần

Mỗi lớp học phần phải thuộc:
- Một học phần.
- Một học kỳ.
- Một giảng viên.

Số lượng sinh viên đăng ký không được vượt quá sĩ số tối đa của lớp.

### 2.4. Quy tắc đăng ký học phần

Sinh viên chỉ được đăng ký khi:
1. Đã đăng nhập.
2. Lớp học phần tồn tại.
3. Lớp đang cho phép đăng ký.
4. Lớp chưa đủ sĩ số.
5. Sinh viên chưa đăng ký lớp đó.

Một sinh viên không được đăng ký trùng cùng một lớp học phần.

### 2.5. Quy tắc hủy đăng ký

- Sinh viên chỉ được hủy những lớp học phần mà mình đã đăng ký.
- Sau khi hủy thành công, số lượng sinh viên đăng ký của lớp giảm đi 1.

### 2.6. Quy tắc nhập điểm

- Chỉ giảng viên được phân công mới được nhập điểm cho lớp.
- Điểm phải nằm trong khoảng từ 0 đến 10.
- Giảng viên được phép cập nhật điểm.
- Hệ thống tự động tính điểm tổng kết.

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

## 6. Mô tả cơ sở dữ liệu
Cơ sở dữ liệu gồm **7 bảng chính**: `users`, `courses`, `semesters`, `classes`, `registrations`, `grades` và `edit_requests`.

###  Bảng `users`

Lưu thông tin tài khoản của các đối tượng sử dụng hệ thống gồm **Admin, Giảng viên và Sinh viên**.

| Trường | Mô tả |
|---|---|
| `id` | Khóa chính |
| `username` | Tên đăng nhập, không được trùng |
| `password` | Mật khẩu đã mã hóa |
| `full_name` | Họ và tên |
| `role` | Vai trò: `student`, `teacher`, `admin` |
| `status` | Trạng thái tài khoản |
| `created_at` | Thời gian tạo tài khoản |

### Bảng `courses`

Lưu thông tin các học phần được quản lý trong hệ thống.

| Trường | Mô tả |
|---|---|
| `id` | Khóa chính |
| `code` | Mã học phần, không được trùng |
| `name` | Tên học phần |
| `credits` | Số tín chỉ |
| `description` | Mô tả học phần |
| `status` | Trạng thái học phần |

Một học phần có thể được mở thành nhiều lớp học phần.

### Bảng `semesters`

Lưu thông tin các học kỳ.

| Trường | Mô tả |
|---|---|
| `id` | Khóa chính |
| `name` | Tên học kỳ |
| `start_date` | Ngày bắt đầu |
| `end_date` | Ngày kết thúc |
| `status` | Trạng thái học kỳ |

Một học kỳ có thể có nhiều lớp học phần.

### Bảng `classes`

Lưu thông tin các lớp học phần được mở từ một học phần trong một học kỳ cụ thể.

| Trường | Mô tả |
|---|---|
| `id` | Khóa chính |
| `class_code` | Mã lớp học phần, không được trùng |
| `course_id` | Khóa ngoại đến `courses` |
| `semester_id` | Khóa ngoại đến `semesters` |
| `teacher_id` | Khóa ngoại đến `users` |
| `max_students` | Sĩ số tối đa |
| `status` | Trạng thái lớp |

Quan hệ:

```text
courses 1 ───── N classes
semesters 1 ─── N classes
users 1 ─────── N classes

### Bảng `registrations`

Bảng `registrations` dùng để lưu thông tin sinh viên đăng ký các lớp học phần.

| Trường | Mô tả |
|---|---|
| `id` | Khóa chính của bảng |
| `student_id` | Khóa ngoại tham chiếu đến `users.id`, xác định sinh viên đăng ký |
| `class_id` | Khóa ngoại tham chiếu đến `classes.id`, xác định lớp học phần được đăng ký |
| `registered_at` | Thời gian sinh viên thực hiện đăng ký |

Bảng `registrations` là bảng liên kết giữa **Sinh viên (`users`)** và **Lớp học phần (`classes`)**.

Quan hệ:
```text
users 1 ───── N registrations
classes 1 ─── N registrations

### Bảng `grades`

Bảng `grades` dùng để lưu thông tin điểm của sinh viên sau khi đăng ký học phần.

| Trường | Mô tả |
|---|---|
| `id` | Khóa chính của bảng |
| `registration_id` | Khóa ngoại tham chiếu đến `registrations.id` |
| `midterm` | Điểm giữa kỳ |
| `final_exam` | Điểm cuối kỳ |
| `total` | Điểm tổng kết |
| `updated_at` | Thời gian cập nhật điểm |

Bảng `grades` liên kết với bảng `registrations` để xác định điểm thuộc về sinh viên và lớp học phần nào.

Quan hệ:

```text
registrations 1 ───── 1 grades
### Bảng `edit_requests`

Bảng `edit_requests` dùng để lưu các yêu cầu chỉnh sửa điểm hoặc thông tin liên quan đến lớp học phần.

| Trường | Mô tả |
|---|---|
| `id` | Khóa chính của bảng |
| `teacher_id` | Khóa ngoại tham chiếu đến `users.id`, xác định giảng viên gửi yêu cầu |
| `class_id` | Khóa ngoại tham chiếu đến `classes.id`, xác định lớp học phần liên quan |
| `registration_id` | Khóa ngoại tham chiếu đến `registrations.id`, xác định lượt đăng ký liên quan |
| `content` | Nội dung yêu cầu chỉnh sửa |
| `status` | Trạng thái yêu cầu: `pending`, `approved`, `rejected` |
| `created_at` | Thời gian tạo yêu cầu |

Bảng `edit_requests` có 3 khóa ngoại:

```text
teacher_id      → users.id
class_id        → classes.id
registration_id → registrations.id
Các khóa ngoại giúp đảm bảo mỗi yêu cầu chỉnh sửa luôn gắn với giảng viên, lớp học phần và lượt đăng ký tồn tại trong hệ thống.

Trạng thái của yêu cầu được quản lý bằng 3 giá trị:

pending: Yêu cầu đang chờ xử lý.
approved: Yêu cầu đã được chấp thuận.
rejected: Yêu cầu đã bị từ chối.

Quy trình xử lý:

Giảng viên tạo yêu cầu
        ↓
     pending
      ↙    ↘
approved   rejected
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
