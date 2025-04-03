<?php
// File: webbanhang/app/controllers/CategoryController.php

require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../config/View.php';

class CategoryController
{
    private CategoryModel $categoryModel;

    public function __construct(PDO $db)
    {
        $this->categoryModel = new CategoryModel($db);
    }

    /**
     * Hiển thị danh sách danh mục.
     */
    public function index(): void
    {
        try {
            $categories = $this->categoryModel->getAllCategories();
            View::render('category/list', ['categories' => $categories], 'Danh sách Danh mục');
        } catch (Exception $e) {
            error_log("Error in CategoryController::index: " . $e->getMessage());
            View::render('error/general', ['message' => 'Không thể tải danh sách danh mục.']);
        }
    }

     /**
      * Hiển thị form thêm danh mục mới.
      */
     public function add(): void
     {
         // Lấy dữ liệu cũ và lỗi từ session nếu có
         $oldInput = $_SESSION['old_input'] ?? [];
         $errors = $_SESSION['errors'] ?? [];
         unset($_SESSION['old_input'], $_SESSION['errors']);

         View::render('category/add', [
             'old_input' => $oldInput,
             'errors' => $errors
         ], 'Thêm Danh mục Mới');
     }

     /**
      * Lưu danh mục mới từ form POST.
      */
     public function save(): void
     {
         if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
             View::redirect('/Category/add');
             return;
         }

         $data = $_POST;

         try {
             $result = $this->categoryModel->addCategory($data);

             if (is_array($result)) {
                 // Lỗi validation hoặc DB
                 $_SESSION['errors'] = $result;
                 $_SESSION['old_input'] = $data;
                 View::redirect('/Category/add');
             } elseif (is_int($result) && $result > 0) {
                 // Thành công
                 View::setFlashMessage('Danh mục đã được thêm thành công!', 'message');
                 View::redirect('/Category'); // Về trang danh sách
             } else {
                 // Lỗi không xác định
                 $_SESSION['errors'] = ['general' => 'Có lỗi không xác định xảy ra khi thêm danh mục.'];
                 $_SESSION['old_input'] = $data;
                 View::redirect('/Category/add');
             }
         } catch (Exception $e) {
             error_log("Exception during category save: " . $e->getMessage() . "\nData: " . print_r($data, true));
             $_SESSION['errors'] = ['exception' => 'Lỗi hệ thống: ' . $e->getMessage()];
             $_SESSION['old_input'] = $data;
             View::redirect('/Category/add');
         }
     }

      /**
       * Hiển thị form sửa danh mục.
       * @param string|null $id ID danh mục từ URL.
       */
      public function edit(string|null $id = null): void
      {
          $categoryId = filter_var($id, FILTER_VALIDATE_INT);
          if ($categoryId === false || $categoryId <= 0) {
              View::render('error/404', ['message' => 'ID danh mục không hợp lệ để sửa.']);
              return;
          }

          try {
              $category = $this->categoryModel->getCategoryById($categoryId);
              if (!$category) {
                  View::render('error/404', ['message' => "Không tìm thấy danh mục với ID = {$categoryId} để sửa."]);
                  return;
              }

              // Lấy lỗi và input cũ nếu có từ session
              $oldInput = $_SESSION['old_input'] ?? [];
              $errors = $_SESSION['errors'] ?? [];
              unset($_SESSION['old_input'], $_SESSION['errors']);

              View::render('category/edit', [
                  'category' => $category,
                  'old_input' => $oldInput,
                  'errors' => $errors
              ], 'Sửa Danh mục: ' . htmlspecialchars($category->name));

          } catch (Exception $e) {
              error_log("Error loading category edit form for ID {$categoryId}: " . $e->getMessage());
              View::render('error/general', ['message' => 'Không thể tải form sửa danh mục.']);
          }
      }

       /**
        * Cập nhật danh mục từ form POST.
        */
       public function update(): void
       {
           if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
               View::redirect('/Category');
               return;
           }

           $categoryId = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
           if ($categoryId === false || $categoryId <= 0) {
               View::setFlashMessage('ID danh mục không hợp lệ để cập nhật.', 'error');
               View::redirect('/Category');
               return;
           }

           $data = $_POST;

           try {
               $result = $this->categoryModel->updateCategory($categoryId, $data);

               if (is_array($result)) {
                   // Lỗi validation hoặc DB
                   $_SESSION['errors'] = $result;
                   $_SESSION['old_input'] = $data;
                   View::redirect('/Category/edit/' . $categoryId);
               } elseif ($result === true) {
                   // Thành công
                   View::setFlashMessage('Danh mục đã được cập nhật thành công!', 'message');
                   View::redirect('/Category'); // Về trang danh sách
               } else {
                   // Lỗi không xác định
                   $_SESSION['errors'] = ['general' => 'Có lỗi không xác định xảy ra khi cập nhật danh mục.'];
                   $_SESSION['old_input'] = $data;
                   View::redirect('/Category/edit/' . $categoryId);
               }
           } catch (Exception $e) {
               error_log("Exception during category update for ID {$categoryId}: " . $e->getMessage() . "\nData: " . print_r($data, true));
               $_SESSION['errors'] = ['exception' => 'Lỗi hệ thống: ' . $e->getMessage()];
               $_SESSION['old_input'] = $data;
               View::redirect('/Category/edit/' . $categoryId);
           }
       }

       /**
        * Xóa danh mục (thường từ form POST).
        * @param string|null $id ID danh mục từ URL.
        */
       public function delete(string|null $id = null): void
       {
           if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
               View::setFlashMessage('Yêu cầu xóa không hợp lệ.', 'error');
               View::redirect('/Category');
               return;
           }

           $categoryId = filter_var($id, FILTER_VALIDATE_INT);
           if ($categoryId === false || $categoryId <= 0) {
               View::setFlashMessage('ID danh mục không hợp lệ để xóa.', 'error');
               View::redirect('/Category');
               return;
           }

           try {
               $result = $this->categoryModel->deleteCategory($categoryId);

               if ($result === true) {
                   View::setFlashMessage('Danh mục đã được xóa thành công!', 'message');
               } elseif (is_string($result)) {
                    // Model trả về thông báo lỗi (ví dụ: còn sản phẩm)
                    View::setFlashMessage($result, 'error');
               }
               else { // $result === false (lỗi DB)
                   View::setFlashMessage('Không thể xóa danh mục. Có thể do lỗi cơ sở dữ liệu.', 'error');
               }
           } catch (Exception $e) {
               error_log("Exception during category deletion for ID {$categoryId}: " . $e->getMessage());
               View::setFlashMessage('Lỗi hệ thống khi xóa danh mục: ' . $e->getMessage(), 'error');
           }

           View::redirect('/Category'); // Luôn redirect về danh sách
       }
}
?>