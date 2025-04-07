<?php

namespace App\Controllers;

use App\Models\ReceiptModel;

class CartController
{
    private ReceiptModel $receiptModel;
    private ?\PDO $db = null; 

    public function __construct()
    {
        
        $db = new \PDO('mysql:host=localhost;dbname=petweb;charset=utf8', 'root', '');
        $this->db = $db;

        
        if (!$this->db) {
            throw new \RuntimeException('Database connection failed.');
        }
        $this->receiptModel = new ReceiptModel($this->db);
    }

    
    public function index()
    {
        $cart = $_SESSION['cart'] ?? [];
        require_once __DIR__ . '/../views/cart/index.php';
    }

    
    public function addToCart()
    {
        
        $data = json_decode(file_get_contents('php://input'), true);

        
        if (!isset($data['product_id'], $data['name'], $data['price'], $data['quantity'])) {
            http_response_code(400); 
            echo json_encode(['status' => 'error', 'message' => 'Invalid input data.']);
            return;
        }

        $productId = $data['product_id'];
        $name = $data['name'];
        $price = $data['price'];
        $quantity = $data['quantity'];

        
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        
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

        
        echo json_encode(['status' => 'success', 'message' => 'Product added to cart successfully!']);
    }

    
    public function checkout()
    {
        if (empty($_SESSION['cart'])) {
            header('Location: /cart');
            exit;
        }

        $userId = $_SESSION['user_id']; 
        $cart = $_SESSION['cart'];

        
        $totalPrice = array_reduce($cart, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0);

        
        $receiptId = $this->receiptModel->createReceipt($userId, $totalPrice);

        
        foreach ($cart as $item) {
            $this->receiptModel->addReceiptItem($receiptId, $item['product_id'], $item['quantity'], $item['price']);
        }

        
        unset($_SESSION['cart']);

        
        $receipt = $this->receiptModel->getReceiptById($receiptId);
        $items = $this->receiptModel->getReceiptItems($receiptId);

        
        require_once __DIR__ . '/../views/cart/receipt2.php';
    }
}