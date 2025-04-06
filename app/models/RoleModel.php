<?php

namespace App\Models;

use PDO;

class RoleModel
{
    private PDO $db;
    private string $table_name = "role";

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    // Fetch all roles
    public function getAllRoles(): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table_name}");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Fetch a single role by ID
    public function getRoleById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table_name} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        return $role ?: null;
    }

    // Fetch a single role by name
    public function getRoleByName(string $name): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table_name} WHERE name = :name");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->execute();
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        return $role ?: null;
    }

    // Create a new role
    public function createRole(string $name, string $description): bool
    {
        $stmt = $this->db->prepare("INSERT INTO {$this->table_name} (name, description) VALUES (:name, :description)");
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Update an existing role
    public function updateRole(int $id, string $name, string $description): bool
    {
        $stmt = $this->db->prepare("UPDATE {$this->table_name} SET name = :name, description = :description WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        return $stmt->execute();
    }

    // Delete a role by ID
    public function deleteRole(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table_name} WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}