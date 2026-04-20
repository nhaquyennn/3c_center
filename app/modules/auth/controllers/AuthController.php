<?php

class AuthController extends Controller
{
    public function login()
    {
        $view = ROOT_PATH . "/modules/auth/views/login.php";
        $header = ROOT_PATH . "/modules/layouts/header_auth.php";
        require_once ROOT_PATH . "/modules/layouts/auth_main.php";
    }

    public function handleLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $userModel = new User();
        $result = $userModel->login($email, $password);

        // email không tồn tại
        if ($result === "email_not_found") {
            $_SESSION['error'] = "Email không tồn tại";
            header("Location: ?module=auth&action=login");
            exit;
        }

        // sai mật khẩu
        if ($result === "wrong_password") {
            $_SESSION['error'] = "Sai mật khẩu";
            header("Location: ?module=auth&action=login");
            exit;
        }

        // user hợp lệ
        $user = $result;

        // tài khoản bị khóa
        if ($user['status'] == 0) {
            $_SESSION['error'] = "Tài khoản bị khóa";
            header("Location: ?module=auth&action=login");
            exit;
        }

        // lưu session
        $_SESSION['user'] = [
            'id' => $user['user_id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];

        // phân quyền
        switch ($user['role']) {
            case 'admin':
                header("Location: ?module=dashboard");
                break;

            case 'parent':
                header("Location: ?module=parent&action=dashboard");
                break;

            case 'teacher':
                header("Location: ?module=teacher");
                break;

            case 'student':
                header("Location: ?module=student");
                break;

            default:
                header("Location: ?module=auth&action=login");
        }

        exit;
    }

    public function logout()
    {
        session_destroy();
        header("Location: ?module=auth&action=login");
    }
}