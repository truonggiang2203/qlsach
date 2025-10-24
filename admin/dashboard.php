<?php
session_start();
if (!isset($_SESSION['phan_quyen']) || $_SESSION['phan_quyen'] != 'admin') {
    header('Location: ../public/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>

    <div class="container">
        <div class="admin-header">
            <h1>Admin: <?php echo htmlspecialchars($_SESSION['ho_ten']); ?></h1>
            <div>
                <a href="../public/index.php" target="_blank">Xem trang chủ</a> |
                <a href="../controllers/AuthController.php?action=logout" class="admin-logout">Đăng xuất</a>
            </div>
        </div>

        <nav class="admin-nav">
            <h2>Bảng điều khiển</h2>
            <ul>
                <li><a href="quan_ly_sach.php">Quản lý Sách</a></li>
                <li><a href="quan_ly_don_hang.php">Quản lý Đơn hàng</a></li>
                <li><a href="quan_ly_nguoi_dung.php">Quản lý Người dùng</a></li>
            </ul>
        </nav>
    </div>
    
    <?php
        include_once '../includes/footer.php';
    ?>
</body>
</html>