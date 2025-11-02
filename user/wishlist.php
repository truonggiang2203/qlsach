<?php
include_once '../includes/header.php';
session_start();

$wishlist = $_SESSION['wishlist'] ?? [];
?>

<div class="container">
    <h2>❤️ Danh sách yêu thích</h2>

    <?php if (empty($wishlist)): ?>
        <p>Bạn chưa thêm sản phẩm nào vào danh sách yêu thích.</p>
    <?php else: ?>
        <div class="product-grid">
            <?php foreach ($wishlist as $item): ?>
                <div class="product-item">
                    <img src="https://via.placeholder.com/250x350?text=<?= urlencode($item['ten_sach']) ?>">
                    <div class="product-info">
                        <h4><?= htmlspecialchars($item['ten_sach']) ?></h4>
                        <div class="product-price"><?= number_format($item['gia'], 0, ',', '.') ?>đ</div>
                        <a href="../controllers/cartController.php?action=add&id_sach=<?= $item['id_sach'] ?>" class="btn">🛒 Thêm vào giỏ</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
