<?php session_start(); ?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QLSách - Cửa hàng sách</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="page-container">
    
    <header class="main-header">
        <div class="logo">QLSách</div>
        <div class="search-bar">
            <input type="text" placeholder="Tìm kiếm sách bạn muốn...">
        </div>
        <div class="user-actions">
            <a href="#">Giỏ hàng (0)</a>
            <?php if (isset($_SESSION['id_tk'])): ?>
                <span>Chào, <b><?php echo htmlspecialchars($_SESSION['ho_ten']); ?></b></span>
                <a href="../controllers/AuthController.php?action=logout">Đăng xuất</a>
                <?php if ($_SESSION['phan_quyen'] == 'admin'): ?>
                    <a href="../admin/dashboard.php">Admin</a>
                <?php endif; ?>
            <?php else: ?>
                <a href="../guest/login.php">Đăng nhập</a>
                <a href="../guest/register.php">Đăng ký</a>
            <?php endif; ?>
        </div>
    </header>

    <nav class="category-nav">
        <ul>
            <li><a href="#">Sách Kinh Tế</a></li>
            <li><a href="#">Sách Văn Học</a></li>
            <li><a href="#">Sách Kỹ Năng</a></li>
            <li><a href="#">Sách Thiếu Nhi</a></li>
            <li><a href="#">Sách Ngoại Ngữ</a></li>
        </ul>
    </nav>