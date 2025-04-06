<?php
namespace App\Config;

use PDO;
use PDOException;
use Dotenv\Dotenv;

class Database
{
    private string $host;
    private string $dbname;
    private string $username;
    private string $password;
    private ?PDO $conn = null;

    public function __construct()
    {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        // Load environment variables from .env file

        $this->host = getenv('DB_HOST');
        $this->dbname = getenv('DB_NAME');
        $this->username = getenv('DB_USER');
        $this->password = getenv('DB_PASSWORD');
    }

    public function getConnection(): ?PDO
    {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host=localhost;dbname=petweb;charset=utf8";
                $this->conn = new PDO($dsn, "root", "");
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                echo "Connection failed: " . $e->getMessage();
            }
        }
        return $this->conn;
    }
}
?>