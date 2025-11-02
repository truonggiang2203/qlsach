<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QLSách - Cửa hàng sách</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>
<div class="page-container">

<!-- === HEADER CHÍNH === -->
<header class="main-header">
    <div class="logo">
        <a href="../public/index.php" style="text-decoration:none; color:var(--primary-color);">
            📚 QLSách
        </a>
    </div>

    <div class="search-bar">
        <form action="../public/search.php" method="GET">
            <input type="text" name="keyword" placeholder="Tìm kiếm sách bạn muốn...">
        </form>
    </div>

    <div class="user-actions">
        <!-- 🛒 Giỏ hàng -->
        <a href="../user/cart.php">
            🛒 Giỏ hàng 
            (<?= isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'so_luong')) : 0 ?>)
        </a>

        <?php if (isset($_SESSION['id_tk'])): ?>
            <!-- 👤 Nếu đã đăng nhập -->
            <span>Chào, <b><?= htmlspecialchars($_SESSION['ho_ten']) ?></b></span>

            <!-- 📄 Hồ sơ người dùng -->
            <a href="../user/profile.php">Tài khoản</a>

            <!-- 📦 Đơn hàng -->
            <a href="../user/orders.php">Đơn hàng</a>

            <!-- ⚙️ Nếu là admin -->
            <?php if (!empty($_SESSION['phan_quyen']) && $_SESSION['phan_quyen'] === 'admin'): ?>
                <a href="../admin/dashboard.php">Quản trị</a>
            <?php endif; ?>

            <!-- 🚪 Đăng xuất -->
            <a href="../controllers/authController.php?action=logout" class="btn-logout">Đăng xuất</a>

        <?php else: ?>
            <!-- 🔑 Nếu chưa đăng nhập -->
            <a href="../guest/login.php">Đăng nhập</a>
            <a href="../guest/register.php">Đăng ký</a>
        <?php endif; ?>
    </div>
</header>

<!-- === THANH DANH MỤC (NAV) === -->
<nav class="category-nav">
    <ul>
        <li><a href="../public/index.php">Trang chủ</a></li>
        <li><a href="../public/search.php?category=KT">Sách Kinh Tế</a></li>
        <li><a href="../public/search.php?category=VH">Sách Văn Học</a></li>
        <li><a href="../public/search.php?category=KN">Sách Kỹ Năng</a></li>
        <li><a href="../public/search.php?category=TN">Sách Thiếu Nhi</a></li>
        <li><a href="../public/search.php?category=NN">Sách Ngoại Ngữ</a></li>
        <li><a href="../public/search.php?new=1">📕 Sách Mới</a></li>
        <li><a href="../public/search.php?hot=1">🔥 Bán Chạy</a></li>
    </ul>
</nav>
