<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="/assets/css/admin-category-create.css">
    <link rel="stylesheet" href="/assets/css/admin-sidebar.css">
</head>
<body>
    <?php require_once __DIR__ . '/../partials/admin-sidebar.php'; ?>

    <div class="content">
    <?php require_once __DIR__ . '/../partials/admin-sidebar.php'; ?>
    <div class="container">
        <h1>Create Category</h1>
        <a href="/admin/category-management" class="back-button">Back to Category Management</a>
        <form action="/admin/category/store" method="POST">
            <label for="name">Category Name:</label>
            <input type="text" id="name" name="name" placeholder="Enter category name" required>

            <button type="submit">Create Category</button>
        </form>
    </div>
</body>
</html>