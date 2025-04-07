<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Product</title>
    <link rel="stylesheet" href="/assets/css/product-create.css">
    <link rel="stylesheet" href="/assets/css/admin-sidebar.css">
</head>
<body>
    <?php require_once __DIR__ . '/../partials/admin-sidebar.php'; ?>
    <div class="container">
        <h1>Create Product</h1>
        <a href="/admin/product-management" class="back-button">Go Back</a>
        <form action="/admin/product/store" method="POST" enctype="multipart/form-data">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>Description:</label>
            <textarea name="description" required></textarea>

            <label>Price:</label>
            <input type="number" name="price" step="0.01" required>

            <label>Category:</label>
            <select name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?= htmlspecialchars($category['id']) ?>">
                        <?= htmlspecialchars($category['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Image:</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Create Product</button>
        </form>
    </div>
</body>
</html>
