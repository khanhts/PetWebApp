<?php

namespace App\Controllers;

use App\Models\ReceiptModel;
use App\Config\Database;

class ReceiptController
{
    private ReceiptModel $receiptModel;
    private ?\PDO $db = null; 

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->receiptModel = new ReceiptModel($this->db);
    }

    public function myReceipts()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }

        $userId = $_SESSION['user_id'];
        $receipts = $this->receiptModel->getReceiptsByUserId($userId);

        require_once __DIR__ . '/../views/receipts/my-receipts.php';
    }

    public function receiptDetails($receiptId)
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login'); 
            exit;
        }

        $receipt = $this->receiptModel->getReceiptById($receiptId);

        if (!$receipt || $receipt['user_id'] != $_SESSION['user_id']) {
            header('Location: /receipts/me'); 
            exit;
        }

        $items = $this->receiptModel->getReceiptItems($receiptId);

        require_once __DIR__ . '/../views/receipts/details.php';
    }
}