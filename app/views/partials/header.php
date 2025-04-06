<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pet Services & Products</title>
    <link rel="stylesheet" href="assets/css/styles.css"> <!-- Link to your CSS file -->
</head>
<body>

    <!-- Navbar Section -->
    <nav class="navbar">
        <div class="navbar-container">
            <ul>
                <li><a href="/">Home</a></li>
                <li><a href="products">Products</a></li>
                <?php
                if (isset($_SESSION['user_id'])): ?>
                    <li><a href="services">Services</a></li>
                    <li><a href="cart">Cart</a></li>
                    <li class="logout-link"><a href="logout">Logout</a></li>
                <?php else: ?>
                    <li class="login-link"><a href="login">Login</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>
