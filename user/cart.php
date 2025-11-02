<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once '../includes/header.php';

$cart_items = (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) ? $_SESSION['cart'] : [];
$total = 0;
?>

<div class="container" style="padding: 40px 0;">
    <h2>🛍️ Giỏ hàng của bạn</h2>

    <?php if (empty($cart_items)): ?>
        <div class="empty-cart" style="text-align:center; margin-top:40px;">
            <p>🛒 Giỏ hàng của bạn đang trống.</p>
            <a href="../public/index.php" class="btn">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <form action="../controllers/cartController.php?action=update" method="POST">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Giá</th>
                        <th>Số lượng</th>
                        <th>Thành tiền</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cart_items as $item): ?>
                        <?php $thanh_tien = $item['gia'] * $item['so_luong']; $total += $thanh_tien; ?>
                        <tr>
                            <td><?= htmlspecialchars($item['ten_sach']) ?></td>
                            <td><?= number_format($item['gia'], 0, ',', '.') ?>đ</td>
                            <td>
                                <input type="number" name="quantities[<?= $item['id_sach'] ?>]" 
                                    value="<?= $item['so_luong'] ?>" 
                                    min="1" style="width:60px;text-align:center;">
                            </td>
                            <td><?= number_format($thanh_tien, 0, ',', '.') ?>đ</td>
                            <td>
                                <a href="../controllers/cartController.php?action=remove&id_sach=<?= $item['id_sach'] ?>">❌ Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="cart-summary">
                <h3>Tổng cộng: <?= number_format($total, 0, ',', '.') ?>đ</h3>
                <button type="submit" class="btn">🔄 Cập nhật giỏ hàng</button>
                <a href="../controllers/cartController.php?action=clear" class="btn btn-danger">🧹 Xóa giỏ hàng</a>
                <a href="checkout.php" class="btn">💳 Thanh toán</a>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
