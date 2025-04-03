<?php
// app/controllers/OrderController.php
require_once "app/models/OrderModel.php";
// Nếu bạn có UserModel, cũng nên require ở đây để dùng nếu cần
// require_once "app/models/UserModel.php";

class OrderController {

    private $orderModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        // Khởi động session nếu chưa có
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // ---- SET CỨNG DỮ LIỆU USER ĐỂ TEST (THEO YÊU CẦU) ----
        // Giả lập User với ID = 1 và Role = 'user'
        //user
        $_SESSION['user_id'] = 1;
        $_SESSION['user_role'] = 'user';

        //admin
        // $_SESSION['user_id'] = 2;
        // $_SESSION['user_role'] = 'admin';

        // --------------------------------
        // ----------------------

        // Quan trọng: Trong ứng dụng thực tế, user_id và role phải được set sau khi đăng nhập thành công, không set cứng ở đây.
    }

    // Action tạo đơn hàng (giữ nguyên hoặc chỉnh sửa nếu cần)
    public function create() {
        $userId = $_SESSION['user_id'] ?? null; // Lấy userId từ session giả lập

        $requestData = json_decode(file_get_contents('php://input'), true);
        $customerInfo = $requestData['customer'] ?? null;
        $cartItems = $requestData['items'] ?? [];
        $totalAmount = $requestData['total'] ?? 0;

        if (!$customerInfo || empty($customerInfo['name']) || empty($customerInfo['phone']) || empty($cartItems) || $totalAmount <= 0) {
             http_response_code(400);
             echo json_encode(['success' => false, 'message' => 'Thông tin khách hàng hoặc giỏ hàng không hợp lệ.']);
             exit;
         }

        // Đảm bảo createOrder trong Model xử lý $userId đúng cách (có thể là null)
        $orderId = $this->orderModel->createOrder($userId, $customerInfo, $cartItems, $totalAmount);

        if ($orderId) {
            echo json_encode(['success' => true, 'orderId' => $orderId]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Đã xảy ra lỗi khi tạo đơn hàng.']);
        }
        exit;
    }

    // Action xem hóa đơn chi tiết
    public function invoice() {
        $orderId = $_GET['id'] ?? null;
        if (!$orderId) {
            echo "Thiếu mã đơn hàng."; exit;
        }

        $order = $this->orderModel->getOrderDetails((int)$orderId);

        if (!$order) {
             echo "Không tìm thấy đơn hàng."; exit;
        }

        // ***** KIỂM TRA QUYỀN XEM HÓA ĐƠN *****
        $canView = false;
        if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
            $currentUserId = $_SESSION['user_id'];
            $currentUserRole = $_SESSION['user_role'];

            // Admin có thể xem mọi đơn
            if ($currentUserRole === 'admin') {
                $canView = true;
            }
            // User chỉ có thể xem đơn của mình (kiểm tra user_id trong đơn hàng)
            // Sửa lỗi: phải kiểm tra $order['user_id'] tồn tại trước khi so sánh
            elseif ($currentUserRole === 'user' && isset($order['user_id']) && $order['user_id'] == $currentUserId) {
                $canView = true;
            }
        }

        if ($canView) {
            // Truyền biến $order vào view invoice.php
            include "app/views/order/invoice.php";
        } else {
             http_response_code(403); // Forbidden
             echo "Bạn không có quyền xem hóa đơn này.";
             // Hoặc redirect về trang đăng nhập/trang chủ
             // header('Location: /CART/index.php');
             exit;
         }
         // ********************************************
    }

    // Action cho User xem danh sách đơn hàng của mình
    public function myOrders() {
         // Kiểm tra đã "đăng nhập" (qua session giả lập) và đúng vai trò 'user'
         if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'user') {
              http_response_code(403);
              echo "Vui lòng đăng nhập với tài khoản user để xem đơn hàng.";
              // header('Location: index.php?controller=auth&action=login'); // Redirect nếu có trang login
              exit;
          }

         $userId = $_SESSION['user_id']; // Đã được set cứng ở constructor
         $orders = $this->orderModel->getOrdersByUserId($userId); // Gọi hàm lấy đơn hàng theo user ID

         // Gọi view để hiển thị danh sách $orders
         // Đảm bảo bạn đã tạo file view này
         include "app/views/order/my_orders_list.php";
     }

     // Action cho Admin xem tất cả đơn hàng
     public function listAll() {
         // Kiểm tra đã "đăng nhập" và đúng vai trò 'admin'
         if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
              http_response_code(403); // Forbidden
              echo "Bạn không có quyền truy cập trang này.";
              // header('Location: index.php?controller=auth&action=login');
               exit;
          }

         $orders = $this->orderModel->getAllOrders(); // Gọi hàm lấy tất cả đơn hàng

         // Gọi view để hiển thị danh sách $orders cho admin
         // Đảm bảo bạn đã tạo file view này
         include "app/views/order/admin_orders_list.php";
     }
}
?>