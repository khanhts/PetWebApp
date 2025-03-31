<?php
class Database {
    private $host = "localhost";  // Máy chủ MySQL
    private $db_name = "petcare"; // Đổi thành tên database của bạn
    private $username = "root";   // Tên người dùng MySQL
    private $password = "";       // Mật khẩu MySQL (để trống nếu dùng XAMPP)
    public $conn;

    // ✅ Hàm kết nối database
    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8",
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            die("Lỗi kết nối database: " . $exception->getMessage()); // Hiển thị lỗi nếu có
        }
        return $this->conn;
    }

    
}
?>
