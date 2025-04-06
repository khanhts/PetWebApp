<?php

namespace App\Models;

use PDO;
use PDOException;

class ProductModel
{
    private PDO $db;
    private string $table_name = "product";

    
    private string $uploadDirSystem;
    
    private string $uploadDirWeb = '/webbanhang/public/uploads/images/products/'; // *** KIỂM TRA KỸ ĐƯỜNG DẪN NÀY ***

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Fetch all products
    public function getAllProducts(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table_name}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProductById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table_name} WHERE id = :id");
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

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return $this->uploadDirWeb . $filename;
        } else {
            error_log("Image upload failed for file: " . $file['name']);
            return null;
        }
    }

    // Fetch products based on search query
    public function getProducts(string $search = ''): array
    {
        if (!empty($search)) {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table_name} WHERE name LIKE :search OR description LIKE :search");
            $searchTerm = '%' . $search . '%';
            $stmt->bindParam(':search', $searchTerm, PDO::PARAM_STR);
        } else {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table_name}");
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>