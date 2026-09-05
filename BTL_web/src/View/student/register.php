<?php

$title = 'Đăng ký tín chỉ';

include dirname(__DIR__) . '/layout/header.php';

?>

<!-- =========================================================
     THÔNG TIN SINH VIÊN + CHỌN HỌC KỲ
========================================================= -->

<div class="card">

    <h2>Đăng ký tín chỉ</h2>

    <p>
        <b><?= e($student['student_code']) ?></b>
        · <?= e($student['full_name']) ?>
        · <?= e($student['faculty_name']) ?>
        · <?= e($student['cohort']) ?>
    </p>


    <?php if (!$active): ?>

        <div class="alert error">
            Hiện không có đợt đăng ký tín chỉ nào đang mở.
        </div>

    <?php else: ?>

        <p>
            Đợt đăng ký đang mở:
            <b><?= e($active['name']) ?></b>

            (
            <?= e($active['registration_start']) ?>
            →
            <?= e($active['registration_end']) ?>
            )
        </p>

    <?php endif; ?>


    <!-- =====================================================
         CHỌN HỌC KỲ
    ====================================================== -->

    <form method="GET">

        <input
            type="hidden"
            name="r"
            value="student/register"
        >

        <label for="semester_id">
            Chọn học kỳ
        </label>

        <select
            id="semester_id"
            name="semester_id"
            onchange="this.form.submit()"
        >

            <option value="">
                -- Chọn --
            </option>

            <?php foreach ($semesters as $s): ?>

                <option
                    value="<?= (int)$s['id'] ?>"
                    <?= (
                        $semester
                        && $semester['id'] == $s['id']
                    )
                        ? 'selected'
                        : ''
                    ?>
                >
                    <?= e($s['name']) ?>
                </option>

            <?php endforeach; ?>

        </select>

    </form>

</div>


<?php if ($semester): ?>


<!-- =========================================================
     DANH SÁCH LỚP HỌC PHẦN
========================================================= -->

<div class="card">

    <h3>
        Danh sách lớp học phần phù hợp
    </h3>

    <p class="muted">
        Hệ thống chỉ hiển thị lớp thuộc học kỳ đã chọn
        và các học phần được khoa của sinh viên phép đăng ký.
        Đợt đăng ký phải đang mở.
    </p>


    <!-- =====================================================
         TÌM KIẾM THÔNG MINH
    ====================================================== -->

    <div class="smart-search">

        <label for="courseSearch">
            Tìm kiếm học phần
        </label>


        <div class="search-box">

            <input
                type="text"
                id="courseSearch"
                class="smart-search-input"
                placeholder="Nhập mã hoặc tên học phần..."
                autocomplete="off"
            >

            <button
                type="button"
                id="clearSearch"
                class="clear-search-btn"
                style="display:none;"
            >
                ×
            </button>

        </div>


        <div
            id="searchResultText"
            class="search-result-text"
        ></div>

    </div>


    <!-- =====================================================
         KHÔNG CÓ DỮ LIỆU BAN ĐẦU
    ====================================================== -->

    <?php if (empty($rows)): ?>

        <div class="alert">

            Hiện chưa có lớp học phần phù hợp để đăng ký.

        </div>


    <?php else: ?>


        <!-- =================================================
             BẢNG LỚP HỌC PHẦN
        ================================================== -->

        <div class="table-wrap">

            <table id="courseTable">

                <thead>

                    <tr>

                        <th>STT</th>

                        <th>Mã HP</th>

                        <th>Học phần</th>

                        <th>TC</th>

                        <th>Lớp</th>

                        <th>Giảng viên</th>

                        <th>Lịch</th>

                        <th>Phòng</th>

                        <th>Sĩ số</th>

                        <th>Thao tác</th>

                    </tr>

                </thead>


                <tbody id="courseTableBody">


                    <?php foreach ($rows as $index => $r): ?>


                        <tr
                            class="course-row"
                            data-search="<?= e(
                                $r['course_code']
                                . ' '
                                . $r['course_name']
                                . ' '
                                . $r['class_code']
                            ) ?>"
                        >


                            <!-- ==========================
                                 STT
                            =========================== -->

                            <td class="stt-cell">

                                <?= $index + 1 ?>

                            </td>


                            <!-- ==========================
                                 MÃ HỌC PHẦN
                            =========================== -->

                            <td>

                                <b>
                                    <?= e($r['course_code']) ?>
                                </b>

                            </td>


                            <!-- ==========================
                                 TÊN HỌC PHẦN
                            =========================== -->

                            <td>

                                <?= e($r['course_name']) ?>

                            </td>


                            <!-- ==========================
                                 TÍN CHỈ
                            =========================== -->

                            <td>

                                <?= e($r['credits']) ?>

                            </td>


                            <!-- ==========================
                                 LỚP HỌC PHẦN
                            =========================== -->

                            <td>

                                <b>
                                    <?= e($r['class_code']) ?>
                                </b>

                            </td>


                            <!-- ==========================
                                 GIẢNG VIÊN
                            =========================== -->

                            <td>

                                <?= e($r['lecturer_name']) ?>

                            </td>


                            <!-- ==========================
                                 LỊCH HỌC
                            =========================== -->

                            <td>

                                Thứ
                                <?= e($r['weekday']) ?>

                                · Tiết

                                <?= e(
                                    $r['start_period']
                                    . '-'
                                    . $r['end_period']
                                ) ?>

                            </td>


                            <!-- ==========================
                                 PHÒNG
                            =========================== -->

                            <td>

                                <?= e($r['room']) ?>

                            </td>


                            <!-- ==========================
                                 SĨ SỐ
                            =========================== -->

                            <td>

                                <span
                                    class="student-count
                                    <?= (
                                        (int)$r['registered_count']
                                        >=
                                        (int)$r['max_students']
                                    )
                                        ? 'full'
                                        : ''
                                    ?>"
                                >

                                    <?= e(
                                        $r['registered_count']
                                    ) ?>

                                    /

                                    <?= e(
                                        $r['max_students']
                                    ) ?>

                                </span>

                            </td>


                            <!-- ==========================
                                 THAO TÁC
                            =========================== -->

                            <td>

                                <?php if (
                                    (int)$r['registered_count']
                                    <
                                    (int)$r['max_students']
                                ): ?>


                                    <form
                                        method="post"
                                        action="<?= e(BASE_URL) ?>?r=student/register/add"
                                        class="register-form"
                                    >

                                        <input
                                            type="hidden"
                                            name="_csrf"
                                            value="<?= e(csrf_token()) ?>"
                                        >


                                        <input
                                            type="hidden"
                                            name="class_id"
                                            value="<?= (int)$r['id'] ?>"
                                        >


                                        <button
                                            class="btn small"
                                            type="submit"
                                        >
                                            Đăng ký
                                        </button>

                                    </form>


                                <?php else: ?>


                                    <span class="badge off">
                                        Đầy
                                    </span>


                                <?php endif; ?>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>

        </div>


        <!-- =================================================
             KHÔNG TÌM THẤY KHI SEARCH
        ================================================== -->

        <div
            id="noSearchResult"
            class="alert"
            style="display:none;"
        >

            Không tìm thấy học phần phù hợp.

        </div>


    <?php endif; ?>


</div>


<?php endif; ?>


<!-- =========================================================
     CÁC HỌC PHẦN ĐÃ ĐĂNG KÝ
========================================================= -->

<div class="card">

    <h3>
        Đã đăng ký
    </h3>


    <?php if (empty($history)): ?>


        <p class="muted">
            Bạn chưa đăng ký học phần nào.
        </p>


    <?php else: ?>


        <div class="table-wrap">

            <table>

                <thead>

                    <tr>

                        <th>Học kỳ</th>

                        <th>Mã HP</th>

                        <th>Học phần</th>

                        <th>Lớp</th>

                        <th>Lịch</th>

                        <th>Thao tác</th>

                    </tr>

                </thead>


                <tbody>


                    <?php foreach ($history as $r): ?>


                        <tr>


                            <!-- HỌC KỲ -->

                            <td>

                                <?= e(
                                    $r['semester_name']
                                ) ?>

                            </td>


                            <!-- MÃ HP -->

                            <td>

                                <?= e(
                                    $r['course_code']
                                ) ?>

                            </td>


                            <!-- HỌC PHẦN -->

                            <td>

                                <?= e(
                                    $r['course_name']
                                ) ?>

                            </td>


                            <!-- LỚP -->

                            <td>

                                <?= e(
                                    $r['class_code']
                                ) ?>

                            </td>


                            <!-- LỊCH -->

                            <td>

                                Thứ
                                <?= e(
                                    $r['weekday']
                                ) ?>

                                · Tiết

                                <?= e(
                                    $r['start_period']
                                    . '-'
                                    . $r['end_period']
                                ) ?>

                            </td>


                            <!-- HỦY -->

                            <td>

                                <form
                                    method="post"
                                    action="<?= e(BASE_URL) ?>?r=student/register/cancel"
                                    class="register-form"
                                >

                                    <input
                                        type="hidden"
                                        name="_csrf"
                                        value="<?= e(csrf_token()) ?>"
                                    >


                                    <input
                                        type="hidden"
                                        name="registration_id"
                                        value="<?= (int)$r['id'] ?>"
                                    >


                                    <button
                                        class="btn small danger"
                                        type="submit"
                                    >
                                        Hủy
                                    </button>

                                </form>

                            </td>


                        </tr>


                    <?php endforeach; ?>


                </tbody>

            </table>

        </div>


    <?php endif; ?>

</div>


<!-- =========================================================
     CSS
========================================================= -->

<style>

/* =========================================================
   TÌM KIẾM THÔNG MINH
========================================================= */

.smart-search {

    width: 100%;

    margin: 18px 0 20px;

    box-sizing: border-box;
}


.smart-search label {

    display: block;

    margin-bottom: 8px;

    color: #315b87;

    font-size: 14px;

    font-weight: 600;
}


/* Ô nhập + nút X */

.search-box {

    position: relative;

    width: 100%;
}


.smart-search-input {

    width: 100%;

    height: 42px;

    box-sizing: border-box;

    padding: 0 42px 0 14px;

    border: 1px solid #cbd5e1;

    border-radius: 8px;

    background: #ffffff;

    color: #374151;

    font-size: 14px;

    outline: none;

    transition:
        border-color .2s,
        box-shadow .2s;
}


.smart-search-input:focus {

    border-color: #5d89b2;

    box-shadow:
        0 0 0 3px rgba(93, 137, 178, .12);
}


.smart-search-input::placeholder {

    color: #94a3b8;
}


/* =========================================================
   NÚT XÓA TÌM KIẾM
========================================================= */

.clear-search-btn {

    position: absolute;

    right: 8px;

    top: 50%;

    transform: translateY(-50%);

    width: 28px;

    height: 28px;

    padding: 0;

    border: none;

    border-radius: 50%;

    background: #e5e7eb;

    color: #475569;

    font-size: 20px;

    line-height: 28px;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;
}


.clear-search-btn:hover {

    background: #cbd5e1;

    color: #1e293b;
}


/* =========================================================
   THÔNG BÁO KẾT QUẢ
========================================================= */

.search-result-text {

    min-height: 20px;

    margin-top: 8px;

    color: #64748b;

    font-size: 14px;
}


/* =========================================================
   TABLE
========================================================= */

.table-wrap {

    width: 100%;

    overflow-x: auto;
}


.table-wrap table {

    width: 100%;

    min-width: 1100px;

    border-collapse: collapse;
}


/* =========================================================
   SỐ LƯỢNG SINH VIÊN
========================================================= */

.student-count {

    font-weight: 600;

    white-space: nowrap;
}


.student-count.full {

    color: #dc2626;
}


/* =========================================================
   FORM ĐĂNG KÝ
========================================================= */

.register-form {

    margin: 0;

    padding: 0;
}


/* =========================================================
   MOBILE
========================================================= */

@media (max-width: 768px) {

    .smart-search {

        margin-top: 15px;
    }


    .smart-search-input {

        height: 40px;
    }


    .table-wrap {

        overflow-x: auto;
    }

}

</style>


<!-- =========================================================
     JAVASCRIPT TÌM KIẾM THÔNG MINH
========================================================= -->

<script>

document.addEventListener(
    'DOMContentLoaded',
    function () {

        /*
         * Lấy ô tìm kiếm
         */

        const searchInput =
            document.getElementById(
                'courseSearch'
            );


        /*
         * Lấy nút Xóa
         */

        const clearButton =
            document.getElementById(
                'clearSearch'
            );


        /*
         * Lấy các dòng học phần
         */

        const rows =
            document.querySelectorAll(
                '.course-row'
            );


        /*
         * Thông báo không tìm thấy
         */

        const noResult =
            document.getElementById(
                'noSearchResult'
            );


        /*
         * Thông báo số lượng kết quả
         */

        const resultText =
            document.getElementById(
                'searchResultText'
            );


        /*
         * Nếu trang không có ô tìm kiếm
         * thì dừng JavaScript.
         */

        if (!searchInput) {

            return;

        }


        /*
         * Hàm tìm kiếm
         */

        function searchCourses() {


            /*
             * Lấy từ khóa người dùng nhập
             */

            const keyword =
                searchInput.value
                    .trim()
                    .toLowerCase();


            /*
             * Biến đếm kết quả
             */

            let found = 0;


            /*
             * Duyệt tất cả lớp học phần
             */

            rows.forEach(
                function (row) {


                    /*
                     * Lấy dữ liệu tìm kiếm
                     *
                     * Bao gồm:
                     * - Mã học phần
                     * - Tên học phần
                     * - Mã lớp học phần
                     */

                    const searchData =
                        (
                            row.dataset.search
                            || ''
                        )
                        .toLowerCase();


                    /*
                     * Nếu không nhập gì
                     * hoặc dữ liệu có chứa từ khóa
                     * thì hiển thị dòng.
                     */

                    if (
                        keyword === ''
                        ||
                        searchData.includes(
                            keyword
                        )
                    ) {

                        row.style.display = '';

                        found++;

                    } else {

                        row.style.display = 'none';

                    }

                }
            );


            /*
             * Hiện / ẩn nút Xóa
             */

            if (keyword !== '') {

                clearButton.style.display =
                    'flex';

            } else {

                clearButton.style.display =
                    'none';

            }


            /*
             * Đánh lại STT cho những dòng
             * đang được hiển thị.
             */

            let stt = 1;


            rows.forEach(
                function (row) {

                    if (
                        row.style.display !== 'none'
                    ) {

                        const cell =
                            row.querySelector(
                                '.stt-cell'
                            );

                        if (cell) {

                            cell.textContent =
                                stt++;

                        }

                    }

                }
            );


            /*
             * Hiển thị thông báo kết quả
             */

            if (keyword !== '' && found > 0) {

                resultText.textContent =
                    'Tìm thấy '
                    + found
                    + ' lớp học phần.';

            } else if (
                keyword !== ''
                &&
                found === 0
            ) {

                resultText.textContent =
                    'Không tìm thấy học phần phù hợp.';

            } else {

                resultText.textContent = '';

            }


            /*
             * Hiện thông báo không có kết quả
             */

            if (
                keyword !== ''
                &&
                found === 0
            ) {

                if (noResult) {

                    noResult.style.display =
                        'block';

                }

            } else {

                if (noResult) {

                    noResult.style.display =
                        'none';

                }

            }

        }


        /*
         * TÌM KIẾM NGAY KHI GÕ
         *
         * Sự kiện input chạy mỗi khi
         * người dùng nhập / xóa ký tự.
         */

        searchInput.addEventListener(
            'input',
            searchCourses
        );


        /*
         * Nút XÓA
         */

        if (clearButton) {

            clearButton.addEventListener(
                'click',
                function () {

                    searchInput.value = '';

                    searchInput.focus();

                    searchCourses();

                }
            );

        }


    }
);

</script>


<?php

include dirname(__DIR__) . '/layout/footer.php';

?>