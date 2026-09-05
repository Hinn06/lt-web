(function () {
    'use strict';

    const root = document.querySelector('[data-admin-search]');
    if (!root) return;

    const type = root.dataset.type;
    const endpoint = root.dataset.endpoint;
    const baseUrl = root.dataset.baseUrl;
    const csrf = root.dataset.csrf || '';

    const input = root.querySelector('[data-search-input]');
    const clearButton = root.querySelector('[data-search-clear]');
    const tbody = root.querySelector('[data-search-body]');
    const pagination = root.querySelector('[data-search-pagination]');
    const resultInfo = root.querySelector('[data-search-result]');

    if (!input || !tbody || !pagination) return;

    let timer = null;
    let controller = null;
    let requestNumber = 0;

    function normalize(value) {
        return String(value ?? '')
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/đ/g, 'd')
            .replace(/Đ/g, 'D')
            .toLowerCase()
            .trim();
    }

    function text(value) {
        return document.createTextNode(String(value ?? ''));
    }

    function td(value, className) {
        const cell = document.createElement('td');
        if (className) cell.className = className;
        cell.appendChild(text(value));
        return cell;
    }

    function link(url, label, className, title) {
        const a = document.createElement('a');
        a.href = url;
        a.className = className || '';
        a.title = title || label;
        a.setAttribute('aria-label', title || label);
        a.appendChild(text(label));
        return a;
    }

    function deleteForm(url, id, title) {
        const form = document.createElement('form');
        form.method = 'post';
        form.action = url;
        form.className = 'js-delete-form';
        form.addEventListener('submit', function (event) {
            if (!window.confirm(title || 'Bạn có chắc muốn xóa?')) {
                event.preventDefault();
            }
        });

        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_csrf';
        token.value = csrf;
        form.appendChild(token);

        const idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = String(id);
        form.appendChild(idInput);

        const button = document.createElement('button');
        button.type = 'submit';
        button.className = 'icon-btn danger';
        button.title = 'Xóa';
        button.setAttribute('aria-label', 'Xóa');
        button.appendChild(text('🗑'));
        form.appendChild(button);

        return form;
    }

    function actionsCell(children) {
        const cell = document.createElement('td');
        cell.className = 'actions';
        const wrap = document.createElement('div');
        wrap.className = 'action-row';
        children.forEach(function (child) { wrap.appendChild(child); });
        cell.appendChild(wrap);
        return cell;
    }

    function renderStudents(rows) {
        rows.forEach(function (row) {
            const tr = document.createElement('tr');
            tr.appendChild(td(row.student_code));
            tr.appendChild(td(row.full_name));
            tr.appendChild(td(row.username));
            tr.appendChild(td(row.class_name));
            tr.appendChild(td(row.faculty_name));
            tr.appendChild(td(row.cohort));

            const status = document.createElement('span');
            status.className = 'badge' + (!Number(row.status) ? ' off' : '');
            status.appendChild(text(Number(row.status) ? 'Hoạt động' : 'Khóa'));
            const statusTd = document.createElement('td');
            statusTd.appendChild(status);
            tr.appendChild(statusTd);

            const edit = link(
                baseUrl + '?r=admin/student/edit&id=' + encodeURIComponent(row.user_id),
                '✎', 'icon-btn', 'Sửa'
            );
            const del = deleteForm(
                baseUrl + '?r=admin/student/delete',
                row.user_id,
                'Bạn có chắc muốn xóa sinh viên này?'
            );
            tr.appendChild(actionsCell([edit, del]));
            tbody.appendChild(tr);
        });
    }

    function renderLecturers(rows) {
        rows.forEach(function (row) {
            const tr = document.createElement('tr');
            tr.appendChild(td(row.lecturer_code));
            tr.appendChild(td(row.full_name));
            tr.appendChild(td(row.username));
            tr.appendChild(td(row.faculty_name));

            const status = document.createElement('span');
            status.className = 'badge' + (!Number(row.status) ? ' off' : '');
            status.appendChild(text(Number(row.status) ? 'Hoạt động' : 'Khóa'));
            const statusTd = document.createElement('td');
            statusTd.appendChild(status);
            tr.appendChild(statusTd);

            const edit = link(
                baseUrl + '?r=admin/lecturer/edit&id=' + encodeURIComponent(row.user_id),
                '✎', 'icon-btn', 'Sửa'
            );
            const del = deleteForm(
                baseUrl + '?r=admin/lecturer/delete',
                row.user_id,
                'Bạn có chắc muốn xóa giảng viên này?'
            );
            tr.appendChild(actionsCell([edit, del]));
            tbody.appendChild(tr);
        });
    }

    function renderCourses(rows) {
        rows.forEach(function (row) {
            const tr = document.createElement('tr');
            tr.appendChild(td(row.code, 'course-code'));
            tr.appendChild(td(row.name, 'course-name'));
            tr.appendChild(td(row.credits));
            tr.appendChild(td(row.faculties || ''));

            const status = document.createElement('span');
            status.className = 'badge' + (!Number(row.status) ? ' off' : '');
            status.appendChild(text(Number(row.status) ? 'Hoạt động' : 'Khóa'));
            const statusTd = document.createElement('td');
            statusTd.appendChild(status);
            tr.appendChild(statusTd);

            const edit = link(
                baseUrl + '?r=admin/course/edit&id=' + encodeURIComponent(row.id),
                '✎', 'icon-btn', 'Sửa'
            );
            const del = deleteForm(
                baseUrl + '?r=admin/course/delete',
                row.id,
                'Bạn có chắc muốn xóa học phần này?'
            );
            tr.appendChild(actionsCell([edit, del]));
            tbody.appendChild(tr);
        });
    }

    function scheduleText(schedule) {
        const days = {
            2: 'Thứ 2', 3: 'Thứ 3', 4: 'Thứ 4', 5: 'Thứ 5',
            6: 'Thứ 6', 7: 'Thứ 7', 8: 'Chủ nhật'
        };
        const day = days[Number(schedule.weekday)] || ('Thứ ' + schedule.weekday);
        const dates = (schedule.start_date || '') + ' - ' + (schedule.end_date || '');
        return day + ' · Tiết ' + schedule.start_period + '-' + schedule.end_period +
            ' · ' + dates + ' · ' + (schedule.room || '');
    }

    function renderClasses(rows) {
        rows.forEach(function (row) {
            const tr = document.createElement('tr');
            tr.appendChild(td(row.class_code));
            tr.appendChild(td((row.course_code || '') + ' - ' + (row.course_name || '')));
            tr.appendChild(td(row.semester_name));
            tr.appendChild(td(row.lecturer_name));

            const scheduleTd = document.createElement('td');
            const schedules = Array.isArray(row.schedules) ? row.schedules : [];
            if (!schedules.length) {
                scheduleTd.appendChild(text('Chưa có lịch'));
            } else {
                schedules.forEach(function (schedule, index) {
                    const div = document.createElement('div');
                    div.className = 'schedule-line';
                    div.appendChild(text(scheduleText(schedule)));
                    scheduleTd.appendChild(div);
                });
            }
            tr.appendChild(scheduleTd);

            const count = String(row.registered_count ?? 0) + '/' + String(row.max_students ?? 0);
            tr.appendChild(td(count));

            const detail = link(
                baseUrl + '?r=admin/class/detail&id=' + encodeURIComponent(row.id),
                '👥', 'icon-btn', 'Danh sách sinh viên'
            );
            const edit = link(
                baseUrl + '?r=admin/class/edit&id=' + encodeURIComponent(row.id),
                '✎', 'icon-btn', 'Sửa'
            );
            const del = deleteForm(
                baseUrl + '?r=admin/class/delete',
                row.id,
                'Bạn có chắc muốn xóa lớp học phần này?'
            );
            tr.appendChild(actionsCell([detail, edit, del]));
            tbody.appendChild(tr);
        });
    }

    function renderRows(rows) {
        tbody.replaceChildren();

        if (!rows.length) {
            const tr = document.createElement('tr');
            const tdEmpty = document.createElement('td');
            tdEmpty.colSpan = type === 'students' ? 8 : type === 'lecturers' ? 6 : type === 'courses' ? 6 : 7;
            tdEmpty.className = 'empty-row';
            tdEmpty.appendChild(text('Không tìm thấy dữ liệu phù hợp.'));
            tr.appendChild(tdEmpty);
            tbody.appendChild(tr);
            return;
        }

        if (type === 'students') renderStudents(rows);
        else if (type === 'lecturers') renderLecturers(rows);
        else if (type === 'courses') renderCourses(rows);
        else if (type === 'classes') renderClasses(rows);
    }

    function pageButton(page, current, label) {
        const a = document.createElement('a');
        a.href = baseUrl + '?r=admin/' + type + '&q=' + encodeURIComponent(input.value.trim()) + '&page=' + page;
        a.className = page === current ? 'active' : '';
        a.appendChild(text(label || page));
        a.addEventListener('click', function (event) {
            event.preventDefault();
            load(page, true);
        });
        return a;
    }

    function renderPagination(info) {
        pagination.replaceChildren();
        const current = Number(info.page || 1);
        const pages = Number(info.pages || 1);

        if (pages <= 1) return;

        if (current > 1) pagination.appendChild(pageButton(current - 1, current, '‹'));

        const start = Math.max(1, current - 2);
        const end = Math.min(pages, current + 2);
        for (let i = start; i <= end; i++) {
            pagination.appendChild(pageButton(i, current, String(i)));
        }

        if (current < pages) pagination.appendChild(pageButton(current + 1, current, '›'));
    }

    function updateUrl(page) {
        const url = new URL(window.location.href);
        url.searchParams.set('r', 'admin/' + type);
        const q = input.value.trim();
        if (q) url.searchParams.set('q', q);
        else url.searchParams.delete('q');
        if (page > 1) url.searchParams.set('page', String(page));
        else url.searchParams.delete('page');
        window.history.replaceState({}, '', url.toString());
    }

    async function load(page, updateHistory) {
        page = Math.max(1, Number(page) || 1);
        const keyword = input.value.trim();
        const thisRequest = ++requestNumber;

        if (controller) controller.abort();
        controller = new AbortController();

        root.classList.add('is-loading');

        try {
            const url = endpoint +
                '?type=' + encodeURIComponent(type) +
                '&q=' + encodeURIComponent(keyword) +
                '&page=' + encodeURIComponent(page);

            const response = await fetch(url, {
                method: 'GET',
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
                signal: controller.signal
            });

            if (!response.ok) throw new Error('HTTP ' + response.status);

            const json = await response.json();
            if (thisRequest !== requestNumber) return;
            if (!json.ok) throw new Error(json.message || 'Không thể tải dữ liệu.');

            renderRows(json.data || []);
            renderPagination(json.pagination || {});

            const total = Number(json.pagination?.total || 0);
            if (resultInfo) {
                resultInfo.textContent = keyword
                    ? 'Tìm thấy ' + total + ' kết quả cho “' + keyword + '”.'
                    : 'Tổng số: ' + total + ' bản ghi.';
            }

            if (updateHistory) updateUrl(Number(json.pagination?.page || page));
        } catch (error) {
            if (error.name === 'AbortError') return;

            tbody.replaceChildren();
            const tr = document.createElement('tr');
            const cell = document.createElement('td');
            cell.colSpan = type === 'students' ? 8 : type === 'lecturers' ? 6 : type === 'courses' ? 6 : 7;
            cell.className = 'empty-row error-text';
            cell.appendChild(text('Không thể tải dữ liệu. Vui lòng thử lại.'));
            tr.appendChild(cell);
            tbody.appendChild(tr);
        } finally {
            if (thisRequest === requestNumber) {
                root.classList.remove('is-loading');
            }
        }
    }

    input.addEventListener('input', function () {
        clearTimeout(timer);
        const keyword = input.value.trim();
        clearButton.hidden = keyword === '';

        timer = setTimeout(function () {
            load(1, true);
        }, 300);
    });

    clearButton.addEventListener('click', function () {
        input.value = '';
        clearButton.hidden = true;
        load(1, true);
        input.focus();
    });

    window.addEventListener('popstate', function () {
        const params = new URLSearchParams(window.location.search);
        input.value = params.get('q') || '';
        clearButton.hidden = input.value.trim() === '';
        load(Number(params.get('page') || 1), false);
    });

    clearButton.hidden = input.value.trim() === '';
})();
