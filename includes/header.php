<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// === PHẦN BỊ THIẾU CỦA BẠN LÀ ĐÂY ===
// NẠP DỮ LIỆU ĐỘNG CHO THANH NAV
// Sử dụng __DIR__ để đảm bảo đường dẫn luôn đúng
require_once __DIR__ . '/../models/Category.php'; 
$categoryModelNav = new Category();
$parentCategoriesNav = $categoryModelNav->getAllParentCategories();
$subCategoriesNav = $categoryModelNav->getAllSubCategories();
// === KẾT THÚC PHẦN BỊ THIẾU ===
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QLSách - Cửa hàng sách</title>
    <link rel="stylesheet" href="/qlsach/public/css/style.css">
</head>
<body>
<div class="page-container">

<header class="main-header">
    <div class="logo">
        <a href="/qlsach/public/index.php" style="text-decoration:none; color:var(--primary);">
            📚 QLSách
        </a>
    </div>

    <div class="search-bar">
        <form action="/qlsach/public/search.php" method="GET">
            <input type="text" name="keyword" placeholder="Tìm kiếm sách bạn muốn...">
        </form>
    </div>

    <div class="user-actions">
        <a href="/qlsach/user/cart.php">
            <img src="/qlsach/images/cart-icon.png" alt="Giỏ hàng" class="nav-icon">
            Giỏ hàng 
            (<?php
                $total_items = 0;
                if (!empty($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        if (isset($item['so_luong'])) {
                            $total_items += $item['so_luong'];
                        }
                    }
                }
                echo $total_items;
            ?>)
        </a>

        <?php if (isset($_SESSION['id_tk'])): ?>
            <span>Chào, <b><?= htmlspecialchars($_SESSION['ho_ten']) ?></b></span>
            <a href="/qlsach/user/profile.php">Tài khoản</a>
            <a href="/qlsach/user/orders.php">Đơn hàng</a>
            <?php if (!empty($_SESSION['phan_quyen']) && $_SESSION['phan_quyen'] === 'admin'): ?>
                <a href="/qlsach/admin/dashboard.php">Quản trị</a>
            <?php endif; ?>
            <a href="/qlsach/controllers/authController.php?action=logout" class="btn-logout">Đăng xuất</a>
        <?php else: ?>
            <a href="/qlsach/guest/login.php">Đăng nhập</a>
            <a href="/qlsach/guest/register.php">Đăng ký</a>
        <?php endif; ?>
    </div>
</header>

<nav class="category-nav">
    <ul>
        <li>
            <a href="/qlsach/public/index.php" title="Trang chủ">
                <img src="/qlsach/images/home-icon.png" alt="Trang chủ" class="nav-icon">
            </a>
        </li>
        
        <li class="dropdown-trigger">
            <a href="#">
                <img src="/qlsach/images/category-icon.png" alt="Danh mục" class="nav-icon">
                Tất cả danh mục
            </a>
            
            <div class="dropdown-content">
                <?php foreach ($parentCategoriesNav as $parent): ?>
                    <div class="dropdown-column">
                        <a href="/qlsach/public/search.php?category=<?= $parent->id_loai ?>" class="dropdown-header">
                            <?= htmlspecialchars($parent->ten_loai) ?>
                        </a>
                        
                        <?php foreach ($subCategoriesNav as $sub): ?>
                            <?php if ($sub->id_loai == $parent->id_loai): ?>
                                <a href="/qlsach/public/search.php?subcategory=<?= $sub->id_the_loai ?>">
                                    <?= htmlspecialchars($sub->ten_the_loai) ?>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </li>

        <li><a href="/qlsach/public/search.php?new=1">Sách Mới</a></li>
        <li><a href="/qlsach/public/search.php?hot=1">Bán Chạy</a></li>
    </ul>
</nav>