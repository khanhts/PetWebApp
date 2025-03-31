<?php
require_once './models/User.php';

class UserController {
    private $userModel;

    public function __construct() {
        global $pdo;
        $this->userModel = new User($pdo);
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        require './views/users/index.php';
    }

    public function customers() {
        $customers = $this->userModel->getCustomers();
        require './views/users/customers.php';
    }
}
?>
