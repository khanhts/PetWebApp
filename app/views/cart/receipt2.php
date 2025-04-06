<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng #<?php echo htmlspecialchars($receipt['id']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Thêm CSS cho in ấn nếu cần */
        @media print {
            body * {
                visibility: hidden; /* Ẩn tất cả */
            }
            #invoice-section, #invoice-section * {
                visibility: visible; /* Chỉ hiện phần hóa đơn */
            }
            #invoice-section {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                 display: none; /* Ẩn các nút khi in */
            }
        }
    </style>
</head>
<body class="bg-gray-100 p-6">
    <div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md" id="invoice-section">
        <h1 class="text-3xl font-bold text-center mb-6 text-blue-600">Checkout successful</h1>

        <div class="grid grid-cols-2 gap-6 mb-6 border-b pb-4">
            <div>
                <h2 class="text-lg font-semibold mb-2">Receipt Info</h2>
                <p><strong>Receipt No:</strong> #<?php echo htmlspecialchars($receipt['id']); ?></p>
                <p><strong>Create date:</strong> <?php echo date('d/m/Y H:i:s', strtotime($receipt['purchase_date'])); ?></p>
                <p><strong>Payment status:</strong> <span class="font-medium text-orange-600">
                    <?php
                        // Bạn có thể hiển thị trạng thái tùy chỉnh hơn
                        switch ($receipt['payment_status']) {
                            case 'pending': echo 'Payment pending / In cash'; break;
                            case 'processing': echo 'In process'; break;
                            case 'completed': echo 'Completed'; break;
                            case 'cancelled': echo 'Cancelled'; break;
                            default: echo htmlspecialchars($receipt['payment_status']);
                        }
                    ?>
                </span></p>
            </div>
            <div>
                <h2 class="text-lg font-semibold mb-2">Customer information</h2>
                <p><strong>Fullname:</strong> <?php echo htmlspecialchars($_SESSION['fullname']); ?></p>
                <p><strong>Phone:</strong> <?php echo ($_SESSION['phone']); ?></p>
                <p><strong>Address:</strong> <?php echo (htmlspecialchars($_SESSION['address'])); ?></p>
            </div>
        </div>

        <h2 class="text-xl font-semibold mb-4">Receipt Detail</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 mb-6">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Price</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($receipt) && !empty($receipt)): ?>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500">$<?php echo number_format($item['price'], 0, ',', '.'); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> $</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Product missing.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                     <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-700 uppercase">Tổng cộng</td>
                        <td class="px-6 py-3 text-right text-lg font-bold text-red-600"><?php echo number_format($receipt['total_price'], 0, ',', '.'); ?>$</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 pt-4 border-t text-center text-gray-700">
             <p class="font-semibold text-lg">Cảm ơn bạn đã đặt hàng!</p>
             <p>Please prepare <strong class="text-red-600"><?php echo number_format($receipt['total_price'], 0, ',', '.'); ?> $</strong> when the products arrived.</p>
        </div>

    </div> <div class="max-w-3xl mx-auto mt-6 text-center no-print">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded mr-2">
            Print Receipt
        </button>
        <a href="/products" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
            Continue Shopping
        </a>
    </div>

</body>
</html>