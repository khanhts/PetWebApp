<?php

namespace App\Controllers;

use App\Models\ReceiptModel;

class CartController
{
    private ReceiptModel $receiptModel;
    private ?\PDO $db = null; 

    public function __construct()
    {
        // Assuming you have a database connection instance
        $db = new \PDO('mysql:host=localhost;dbname=petweb;charset=utf8', 'root', '');
        $this->db = $db;

        // Check if the connection was successful
        if (!$this->db) {
            throw new \RuntimeException('Database connection failed.');
        }
        $this->receiptModel = new ReceiptModel($this->db);
    }

    // Display the cart
    public function index()
    {
        $cart = $_SESSION['cart'] ?? [];
        require_once __DIR__ . '/../views/cart/index.php';
    }

    // Add a product to the cart
    public function addToCart()
    {
        // Decode the JSON input
        $data = json_decode(file_get_contents('php://input'), true);

        // Validate the required keys
        if (!isset($data['product_id'], $data['name'], $data['price'], $data['quantity'])) {
            http_response_code(400); // Bad Request
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
            return;
        }

        $productId = $data['product_id'];
        $name = $data['name'];
        $price = $data['price'];
        $quantity = $data['quantity'];

        // Initialize the cart if it doesn't exist
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Add or update the product in the cart
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = [
                'product_id' => $productId,
                'name' => $name,
                'price' => $price,
                'quantity' => $quantity,
            ];
        }

        // Return a success response
        echo json_encode(['status' => 'success', 'message' => 'Product added to cart successfully!']);
    }

    // Checkout and create a receipt
    public function checkout()
    {
        if (empty($_SESSION['cart'])) {
            header('Location: /cart');
            exit;
        }

        $userId = $_SESSION['user_id']; // Assuming the user is logged in
        $cart = $_SESSION['cart'];

        // Calculate total price
        $totalPrice = array_reduce($cart, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        // Create a receipt
        $receiptId = $this->receiptModel->createReceipt($userId, $totalPrice);

        // Add items to the receipt
        foreach ($cart as $item) {
            $this->receiptModel->addReceiptItem($receiptId, $item['product_id'], $item['quantity'], $item['price']);
        }

        // Clear the cart
        unset($_SESSION['cart']);

        // Fetch receipt and items for the view
        $receipt = $this->receiptModel->getReceiptById($receiptId);
        $items = $this->receiptModel->getReceiptItems($receiptId);

        // Load the receipt view
        require_once __DIR__ . '/../views/cart/receipt2.php';
    }
}