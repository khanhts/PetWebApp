<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="/assets/css/admin-category-edit.css">
    <link rel="stylesheet" href="/assets/css/admin-sidebar.css">
</head>
<body>
    <?php require_once __DIR__ . '/../partials/admin-sidebar.php'; ?>
    <div class="container">
        <h1>Edit Category</h1>
        <a href="/admin/category-management" class="back-button">Back to Category Management</a>
        <form action="/admin/category/update/<?php echo htmlspecialchars($category['id']); ?>" method="POST">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
            <button type="submit">Update Category</button>
        </form>
    </div>
</body>
</html>