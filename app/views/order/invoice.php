<?php
// app/views/order/invoice.php

// Biến $order được truyền từ OrderController::invoice()
if (!isset($order) || !$order) {
    echo "Không có thông tin đơn hàng để hiển thị.";
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác nhận đơn hàng #<?php echo htmlspecialchars($order['id']); ?></title>
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
        <h1 class="text-3xl font-bold text-center mb-6 text-blue-600">Xác Nhận Đặt Hàng Thành Công</h1>

        <div class="grid grid-cols-2 gap-6 mb-6 border-b pb-4">
            <div>
                <h2 class="text-lg font-semibold mb-2">Thông tin đơn hàng</h2>
                <p><strong>Mã đơn hàng:</strong> #<?php echo htmlspecialchars($order['id']); ?></p>
                <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i:s', strtotime($order['created_at'])); ?></p>
                <p><strong>Trạng thái:</strong> <span class="font-medium text-orange-600">
                    <?php
                        // Bạn có thể hiển thị trạng thái tùy chỉnh hơn
                        switch ($order['status']) {
                            case 'pending': echo 'Chờ xử lý / Thanh toán tại chỗ'; break;
                            case 'processing': echo 'Đang xử lý'; break;
                            case 'completed': echo 'Đã hoàn thành'; break;
                            case 'cancelled': echo 'Đã hủy'; break;
                            default: echo htmlspecialchars($order['status']);
                        }
                    ?>
                </span></p>
            </div>
            <div>
                <h2 class="text-lg font-semibold mb-2">Thông tin khách hàng</h2>
                <p><strong>Họ tên:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
                <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
                <?php if (!empty($order['customer_address'])): ?>
                    <p><strong>Địa chỉ:</strong> <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <h2 class="text-xl font-semibold mb-4">Chi tiết sản phẩm</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 mb-6">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sản phẩm</th>
                        <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Số lượng</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Đơn giá</th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($order['items']) && !empty($order['items'])): ?>
                        <?php foreach ($order['items'] as $item): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($item['product_name']); ?></div>
                                    </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500"><?php echo htmlspecialchars($item['quantity']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-500"><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium text-gray-900"><?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?> VND</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">Không có sản phẩm nào trong đơn hàng này.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="bg-gray-50">
                     <tr>
                        <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-700 uppercase">Tổng cộng</td>
                        <td class="px-6 py-3 text-right text-lg font-bold text-red-600"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VND</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="mt-6 pt-4 border-t text-center text-gray-700">
             <p class="font-semibold text-lg">Cảm ơn bạn đã đặt hàng!</p>
             <p>Vui lòng chuẩn bị số tiền <strong class="text-red-600"><?php echo number_format($order['total_amount'], 0, ',', '.'); ?> VND</strong> để thanh toán khi nhận hàng hoặc tại cửa hàng.</p>
        </div>

    </div> <div class="max-w-3xl mx-auto mt-6 text-center no-print">
        <button onclick="window.print()" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-6 rounded mr-2">
            In hóa đơn
        </button>
        <a href="/CART/app/views/product/list.php" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-6 rounded">
            Tiếp tục mua sắm
        </a>
    </div>

</body>
</html>