<?php

namespace App\Controllers;

use App\Config\Database;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class ProductController
{
    private ProductModel $productModel;
    private CategoryModel $categoryModel;
    private ?\PDO $db = null; 

    public function __construct()
    {
        $this->db = (new Database())->getConnection(); 

        
        if (!$this->db) {
            throw new \RuntimeException('Database connection failed.');
        }
        
        
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    
    public function index()
    {
        $search = $_GET['search'] ?? ''; 
        $products = $this->productModel->getProducts($search); 
        require_once __DIR__ . '/../Views/products/index.php';
    }

    
    public function show(int $id)
    {
        $product = $this->productModel->getProductById($id);
        if ($product) {
            require_once __DIR__ . '/../views/products/details.php';
        } else {
            http_response_code(404);
            echo "Product not found.";
        }
    }

    
    public function create()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /accessDenied');
            exit;
        }
        
        $categories = $this->categoryModel->getAllCategories();
        require_once __DIR__ . '/../Views/admin/product-create.php';
    }

    
    public function store()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /accessDenied');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $categoryId = $_POST['category_id'] ?? null; 

            
            $errors = [];
            if (empty($name)) {
                $errors[] = 'Product name is required.';
            }
            if (empty($description)) {
                $errors[] = 'Product description is required.';
            }
            if (!is_numeric($price)) {
                $errors[] = 'Product price must be a valid number.';
            }

            
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->productModel->handleImageUpload($_FILES['image']);
                if (!$imagePath) {
                    $errors[] = 'Failed to upload the image.';
                }
            } elseif (isset($_FILES['image']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE) {
                $errors[] = 'Error uploading the image. Please try again.';
            }

            
            if (!empty($errors)) {
                
                require_once __DIR__ . '/../Views/admin/product-create.php';
                return;
            }

            
            $this->productModel->createProduct($name, $description, $price, $imagePath, $categoryId);

            
            header('Location: /admin/product-management');
            exit;
        } else {
            
            http_response_code(405);
            echo 'Method Not Allowed';
        }
    }

    public function productManagement()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /accessDenied');
            exit;
        }
        $productModel = new ProductModel($this->db);

        
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 10; 
        $offset = ($currentPage - 1) * $itemsPerPage;

        
        $products = $productModel->getPaginatedProducts($itemsPerPage, $offset);

        
        $totalProducts = $productModel->getTotalProducts();
        $totalPages = ceil($totalProducts / $itemsPerPage);

        require_once __DIR__ . '/../views/admin/product-management.php';
    }

    public function deleteProduct(int $productId)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /accessDenied');
            exit;
        }
        $productModel = new ProductModel($this->db);
        $success = $productModel->deleteProductById($productId);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete product.']);
        }
    }

    public function edit($id)
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /accessDenied');
            exit;
        }
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /accessDenied');
            exit;
        }

        
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            echo "Product not found.";
            exit;
        }

        
        $categories = $this->categoryModel->getAllCategories();

        
        require_once __DIR__ . '/../views/admin/product-edit.php';
    }

    public function update($id)
    {
        
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /accessDenied');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $categoryId = $_POST['category_id'] ?? null;

            
            $errors = [];
            if (empty($name)) {
                $errors[] = 'Product name is required.';
            }
            if (empty($description)) {
                $errors[] = 'Product description is required.';
            }
            if (!is_numeric($price)) {
                $errors[] = 'Product price must be a valid number.';
            }

            
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->productModel->handleImageUpload($_FILES['image']);
                if (!$imagePath) {
                    $errors[] = 'Failed to upload the image.';
                }
            }

            
            if (!empty($errors)) {
                $product = $this->productModel->getProductById($id);
                $categories = $this->categoryModel->getAllCategories();
                require_once __DIR__ . '/../views/admin/product-edit.php';
                return;
            }

            
            $this->productModel->updateProduct($id, $name, $description, $price, $imagePath, $categoryId);

            
            header('Location: /admin/product-management');
            exit;
        }
    }
}
?>