<!-- filepath: c:\xampp\htdocs\PetWebApp\app\views\admin\product-edit.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="/assets/css/product-edit.css">
    <link rel="stylesheet" href="/assets/css/admin-sidebar.css">
</head>
<body>
    <?php require_once __DIR__ . '/../partials/admin-sidebar.php'; ?>
    <div class="container">
        <h1>Edit Product</h1>
        <a href="/admin/product-management" class="back-button">Go Back</a>
        <form action="/admin/product/update/<?php echo htmlspecialchars($product['id']); ?>" method="POST" enctype="multipart/form-data">
            <label>Name:</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>

            <label>Description:</label>
            <textarea name="description" required><?php echo htmlspecialchars($product['description']); ?></textarea>

            <label>Price:</label>
            <input type="number" name="price" step="0.01" value="<?php echo htmlspecialchars($product['price']); ?>" required>

            <label>Category:</label>
            <select name="category_id" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo htmlspecialchars($category['id']); ?>" <?php echo ($category['id'] == $product['category_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($category['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Current Image:</label>
            <img src="/uploads<?php echo htmlspecialchars($product['image_path']); ?>" alt="Product Image" width="100">
            <label>Change Image:</label>
            <input type="file" name="image" accept="image/*">

            <button type="submit">Update Product</button>
        </form>
    </div>
</body>
</html>