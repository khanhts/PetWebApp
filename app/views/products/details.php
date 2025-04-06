<!-- filepath: c:\xampp\htdocs\PetWebApp\app\views\product\details.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details</title>
    <link rel="stylesheet" href="/assets/css/product-details.css">
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const addToCartButton = document.querySelector('.add-to-cart');

            addToCartButton.addEventListener('click', function () {
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
                        product_id: productId,
                        name: productName,
                        price: productPrice,
                        quantity: 1, // Default quantity
                    }),
                })
                    .then(response => {
                        if (response.ok) {
                            alert('Product added to cart!');
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
    </script>
</head>
<body>
    <div class="container product-details">
        <img src="/uploads<?php echo $product['image_path']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
        <h2><?php echo htmlspecialchars($product['name']); ?></h2>
        <p><?php echo htmlspecialchars($product['description']); ?></p>
        <p class="price">$<?php echo htmlspecialchars($product['price']); ?></p>
        <button 
            class="add-to-cart" 
            data-id="<?php echo $product['id']; ?>" 
            data-name="<?php echo htmlspecialchars($product['name']); ?>" 
            data-price="<?php echo htmlspecialchars($product['price']); ?>">
            Add to Cart
        </button>
        <p><a href="/products" class="back-link">Back to Products</a></p>
    </div>
</body>
</html>