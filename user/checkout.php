<?php
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../includes/header.php'; // Đã bao gồm session_start()

// 1. KIỂM TRA ĐĂNG NHẬP
if (!isset($_SESSION['id_tk'])) {
    header("Location: ../guest/login.php");
    exit;
}

$cartModel = new Cart();
$fullCart = $cartModel->getItems(); // Lấy giỏ hàng đầy đủ

// 2. LOGIC LỌC SẢN PHẨM ĐÃ CHỌN TỪ GIỎ HÀNG
// Chỉ lọc khi người dùng POST từ cart.php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items'])) {
    $selected_ids = $_POST['selected_items'];
    $checkoutCart = [];

    if (empty($selected_ids)) {
        echo "<script>alert('Bạn chưa chọn sản phẩm nào để thanh toán!'); window.location.href='cart.php';</script>";
        exit;
    }

    foreach ($selected_ids as $id_sach) {
        if (isset($fullCart[$id_sach])) {
            $checkoutCart[$id_sach] = $fullCart[$id_sach];
        }
    }
    // Lưu giỏ hàng đã lọc vào session tạm
    $_SESSION['checkout_cart'] = $checkoutCart;

} 
// Nếu người dùng F5 lại trang checkout, đọc từ session tạm
else if (isset($_SESSION['checkout_cart'])) {
    $checkoutCart = $_SESSION['checkout_cart'];
} 
// Nếu không có gì, quay về giỏ hàng
else {
    echo "<script>alert('Giỏ hàng trống hoặc phiên đã hết hạn!'); window.location.href='cart.php';</script>";
    exit;
}

if (empty($checkoutCart)) {
    echo "<script>alert('Giỏ hàng thanh toán của bạn bị trống!'); window.location.href='cart.php';</script>";
    exit;
}

// 3. TÍNH TỔNG TIỀN (DÙNG KEY TIẾNG ANH)
$total = 0;
$totalDiscount = 0;
foreach ($checkoutCart as $item) {
    $price = $item['price'];
    $quantity = $item['quantity'];
    $discount_percent = $item['discount_percent'] ?? 0;
    
    $total += ($price * (1 - $discount_percent / 100)) * $quantity;
}
?>

<div class="container">
    <h2>Xác nhận đơn hàng</h2>

    <form method="POST" action="../controllers/orderController.php?action=create" class="checkout-form">
        <h3>Thông tin giao hàng</h3>
        <div class="form-group">
            <label>Họ tên:</label>
            <input type="text" value="<?= htmlspecialchars($_SESSION['ho_ten'] ?? 'Chưa cập nhật') ?>" disabled>
        </div>
        <div class="form-group">
            <label>Địa chỉ nhận hàng:</label>
            <input type="text" name="dia_chi" placeholder="Nhập địa chỉ cụ thể..." required>
        </div>

        <div class="form-group">
            <label>Phương thức thanh toán:</label>
            <select name="id_pttt" required>
                <option value="PT001">Thanh toán khi nhận hàng (COD)</option>
                <option value="PT002">Ví điện tử MoMo</option>
                <option value="PT003">Thẻ ngân hàng</option>
            </select>
        </div>

        <h3>Đơn hàng của bạn (Chỉ sản phẩm đã chọn)</h3>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($checkoutCart as $sp): 
                    $discountedPrice = $sp['price'] * (1 - $sp['discount_percent'] / 100);
                    $subtotal = $discountedPrice * $sp['quantity'];
                ?>
                    <tr>
                        <td><?= htmlspecialchars($sp['name']) ?></td> <td><?= number_format($discountedPrice, 0, ',', '.') ?>đ</td> <td><?= $sp['quantity'] ?></td> <td><?= number_format($subtotal, 0, ',', '.') ?>đ</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Tổng cộng: <span><?= number_format($total, 0, ',', '.') ?>đ</span></h3>
        </div>

        <button type="submit" class="btn">Xác nhận đặt hàng</button>
        <a href="cart.php" class="btn btn-secondary">⬅ Quay lại giỏ hàng</a>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>