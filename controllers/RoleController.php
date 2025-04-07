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

    
    public function index()
    {
        $roles = $this->roleModel->getAllRoles();
        require_once __DIR__ . '/../views/role/index.php';
    }

    
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

    
    public function create()
    {
        require_once __DIR__ . '/../views/role/create.php';
    }

    
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

    
    public function delete(int $id)
    {
        $this->roleModel->deleteRole($id);
        header('Location: /roles');
    }
}