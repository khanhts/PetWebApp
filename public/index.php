<?php
session_start();
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controllers\ProductController;
use App\Controllers\HomeController;
use App\Controllers\UserController;
use App\Controllers\CartController;
use App\Controllers\AppointmentController;
use App\Controllers\AdminController;
use App\Models\ProductModel;

// Parse the URL
$requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$requestUri = trim($requestUri, '/');

// Simple route matching
if ($requestUri === '' || $requestUri === 'home') {
    $controller = new HomeController();
    $controller->index();
} elseif ($requestUri === 'signup'&& $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new UserController();
    $controller->showSignupForm();
} elseif ($requestUri === 'signup'&& $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new UserController();
    $controller->signup();
} elseif ($requestUri === 'login' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new UserController();
    $controller->showLoginForm();
} elseif ($requestUri === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new UserController();
    $controller->login();
} elseif ($requestUri === 'logout') {
    $controller = new UserController();
    $controller->logout();
} elseif ($requestUri === 'products') {
    $controller = new ProductController();
    $controller->index(); // Handles product listing and search
} elseif (preg_match('#^products/(\d+)$#', $requestUri, $matches)) {
    $productId = (int)$matches[1];
    $controller = new ProductController();
    $controller->show($productId);
} elseif ($requestUri === 'cart') {
    $controller = new CartController();
    $controller->index();
} elseif ($requestUri === 'cart/add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CartController();
    $controller->addToCart();
} elseif ($requestUri === 'cart/checkout' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new CartController();
    $controller->checkout();
} elseif ($requestUri === 'services'){
    $controller = new AppointmentController();
    $controller->index();
} elseif ($requestUri === 'appointment/getDisabledDates'){
    $controller = new AppointmentController();
    $controller->getDisabledDates();
} elseif ($requestUri === 'appointment/create'){
    $controller = new AppointmentController();
    $controller->add();
} elseif ($requestUri === 'admin/login') {
    $controller = new AdminController();
    $controller->login();
} elseif ($requestUri === 'admin/logout') {
    $controller = new AdminController();
    $controller->logout();
} elseif ($requestUri === 'admin/product/create' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new ProductController();
    $controller->create(); // Show the product creation form
} elseif ($requestUri === 'admin/product/store' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new ProductController();
    $controller->store(); // Handle the product creation form submission
} elseif ($requestUri === 'admin/product-management') {
    $controller = new ProductController();
    $controller->productManagement();
} elseif (preg_match('/^admin\/product\/delete\/(\d+)$/', $requestUri, $matches)) {
    $controller = new ProductController();
    $controller->deleteProduct((int)$matches[1]);
}
elseif ($requestUri === 'accessDenied') {
    require_once __DIR__ . '/../app/views/accessDenied.php';
}
else {
    http_response_code(404);
    echo '404 - Not Found';
}
