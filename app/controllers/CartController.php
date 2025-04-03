<?php
require_once "app/models/CartModel.php";

class CartController {
    public function add() {
        $product_id = $_GET['id'];

        $cartModel = new CartModel();
        $cartModel->addToCart($product_id);
        header("Location: index.php?controller=cart&action=view");
    }

    public function view() {
        $cartModel = new CartModel();
        $cartItems = $cartModel->getCartItems();
        require_once "app/views/cart/cart.html";
    }

    public function remove() {
        $id = $_GET['id'];

        $cartModel = new CartModel();
        $cartModel->removeFromCart($id);
        header("Location: index.php?controller=cart&action=view");
    }

    public function clear() {
        $cartModel = new CartModel();
        $cartModel->clearCart();
        header("Location: index.php?controller=cart&action=view");
    }
}
?>
