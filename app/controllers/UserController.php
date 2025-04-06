<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\RoleModel;
use App\Config\Database;

use PDO;

class UserController {
    private $userModel;
    private $roleModel;
    private ?\PDO $db = null; 

    public function __construct() {
         // Get the DB connection
         $this->db = (new Database())->getConnection();

         // Check if the connection was successful
         if (!$this->db) {
             throw new \RuntimeException('Database connection failed.');
         }
        $this->userModel = new UserModel($this->db);
        $this->roleModel = new RoleModel($this->db);
    }

    // Show the signup form
    public function showSignupForm() {
        include_once __DIR__ . '/../views/auth/signup.php';
    }

    // Handle signup logic
    public function signup() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fullname = $_POST['fullname'];
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $phone = $_POST['phone'];
            $address = $_POST['address'];
            $role = $this->roleModel->getRoleByName('admin');
            if ($role) {
                $roleId = $role['id'];
            } else {
                // Handle the case where the role is not found
                $errorMessage = "Role not found.";
                include_once __DIR__ . '/../views/auth/signup.php';
                return;
            }

            // Call UserModel to create a new user
            $result = $this->userModel->createUser($fullname, $email, $password, $phone, $address, $roleId);

            if ($result) {
                $successMessage = "Account created successfully!";
                include_once __DIR__ . '/../views/auth/signup.php';
            } else {
                $errorMessage = "Failed to create account. Please try again.";
                include_once __DIR__ . '/../views/auth/signup.php';
            }
        }
    }

    // Show the login form
    public function showLoginForm() {
        $errorMessage = $_SESSION['error_message'] ?? ''; // Retrieve error message
        unset($_SESSION['error_message']); // Clear the error message
        include_once __DIR__ . '/../views/auth/login.php';
    }

    // Handle login logic
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $user = $this->userModel->getUserByEmail($email);

            if ($user && $user['role_name'] !== 'admin' && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['fullname'] = $user['fullname'];
                $_SESSION['phone'] = $user['phone'];
                $_SESSION['address'] = $user['address'];
                $_SESSION['role'] = $user['role_name']; 
                header("Location: /");
            } else {
                $_SESSION['error_message'] = "Invalid email or password";
                header("Location: /login");
                exit;
            }
        }
    }

    // Logout the user
    public function logout() {
        session_start();
        session_unset();
        session_destroy();
        header('Location: /login');
        exit;
    }
}
?>