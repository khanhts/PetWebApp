<?php require_once __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt</title>
    <link rel="stylesheet" href="/assets/css/receipt.css">
</head>
<body>
    <div class="container">
        <h1>Receipt</h1>
        <p><strong>Receipt ID:</strong> <?php echo htmlspecialchars($receipt['id']); ?></p>
        <p><strong>User ID:</strong> <?php echo htmlspecialchars($receipt['user_id']); ?></p>
        <p><strong>Total Price:</strong> $<?php echo number_format($receipt['total_price'], 2); ?></p>
        <p><strong>Purchase Date:</strong> <?php echo htmlspecialchars($receipt['purchase_date']); ?></p>
        <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($receipt['payment_status']); ?></p>

        <h2>Items</h2>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                        <td><?php echo $item['quantity']; ?></td>
                        <td>$<?php echo number_format($item['price'], 2); ?></td>
                        <td>$<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <p><strong>Grand Total:</strong> $<?php echo number_format(array_reduce($items, function ($sum, $item) {
            return $sum + ($item['price'] * $item['quantity']);
        }, 0), 2); ?></p>

        <a href="/products" class="button">Continue Shopping</a>
    </div>
</body>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</html>