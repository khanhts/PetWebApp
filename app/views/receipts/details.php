<?php require_once __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt Details</title>
    <link rel="stylesheet" href="/assets/css/receipt-details.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
</head>
<body>
    <div class="container">
        <h1>Receipt Details</h1>
        <div class="receipt-info">
            <p><strong>Receipt ID:</strong> <?php echo htmlspecialchars($receipt['id']); ?></p>
            <p><strong>Customer name:</strong> <?php echo htmlspecialchars($receipt['fullname']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($receipt['email']); ?></p>
            <p><strong>Phone:</strong> <?php echo htmlspecialchars($receipt['phone']); ?></p>
            <p><strong>Total Price:</strong> $<?php echo number_format($receipt['total_price'], 2); ?></p>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($receipt['purchase_date']); ?></p>
        </div>
        <h2>Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['quantity'] * $item['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="/receipts/me" class="back-button">Back to My Receipts</a>
    </div>
</body>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</html>