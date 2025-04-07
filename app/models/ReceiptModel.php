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

        return (int)$this->db->lastInsertId(); 
    }

    
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

    
    public function getReceiptById($receiptId): ?array
    {
        $query = "
            SELECT 
                r.*, 
                u.fullname, 
                u.email, 
                u.phone
            FROM {$this->table_name} r
            LEFT JOIN users u ON r.user_id = u.id
            WHERE r.id = :receipt_id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':receipt_id', $receiptId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    
    public function getReceiptItems($receiptId): array
    {
        $query = "
            SELECT 
                ri.*, 
                p.name AS product_name
            FROM {$this->table_items} ri
            LEFT JOIN product p ON ri.product_id = p.id
            WHERE ri.receipt_id = :receipt_id
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':receipt_id', $receiptId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
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

    
    public function getReceiptsByUserId(int $userId): array
    {
        $query = "
            SELECT 
                r.id AS receipt_id, 
                r.total_price, 
                r.purchase_date
            FROM {$this->table_name} r
            WHERE r.user_id = :user_id
            ORDER BY r.purchase_date DESC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}