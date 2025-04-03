<?php
// File: webbanhang/app/controllers/ProductController.php

require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../config/View.php';

class ProductController
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;

    public function __construct(PDO $db)
    {
        $this->productModel = new ProductModel($db);
        $this->categoryModel = new CategoryModel($db); // Cần cho form add/edit
    }

    /**
     * Hiển thị danh sách tất cả sản phẩm (Trang chủ mặc định).
     */
    public function index(): void
    {
        try {
            $products = $this->productModel->getAllProducts();
            View::render('product/list', ['products' => $products], 'Danh sách Sản phẩm');
        } catch (Exception $e) {
            error_log("Error in ProductController::index: " . $e->getMessage());
            View::render('error/general', ['message' => 'Không thể tải danh sách sản phẩm.']);
        }
    }

    /**
     * Hiển thị chi tiết một sản phẩm.
     * @param string|null $id ID sản phẩm từ URL.
     */
    public function show(string|null $id = null): void
    {
        $productId = filter_var($id, FILTER_VALIDATE_INT);
        if ($productId === false || $productId <= 0) {
            View::render('error/404', ['message' => 'ID sản phẩm không hợp lệ.']);
            return;
        }

        try {
            $product = $this->productModel->getProductById($productId);

            if ($product) {
                View::render('product/show', ['product' => $product], 'Chi tiết: ' . htmlspecialchars($product->name));
            } else {
                View::render('error/404', ['message' => "Không tìm thấy sản phẩm với ID = {$productId}."]);
            }
        } catch (Exception $e) {
            error_log("Error in ProductController::show for ID {$productId}: " . $e->getMessage());
            View::render('error/general', ['message' => 'Không thể tải chi tiết sản phẩm.']);
        }
    }

    /**
     * Hiển thị form thêm sản phẩm mới.
     */
    public function add(): void
    {
        try {
            $categories = $this->categoryModel->getAllCategories();
            // Lấy dữ liệu cũ và lỗi từ session nếu có (sau khi redirect về từ 'save')
             $oldInput = $_SESSION['old_input'] ?? [];
             $errors = $_SESSION['errors'] ?? [];
             unset($_SESSION['old_input'], $_SESSION['errors']); // Xóa khỏi session

            View::render('product/add', [
                'categories' => $categories,
                'old_input' => $oldInput, // Truyền input cũ vào view
                'errors' => $errors       // Truyền lỗi vào view
            ], 'Thêm Sản phẩm Mới');
        } catch (Exception $e) {
            error_log("Error loading categories for add product form: " . $e->getMessage());
            View::render('error/general', ['message' => 'Không thể tải form thêm sản phẩm.']);
        }
    }

    /**
     * Lưu sản phẩm mới từ form POST.
     */
    public function save(): void
    {
        // Chỉ xử lý nếu request là POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            View::redirect('/Product/add'); // Redirect về form add nếu không phải POST
            return;
        }

        // Lấy dữ liệu từ POST và FILES
        $data = $_POST;
        $imageFile = $_FILES['image'] ?? null; // Lấy thông tin file ảnh (có thể null)

        try {
            $result = $this->productModel->addProduct($data, $imageFile);

            if (is_array($result)) {
                // Có lỗi validation hoặc lỗi upload/database từ model
                // Lưu lỗi và dữ liệu cũ vào session để hiển thị lại trên form
                $_SESSION['errors'] = $result;
                $_SESSION['old_input'] = $data;
                View::redirect('/Product/add'); // Quay lại form add
            } elseif (is_int($result) && $result > 0) {
                // Thêm thành công (result là ID sản phẩm mới)
                 View::setFlashMessage('Sản phẩm đã được thêm thành công!', 'message'); // Dùng flash message
                View::redirect('/Product/show/' . $result); // Redirect đến trang chi tiết sản phẩm mới
                // Hoặc View::redirect('/Product'); // Redirect về trang danh sách
            } else {
                // Lỗi không xác định từ model (không phải mảng lỗi, cũng không phải ID > 0)
                 $_SESSION['errors'] = ['general' => 'Có lỗi không xác định xảy ra khi thêm sản phẩm.'];
                 $_SESSION['old_input'] = $data;
                 View::redirect('/Product/add');
            }
        } catch (Exception $e) {
            error_log("Exception during product save: " . $e->getMessage() . "\nData: " . print_r($data, true));
            $_SESSION['errors'] = ['exception' => 'Lỗi hệ thống: ' . $e->getMessage()]; // Hiển thị lỗi chi tiết khi debug
            $_SESSION['old_input'] = $data;
            View::redirect('/Product/add');
        }
    }

    /**
     * Hiển thị form sửa thông tin sản phẩm.
     * @param string|null $id ID sản phẩm từ URL.
     */
    public function edit(string|null $id = null): void
    {
        $productId = filter_var($id, FILTER_VALIDATE_INT);
        if ($productId === false || $productId <= 0) {
            View::render('error/404', ['message' => 'ID sản phẩm không hợp lệ để sửa.']);
            return;
        }

        try {
            $product = $this->productModel->getProductById($productId);
            if (!$product) {
                View::render('error/404', ['message' => "Không tìm thấy sản phẩm với ID = {$productId} để sửa."]);
                return;
            }

            $categories = $this->categoryModel->getAllCategories();
             // Lấy dữ liệu cũ và lỗi từ session nếu có (sau khi redirect về từ 'update')
             $oldInput = $_SESSION['old_input'] ?? [];
             $errors = $_SESSION['errors'] ?? [];
             unset($_SESSION['old_input'], $_SESSION['errors']);

            View::render('product/edit', [
                'product' => $product,
                'categories' => $categories,
                'old_input' => $oldInput, // Truyền input cũ (nếu có lỗi)
                'errors' => $errors       // Truyền lỗi (nếu có)
            ], 'Sửa Sản phẩm: ' . htmlspecialchars($product->name));

        } catch (Exception $e) {
            error_log("Error loading product edit form for ID {$productId}: " . $e->getMessage());
            View::render('error/general', ['message' => 'Không thể tải form sửa sản phẩm.']);
        }
    }

    /**
     * Cập nhật thông tin sản phẩm từ form POST.
     */
    public function update(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             View::redirect('/Product'); // Hoặc trang lỗi
             return;
        }

        $productId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if ($productId === false || $productId <= 0) {
            View::setFlashMessage('ID sản phẩm không hợp lệ để cập nhật.', 'error');
            View::redirect('/Product');
            return;
        }

        $data = $_POST;
        $newImageFile = $_FILES['image'] ?? null;

        try {
            $result = $this->productModel->updateProduct($productId, $data, $newImageFile);

            if (is_array($result)) {
                // Có lỗi validation hoặc lỗi upload/database từ model
                $_SESSION['errors'] = $result;
                $_SESSION['old_input'] = $data; // Lưu lại input (trừ id)
                View::redirect('/Product/edit/' . $productId); // Quay lại form edit
            } elseif ($result === true) {
                // Cập nhật thành công
                View::setFlashMessage('Sản phẩm đã được cập nhật thành công!', 'message');
                View::redirect('/Product/show/' . $productId); // Redirect đến trang chi tiết
            } else {
                // Lỗi không xác định khác (ví dụ: update trả về false)
                $_SESSION['errors'] = ['general' => 'Có lỗi không xác định xảy ra khi cập nhật sản phẩm.'];
                $_SESSION['old_input'] = $data;
                View::redirect('/Product/edit/' . $productId);
            }
        } catch (Exception $e) {
             error_log("Exception during product update for ID {$productId}: " . $e->getMessage() . "\nData: " . print_r($data, true));
             $_SESSION['errors'] = ['exception' => 'Lỗi hệ thống: ' . $e->getMessage()];
             $_SESSION['old_input'] = $data;
             View::redirect('/Product/edit/' . $productId);
        }
    }

     /**
      * Xóa sản phẩm (thường được gọi từ form POST).
      * @param string|null $id ID sản phẩm từ URL.
      */
    public function delete(string|null $id = null): void
    {
         // Nên kiểm tra request method là POST để bảo mật hơn
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             View::setFlashMessage('Yêu cầu xóa không hợp lệ.', 'error');
             View::redirect('/Product');
             return;
         }

        $productId = filter_var($id, FILTER_VALIDATE_INT);
        if ($productId === false || $productId <= 0) {
            View::setFlashMessage('ID sản phẩm không hợp lệ để xóa.', 'error');
            View::redirect('/Product');
            return;
        }

        try {
            if ($this->productModel->deleteProduct($productId)) {
                View::setFlashMessage('Sản phẩm đã được xóa thành công!', 'message');
            } else {
                View::setFlashMessage('Không thể xóa sản phẩm. Có thể do lỗi cơ sở dữ liệu.', 'error');
            }
        } catch (Exception $e) {
             error_log("Exception during product deletion for ID {$productId}: " . $e->getMessage());
            View::setFlashMessage('Lỗi hệ thống khi xóa sản phẩm: ' . $e->getMessage(), 'error'); // Hiển thị lỗi chi tiết khi debug
        }

        View::redirect('/Product'); // Luôn redirect về trang danh sách sau khi xóa (hoặc xử lý xong)
    }
}
?>