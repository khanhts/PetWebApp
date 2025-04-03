<?php
// File: webbanhang/app/controllers/ProductApiController.php

require_once 'app/models/ProductModel.php';
require_once 'app/config/database.php';

class ProductApiController
{
    private $productModel;

    public function __construct()
    {
        try {
            $database = new Database();
            $db = $database->getConnection();
            $this->productModel = new ProductModel($db);
        } catch (RuntimeException $e) {
            $this->sendResponse(['message' => 'Lỗi máy chủ nội bộ: Không thể khởi tạo model.'], 500);
        } catch (Exception $e) {
             $this->sendResponse(['message' => 'Lỗi không xác định khi khởi tạo API.'], 500);
        }
    }

    /**
     * Gửi phản hồi JSON chuẩn hóa
     * @param mixed $data Dữ liệu cần gửi (thường là mảng hoặc object)
     * @param int $statusCode Mã HTTP status
     */
    private function sendResponse($data, int $statusCode = 200): void {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit; // Dừng script sau khi gửi response
    }

    /**
     * GET /api/product - Lấy danh sách sản phẩm
     */
    public function index(): void {
        $products = $this->productModel->getAllProducts();
        $this->sendResponse($products);
    }

    /**
     * GET /api/product/{id} - Lấy thông tin sản phẩm theo ID
     * @param string|null $id ID từ URL
     */
    public function show($id = null): void {
        $productId = filter_var($id, FILTER_VALIDATE_INT);
        if ($productId === false || $productId <= 0) {
            $this->sendResponse(['message' => 'ID sản phẩm không hợp lệ.'], 400); // Bad Request
        }

        $product = $this->productModel->getProductById($productId);
        if ($product) {
            $this->sendResponse($product);
        } else {
            $this->sendResponse(['message' => 'Sản phẩm không tồn tại.'], 404); // Not Found
        }
    }

    /**
     * POST /api/product - Thêm sản phẩm mới
     * Expects multipart/form-data
     */
    public function store(): void {
        // API POST -> lấy dữ liệu từ $_POST và $_FILES
         $data = [
            'name' => $_POST['name'] ?? null,
            'description' => $_POST['description'] ?? null,
            'price' => $_POST['price'] ?? null,
            'category_id' => $_POST['category_id'] ?? null,
        ];
        $imageInfo = $_FILES['image'] ?? null;

        $result = $this->productModel->addProduct($data, $imageInfo);

        if ($result === true) {
             // Lấy sản phẩm vừa thêm để trả về (tùy chọn)
             // $lastId = $this->productModel->getLastInsertId(); // Cần thêm hàm này vào model
             // $newProduct = $this->productModel->getProductById($lastId);
             // $this->sendResponse($newProduct, 201); // Created
            $this->sendResponse(['message' => 'Thêm sản phẩm thành công.'], 201); // Created
        } else if (is_array($result)) {
            // Lỗi validation hoặc upload
            $this->sendResponse(['message' => 'Dữ liệu không hợp lệ.', 'errors' => $result], 400); // Bad Request
        } else {
             // Lỗi database hoặc không xác định
             $this->sendResponse(['message' => 'Lỗi máy chủ nội bộ khi thêm sản phẩm.'], 500);
        }
    }

    /**
     * POST /api/product/update/{id} - Cập nhật sản phẩm (dùng POST mô phỏng PUT/PATCH)
     * Expects multipart/form-data
     * @param string|null $id ID từ URL
     */
    public function update($id = null): void {
         $productId = filter_var($id, FILTER_VALIDATE_INT);
         if ($productId === false || $productId <= 0) {
            $this->sendResponse(['message' => 'ID sản phẩm không hợp lệ.'], 400);
         }

         // Dùng POST nên lấy từ $_POST, $_FILES
          $data = [
            'name' => $_POST['name'] ?? null,
            'description' => $_POST['description'] ?? null,
            'price' => $_POST['price'] ?? null,
            'category_id' => $_POST['category_id'] ?? null,
          ];
          // Lưu ý: Cần kiểm tra xem các trường có được gửi hay không nếu dùng PATCH
          // Ở đây coi như gửi đủ các trường như PUT

          $imageInfo = $_FILES['image'] ?? null;

          $result = $this->productModel->updateProduct($productId, $data, $imageInfo);

         if ($result === true) {
            // Lấy sản phẩm vừa cập nhật để trả về (tùy chọn)
            // $updatedProduct = $this->productModel->getProductById($productId);
            // $this->sendResponse($updatedProduct);
             $this->sendResponse(['message' => 'Cập nhật sản phẩm thành công.']);
        } else if (is_array($result)) {
            // Lỗi validation/upload hoặc ID không tồn tại (nếu model trả về lỗi 'id')
             if (isset($result['id'])) {
                 $this->sendResponse(['message' => $result['id']], 404); // Not Found
             } else {
                 $this->sendResponse(['message' => 'Dữ liệu không hợp lệ.', 'errors' => $result], 400);
             }
        } else {
             $this->sendResponse(['message' => 'Lỗi máy chủ nội bộ khi cập nhật sản phẩm.'], 500);
        }
    }

    /**
     * DELETE /api/product/{id} - Xóa sản phẩm
     * @param string|null $id ID từ URL
     */
    public function destroy($id = null): void {
        $productId = filter_var($id, FILTER_VALIDATE_INT);
        if ($productId === false || $productId <= 0) {
            $this->sendResponse(['message' => 'ID sản phẩm không hợp lệ.'], 400);
        }

        // Kiểm tra sản phẩm tồn tại trước khi xóa (tùy chọn nhưng nên có)
        if (!$this->productModel->getProductById($productId)) {
             $this->sendResponse(['message' => 'Sản phẩm không tồn tại.'], 404);
        }


        if ($this->productModel->deleteProduct($productId)) {
            // Trả về 204 No Content thường phù hợp hơn cho DELETE thành công
            // header('Content-Type: application/json'); // Header vẫn cần
            http_response_code(204);
            exit;
            // Hoặc trả về 200 OK với message
            // $this->sendResponse(['message' => 'Xóa sản phẩm thành công.']);
        } else {
            $this->sendResponse(['message' => 'Lỗi máy chủ nội bộ khi xóa sản phẩm.'], 500);
        }
    }
}
?>