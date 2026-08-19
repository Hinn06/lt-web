<?php

require_once "../app/models/User.php";

class AuthController
{
    private $userModel;

    public function __construct($pdo)
    {
        $this->userModel = new User($pdo);
    }

    public function login($username, $password)
    {
        $user = $this->userModel->findByUsername($username);

        // Không tìm thấy tài khoản
        if (!$user) {
            return [
                "success" => false,
                "message" => "Tài khoản không tồn tại hoặc đã bị khóa."
            ];
        }

        // Kiểm tra mật khẩu
        if (!password_verify($password, $user["password"])) {
            return [
                "success" => false,
                "message" => "Mật khẩu không chính xác."
            ];
        }

        // Đăng nhập thành công
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["full_name"] = $user["full_name"];
        $_SESSION["role"] = $user["role"];

        return [
            "success" => true,
            "role" => $user["role"]
        ];
    }
}