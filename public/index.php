<?php
// Include the header file
include 'includes/header.php';

// Example: Fetch some sample products for the homepage (replace with database queries later)
$featuredProducts = [
    ['name' => 'Dog Food', 'description' => 'High-quality food for your dog.', 'price' => '$20', 'image' => 'dog_food.jpg'],
    ['name' => 'Cat Toys', 'description' => 'Fun and engaging toys for your cat.', 'price' => '$10', 'image' => 'cat_toys.jpg'],
    ['name' => 'Pet Leash', 'description' => 'Durable leash for walking your pet.', 'price' => '$15', 'image' => 'leash.jpg']
];
?>

    <!-- Welcome Message Section -->
    <section id="welcome" class="container">
        <h1>Welcome to Pet Paradise!</h1>
        <p>Your one-stop shop for pet services and products. We provide high-quality pet care services and a wide range of products to keep your pets happy and healthy!</p>
    </section>

    <!-- Featured Products Section -->
    <section id="featured-products" class="container">
        <h2>Featured Products</h2>
        <div class="product-list">
            <?php foreach ($featuredProducts as $product): ?>
                <div class="product-item">
                    <img src="assets/images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>" />
                    <h3><?php echo $product['name']; ?></h3>
                    <p><?php echo $product['description']; ?></p>
                    <p><strong>Price:</strong> <?php echo $product['price']; ?></p>
                    <a href="product_details.php?product=<?php echo urlencode($product['name']); ?>">View Details</a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php
// Include the footer file
include 'includes/footer.php';
?>
