<?php
class ProductModel
{
    private PDO $conn; 
    private string $table_name = "product";

    
    private string $uploadDirSystem;
    
    private string $uploadDirWeb = '/webbanhang/public/uploads/images/products/'; // *** KIỂM TRA KỸ ĐƯỜNG DẪN NÀY ***

    public function __construct(PDO $db)
    {
        $this->conn = $db;

        $this->uploadDirSystem = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . 'products' . DIRECTORY_SEPARATOR;

        if (!is_dir($this->uploadDirSystem)) {
            if (!mkdir($this->uploadDirSystem, 0775, true)) {
                 error_log("FATAL: Không thể tạo thư mục upload: " . $this->uploadDirSystem);
                throw new RuntimeException("Không thể tạo thư mục upload hệ thống. Vui lòng kiểm tra quyền ghi.");
            }
        }
        if (!is_writable($this->uploadDirSystem)) {
            error_log("FATAL: Thư mục upload không có quyền ghi: " . $this->uploadDirSystem);
            throw new RuntimeException("Thư mục upload hệ thống không có quyền ghi. Vui lòng kiểm tra quyền hạn của web server.");
        }
    }

    /**
     * Lấy danh sách tất cả sản phẩm kèm tên danh mục.
     * @return array Mảng các đối tượng sản phẩm hoặc mảng rỗng.
     */
    public function getAllProducts(): array
    {
        try {
            $query = "SELECT p.id, p.name, p.description, p.price, p.image_path, p.category_id, c.name as category_name
                      FROM " . $this->table_name . " p
                      LEFT JOIN category c ON p.category_id = c.id
                      ORDER BY p.id DESC"; // Hoặc ORDER BY p.name ASC tùy yêu cầu
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            error_log("Error fetching all products: " . $e->getMessage());
            return []; // Trả về mảng rỗng nếu có lỗi
        }
    }

    /**
     * Lấy thông tin chi tiết một sản phẩm bằng ID.
     * @param int $id ID sản phẩm.
     * @return object|false Đối tượng sản phẩm hoặc false nếu không tìm thấy/lỗi.
     */
    public function getProductById(int $id): object|false
    {
        if ($id <= 0) return false;

        try {
            $query = "SELECT p.id, p.name, p.description, p.price, p.category_id, p.image_path, c.name as category_name
                      FROM " . $this->table_name . " p
                      LEFT JOIN category c ON p.category_id = c.id
                      WHERE p.id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_OBJ); // Trả về object hoặc false nếu không có dòng nào
        } catch (PDOException $e) {
            error_log("Error fetching product ID {$id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Thêm sản phẩm mới vào CSDL.
     * @param array $data Mảng chứa dữ liệu sản phẩm ('name', 'description', 'price', 'category_id').
     * @param array|null $imageFile Thông tin file ảnh từ $_FILES['image'] hoặc null.
     * @return int|array ID sản phẩm mới nếu thành công, mảng lỗi nếu thất bại.
     */
    public function addProduct(array $data, ?array $imageFile): int|array
    {
        $errors = $this->validateProductData($data);
        $imagePath = null; 
        $uploadedFileSysPath = null; 

        if ($imageFile && $imageFile['error'] === UPLOAD_ERR_OK) {
             $uploadResult = $this->handleImageUpload($imageFile); // Không cần truyền ảnh cũ khi thêm mới
             if (isset($uploadResult['error'])) {
                 $errors['image'] = $uploadResult['error'];
             } else {
                 $imagePath = $uploadResult['web_path']; // Lấy đường dẫn web nếu thành công
                 $uploadedFileSysPath = $uploadResult['system_path']; // Lưu lại đường dẫn hệ thống
             }
        } elseif ($imageFile && $imageFile['error'] !== UPLOAD_ERR_NO_FILE) {
             // Có lỗi upload khác (vd: quá dung lượng cấu hình server)
             $errors['image'] = $this->getUploadErrorMessage($imageFile['error']);
        } 

        if (!empty($errors)) {
            // Nếu có lỗi VÀ đã lỡ upload ảnh thành công -> xóa ảnh vừa upload
            if ($uploadedFileSysPath && file_exists($uploadedFileSysPath)) {
                @unlink($uploadedFileSysPath);
                error_log("Rollback image upload due to validation errors. Deleted: " . $uploadedFileSysPath);
            }
            return $errors; // Trả về mảng lỗi
        }

        try {
            $this->conn->beginTransaction(); // Bắt đầu transaction

            $query = "INSERT INTO " . $this->table_name . " (name, description, price, category_id, image_path, created_at, updated_at)
                      VALUES (:name, :description, :price, :category_id, :image_path, NOW(), NOW())";
            $stmt = $this->conn->prepare($query);

            $name = htmlspecialchars(strip_tags($data['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars(strip_tags($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');
            $price = filter_var($data['price'] ?? 0, FILTER_VALIDATE_FLOAT);
            $categoryId = filter_var($data['category_id'] ?? null, FILTER_VALIDATE_INT);

            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':price', ($price !== false && $price >= 0) ? $price : 0); // Đảm bảo giá không âm
            $stmt->bindValue(':category_id', $categoryId ?: null, PDO::PARAM_INT); // Nếu categoryId = 0 hoặc không hợp lệ -> NULL
             // Lưu đường dẫn web (hoặc null) vào CSDL
            $stmt->bindValue(':image_path', $imagePath, $imagePath === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            $stmt->execute();
            $lastId = $this->conn->lastInsertId(); // Lấy ID của sản phẩm vừa thêm

            $this->conn->commit(); // Hoàn tất transaction thành công
            return (int)$lastId; // Trả về ID sản phẩm mới

        } catch (PDOException $e) {
            $this->conn->rollBack(); // Hủy bỏ transaction nếu có lỗi CSDL
            error_log("Database error adding product: " . $e->getMessage());

            if ($uploadedFileSysPath && file_exists($uploadedFileSysPath)) {
                @unlink($uploadedFileSysPath);
                 error_log("Rollback image upload due to DB error. Deleted: " . $uploadedFileSysPath);
            }
            return ['database' => 'Lỗi cơ sở dữ liệu khi thêm sản phẩm: ' . $e->getMessage()]; // Có thể ẩn $e->getMessage() trên production
        }
    }

    /**
     * Cập nhật thông tin sản phẩm.
     * @param int $id ID sản phẩm cần cập nhật.
     * @param array $data Dữ liệu mới ('name', 'description', 'price', 'category_id').
     * @param array|null $newImageFile File ảnh mới (nếu có) từ $_FILES['image'].
     * @return bool|array True nếu thành công, mảng lỗi nếu thất bại.
     */
    public function updateProduct(int $id, array $data, ?array $newImageFile): bool|array
    {
        $currentProduct = $this->getProductById($id);
        if (!$currentProduct) {
            return ['id' => 'Sản phẩm không tồn tại để cập nhật.'];
        }
        $oldImageWebPath = $currentProduct->image_path; // Đường dẫn web ảnh cũ (có thể null)
        $newImageWebPath = $oldImageWebPath; // Mặc định giữ ảnh cũ
        $newImageUploadedSysPath = null; // Đường dẫn hệ thống của ảnh mới (nếu upload thành công)
        $deletedOldImage = false; // Đánh dấu nếu ảnh cũ đã bị xóa thành công

        $errors = $this->validateProductData($data);

        if ($newImageFile && $newImageFile['error'] === UPLOAD_ERR_OK) {
             $uploadResult = $this->handleImageUpload($newImageFile); // Upload trước
             if (isset($uploadResult['error'])) {
                 $errors['image'] = $uploadResult['error'];
             } else {
                 $newImageWebPath = $uploadResult['web_path'];
                 $newImageUploadedSysPath = $uploadResult['system_path']; // Lưu lại để rollback nếu cần

                 if ($oldImageWebPath) {
                     $oldImageSysPath = $this->convertWebPathToSystemPath($oldImageWebPath);
                     if ($oldImageSysPath && file_exists($oldImageSysPath)) {
                         if (@unlink($oldImageSysPath)) {
                             $deletedOldImage = true; // Đánh dấu đã xóa thành công
                             error_log("Deleted old image during update: " . $oldImageSysPath);
                         } else {
                             error_log("Failed to delete old image during update: " . $oldImageSysPath);
                         }
                     } else {
                          error_log("Old image file not found for deletion during update: " . ($oldImageSysPath ?: 'Invalid Path') . " (from web path: " . $oldImageWebPath . ")");
                     }
                 }
             }
        } elseif ($newImageFile && $newImageFile['error'] !== UPLOAD_ERR_NO_FILE) {
            // Có lỗi upload khác
            $errors['image'] = $this->getUploadErrorMessage($newImageFile['error']);
        } 

        if (!empty($errors)) {
             if ($newImageUploadedSysPath && file_exists($newImageUploadedSysPath)) {
                 @unlink($newImageUploadedSysPath);
                 error_log("Rollback new image upload due to validation errors. Deleted: " . $newImageUploadedSysPath);
                 if ($deletedOldImage) {
                     error_log("WARNING: Old image was deleted, but update failed. Manual check needed for product ID {$id}.");
                 }
             }
            return $errors; // Trả về mảng lỗi
        }


        try {
            $this->conn->beginTransaction();

            $query = "UPDATE " . $this->table_name . "
                      SET name=:name, description=:description, price=:price, category_id=:category_id, image_path=:image_path, updated_at=NOW()
                      WHERE id=:id";
            $stmt = $this->conn->prepare($query);

            $name = htmlspecialchars(strip_tags($data['name'] ?? ''), ENT_QUOTES, 'UTF-8');
            $description = htmlspecialchars(strip_tags($data['description'] ?? ''), ENT_QUOTES, 'UTF-8');
            $price = filter_var($data['price'] ?? 0, FILTER_VALIDATE_FLOAT);
            $categoryId = filter_var($data['category_id'] ?? null, FILTER_VALIDATE_INT);

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->bindValue(':name', $name);
            $stmt->bindValue(':description', $description);
            $stmt->bindValue(':price', ($price !== false && $price >= 0) ? $price : 0);
            $stmt->bindValue(':category_id', $categoryId ?: null, PDO::PARAM_INT);
            $stmt->bindValue(':image_path', $newImageWebPath, $newImageWebPath === null ? PDO::PARAM_NULL : PDO::PARAM_STR);

            $success = $stmt->execute();

            $this->conn->commit();
            return $success; 
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Database error updating product ID {$id}: " . $e->getMessage());

            if ($newImageUploadedSysPath && file_exists($newImageUploadedSysPath)) {
                @unlink($newImageUploadedSysPath);
                 error_log("Rollback new image upload due to DB update error. Deleted: " . $newImageUploadedSysPath);
                 if ($deletedOldImage) {
                     error_log("WARNING: Old image was deleted, but DB update failed. Manual check needed for product ID {$id}.");
                 }
            }
            return ['database' => 'Lỗi cơ sở dữ liệu khi cập nhật sản phẩm: ' . $e->getMessage()];
        }
    }

    /**
     * Xóa sản phẩm khỏi CSDL và xóa file ảnh liên quan (nếu có).
     * @param int $id ID sản phẩm cần xóa.
     * @return bool True nếu xóa thành công, False nếu thất bại.
     */
    public function deleteProduct(int $id): bool
    {
        if ($id <= 0) return false;

        $product = $this->getProductById($id);
        $imageWebPathToDelete = $product ? $product->image_path : null;

        try {
            $this->conn->beginTransaction(); // Sử dụng transaction

            $query = "DELETE FROM " . $this->table_name . " WHERE id=:id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                $imageDeleted = true; // Mặc định là thành công nếu không có ảnh
                if ($imageWebPathToDelete) {
                    $fileSystemPath = $this->convertWebPathToSystemPath($imageWebPathToDelete);
                    if ($fileSystemPath && file_exists($fileSystemPath) && is_file($fileSystemPath)) {
                        if (!@unlink($fileSystemPath)) {
                            error_log("Failed to delete image file during product deletion: " . $fileSystemPath);
                            $imageDeleted = false; // Đánh dấu xóa ảnh thất bại
                            
                        } else {
                             error_log("Deleted image file during product deletion: " . $fileSystemPath);
                        }
                    } else {
                         error_log("Image file not found for deletion during product deletion: " . ($fileSystemPath ?: 'Invalid Path') . " (from web path: " . $imageWebPathToDelete . ")");
                    }
                }

                $this->conn->commit(); 
                return true; 
            } else {
                $this->conn->rollBack();
                error_log("Failed to execute delete statement for product ID {$id}.");
                return false;
            }
        } catch (PDOException $e) {
            $this->conn->rollBack(); // Hủy bỏ transaction khi có lỗi CSDL
            error_log("Database error deleting product ID {$id}: " . $e->getMessage());
            return false;
        }
    }


    /**
     * Xử lý upload file ảnh và trả về đường dẫn web và hệ thống.
     * @param array $fileInfo Thông tin từ $_FILES['image'] (giả định đã kiểm tra UPLOAD_ERR_OK).
     * @return array Mảng chứa ['web_path' => ..., 'system_path' => ...] hoặc ['error' => ...].
     */
    private function handleImageUpload(array $fileInfo): array
    {
        $fileName = basename($fileInfo["name"]);
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $fileNameWithoutExt = pathinfo($fileName, PATHINFO_FILENAME);
        $safeFileName = preg_replace("/[^a-zA-Z0-9_-]/", "_", $fileNameWithoutExt);
        $safeFileName = substr($safeFileName, 0, 100); // Giới hạn độ dài tên file

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp']; // Cho phép thêm webp
        $maxFileSize = 5 * 1024 * 1024; // 5 MB

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $fileMimeType = $finfo->file($fileInfo['tmp_name']);
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];

        if (!in_array($fileExtension, $allowedTypes) || !in_array($fileMimeType, $allowedMimeTypes)) {
            return ['error' => 'Định dạng file không hợp lệ. Chỉ chấp nhận JPG, JPEG, PNG, GIF, WEBP.'];
        }
        if ($fileInfo["size"] > $maxFileSize) {
            return ['error' => 'Kích thước file quá lớn (tối đa 5MB).'];
        }
        if ($fileInfo["size"] === 0) {
            return ['error' => 'File ảnh không được rỗng.'];
        }

        $uniqueName = $safeFileName . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.' . $fileExtension;
        $targetFilePathSystem = $this->uploadDirSystem . $uniqueName; // Đường dẫn hệ thống để lưu
        $targetFilePathWeb = rtrim($this->uploadDirWeb, '/') . '/' . $uniqueName; // Đường dẫn WEB để lưu vào DB

        if (move_uploaded_file($fileInfo["tmp_name"], $targetFilePathSystem)) {
             error_log("Image uploaded successfully: {$targetFilePathSystem} (Web: {$targetFilePathWeb})");
            return [
                'web_path' => $targetFilePathWeb,
                'system_path' => $targetFilePathSystem
            ];
        } else {
             error_log("Failed to move uploaded file '{$fileInfo['tmp_name']}' to '{$targetFilePathSystem}'. Check permissions and path.");
            // Kiểm tra lỗi chi tiết hơn nếu có thể
            $lastError = error_get_last();
             $errorMessage = 'Không thể lưu file ảnh đã upload.';
             if ($lastError) {
                 $errorMessage .= ' (Error: ' . $lastError['message'] . ')';
             }
            return ['error' => $errorMessage];
        }
    }

     /**
      * Chuyển đổi đường dẫn web (lưu trong DB) sang đường dẫn hệ thống để thao tác file.
      * @param string $webPath Đường dẫn web (ví dụ: /webbanhang/public/uploads/images/products/prod_abc.jpg)
      * @return string|false Đường dẫn hệ thống tương ứng hoặc false nếu không hợp lệ/không khớp cấu hình.
      */
    private function convertWebPathToSystemPath(string $webPath): string|false {
        // Kiểm tra xem $webPath có bắt đầu bằng $uploadDirWeb hay không
        if (strpos($webPath, $this->uploadDirWeb) === 0) {
            // Lấy phần tên file từ đường dẫn web
            $fileName = basename($webPath);
            // Ghép với đường dẫn hệ thống đã được định nghĩa
            return rtrim($this->uploadDirSystem, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $fileName;
        } else {
            // Nếu đường dẫn web không khớp với cấu hình -> không thể xác định file hệ thống an toàn
            error_log("Web path '{$webPath}' does not match configured web upload directory '{$this->uploadDirWeb}'. Cannot convert to system path.");
            return false;
        }
    }

    /**
     * Hàm kiểm tra dữ liệu sản phẩm (trừ ảnh).
     * @param array $data Dữ liệu sản phẩm.
     * @return array Mảng lỗi, rỗng nếu không có lỗi.
     */
    private function validateProductData(array $data): array
    {
        $errors = [];
        // Name
        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = 'Tên sản phẩm không được để trống.';
        } elseif (mb_strlen(trim($data['name']), 'UTF-8') > 255) { // Sử dụng mb_strlen cho UTF-8
            $errors['name'] = 'Tên sản phẩm quá dài (tối đa 255 ký tự).';
        }

        // Description
        if (empty(trim($data['description'] ?? ''))) {
            $errors['description'] = 'Mô tả không được để trống.';
        } // Có thể thêm giới hạn độ dài nếu cần

        // Price
        if (!isset($data['price']) || $data['price'] === '') { // Kiểm tra cả rỗng
            $errors['price'] = 'Giá sản phẩm không được để trống.';
        } elseif (!is_numeric($data['price'])) {
            $errors['price'] = 'Giá sản phẩm phải là một số.';
        } elseif (filter_var($data['price'], FILTER_VALIDATE_FLOAT) === false) {
            $errors['price'] = 'Giá sản phẩm không hợp lệ.';
        } elseif ((float)$data['price'] < 0) {
            $errors['price'] = 'Giá sản phẩm không được âm.';
        }

        // Category ID
        if (empty($data['category_id'])) {
            $errors['category_id'] = 'Vui lòng chọn danh mục.';
        } elseif (filter_var($data['category_id'], FILTER_VALIDATE_INT) === false || (int)$data['category_id'] <= 0) {
            $errors['category_id'] = 'ID danh mục không hợp lệ.';
        } else {
          
        }
        return $errors;
    }

    /**
      * Lấy thông báo lỗi upload dễ hiểu hơn.
      * @param int $errorCode Mã lỗi từ $_FILES['error']
      * @return string Thông báo lỗi
      */
     private function getUploadErrorMessage(int $errorCode): string
     {
         switch ($errorCode) {
             case UPLOAD_ERR_INI_SIZE:
                 return "File upload vượt quá dung lượng cho phép bởi cấu hình server (upload_max_filesize).";
             case UPLOAD_ERR_FORM_SIZE:
                 return "File upload vượt quá dung lượng chỉ định trong form HTML (MAX_FILE_SIZE).";
             case UPLOAD_ERR_PARTIAL:
                 return "File chỉ được upload một phần.";
             case UPLOAD_ERR_NO_TMP_DIR:
                 return "Thiếu thư mục tạm.";
             case UPLOAD_ERR_CANT_WRITE:
                 return "Không thể ghi file lên đĩa.";
             case UPLOAD_ERR_EXTENSION:
                 return "Một extension PHP đã chặn việc upload file.";
             default:
                 return "Lỗi upload không xác định.";
         }
     }

     
}
?>