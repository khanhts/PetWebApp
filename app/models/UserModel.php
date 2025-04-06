<?php

namespace App\Models;

use PDO;
use Exception;

class UserModel {
    private $conn; 
    private string $table_name = "users";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Create a new user
    public function createUser($fullname, $email, $password, $phone, $address, $role): bool {
        $stmt = $this->conn->prepare("
            INSERT INTO {$this->table_name} (fullname, email, password, phone, address, role)
            VALUES (:fullname, :email, :password, :phone, :address, :role)
        ");
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':phone', $phone);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':role', $role);
        return $stmt->execute();
    }

    // Get a user by email
    public function getUserByEmail(string $email): ?array {
        $stmt = $this->conn->prepare("
            SELECT users.id, users.email, users.password, users.fullname, users.phone, users.address,role.name AS role_name
            FROM {$this->table_name}
            INNER JOIN role ON users.role = role.id
            WHERE users.email = :email
        ");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null;
    }

    // Update user details
    public function updateUser($id, $email, $fullname, $gender, $birthYear, $role, $status) {
        $sql = "UPDATE {$this->table_name} SET email = :email, fullname = :fullname, gender = :gender, 
                birth_year = :birth_year, role = :role, status = :status, updated_at = CURRENT_TIMESTAMP 
                WHERE id = :id";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':fullname', $fullname);
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':birth_year', $birthYear);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
            return $stmt->rowCount(); // Return the number of affected rows
        } catch (Exception $e) {
            die("Error updating user: " . $e->getMessage());
        }
    }

    // Delete a user
    public function deleteUser($id) {
        $sql = "DELETE FROM {$this->table_name} WHERE id = :id";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);

        try {
            $stmt->execute();
            return $stmt->rowCount(); // Return the number of affected rows
        } catch (Exception $e) {
            die("Error deleting user: " . $e->getMessage());
        }
    }

    // List all users (for example, in admin dashboard)
    public function getAllUsers() {
        $sql = "SELECT * FROM {$this->table_name}";
        
        $stmt = $this->conn->query($sql);

        try {
            return $stmt->fetchAll(PDO::FETCH_ASSOC); // Return all users as an array of associative arrays
        } catch (Exception $e) {
            die("Error fetching users: " . $e->getMessage());
        }
    }
}
