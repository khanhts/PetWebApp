<?php

namespace App\Models;

use PDO;

class ReceiptModel
{
    private PDO $db;
    private string $table_name = "receipt";
    private string $table_items = "receipt_items";
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Create a new receipt
    public function createReceipt(int $userId, float $totalPrice, string $paymentStatus = 'pending'): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table_name} (user_id, total_price, payment_status)
            VALUES (:user_id, :total_price, :payment_status)
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':total_price', $totalPrice);
        $stmt->bindParam(':payment_status', $paymentStatus);
        $stmt->execute();

        return (int)$this->db->lastInsertId(); // Return the ID of the newly created receipt
    }

    // Add items to a receipt
    public function addReceiptItem(int $receiptId, int $productId, int $quantity, float $price): bool
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table_items} (receipt_id, product_id, quantity, price)
            VALUES (:receipt_id, :product_id, :quantity, :price)
        ");
        $stmt->bindParam(':receipt_id', $receiptId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price', $price);
        return $stmt->execute();
    }

    // Get a receipt by ID
    public function getReceiptById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT r.*, u.fullname AS user_name
            FROM {$this->table_name} r
            INNER JOIN users u ON r.user_id = u.id
            WHERE r.id = :id
        ");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $receipt = $stmt->fetch(PDO::FETCH_ASSOC);

        return $receipt ?: null;
    }

    // Get items for a specific receipt
    public function getReceiptItems(int $receiptId): array
    {
        $stmt = $this->db->prepare("
            SELECT ri.*, p.name AS product_name
            FROM {$this->table_items} ri
            INNER JOIN product p ON ri.product_id = p.id
            WHERE ri.receipt_id = :receipt_id
        ");
        $stmt->bindParam(':receipt_id', $receiptId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Update payment status of a receipt
    public function updatePaymentStatus(int $receiptId, string $paymentStatus): bool
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table_name}
            SET payment_status = :payment_status
            WHERE id = :receipt_id
        ");
        $stmt->bindParam(':payment_status', $paymentStatus);
        $stmt->bindParam(':receipt_id', $receiptId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Get all receipts for a specific user
    public function getReceiptsByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table_name}
            WHERE user_id = :user_id
            ORDER BY purchase_date DESC
        ");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}