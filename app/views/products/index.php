<?php require_once __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product List</title>
    <link rel="stylesheet" href="/assets/css/product.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const productCards = document.querySelectorAll('.product-card');

            // Handle product card click for details
            productCards.forEach(card => {
                card.addEventListener('click', function () {
                    const productId = this.getAttribute('data-id');
                    window.location.href = `/products/${productId}`;
                });
            });

            // Handle Add to Cart button click
            const addToCartButtons = document.querySelectorAll('.add-to-cart');
            addToCartButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    event.stopPropagation();
                    const productId = this.getAttribute('data-id');
                    const productName = this.getAttribute('data-name');
                    const productPrice = this.getAttribute('data-price');

                    // Send POST request to add product to cart
                    fetch('/cart/add', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: productId, // Use "product_id" to match the server-side key
                            name: productName,
                            price: productPrice,
                            quantity: 1, // Default quantity
                        }),
                    })
                    .then(response => response.json()) // Parse JSON response
                    .then(data => {
                        if (data.status === 'success') {
                            alert(data.message); // Display success message
                        } else {
                            alert('Failed to add product to cart.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred.');
                    });
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>Our Products</h1>
        <!-- Search Bar -->
        <form method="GET" action="/products" class="search-form">
            <input type="text" name="search" placeholder="Search products..." value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
            <button type="submit">Search</button>
        </form>

        <!-- Current Search Text -->
        <?php if (!empty($_GET['search'])): ?>
            <div class="current-search">
                <p>Showing results for: <strong><?php echo htmlspecialchars($_GET['search']); ?></strong></p>
                <a href="/products" class="clear-search">X</a>
            </div>
        <?php endif; ?>

        <div class="product-list">
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-id="<?php echo $product['id']; ?>">
                    <img src="/uploads<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['description']); ?></p>
                        <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
                        <?php
                        if (isset($_SESSION['user_id'])): ?>
                            <button 
                            class="add-to-cart" 
                            data-id="<?php echo $product['id']; ?>" 
                            data-name="<?php echo htmlspecialchars($product['name']); ?>" 
                            data-price="<?php echo htmlspecialchars($product['price']); ?>">
                            Add to Cart
                        </button>
                        <?php else: ?>
                            <a href="/login" class="login-prompt">Login to add to cart</a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
