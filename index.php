<?php
require_once 'controllers/AppointmentController.php';
require_once 'controllers/CustomerController.php';

$controller = $_GET['controller'] ?? 'appointments';
$action = $_GET['action'] ?? 'index';

if ($controller == 'appointments') {
    $controllerInstance = new AppointmentController();
} elseif ($controller == 'customers') {
    $controllerInstance = new CustomerController();
} else {
    die("Controller không hợp lệ!");
}

if (method_exists($controllerInstance, $action)) {
    $controllerInstance->$action($_GET['id'] ?? null);
} else {
    die("Action không tồn tại!");
}
?>
