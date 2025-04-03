<?php
// Đảm bảo session đã được khởi tạo (tốt nhất là gọi ở index.php hoặc controller)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- SET CỨNG DỮ LIỆU USER ĐỂ TEST ----
// Phần này bạn đang đặt trong OrderController, nên có thể không cần ở đây.
// Nếu bạn muốn test giao diện nút trực tiếp từ file view này, hãy bỏ comment các dòng dưới
// và thay đổi 'user'/'admin' nếu cần.
$_SESSION['user_id'] = 1;
$_SESSION['user_role'] = 'user'; 
// $_SESSION['user_id'] = 2;
// $_SESSION['user_role'] = 'admin'; // hoặc 'admin'
// --------------------------------------
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách sản phẩm</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-6">
    
    <!-- Header -->
    <header class="bg-blue-600 text-white text-center py-4 text-xl font-semibold flex justify-between px-6">
        <span>Cửa hàng trực tuyến</span>
        <a href="/CART/app/views/cart/cart.html" class="bg-white text-blue-600 py-2 px-4 rounded">🛒 Giỏ hàng</a>
        <?php
            // --- ĐOẠN MÃ MỚI CHO NÚT ĐƠN HÀNG ---
            // Chỉ hiển thị nút nếu người dùng đã đăng nhập
            if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])):
                $buttonText = '';       // Text hiển thị trên nút
                $buttonLink = '#';      // Đường dẫn của nút
                $buttonClass = 'text-white py-2 px-4 rounded text-sm'; // Class CSS cơ bản

                // Xác định text, link và style dựa trên vai trò
                if ($_SESSION['user_role'] === 'admin') {
                    $buttonText = 'Quản lý Đơn hàng';
                    $buttonLink = '/CART/index.php?controller=order&action=listAll';
                    $buttonClass .= ' bg-red-500 hover:bg-red-700'; // Style cho admin
                } elseif ($_SESSION['user_role'] === 'user') {
                    $buttonText = 'Đơn hàng của tôi';
                    $buttonLink = '/CART/index.php?controller=order&action=myOrders';
                    $buttonClass .= ' bg-green-500 hover:bg-green-700'; // Style cho user
                }

                // Chỉ hiển thị thẻ <a> nếu buttonText đã được xác định (vai trò hợp lệ)
                if (!empty($buttonText)):
            ?>
                <a href="<?php echo htmlspecialchars($buttonLink); ?>" class="<?php echo $buttonClass; ?>">
                    <?php echo htmlspecialchars($buttonText); ?>
                </a>
            <?php
                endif; // Kết thúc kiểm tra !empty($buttonText)
            endif; // Kết thúc kiểm tra isset($_SESSION...)
            // --- KẾT THÚC ĐOẠN MÃ MỚI ---
            ?>
    </header>
    
    <!-- Danh sách sản phẩm -->
    <section class="max-w-6xl mx-auto my-6">
        <h2 class="text-2xl font-bold mb-4">Danh sách sản phẩm</h2>
        <div id="product-list" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"></div>
    </section>
    
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const products = [
                { id: 1, name: "Laptop Dell", price: 15000000, image: "https://via.placeholder.com/150" },
                { id: 2, name: "Điện thoại Samsung", price: 8000000, image: "https://via.placeholder.com/150" },
                { id: 3, name: "Tai nghe Bluetooth", price: 1200000, image: "https://via.placeholder.com/150" }
            ];
            
            const productContainer = document.getElementById("product-list");

            productContainer.innerHTML = products.map(product => `
                <div class="bg-white p-4 rounded-lg shadow-md">
                    <img src="${product.image}" alt="${product.name}" class="w-full h-40 object-cover rounded-md">
                    <h3 class="mt-2 font-semibold">${product.name}</h3>
                    <p class="text-gray-600">${product.price.toLocaleString()} VND</p>
                    <div class="flex items-center gap-2 mt-2">
                        <button onclick="updateQuantity(${product.id}, -1)" class="bg-gray-300 px-2 py-1 rounded">-</button>
                        <span id="quantity-${product.id}">1</span>
                        <button onclick="updateQuantity(${product.id}, 1)" class="bg-gray-300 px-2 py-1 rounded">+</button>
                    </div>
                    <button onclick="addToCart(${product.id}, '${product.name}', ${product.price})" 
                        class="mt-3 bg-blue-500 text-white py-1 px-3 rounded">Thêm vào giỏ</button>
                </div>
            `).join('');
        });

        function updateQuantity(id, change) {
            const quantityElem = document.getElementById(`quantity-${id}`);
            let quantity = parseInt(quantityElem.textContent) + change;
            if (quantity < 1) quantity = 1;
            quantityElem.textContent = quantity;
        }

        function addToCart(id, name, price) {
            const quantity = parseInt(document.getElementById(`quantity-${id}`).textContent);
            let cart = JSON.parse(localStorage.getItem("cart")) || [];
            let existingProduct = cart.find(item => item.id === id);

            if (existingProduct) {
                existingProduct.quantity += quantity;
            } else {
                cart.push({ id, name, price, quantity });
            }

            localStorage.setItem("cart", JSON.stringify(cart));
            alert("Đã thêm vào giỏ hàng!");
        }
    </script>

</body>
</html>
