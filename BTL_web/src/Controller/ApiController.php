<?php

namespace App\Controller;

use App\Core\Auth;
use App\Repository\StudentRepository;
use App\Repository\LecturerRepository;
use App\Repository\CourseRepository;
use App\Repository\ClassRepository;

class ApiController
{
    public function __construct(private \PDO $pdo)
    {
    }

    public function lecturersByCourse(): void
    {
        Auth::requireRole('admin');
        header('Content-Type: application/json; charset=utf-8');

        $id = filter_input(INPUT_GET, 'course_id', FILTER_VALIDATE_INT);

        if (!$id) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'course_id không hợp lệ'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $repo = new LecturerRepository($this->pdo);
            echo json_encode([
                'ok' => true,
                'data' => $repo->byCourse($id)
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'Không thể tải danh sách giảng viên.'
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public function adminSearch(): void
    {
        Auth::requireRole('admin');
        header('Content-Type: application/json; charset=utf-8');

        $type = trim($_GET['type'] ?? '');
        $q = trim($_GET['q'] ?? '');
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 8;

        $allowed = ['students', 'lecturers', 'courses', 'classes'];

        if (!in_array($type, $allowed, true)) {
            http_response_code(422);
            echo json_encode([
                'ok' => false,
                'message' => 'Loại dữ liệu không hợp lệ.'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            switch ($type) {
                case 'students':
                    $repo = new StudentRepository($this->pdo);
                    break;
                case 'lecturers':
                    $repo = new LecturerRepository($this->pdo);
                    break;
                case 'courses':
                    $repo = new CourseRepository($this->pdo);
                    break;
                case 'classes':
                    $repo = new ClassRepository($this->pdo);
                    break;
            }

            $total = $repo->count($q);
            $pages = max(1, (int)ceil($total / $limit));
            $page = min($page, $pages);
            $offset = ($page - 1) * $limit;
            $rows = $repo->findPage($q, $limit, $offset);

            echo json_encode([
                'ok' => true,
                'data' => $rows,
                'pagination' => [
                    'page' => $page,
                    'pages' => $pages,
                    'total' => $total,
                    'limit' => $limit
                ]
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'ok' => false,
                'message' => 'Không thể tải dữ liệu tìm kiếm.'
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
