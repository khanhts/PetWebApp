<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Config\Database;

class AdminController
{
    private $userModel;
    private ?\PDO $db = null;

    public function __construct()
    {
        
        $this->db = (new Database())->getConnection();
        $this->userModel = new UserModel($this->db);
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            
            $admin = $this->userModel->getUserByEmail($email);

            if ($admin && $admin['role_name'] === 'admin' && password_verify($password, $admin['password'])) {
                session_unset();
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['role'] = $admin['role_name'];
                header('Location: /admin/product-management'); 
                exit;
            } else {
                $_SESSION['admin_login_error'] = "Invalid email or password";
            }
        }

        require_once __DIR__ . '/../views/admin/login.php';
    }

    public function logout()
    {
        session_unset();
        header('Location: /admin/login');
        exit;
    }
}