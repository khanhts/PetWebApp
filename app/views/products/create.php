<form action="/products/store" method="POST" enctype="multipart/form-data">
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
            <option value="<?= htmlspecialchars($category->id) ?>">
                <?= htmlspecialchars($category->name) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <label>Image:</label>
    <input type="file" name="image" accept="image/*">

    <button type="submit">Create Product</button>
</form>
