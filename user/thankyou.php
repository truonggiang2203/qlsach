<?php 
include_once '../includes/header.php';
require_once '../models/Payment.php';

$id_don_hang = $_GET['id_don_hang'] ?? '';
$payment_status = $_GET['payment'] ?? '';

$paymentModel = new Payment();
$paymentInfo = null;

if ($id_don_hang) {
    $paymentInfo = $paymentModel->getPaymentInfo($id_don_hang);
}

$isPaymentSuccess = $payment_status === 'success' || ($paymentInfo && $paymentInfo->trang_thai_tt == 1);
?>
<div class="container" style="max-width: 800px; margin: 40px auto; padding: 0 20px;">
    <div class="thankyou-box">
        <?php if ($isPaymentSuccess): ?>
            <div class="success-icon" style="width: 80px; height: 80px; margin: 0 auto 24px; background: #4CAF50; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                ✅
            </div>
            <h2 style="color: #4CAF50; margin-bottom: 16px;">Đặt hàng & Thanh toán thành công!</h2>
            <p style="color: #666; margin-bottom: 24px;">Cảm ơn bạn đã mua sắm tại <b style="color: var(--primary);">Nhà Sách Tri Thức</b>. Đơn hàng của bạn đã được xác nhận và thanh toán thành công.</p>
        <?php else: ?>
            <div class="success-icon" style="width: 80px; height: 80px; margin: 0 auto 24px; background: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 48px;">
                📦
            </div>
            <h2 style="color: var(--primary); margin-bottom: 16px;">Đặt hàng thành công!</h2>
            <p style="color: #666; margin-bottom: 24px;">Cảm ơn bạn đã mua sắm tại <b style="color: var(--primary);">Nhà Sách Tri Thức</b>. Đơn hàng của bạn đã được xác nhận.</p>
            <?php if ($paymentInfo && $paymentInfo->ten_pttt && strpos($paymentInfo->ten_pttt, 'COD') !== false): ?>
                <div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 16px; border-radius: 6px; margin-bottom: 24px; text-align: left;">
                    <strong style="color: #856404;">💡 Lưu ý:</strong>
                    <p style="margin: 8px 0 0 0; color: #856404;">Bạn sẽ thanh toán khi nhận hàng. Vui lòng chuẩn bị đúng số tiền để thanh toán cho nhân viên giao hàng.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 24px; text-align: left;">
            <div style="margin-bottom: 12px;">
                <strong style="color: #666;">Mã đơn hàng:</strong>
                <span style="font-size: 20px; font-weight: 700; color: var(--primary); margin-left: 8px;">
                    <?= htmlspecialchars($id_don_hang) ?>
                </span>
            </div>
            <?php if ($paymentInfo): ?>
                <div style="margin-bottom: 8px;">
                    <strong style="color: #666;">Phương thức thanh toán:</strong>
                    <span style="margin-left: 8px; color: var(--text);">
                        <?= htmlspecialchars($paymentInfo->ten_pttt) ?>
                    </span>
                </div>
                <div>
                    <strong style="color: #666;">Trạng thái thanh toán:</strong>
                    <span style="margin-left: 8px; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; <?= $paymentInfo->trang_thai_tt == 1 ? 'background: #4CAF50; color: white;' : 'background: #ffc107; color: #856404;' ?>">
                        <?= $paymentInfo->trang_thai_tt == 1 ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                    </span>
                </div>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 12px; justify-content: center; flex-wrap: wrap;">
            <a href="../public/index.php" class="btn" style="padding: 14px 28px; background: var(--primary); color: white; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;">
                Tiếp tục mua sắm
            </a>
            <a href="orders.php" class="btn" style="padding: 14px 28px; background: white; color: var(--primary); border: 2px solid var(--primary); border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.3s;">
                Xem đơn hàng
            </a>
        </div>
    </div>
</div>
<?php include_once '../includes/footer.php'; ?>

<style>
.thankyou-box {
    background: white;
    padding: 40px;
    border-radius: 12px;
    text-align: center;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-top: 40px;
}

.thankyou-box .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(93, 162, 213, 0.3);
}
</style>
