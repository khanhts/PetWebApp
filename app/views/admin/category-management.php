<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="/assets/css/admin-category-management.css">
    <link rel="stylesheet" href="/assets/css/admin-sidebar.css">
</head>
<body>
    <?php require_once __DIR__ . '/../partials/admin-sidebar.php'; ?>

    <div class="content">
        <div class="container">
            <h1>Category Management</h1>
            <button class="create-button" onclick="window.location.href='/admin/category/create'">Create New Category</button>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($category['id']); ?></td>
                            <td><?php echo htmlspecialchars($category['name']); ?></td>
                            <td>
                                <button onclick="window.location.href='/admin/category/edit/<?php echo $category['id']; ?>'">Edit</button>
                                <button onclick="deleteCategory(<?php echo $category['id']; ?>)">Delete</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function deleteCategory(categoryId) {
            if (confirm('Are you sure you want to delete this category?')) {
                fetch(`/admin/category/delete/${categoryId}`, {
                    method: 'POST',
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.message);
                        window.location.reload();
                    } else {
                        alert('Failed to delete category.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                });
            }
        }
    </script>
</body>
</html>