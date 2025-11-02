<?php
include_once '../includes/header.php';
require_once '../models/Order.php';

if (!isset($_SESSION['id_tk'])) {
    header("Location: ../guest/login.php");
    exit;
}

$orderModel = new Order();
$orders = $orderModel->getOrdersByUser($_SESSION['id_tk']);
?>

<div class="container" style="padding: 40px 0;">
    <h2>📦 Đơn hàng của bạn</h2>

    <?php if (empty($orders)): ?>
        <p>Chưa có đơn hàng nào.</p>
    <?php else: ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Ngày đặt</th>
                    <th>Địa chỉ</th>
                    <th>Trạng thái</th>
                    <th>Tổng tiền</th>
                    <th>Thanh toán</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $o): ?>
                    <?php $details = $orderModel->getOrderDetails($o->id_don_hang); ?>
                    <tr>
                        <td><b><?= htmlspecialchars($o->id_don_hang) ?></b></td>
                        <td><?= htmlspecialchars($o->ngay_gio_tao_don) ?></td>
                        <td><?= htmlspecialchars($o->dia_chi_nhan_hang) ?></td>
                        <td>
                            <?php
                                $statusColor = [
                                    'Chờ xử lý' => '#007bff',
                                    'Đang giao hàng' => '#ff9800',
                                    'Hoàn tất' => '#28a745',
                                    'Đã hủy' => '#dc3545',
                                ];
                                $color = $statusColor[$o->trang_thai_dh] ?? '#555';
                            ?>
                            <span style="font-weight:bold; color:<?= $color ?>;">
                                <?= htmlspecialchars($o->trang_thai_dh) ?>
                            </span>
                        </td>
                        <td><?= number_format($o->tong_tien, 0, ',', '.') ?>đ</td>
                        <td>
                            <?= ($o->trang_thai_tt ?? 0) == 1 ? '✅ Đã TT' : '💸 Chưa TT' ?>
                        </td>
                        <td>
                            <?php if ($o->id_trang_thai == 1): ?>
                                <a href="../controllers/orderController.php?action=cancel&id_don_hang=<?= $o->id_don_hang ?>"
                                   class="btn btn-danger"
                                   onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">
                                   ❌ Hủy đơn
                                </a>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>

                    <tr>
                        <td colspan="7" style="background:#fafafa; text-align:left; padding:10px 20px;">
                            <b>📚 Sản phẩm trong đơn:</b><br>
                            <?php foreach ($details as $d): ?>
                                • <?= htmlspecialchars($d->ten_sach) ?> 
                                (x<?= $d->so_luong_ban ?>) 
                                – <?= number_format($d->gia_sach_ban, 0, ',', '.') ?>đ<br>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
