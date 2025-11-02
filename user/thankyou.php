<?php include_once '../includes/header.php'; ?>
<div class="container">
    <div class="thankyou-box">
        <h2>🎉 Đặt hàng thành công!</h2>
        <p>Cảm ơn bạn đã mua sắm tại <b>QLSách</b>.</p>
        <p>Mã đơn hàng của bạn là: 
            <span class="highlight">
                <?= htmlspecialchars($_GET['id_don_hang'] ?? '...') ?>
            </span>
        </p>
        <a href="../public/index.php" class="btn">🛍️ Tiếp tục mua sắm</a>
        <a href="orders.php" class="btn btn-secondary">📦 Xem đơn hàng</a>
    </div>
</div>
<?php include_once '../includes/footer.php'; ?>

<style>
.thankyou-box {
    background: #fff;
    padding: 40px;
    border-radius: 8px;
    text-align: center;
    box-shadow: 0 3px 8px rgba(0,0,0,0.1);
    margin-top: 40px;
}
.thankyou-box h2 {
    color: var(--primary-color);
}
.thankyou-box .highlight {
    color: var(--danger-color);
    font-weight: bold;
}
</style>
