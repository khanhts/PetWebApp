<?php

namespace App\Models;

use PDO;
use PDOException;

class ProductModel
{
    private PDO $db;
    private string $table_name = "product";

    
    private string $uploadDirSystem;
    
    private string $uploadDirWeb = '/images/';

    public function __construct(PDO $db)
    {
        $this->db = $db;

        $this->uploadDirSystem = $_SERVER['DOCUMENT_ROOT'] . '/uploads/images/';
    }

    
    public function getAllProducts(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table_name} WHERE isDeleted = 0");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table_name} WHERE id = :id AND isDeleted = 0");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        return $product ?: null;
    }

   
    public function createProduct($name, $description, $price, $imagePath = null, $categoryId = null): bool
    {
        try {
            $query = "INSERT INTO {$this->table_name} (name, description, price, image_path, category_id) 
                    VALUES (:name, :description, :price, :image_path, :category_id)";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':image_path', $imagePath);
            $stmt->bindParam(':category_id', $categoryId);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error creating product: " . $e->getMessage());
            return false;
        }
    }

    public function handleImageUpload($file): ?string
    {
        $filename = uniqid() . '_' . basename($file['name']);
        $targetPath = $this->uploadDirSystem . $filename;

        
        if (!is_dir($this->uploadDirSystem)) {
            mkdir($this->uploadDirSystem, 0777, true);
        }

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $this->uploadDirWeb . $filename;
        } else {
            error_log("Image upload failed for file: " . $file['name']);
            return null;
        }
    }

    
    public function getProducts(string $search = ''): array
    {
        if (!empty($search)) {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table_name} WHERE (name LIKE :search OR description LIKE :search) AND isDeleted = 0");
            $searchTerm = '%' . $search . '%';
            $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table_name} WHERE isDeleted = 0");
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllProductsWithCategory(): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name
            FROM product p
            LEFT JOIN category c ON p.category_id = c.id
            WHERE p.isDeleted = 0
        ");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function deleteProductById(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table_name} SET isDeleted = 1 WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getPaginatedProducts(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name AS category_name
            FROM product p
            LEFT JOIN category c ON p.category_id = c.id
            WHERE p.isDeleted = 0
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTotalProducts(): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table_name} WHERE isDeleted = 0");
        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    public function updateProduct($id, $name, $description, $price, $imagePath = null, $categoryId = null): bool
    {
        try {
            $query = "UPDATE {$this->table_name} 
                      SET name = :name, description = :description, price = :price, category_id = :category_id";

            
            if ($imagePath) {
                $query .= ", image_path = :image_path";
            }

            $query .= " WHERE id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':price', $price);
            $stmt->bindParam(':category_id', $categoryId);
            if ($imagePath) {
                $stmt->bindParam(':image_path', $imagePath);
            }
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating product: " . $e->getMessage());
            return false;
        }
    }
}
?>