<?php

namespace App\Controllers;

use App\Models\RoleModel;
use PDO;

class RoleController
{
    private RoleModel $roleModel;

    public function __construct(PDO $db)
    {
        $this->roleModel = new RoleModel($db);
    }

    // Display all roles
    public function index()
    {
        $roles = $this->roleModel->getAllRoles();
        require_once __DIR__ . '/../views/role/index.php';
    }

    // Show a single role by ID
    public function show(int $id)
    {
        $role = $this->roleModel->getRoleById($id);
        if ($role) {
            require_once __DIR__ . '/../views/role/show.php';
        } else {
            http_response_code(404);
            echo "Role not found.";
        }
    }

    // Show the form to create a new role
    public function create()
    {
        require_once __DIR__ . '/../views/role/create.php';
    }

    // Store a new role
    public function store()
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';

        if (!empty($name) && !empty($description)) {
            $this->roleModel->createRole($name, $description);
            header('Location: /roles');
        } else {
            echo "All fields are required.";
        }
    }

    // Show the form to edit an existing role
    public function edit(int $id)
    {
        $role = $this->roleModel->getRoleById($id);
        if ($role) {
            require_once __DIR__ . '/../views/role/edit.php';
        } else {
            http_response_code(404);
            echo "Role not found.";
        }
    }

    // Update an existing role
    public function update(int $id)
    {
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';

        if (!empty($name) && !empty($description)) {
            $this->roleModel->updateRole($id, $name, $description);
            header('Location: /roles');
        } else {
            echo "All fields are required.";
        }
    }

    // Delete a role
    public function delete(int $id)
    {
        $this->roleModel->deleteRole($id);
        header('Location: /roles');
    }
}