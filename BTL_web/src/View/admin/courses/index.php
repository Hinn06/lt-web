<?php

$title = 'Quản lý học phần';

include dirname(__DIR__, 2) . '/layout/header.php';

?>

<style>

/* =====================================================
   TOOLBAR TIÊU ĐỀ
===================================================== */

.course-toolbar {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 15px;

    margin-bottom: 18px;

    flex-wrap: wrap;
}


.course-toolbar h2 {

    margin: 0;

    color: #315b87;
}


/* =====================================================
   THANH TÌM KIẾM
===================================================== */

.course-search {

    display: flex !important;

    flex-direction: row !important;

    align-items: center !important;

    gap: 10px;

    width: 100%;

    margin: 0 0 18px 0;

    padding: 0;

    flex-wrap: nowrap !important;

    box-sizing: border-box;
}


/* Ô nhập tìm kiếm */

.course-search .input {

    flex: 1 1 auto !important;

    min-width: 0 !important;

    width: auto !important;

    height: 40px;

    margin: 0;

    box-sizing: border-box;
}


/* Tất cả button trong thanh tìm kiếm */

.course-search .btn {

    flex: 0 0 auto !important;

    width: auto !important;

    min-width: 110px;

    height: 40px;

    min-height: 40px;

    margin: 0 !important;

    padding: 8px 16px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    white-space: nowrap;

    box-sizing: border-box;
}


/* =====================================================
   KHUNG THÔNG TIN
===================================================== */

.course-info {

    background: #eef5fa;

    border: 1px solid #d9e6f0;

    border-radius: 10px;

    padding: 12px 15px;

    margin-bottom: 18px;

    color: #536b80;

    line-height: 1.6;
}


/* =====================================================
   BẢNG
===================================================== */

.course-table-wrap {

    width: 100%;

    overflow-x: auto;
}


.course-table {

    width: 100%;

    min-width: 850px;

    border-collapse: collapse;
}


.course-table th {

    background: #edf3f8;

    color: #315b87;

    font-weight: 600;

    padding: 12px 10px;

    text-align: left;

    border-bottom: 1px solid #dce5ee;
}


.course-table td {

    padding: 12px 10px;

    border-bottom: 1px solid #e5ebf0;

    vertical-align: middle;
}


.course-table tr:hover td {

    background: #f8fbfd;
}


/* =====================================================
   MÃ HỌC PHẦN
===================================================== */

.course-code {

    font-weight: 600;

    color: #315b87;

    white-space: nowrap;
}


/* =====================================================
   TÊN HỌC PHẦN
===================================================== */

.course-name {

    font-weight: 500;

    color: #354b60;
}


/* =====================================================
   KHOA
===================================================== */

.faculty-text {

    max-width: 260px;

    line-height: 1.5;
}


/* =====================================================
   NÚT THAO TÁC
===================================================== */

.actions {

    white-space: nowrap;
}


.actions form {

    display: inline;

    margin: 0;
}


.actions .btn {

    margin-right: 5px;
}


/* =====================================================
   NÚT NHỎ
===================================================== */

.actions .btn.small {

    min-width: auto;

    min-height: 34px;

    height: 34px;

    padding: 6px 12px;

    font-size: 13px;
}


/* =====================================================
   DÒNG RỖNG
===================================================== */

.empty-row {

    text-align: center;

    padding: 30px !important;

    color: #7a8794;
}


/* =====================================================
   PHÂN TRANG
===================================================== */

.pagination {

    display: flex;

    justify-content: center;

    align-items: center;

    gap: 6px;

    margin-top: 20px;

    flex-wrap: wrap;
}


.pagination a {

    min-width: 34px;

    height: 34px;

    display: inline-flex;

    align-items: center;

    justify-content: center;

    padding: 0 8px;

    box-sizing: border-box;

    border-radius: 7px;

    border: 1px solid #d5e0ea;

    background: #fff;

    color: #315b87;

    text-decoration: none;

    transition: .2s;
}


.pagination a:hover {

    background: #dbe8f3;
}


.pagination a.active {

    background: #5d89b2;

    border-color: #5d89b2;

    color: #fff;
}


/* =====================================================
   THÔNG TIN KẾT QUẢ
===================================================== */

.result-info {

    color: #718191;

    font-size: 14px;

    margin-top: 12px;
}


/* =====================================================
   RESPONSIVE TABLET
===================================================== */

@media (max-width: 900px) {

    .course-search {

        width: 100%;

        flex-wrap: nowrap !important;
    }


    .course-search .input {

        flex: 1 1 auto !important;

        min-width: 0 !important;
    }


    .course-search .btn {

        flex: 0 0 auto !important;

        min-width: 105px;

        width: auto !important;
    }
}


/* =====================================================
   MOBILE
===================================================== */

@media (max-width: 600px) {

    .course-toolbar {

        align-items: stretch;

        flex-direction: column;
    }


    .course-toolbar h2 {

        margin-bottom: 5px;
    }


    .course-toolbar > .btn {

        width: 100%;

        text-align: center;
    }


    /*
     * Vẫn giữ ô tìm kiếm và nút
     * trên cùng một dòng
     */

    .course-search {

        display: flex !important;

        flex-direction: row !important;

        align-items: center !important;

        gap: 7px;

        width: 100%;

        flex-wrap: nowrap !important;
    }


    .course-search .input {

        flex: 1 1 auto !important;

        min-width: 0 !important;

        width: auto !important;

        height: 40px;

        font-size: 13px;
    }


    .course-search .btn {

        flex: 0 0 auto !important;

        min-width: 88px;

        width: auto !important;

        height: 40px;

        padding: 8px 10px;

        font-size: 13px;
    }


    /*
     * Nút Xóa tìm kiếm vẫn cùng dòng
     */

    .course-search .btn.secondary {

        min-width: 95px;
    }
}


/* =====================================================
   ĐIỆN THOẠI RẤT NHỎ
===================================================== */

@media (max-width: 430px) {

    .course-search {

        gap: 5px;
    }


    .course-search .input {

        font-size: 12px;

        padding-left: 8px;

        padding-right: 8px;
    }


    .course-search .btn {

        min-width: 78px;

        padding-left: 8px;

        padding-right: 8px;

        font-size: 12px;
    }


    .course-search .btn.secondary {

        min-width: 90px;
    }
}

</style>


<!-- =====================================================
     TIÊU ĐỀ + THÊM HỌC PHẦN
===================================================== -->

<div class="course-toolbar">

    <h2>
        Quản lý học phần
    </h2>


    <a
        class="btn"
        href="<?= e(BASE_URL) ?>?r=admin/course/create"
    >
        + Thêm học phần
    </a>

</div>


<!-- =====================================================
     KHUNG NỘI DUNG
===================================================== -->

<div class="card">


    <!-- =================================================
         TÌM KIẾM
    ================================================== -->

    <form
        class="course-search"
        method="get"
        action="<?= e(BASE_URL) ?>"
    >

        <!-- ROUTE -->

        <input
            type="hidden"
            name="r"
            value="admin/courses"
        >


        <!-- Ô TÌM KIẾM -->

        <input
            class="input"
            type="text"
            name="q"
            value="<?= e($q ?? '') ?>"
            placeholder="Tìm mã hoặc tên học phần..."
            autocomplete="off"
        >


        <!-- NÚT TÌM KIẾM -->

        <button
            type="submit"
            class="btn"
        >
            Tìm kiếm
        </button>


        <!-- NÚT XÓA -->

        <?php if (!empty($q)): ?>

            <a
                class="btn secondary"
                href="<?= e(BASE_URL) ?>?r=admin/courses"
            >
                Xóa tìm kiếm
            </a>

        <?php endif; ?>


    </form>


    <!-- =================================================
         BẢNG HỌC PHẦN
    ================================================== -->

    <div class="course-table-wrap">

        <table class="course-table">


            <thead>

                <tr>

                    <th style="width: 110px;">
                        Mã HP
                    </th>


                    <th>
                        Tên học phần
                    </th>


                    <th style="width: 70px;">
                        TC
                    </th>


                    <th>
                        Khoa
                    </th>


                    <th style="width: 120px;">
                        Trạng thái
                    </th>


                    <th style="width: 150px;">
                        Thao tác
                    </th>

                </tr>

            </thead>


            <tbody>


                <?php if (empty($rows)): ?>


                    <tr>

                        <td
                            colspan="6"
                            class="empty-row"
                        >

                            Không tìm thấy học phần phù hợp.

                        </td>

                    </tr>


                <?php else: ?>


                    <?php foreach ($rows as $r): ?>


                        <tr>


                            <!-- =====================
                                 MÃ HỌC PHẦN
                            ====================== -->

                            <td>

                                <span class="course-code">

                                    <?= e($r['code']) ?>

                                </span>

                            </td>


                            <!-- =====================
                                 TÊN HỌC PHẦN
                            ====================== -->

                            <td>

                                <span class="course-name">

                                    <?= e($r['name']) ?>

                                </span>

                            </td>


                            <!-- =====================
                                 TÍN CHỈ
                            ====================== -->

                            <td>

                                <?= e($r['credits']) ?>

                            </td>


                            <!-- =====================
                                 KHOA
                            ====================== -->

                            <td>

                                <div class="faculty-text">

                                    <?= e($r['faculties'] ?? '') ?>

                                </div>

                            </td>


                            <!-- =====================
                                 TRẠNG THÁI
                            ====================== -->

                            <td>

                                <?php if ($r['status']): ?>

                                    <span class="badge">
                                        Hoạt động
                                    </span>

                                <?php else: ?>

                                    <span class="badge off">
                                        Khóa
                                    </span>

                                <?php endif; ?>

                            </td>


                            <!-- =====================
                                 THAO TÁC
                            ====================== -->

                            <td class="actions">


                                <!-- SỬA -->

                                <a
                                    class="btn small secondary"
                                    href="<?= e(BASE_URL) ?>?r=admin/course/edit&id=<?= (int)$r['id'] ?>"
                                >
                                    Sửa
                                </a>


                                <!-- XÓA -->

                                <form
                                    method="post"
                                    action="<?= e(BASE_URL) ?>?r=admin/course/delete"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn xóa học phần này?');"
                                >

                                    <input
                                        type="hidden"
                                        name="_csrf"
                                        value="<?= e(csrf_token()) ?>"
                                    >


                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?= (int)$r['id'] ?>"
                                    >


                                    <button
                                        type="submit"
                                        class="btn small danger"
                                    >
                                        Xóa
                                    </button>

                                </form>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php endif; ?>


            </tbody>

        </table>

    </div>


    <!-- =================================================
         THÔNG TIN KẾT QUẢ
    ================================================== -->

    <?php if (!empty($rows)): ?>

        <div class="result-info">

            <?php if (!empty($q)): ?>

                Kết quả tìm kiếm cho:

                <strong>
                    <?= e($q) ?>
                </strong>

            <?php else: ?>

                Danh sách học phần hiện có.

            <?php endif; ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         PHÂN TRANG
    ================================================== -->

    <?php if ($pages > 1): ?>

        <div class="pagination">


            <?php for ($i = 1; $i <= $pages; $i++): ?>


                <a
                    class="<?= $i === $page ? 'active' : '' ?>"
                    href="<?= e(BASE_URL) ?>?r=admin/courses&q=<?= urlencode($q ?? '') ?>&page=<?= $i ?>"
                >

                    <?= $i ?>

                </a>


            <?php endfor; ?>


        </div>

    <?php endif; ?>


</div>


<?php

include dirname(__DIR__, 2) . '/layout/footer.php';

?>