<?php
// Include the header file
include 'includes/header.php';

// Fetch products or display the list
$products = [
    ['name' => 'Dog Food', 'description' => 'High-quality food for your dog.', 'price' => '$20'],
    ['name' => 'Cat Toys', 'description' => 'Fun and engaging toys for your cat.', 'price' => '$10'],
    ['name' => 'Pet Leash', 'description' => 'Durable leash for walking your pet.', 'price' => '$15']
];

?>

    <section id="products" class="container">
        <h2>All Products</h2>
        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-item">
                    <h3><?php echo $product['name']; ?></h3>
                    <p><?php echo $product['description']; ?></p>
                    <p><strong>Price:</strong> <?php echo $product['price']; ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php
// Include the footer file
include 'includes/footer.php';
?>
