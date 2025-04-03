<?php
$controller = $_GET['controller'] ?? 'product';
$action = $_GET['action'] ?? 'index';

if ($controller == 'product') {
    require_once "app/controllers/ProductController.php";
    (new ProductController())->index();
} elseif ($controller == 'cart') {
    require_once "app/controllers/CartController.php";
    (new CartController())->$action();
}
elseif ($controller == 'order') { // Thêm routing cho OrderController
    require_once "app/controllers/OrderController.php";
    $orderController = new OrderController();
    if (method_exists($orderController, $action)) {
         $orderController->$action();
    } else {
        echo "Action not found for OrderController"; // Hoặc xử lý lỗi 404
    }
}
?>
