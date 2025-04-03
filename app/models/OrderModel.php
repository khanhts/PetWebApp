<?php
// app/models/OrderModel.php
require_once "config/database.php";

class OrderModel {
    private $conn;
    private $table_name = "orders";
    private $items_table_name = "order_items";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Tạo đơn hàng mới và lưu vào CSDL
     * @param int|null $userId ID của người dùng đã đăng nhập (có thể null nếu không đăng nhập)
     * @param array $customerInfo Thông tin khách hàng ['name', 'phone', 'address']
     * @param array $items Danh sách sản phẩm
     * @param float $totalAmount Tổng tiền
     * @return int|false ID của đơn hàng mới tạo hoặc false nếu lỗi
     */
    public function createOrder($userId, $customerInfo, $items, $totalAmount) { // Thêm $userId
        $this->conn->beginTransaction();
        try {
            $query = "INSERT INTO " . $this->table_name . "
                      SET user_id=:user_id, -- Thêm user_id
                          customer_name=:customer_name,
                          customer_phone=:customer_phone,
                          customer_address=:customer_address,
                          total_amount=:total_amount,
                          status=:status,
                          created_at=:created_at";
            $stmt = $this->conn->prepare($query);

            $status = 'pending';
            $createdAt = date('Y-m-d H:i:s');

            // Gán giá trị
            $stmt->bindParam(":user_id", $userId); // Gán user_id
            $stmt->bindParam(":customer_name", $customerInfo['name']);
            $stmt->bindParam(":customer_phone", $customerInfo['phone']);
            $stmt->bindParam(":customer_address", $customerInfo['address']);
            $stmt->bindParam(":total_amount", $totalAmount);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":created_at", $createdAt);

            if (!$stmt->execute()) {
                 error_log("SQL Error createOrder (orders): " . print_r($stmt->errorInfo(), true));
                throw new Exception("Lỗi khi tạo bản ghi order.");
            }

            $orderId = $this->conn->lastInsertId();

            // Thêm vào bảng order_items (giữ nguyên)
            $itemQuery = "INSERT INTO " . $this->items_table_name . "
                          SET order_id=:order_id, product_id=:product_id, product_name=:product_name, quantity=:quantity, price=:price";
            $itemStmt = $this->conn->prepare($itemQuery);

            foreach ($items as $item) {
                 if (!isset($item['id'], $item['name'], $item['price'], $item['quantity'])) {
                     error_log("Dữ liệu item không hợp lệ: " . print_r($item, true));
                     throw new Exception("Dữ liệu item trong giỏ hàng không hợp lệ.");
                 }
                 $itemStmt->bindParam(":order_id", $orderId);
                 $itemStmt->bindParam(":product_id", $item['id']);
                 $itemStmt->bindParam(":product_name", $item['name']);
                 $itemStmt->bindParam(":quantity", $item['quantity']);
                 $itemStmt->bindParam(":price", $item['price']);
                 if (!$itemStmt->execute()) {
                      error_log("Lỗi SQL khi thêm order_items: " . print_r($itemStmt->errorInfo(), true));
                      throw new Exception("Lỗi khi thêm chi tiết sản phẩm cho đơn hàng.");
                  }
             }

            $this->conn->commit();
            return $orderId;

        } catch (Exception $e) {
            $this->conn->rollBack();
            error_log("Order Creation Error: " . $e->getMessage());
            return false;
        }
    }

     /**
      * Lấy thông tin chi tiết đơn hàng và các sản phẩm của nó
      * @param int $orderId ID đơn hàng
      * @return array|false Mảng chứa thông tin đơn hàng và items, hoặc false nếu không tìm thấy
      */
     public function getOrderDetails($orderId) {
         try {
             // Nối bảng users để lấy thông tin người đặt hàng (nếu có)
             $orderQuery = "SELECT o.*, u.username as user_username, u.full_name as user_full_name
                            FROM " . $this->table_name . " o
                            LEFT JOIN users u ON o.user_id = u.id
                            WHERE o.id = :id";
             $stmt = $this->conn->prepare($orderQuery);
             $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
             $stmt->execute();
             $order = $stmt->fetch(PDO::FETCH_ASSOC);

             if (!$order) {
                 return false;
             }

             // Lấy chi tiết các sản phẩm (giữ nguyên)
             $itemsQuery = "SELECT * FROM " . $this->items_table_name . " WHERE order_id = :order_id";
             $itemsStmt = $this->conn->prepare($itemsQuery);
             $itemsStmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
             $itemsStmt->execute();
             $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

             $order['items'] = $items;
             return $order;

         } catch (Exception $e) {
             error_log("Get Order Details Error: " . $e->getMessage());
             return false;
         }
     }

    /**
     * Lấy tất cả đơn hàng (cho Admin) - JOIN với users để lấy tên user nếu có
     * @return array Danh sách các đơn hàng
     */
    public function getAllOrders() {
        try {
            // Câu lệnh JOIN để lấy thêm thông tin sản phẩm trong đơn hàng, nếu có
            $query = "SELECT o.id, o.customer_name, o.customer_phone, o.total_amount, o.status, o.created_at, o.user_id,
                             oi.product_name, oi.quantity, oi.price
                      FROM " . $this->table_name . " o
                      LEFT JOIN order_items oi ON o.id = oi.order_id
                      ORDER BY o.created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get All Orders Error: " . $e->getMessage());
            return []; // Trả về mảng rỗng nếu lỗi
        }
    }
    

    /**
     * Lấy các đơn hàng của một người dùng cụ thể
     * @param int $userId ID của người dùng
     * @return array Danh sách đơn hàng của người dùng đó
     */
    public function getOrdersByUserId($userId) {
        try {
            // Không cần join users ở đây vì đã biết userId rồi
             $query = "SELECT id, customer_name, total_amount, status, created_at
                       FROM " . $this->table_name . "
                       WHERE user_id = :user_id
                       ORDER BY created_at DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Get Orders By User ID Error: " . $e->getMessage());
            return []; // Trả về mảng rỗng nếu lỗi
        }
    }
}
?>