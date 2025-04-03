<?php
// File: webbanhang/app/controllers/CategoryApiController.php

require_once 'app/models/CategoryModel.php';
require_once 'app/config/database.php';

class CategoryApiController
{
    private $categoryModel;

    public function __construct()
    {
        try {
            $database = new Database();
            $db = $database->getConnection();
            $this->categoryModel = new CategoryModel($db);
        } catch (RuntimeException $e) {
            $this->sendResponse(['message' => 'Lỗi máy chủ nội bộ: Không thể khởi tạo model.'], 500);
        } catch (Exception $e) {
            $this->sendResponse(['message' => 'Lỗi không xác định khi khởi tạo API Category.'], 500);
        }
    }

    private function sendResponse($data, int $statusCode = 200): void {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    /**
     * GET /api/category - Lấy danh sách danh mục
     */
    public function index(): void {
        $categories = $this->categoryModel->getAllCategories();
        $this->sendResponse($categories);
    }

    // Thêm các endpoint khác nếu cần (show, store, update, destroy)
}
?>