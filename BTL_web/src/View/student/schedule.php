<?php

require __DIR__ . '/../layout/header.php';

/*
|--------------------------------------------------------------------------
| DỮ LIỆU
|--------------------------------------------------------------------------
*/

$scheduleRows = $scheduleRows ?? [];
$semesters = $semesters ?? [];
$semesterId = $semesterId ?? null;


/*
|--------------------------------------------------------------------------
| HÀM HIỂN THỊ THỨ
|--------------------------------------------------------------------------
|
| Quy ước:
| 2 = Thứ 2
| 3 = Thứ 3
| ...
| 7 = Thứ 7
| 8 = Chủ nhật
|
*/

function formatWeekday(int $weekday): string
{
    return match ($weekday) {
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => 'CN',
        default => '-'
    };
}


/*
|--------------------------------------------------------------------------
| HÀM HIỂN THỊ TIẾT
|--------------------------------------------------------------------------
*/

function formatPeriods(
    int $startPeriod,
    int $endPeriod
): string {

    if ($startPeriod === $endPeriod) {
        return (string) $startPeriod;
    }

    return $startPeriod . '-' . $endPeriod;
}


/*
|--------------------------------------------------------------------------
| HÀM ĐỊNH DẠNG NGÀY
|--------------------------------------------------------------------------
*/

function formatDateVN(?string $date): string
{
    if (!$date) {
        return '-';
    }

    $time = strtotime($date);

    if (!$time) {
        return '-';
    }

    return date('d/m/Y', $time);
}


/*
|--------------------------------------------------------------------------
| GOM DỮ LIỆU THEO LỚP HỌC PHẦN
|--------------------------------------------------------------------------
|
| Một lớp học phần có thể có nhiều buổi:
|
| Thứ 4 - tiết 1-3
| Thứ 6 - tiết 4-6
|
| Vì vậy không tạo 2 dòng STT.
| Các buổi học sẽ được gom chung vào một lớp.
|
*/

$classes = [];

foreach ($scheduleRows as $row) {

    $classId = (int) ($row['class_id'] ?? 0);

    if ($classId <= 0) {
        continue;
    }

    /*
     * Tạo thông tin lớp nếu chưa tồn tại
     */
    if (!isset($classes[$classId])) {

        $classes[$classId] = [
            'class_id' => $classId,

            'class_code' => $row['class_code'] ?? '',

            'course_code' => $row['course_code'] ?? '',

            'course_name' => $row['course_name'] ?? '',

            'credits' => (int) ($row['credits'] ?? 0),

            'lecturer_name' => !empty($row['lecturer_name'])
                ? $row['lecturer_name']
                : 'Chưa phân công',

            'schedules' => []
        ];
    }


    /*
     * Thêm lịch học
     */
    $classes[$classId]['schedules'][] = [

        'weekday' => (int) ($row['weekday'] ?? 0),

        'start_period' => (int) ($row['start_period'] ?? 0),

        'end_period' => (int) ($row['end_period'] ?? 0),

        'start_date' => $row['start_date'] ?? null,

        'end_date' => $row['end_date'] ?? null,

        'room' => $row['room'] ?? '-'
    ];
}


/*
|--------------------------------------------------------------------------
| SẮP XẾP CÁC LỚP
|--------------------------------------------------------------------------
*/

usort(
    $classes,
    function ($a, $b) {

        return strcmp(
            $a['course_name'],
            $b['course_name']
        );
    }
);

?>

<style>

/* =========================================================
   TOÀN BỘ TRANG
========================================================= */

.schedule-page {
    width: 100%;
    padding: 10px 0 35px;
}


/* =========================================================
   TIÊU ĐỀ
========================================================= */

.schedule-title {
    margin: 0 0 18px;

    font-size: 27px;
    font-weight: 700;

    color: #315b87;
}


/* =========================================================
   CARD CHÍNH
========================================================= */

.schedule-card {

    width: 100%;

    background: #ffffff;

    border: 1px solid #dce5ee;

    border-radius: 14px;

    padding: 20px;

    box-shadow:
        0 5px 20px rgba(49, 91, 135, .08);

    box-sizing: border-box;
}


/* =========================================================
   BỘ LỌC
========================================================= */

.schedule-filter {

    display: flex;

    flex-direction: row;

    align-items: center;

    gap: 12px;

    width: 100%;

    margin-bottom: 20px;

    flex-wrap: nowrap;
}


/* Nhãn Học kỳ */

.schedule-filter label {

    flex: 0 0 auto;

    font-size: 14px;

    font-weight: 600;

    color: #374151;

    white-space: nowrap;
}


/* Select học kỳ */

.schedule-filter select {

    flex: 0 0 240px;

    width: 240px;

    height: 40px;

    padding: 0 12px;

    border: 1px solid #cbd5e1;

    border-radius: 7px;

    background: #ffffff;

    color: #374151;

    font-size: 14px;

    outline: none;

    cursor: pointer;

    box-sizing: border-box;
}


/* Focus */

.schedule-filter select:focus {

    border-color: #5d89b2;

    box-shadow:
        0 0 0 2px rgba(93, 137, 178, .12);
}


/* =========================================================
   NÚT XEM LỊCH
========================================================= */

.schedule-filter .btn-view {

    flex: 0 0 auto;

    width: 110px;

    height: 40px;

    padding: 0 15px;

    border: none;

    border-radius: 7px;

    background: #5d89b2;

    color: #ffffff;

    font-size: 14px;

    font-weight: 600;

    cursor: pointer;

    white-space: nowrap;

    transition:
        background .2s ease,
        transform .2s ease;
}


.schedule-filter .btn-view:hover {

    background: #416f98;

    transform: translateY(-1px);
}


.schedule-filter .btn-view:active {

    transform: translateY(0);
}


/* =========================================================
   KHUNG BẢNG
========================================================= */

.schedule-table-wrapper {

    width: 100%;

    overflow-x: auto;

    border: 1px solid #d8e2eb;

    border-radius: 8px;
}


/* =========================================================
   TABLE
========================================================= */

.schedule-table {

    width: 100%;

    min-width: 1250px;

    border-collapse: collapse;

    table-layout: auto;

    font-size: 13px;
}


/* =========================================================
   HEADER TABLE
========================================================= */

.schedule-table thead th {

    background: #3f8da8;

    color: #ffffff;

    padding: 11px 8px;

    border: 1px solid #d2e0e8;

    text-align: center;

    vertical-align: middle;

    font-size: 13px;

    font-weight: 700;

    line-height: 1.4;

    white-space: nowrap;
}


/* =========================================================
   BODY TABLE
========================================================= */

.schedule-table tbody td {

    padding: 10px 8px;

    border: 1px solid #d8e2eb;

    color: #263746;

    background: #ffffff;

    vertical-align: middle;

    line-height: 1.5;
}


/* =========================================================
   DÒNG XEN KẼ
========================================================= */

.schedule-table tbody tr:nth-child(even) td {

    background: #eaf4fc;
}


/* =========================================================
   HOVER
========================================================= */

.schedule-table tbody tr:hover td {

    background: #dcecf8;
}


/* =========================================================
   CỘT STT
========================================================= */

.col-stt {

    width: 45px;

    text-align: center;

    white-space: nowrap;
}


/* =========================================================
   TÊN HỌC PHẦN
========================================================= */

.col-course {

    min-width: 220px;

    text-align: left;
}


/* =========================================================
   MÃ HỌC PHẦN
========================================================= */

.col-code {

    min-width: 110px;

    text-align: center;
}


/* =========================================================
   TÊN LỚP TÍN CHỈ
========================================================= */

.col-class {

    min-width: 170px;

    text-align: center;
}


/* =========================================================
   SỐ TÍN CHỈ
========================================================= */

.col-credit {

    width: 70px;

    text-align: center;

    white-space: nowrap;
}


/* =========================================================
   GIÁO VIÊN
========================================================= */

.col-teacher {

    min-width: 170px;

    text-align: left;
}


/* =========================================================
   PHÒNG
========================================================= */

.col-room {

    min-width: 100px;

    text-align: center;
}


/* =========================================================
   LỊCH HỌC
========================================================= */

.col-schedule {

    min-width: 150px;

    text-align: center;
}


/* =========================================================
   NGÀY HỌC
========================================================= */

.col-date {

    min-width: 170px;

    text-align: center;
}


/* =========================================================
   TỪNG DÒNG LỊCH
========================================================= */

.schedule-line {

    padding: 4px 0;

    line-height: 1.55;
}


/*
 * Nếu có nhiều lịch trong cùng một ô,
 * tạo đường phân cách nhẹ.
 */

.schedule-line + .schedule-line {

    border-top: 1px dashed #cbd5e1;

    margin-top: 4px;

    padding-top: 7px;
}


/* =========================================================
   TÊN MÔN
========================================================= */

.course-name {

    font-weight: 600;

    color: #315b87;
}


/* =========================================================
   MÃ LỚP
========================================================= */

.class-code {

    font-weight: 600;

    color: #374151;
}


/* =========================================================
   PHÒNG ONLINE
========================================================= */

.room-online {

    color: #315b87;

    font-weight: 600;
}


/* =========================================================
   KHÔNG CÓ LỊCH
========================================================= */

.empty-schedule {

    padding: 55px 20px;

    text-align: center;

    color: #64748b;

    font-size: 15px;
}


.empty-icon {

    font-size: 38px;

    margin-bottom: 12px;
}


.empty-title {

    font-size: 17px;

    font-weight: 600;

    color: #475569;

    margin-bottom: 6px;
}


.empty-text {

    font-size: 13px;

    color: #64748b;
}


/* =========================================================
   GHI CHÚ
========================================================= */

.schedule-note {

    margin-top: 15px;

    padding: 11px 13px;

    background: #f7fafc;

    border-left: 4px solid #5d89b2;

    border-radius: 4px;

    color: #475569;

    font-size: 13px;

    line-height: 1.5;
}


.schedule-note strong {

    color: #315b87;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 768px) {

    .schedule-page {

        padding-top: 5px;
    }


    .schedule-title {

        font-size: 23px;
    }


    .schedule-card {

        padding: 12px;
    }


    /*
     * Trên màn hình nhỏ cho phép xuống dòng
     */

    .schedule-filter {

        flex-wrap: wrap;

        align-items: center;
    }


    .schedule-filter select {

        flex: 1 1 200px;

        width: auto;

        min-width: 180px;
    }


    .schedule-filter .btn-view {

        flex: 0 0 110px;

        width: 110px;
    }


    .schedule-table {

        min-width: 1150px;
    }

}

</style>


<!-- =========================================================
     TRANG LỊCH HỌC
========================================================= -->

<div class="schedule-page">


    <!-- =====================================================
         TIÊU ĐỀ
    ====================================================== -->

    <h1 class="schedule-title">
        📅 Lịch học
    </h1>


    <!-- =====================================================
         CARD
    ====================================================== -->

    <div class="schedule-card">


        <!-- =================================================
             BỘ LỌC HỌC KỲ
        ================================================== -->

        <form
            method="GET"
            action="<?= e(BASE_URL) ?>"
            class="schedule-filter"
        >

            <!-- Route -->

            <input
                type="hidden"
                name="r"
                value="student/schedule"
            >


            <!-- Label -->

            <label for="semester_id">
                Học kỳ
            </label>


            <!-- Select -->

            <select
                name="semester_id"
                id="semester_id"
            >

                <option value="">
                    Tất cả học kỳ
                </option>


                <?php foreach ($semesters as $semester): ?>

                    <option
                        value="<?= (int)$semester['id'] ?>"
                        <?= (
                            $semesterId !== null
                            && (int)$semesterId === (int)$semester['id']
                        )
                            ? 'selected'
                            : ''
                        ?>
                    >

                        <?= e($semester['name']) ?>

                        <?php if (!empty($semester['academic_year'])): ?>

                            -
                            <?= e($semester['academic_year']) ?>

                        <?php endif; ?>

                    </option>

                <?php endforeach; ?>

            </select>


            <!-- Nút -->

            <button
                type="submit"
                class="btn-view"
            >
                Xem lịch
            </button>

        </form>


        <!-- =================================================
             KIỂM TRA CÓ LỊCH HAY KHÔNG
        ================================================== -->

        <?php if (empty($classes)): ?>


            <!-- Không có lịch -->

            <div class="empty-schedule">

                <div class="empty-icon">
                    📚
                </div>


                <div class="empty-title">
                    Chưa có lịch học
                </div>


                <div class="empty-text">

                    Bạn cần đăng ký ít nhất một lớp học phần
                    để lịch học được hiển thị.

                </div>

            </div>


        <?php else: ?>


            <!-- =================================================
                 BẢNG LỊCH
            ================================================== -->

            <div class="schedule-table-wrapper">

                <table class="schedule-table">


                    <!-- =========================================
                         HEADER
                    ========================================== -->

                    <thead>

                        <tr>

                            <th class="col-stt">
                                STT
                            </th>


                            <th class="col-course">
                                Tên học phần
                            </th>


                            <th class="col-code">
                                Mã học phần
                            </th>


                            <th class="col-class">
                                Tên lớp tín chỉ
                            </th>


                            <th class="col-credit">
                                Số tín chỉ
                            </th>


                            <th class="col-teacher">
                                Giáo viên
                            </th>


                            <th class="col-room">
                                Phòng
                            </th>


                            <th class="col-schedule">
                                Lịch học
                            </th>


                            <th class="col-date">
                                Từ ngày - Đến ngày
                            </th>

                        </tr>

                    </thead>


                    <!-- =========================================
                         BODY
                    ========================================== -->

                    <tbody>

                        <?php $stt = 1; ?>


                        <?php foreach ($classes as $class): ?>

                            <tr>


                                <!-- =================================
                                     STT
                                ================================== -->

                                <td class="col-stt">

                                    <?= $stt++ ?>

                                </td>


                                <!-- =================================
                                     TÊN HỌC PHẦN
                                ================================== -->

                                <td class="col-course">

                                    <div class="course-name">

                                        <?= e(
                                            $class['course_name']
                                        ) ?>

                                    </div>

                                </td>


                                <!-- =================================
                                     MÃ HỌC PHẦN
                                ================================== -->

                                <td class="col-code">

                                    <?= e(
                                        $class['course_code']
                                    ) ?>

                                </td>


                                <!-- =================================
                                     TÊN LỚP TÍN CHỈ
                                ================================== -->

                                <td class="col-class">

                                    <span class="class-code">

                                        <?= e(
                                            $class['class_code']
                                        ) ?>

                                    </span>

                                </td>


                                <!-- =================================
                                     SỐ TÍN CHỈ
                                ================================== -->

                                <td class="col-credit">

                                    <?= (int)$class['credits'] ?>

                                </td>


                                <!-- =================================
                                     GIÁO VIÊN
                                ================================== -->

                                <td class="col-teacher">

                                    <?= e(
                                        $class['lecturer_name']
                                    ) ?>

                                </td>


                                <!-- =================================
                                     PHÒNG
                                ================================== -->

                                <td class="col-room">

                                    <?php if (
                                        !empty($class['schedules'])
                                    ): ?>


                                        <?php foreach (
                                            $class['schedules']
                                            as $schedule
                                        ): ?>

                                            <div class="schedule-line">

                                                <?php
                                                $room = trim(
                                                    $schedule['room']
                                                    ?? ''
                                                );
                                                ?>


                                                <?php if (
                                                    $room !== ''
                                                ): ?>

                                                    <?php if (
                                                        stripos(
                                                            $room,
                                                            'online'
                                                        ) !== false
                                                    ): ?>

                                                        <span
                                                            class="room-online"
                                                        >
                                                            <?= e($room) ?>
                                                        </span>

                                                    <?php else: ?>

                                                        <?= e($room) ?>

                                                    <?php endif; ?>

                                                <?php else: ?>

                                                    -

                                                <?php endif; ?>

                                            </div>

                                        <?php endforeach; ?>


                                    <?php else: ?>

                                        -

                                    <?php endif; ?>

                                </td>


                                <!-- =================================
                                     LỊCH HỌC
                                ================================== -->

                                <td class="col-schedule">

                                    <?php foreach (
                                        $class['schedules']
                                        as $schedule
                                    ): ?>


                                        <div class="schedule-line">

                                            <strong>
                                                Thứ
                                                <?= formatWeekday(
                                                    (int)$schedule['weekday']
                                                ) ?>
                                            </strong>

                                            <br>

                                            Tiết
                                            <?= formatPeriods(
                                                (int)$schedule['start_period'],
                                                (int)$schedule['end_period']
                                            ) ?>

                                        </div>


                                    <?php endforeach; ?>

                                </td>


                                <!-- =================================
                                     NGÀY HỌC
                                ================================== -->

                                <td class="col-date">

                                    <?php foreach (
                                        $class['schedules']
                                        as $schedule
                                    ): ?>


                                        <div class="schedule-line">

                                            <?= formatDateVN(
                                                $schedule['start_date']
                                            ) ?>

                                            -

                                            <?= formatDateVN(
                                                $schedule['end_date']
                                            ) ?>

                                        </div>


                                    <?php endforeach; ?>

                                </td>

                            </tr>


                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>


            <!-- =================================================
                 GHI CHÚ
            ================================================== -->

            <div class="schedule-note">

                <strong>Ghi chú:</strong>

                Lịch học có thể thay đổi theo thông báo
                của Nhà trường.

            </div>


        <?php endif; ?>


    </div>

</div>


<?php

require __DIR__ . '/../layout/footer.php';

?>