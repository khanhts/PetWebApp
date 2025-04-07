<?php require_once __DIR__ . '/../partials/header.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Receipts</title>
    <link rel="stylesheet" href="/assets/css/my-receipts.css">
    <link rel="stylesheet" href="/assets/css/styles.css">
    <script>
        // Add click event to table rows
        document.addEventListener('DOMContentLoaded', function () {
            const rows = document.querySelectorAll('.receipt-row');
            rows.forEach(row => {
                row.addEventListener('click', function () {
                    const receiptId = this.dataset.receiptId;
                    window.location.href = `/receipts/details/${receiptId}`;
                });
            });
        });
    </script>
</head>
<body>
    <div class="container">
        <h1>My Receipts</h1>
        <?php if (!empty($receipts)): ?>
            <table>
                <thead>
                    <tr>
                        <th>Receipt ID</th>
                        <th>Total Price</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($receipts as $receipt): ?>
                        <tr class="receipt-row" data-receipt-id="<?php echo htmlspecialchars($receipt['receipt_id']); ?>">
                            <td><?php echo htmlspecialchars($receipt['receipt_id']); ?></td>
                            <td>$<?php echo number_format($receipt['total_price'], 2); ?></td>
                            <td><?php echo htmlspecialchars($receipt['purchase_date']); ?></td>
                            <td>
                                <a href="/receipts/details/<?php echo $receipt['receipt_id']; ?>">View Details</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no receipts.</p>
        <?php endif; ?>
    </div>
</body>
<?php require_once __DIR__ . '/../partials/footer.php'; ?>
</html>