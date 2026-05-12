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

        if ($result === "email_not_found") {
            $_SESSION['error'] = "Email không tồn tại";
            header("Location: ?module=auth&action=login");
            exit;
        }

        if ($result === "wrong_password") {
            $_SESSION['error'] = "Sai mật khẩu";
            header("Location: ?module=auth&action=login");
            exit;
        }

        $user = $result;

        if ($user['status'] == 0) {
            $_SESSION['error'] = "Tài khoản bị khóa";
            header("Location: ?module=auth&action=login");
            exit;
        }

        // =========================
        // SESSION LOGIN
        // =========================
        $_SESSION['user'] = [
            'id' => $user['user_id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];

        switch ($user['role']) {

            case 'admin':
                header("Location: ?module=dashboard&action=index");
                break;

            case 'teacher':
                header("Location: ?module=class&action=index");
                // hoặc teacher landing page bạn muốn
                break;

            case 'parent':
                header("Location: ?module=parent&action=index");
                break;

            case 'student':
                header("Location: ?module=student&action=index");
                break;

            default:
                header("Location: ?module=auth&action=login");
                break;
        }
        exit;

        exit;
    }

    public function logout()
    {
        session_destroy();
        header("Location: ?module=auth&action=login");
        exit;
    }
}