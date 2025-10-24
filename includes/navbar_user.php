<nav class="navbar">
    <div class="nav-links">
        <a href="../public/index.php">Trang chủ</a>
        <a href="#">Giỏ hàng</a>
        <a href="#">Đơn hàng của tôi</a>
    </div>
    <div class="nav-user">
        <span>Xin chào, <b><?php echo htmlspecialchars($_SESSION['ho_ten']); ?></b></span>
        
        <?php if ($_SESSION['phan_quyen'] == 'admin'): ?>
            <a href="../admin/dashboard.php">Vào Admin</a> |
        <?php endif; ?>

        <a href="../controllers/AuthController.php?action=logout" class="btn-logout">
            Đăng xuất
        </a>
    </div>
</nav>