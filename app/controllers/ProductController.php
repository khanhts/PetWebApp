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
        // Assuming you have a database connection instance
        $db = new \PDO('mysql:host=localhost;dbname=petweb;charset=utf8', 'root', '');
        $this->db = $db;

        // Check if the connection was successful
        if (!$this->db) {
            throw new \RuntimeException('Database connection failed.');
        }
        
        // Instantiate the models
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
    }

    // Display all products or search results
    public function index()
    {
        $search = $_GET['search'] ?? ''; // Get the search query from the URL
        $products = $this->productModel->getProducts($search); // Fetch products based on the search query
        require_once __DIR__ . '/../Views/products/index.php';
    }

    // Display single product by ID
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

    // Show create product form
    public function create()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /accessDenied');
            exit;
        }
        // Fetch all categories to display in the form
        $categories = $this->categoryModel->getAllCategories();
        require_once __DIR__ . '/../Views/products/create.php';
    }

    // Handle product creation
    public function store()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
            header('Location: /accessDenied');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Retrieve POST data
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $categoryId = $_POST['category_id'] ?? null; // If you have categories

            // Validate form fields
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

            // Handle image upload
            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $imagePath = $this->productModel->handleImageUpload($_FILES['image']);
            }

            // If errors exist, return to form with error messages
            if (!empty($errors)) {
                // Pass errors to the view
                require_once __DIR__ . '/../Views/products/create.php';
                return;
            }

            // Create product
            $this->productModel->createProduct($name, $description, $price, $imagePath, $categoryId);

            // Redirect to products page after creation
            header('Location: /products');
            exit;
        } else {
            // If method is not POST, return 405
            http_response_code(405);
            echo 'Method Not Allowed';
        }
    }

    public function productManagement()
    {
        $productModel = new ProductModel($this->db);

        // Get the current page from the query string, default to 1
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $itemsPerPage = 10; // Number of products per page
        $offset = ($currentPage - 1) * $itemsPerPage;

        // Fetch products for the current page
        $products = $productModel->getPaginatedProducts($itemsPerPage, $offset);

        // Get the total number of products
        $totalProducts = $productModel->getTotalProducts();
        $totalPages = ceil($totalProducts / $itemsPerPage);

        require_once __DIR__ . '/../views/admin/product-management.php';
    }

    public function deleteProduct(int $productId)
    {
        $productModel = new ProductModel($this->db);
        $success = $productModel->deleteProductById($productId);

        header('Content-Type: application/json');
        if ($success) {
            echo json_encode(['status' => 'success', 'message' => 'Product deleted successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete product.']);
        }
    }
}
?>