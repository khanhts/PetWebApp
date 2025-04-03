<?php
// app/models/UserModel.php
require_once "config/database.php";

class UserModel {
    private $conn;
    private $table_name = "users";

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Tìm user bằng ID
    public function findById($id) {
        $query = "SELECT id, username, full_name, role FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Tìm user bằng username (dùng cho đăng nhập)
    public function findByUsername($username) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Thêm user mới (cần mã hóa mật khẩu trước khi gọi hàm này)
    public function create($username, $hashedPassword, $fullName, $role = 'user') {
         $query = "INSERT INTO " . $this->table_name . "
                   SET username=:username, password=:password, full_name=:full_name, role=:role";
         $stmt = $this->conn->prepare($query);

         $stmt->bindParam(":username", $username);
         $stmt->bindParam(":password", $hashedPassword);
         $stmt->bindParam(":full_name", $fullName);
         $stmt->bindParam(":role", $role);

         if ($stmt->execute()) {
             return $this->conn->lastInsertId();
         }
         return false;
     }

     // (Thêm các phương thức khác nếu cần: update, delete, etc.)
}
?>