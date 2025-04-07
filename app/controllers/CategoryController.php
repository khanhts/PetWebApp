<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Config\Database;

class CategoryController
{
    private $categoryModel;
    private ?\PDO $db = null; 
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    public function categoryManagement()
    {
        $categories = $this->categoryModel->getAllCategories();
        require_once __DIR__ . '/../views/admin/category-management.php';
    }

    public function create()
    {
        require_once __DIR__ . '/../views/admin/category-create.php';
    }

    public function store()
    {
        $name = $_POST['name'] ?? '';
        if (!empty($name)) {
            $this->categoryModel->createCategory($name);
        }
        header('Location: /admin/category-management');
        exit;
    }

    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        require_once __DIR__ . '/../views/admin/category-edit.php';
    }

    public function update($id)
    {
        $name = $_POST['name'] ?? '';
        if (!empty($name)) {
            $this->categoryModel->updateCategory($id, $name);
        }
        header('Location: /admin/category-management');
        exit;
    }

    public function delete($id)
    {
        $this->categoryModel->deleteCategory($id);
        echo json_encode(['status' => 'success', 'message' => 'Category deleted successfully.']);
    }
}