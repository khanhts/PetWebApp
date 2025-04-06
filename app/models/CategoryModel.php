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
        try {
            $query = "SELECT id, name FROM " . $this->table_name . " ORDER BY name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error fetching categories: " . $e->getMessage());
            return [];
        }
    }
}
