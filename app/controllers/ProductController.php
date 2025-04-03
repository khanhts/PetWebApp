<?php
require_once "app/models/ProductModel.php";

class ProductController {
    public function index() {
        $productModel = new ProductModel();
        $products = $productModel->getAllProducts();
        require_once "app/views/product/list.php";
    }
}
?>
