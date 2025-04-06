<?php
// Bắt đầu session nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Hiển thị lỗi (Chỉ bật khi debug)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Gọi file header.php (nếu có)
$headerPath = __DIR__ . '/views/layouts/header.php';

if (file_exists($headerPath)) {
    include $headerPath;
} else {
    die("Lỗi: Không tìm thấy file header.php tại " . realpath($headerPath));
}

// Lấy controller và action từ URL, mặc định là "appointment/index"
$controller = $_GET['controller'] ?? 'appointment';
$action = $_GET['action'] ?? 'index';

// Danh sách controller hợp lệ (để tránh lỗi nhập sai URL)
$validControllers = ['appointment', 'pet', 'user'];

if (!in_array($controller, $validControllers)) {
    die("Lỗi: Controller '$controller' không hợp lệ.");
}

// Định nghĩa danh sách các controller và file tương ứng
$controllerMap = [
    'appointment' => 'controllers/AppointmentController.php',
    'pet'         => 'controllers/PetController.php',
    'user'        => 'controllers/UserController.php',
];

// Kiểm tra xem file controller có tồn tại không
$controllerPath = __DIR__ . '/' . $controllerMap[$controller];

if (!file_exists($controllerPath)) {
    die("Lỗi: Không tìm thấy file controller '$controllerPath'.");
}

// Gọi file controller
require_once $controllerPath;

// Khởi tạo controller
$controllerClass = ucfirst($controller) . "Controller";

if (!class_exists($controllerClass)) {
    die("Lỗi: Không tìm thấy class '$controllerClass'.");
}

$controllerInstance = new $controllerClass();

// Kiểm tra action có tồn tại không
if (!method_exists($controllerInstance, $action)) {
    die("Lỗi: Action '$action' không tồn tại trong controller '$controllerClass'.");
}
// Route API lấy danh sách ngày bị chặn
if ($controller === 'appointment' && $action === 'getDisabledDates') {
    $appointmentController->getDisabledDates();
}


// Gọi action
$controllerInstance->$action();
