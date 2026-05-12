<?php

class User
{
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect(); // PDO
    }

    // Tìm user theo email (PDO chuẩn)
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        return $stmt->fetch(PDO::FETCH_ASSOC); 
    }

    // Login
    public function login($email, $password)
    {
        $user = $this->findByEmail($email);

        if (!$user) {
            return "email_not_found";
        }
// Đã hash
        // if (!password_verify($password, $user['password'])) {
        //     return "wrong_password";
        // }
        if ($password !== $user['password']) {
            return "wrong_password";
        }
        return $user;
    }
}