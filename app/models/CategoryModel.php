<?php

namespace App\Models;
use PDO;
use PDOException;

class CategoryModel
{
    private $conn;
    private string $table_name = "category";

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAllCategories(): array
    {
        $stmt = $this->conn->prepare("SELECT id, name FROM category");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
