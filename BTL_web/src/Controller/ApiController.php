<?php
namespace App\Controller;
use App\Core\Auth;
use App\Repository\LecturerRepository;
class ApiController {
 public function __construct(private \PDO $pdo){}
 public function lecturersByCourse():void{Auth::requireRole('admin');header('Content-Type: application/json; charset=utf-8');$id=filter_input(INPUT_GET,'course_id',FILTER_VALIDATE_INT);if(!$id){http_response_code(422);echo json_encode(['ok'=>false,'message'=>'course_id không hợp lệ'],JSON_UNESCAPED_UNICODE);return;}echo json_encode(['ok'=>true,'data'=>(new LecturerRepository($this->pdo))->byCourse($id)],JSON_UNESCAPED_UNICODE);}
}
