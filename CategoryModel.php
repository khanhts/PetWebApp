<?php
// File: webbanhang/app/models/CategoryModel.php

class CategoryModel
{
    private PDO $conn;
    private string $table_name = "category";

    public function __construct(PDO $db)
    {
        $this->conn = $db;
    }

    /**
     * Lấy tất cả các danh mục.
     * @return array Mảng các đối tượng danh mục hoặc mảng rỗng.
     */
    public function getAllCategories(): array
    {
        try {
            $query = "SELECT id, name, description FROM " . $this->table_name . " ORDER BY name ASC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error fetching all categories: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Lấy thông tin một danh mục bằng ID.
     * @param int $id ID danh mục.
     * @return object|false Đối tượng danh mục hoặc false nếu không tìm thấy/lỗi.
     */
    public function getCategoryById(int $id): object|false
    {
         if ($id <= 0) return false;
        try {
            $query = "SELECT id, name, description FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error fetching category ID {$id}: " . $e->getMessage());
            return false;
        }
    }

     /**
      * Thêm danh mục mới.
      * @param array $data Mảng chứa 'name', 'description'.
      * @return int|array ID danh mục mới nếu thành công, mảng lỗi nếu thất bại.
      */
     public function addCategory(array $data): int|array
     {
         $errors = $this->validateCategoryData($data);
         if (!empty($errors)) {
             return $errors;
         }

         try {
             $this->conn->beginTransaction();
             $query = "INSERT INTO " . $this->table_name . " (name, description, created_at, updated_at) VALUES (:name, :description, NOW(), NOW())";
             $stmt = $this->conn->prepare($query);

             $name = htmlspecialchars(strip_tags($data['name'] ?? ''), ENT_QUOTES, 'UTF-8');
             $description = htmlspecialchars(strip_tags($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');

             $stmt->bindValue(':name', $name);
             $stmt->bindValue(':description', $description);

             $stmt->execute();
             $lastId = $this->conn->lastInsertId();
             $this->conn->commit();
             return (int)$lastId;

         } catch (PDOException $e) {
             $this->conn->rollBack();
             error_log("Database error adding category: " . $e->getMessage());
             // Kiểm tra lỗi trùng tên (unique constraint)
              if ($e->getCode() == 23000) { // Mã lỗi SQLSTATE cho unique constraint violation
                  return ['name' => 'Tên danh mục này đã tồn tại.'];
              }
             return ['database' => 'Lỗi cơ sở dữ liệu khi thêm danh mục.'];
         }
     }

     /**
      * Cập nhật danh mục.
      * @param int $id ID danh mục cần cập nhật.
      * @param array $data Dữ liệu mới ('name', 'description').
      * @return bool|array True nếu thành công, mảng lỗi nếu thất bại.
      */
     public function updateCategory(int $id, array $data): bool|array
     {
          if ($id <= 0) return ['id' => 'ID danh mục không hợp lệ.'];
          // Kiểm tra danh mục tồn tại trước khi cập nhật
          $category = $this->getCategoryById($id);
          if (!$category) {
              return ['id' => 'Danh mục không tồn tại để cập nhật.'];
          }

         $errors = $this->validateCategoryData($data);
         if (!empty($errors)) {
             return $errors;
         }

         try {
             $this->conn->beginTransaction();
             $query = "UPDATE " . $this->table_name . " SET name = :name, description = :description, updated_at = NOW() WHERE id = :id";
             $stmt = $this->conn->prepare($query);

             $name = htmlspecialchars(strip_tags($data['name'] ?? ''), ENT_QUOTES, 'UTF-8');
             $description = htmlspecialchars(strip_tags($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');

             $stmt->bindParam(':id', $id, PDO::PARAM_INT);
             $stmt->bindValue(':name', $name);
             $stmt->bindValue(':description', $description);

             $success = $stmt->execute();
             $this->conn->commit();
             return $success;

         } catch (PDOException $e) {
             $this->conn->rollBack();
             error_log("Database error updating category ID {$id}: " . $e->getMessage());
             if ($e->getCode() == 23000) {
                 return ['name' => 'Tên danh mục này đã tồn tại.'];
             }
             return ['database' => 'Lỗi cơ sở dữ liệu khi cập nhật danh mục.'];
         }
     }

     /**
      * Xóa danh mục.
      * @param int $id ID danh mục cần xóa.
      * @return bool|string True nếu thành công, False nếu lỗi DB, String thông báo lỗi nếu còn sản phẩm.
      */
     public function deleteCategory(int $id): bool|string
     {
         if ($id <= 0) return false;

          // 1. Kiểm tra xem có sản phẩm nào thuộc danh mục này không
          if ($this->hasProducts($id)) {
              return "Không thể xóa danh mục này vì vẫn còn sản phẩm thuộc về nó.";
          }

         // 2. Nếu không còn sản phẩm, tiến hành xóa danh mục
         try {
             $this->conn->beginTransaction();
             $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
             $stmt = $this->conn->prepare($query);
             $stmt->bindParam(':id', $id, PDO::PARAM_INT);
             $success = $stmt->execute();
             $this->conn->commit();
             return $success;
         } catch (PDOException $e) {
             $this->conn->rollBack();
             error_log("Database error deleting category ID {$id}: " . $e->getMessage());
             return false;
         }
     }

     /**
      * Kiểm tra xem một danh mục có sản phẩm nào liên kết không.
      * @param int $categoryId ID danh mục.
      * @return bool True nếu có sản phẩm, False nếu không có hoặc lỗi.
      */
     private function hasProducts(int $categoryId): bool
     {
         try {
             $query = "SELECT 1 FROM product WHERE category_id = :category_id LIMIT 1";
             $stmt = $this->conn->prepare($query);
             $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
             $stmt->execute();
             return $stmt->fetchColumn() !== false; // Trả về true nếu tìm thấy ít nhất 1 sản phẩm
         } catch (PDOException $e) {
             error_log("Error checking products for category ID {$categoryId}: " . $e->getMessage());
             return true; // Trả về true (an toàn) nếu có lỗi DB để ngăn việc xóa
         }
     }

    /**
     * Hàm kiểm tra dữ liệu danh mục.
     * @param array $data Dữ liệu danh mục.
     * @return array Mảng lỗi, rỗng nếu không có lỗi.
     */
    private function validateCategoryData(array $data): array
    {
        $errors = [];
        // Name
        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Tên danh mục không được để trống.';
        } elseif (mb_strlen(trim($data['name']), 'UTF-8') > 100) { // Giới hạn độ dài ví dụ
            $errors['name'] = 'Tên danh mục quá dài (tối đa 100 ký tự).';
        }
         // Description (có thể cho phép rỗng)
         // if (empty(trim($data['description'] ?? ''))) {
         //     $errors['description'] = 'Mô tả không được để trống.';
         // }
        return $errors;
    }
}
?>