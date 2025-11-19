<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nạp Category để tạo menu danh mục
require_once __DIR__ . '/../models/Category.php';

$categoryModelNav = new Category();
$parentCategoriesNav = $categoryModelNav->getAllParentCategories();
$subCategoriesNav = $categoryModelNav->getAllSubCategories();
// Counts to show number of books
$countsByParent = $categoryModelNav->countBooksByParent();
$countsBySub = $categoryModelNav->countBooksBySubcategory();

// Lấy số lượng giỏ hàng từ session
$cartCount = $_SESSION['cartCount'] ?? 0;

// Lấy số lượng sách trong danh sách so sánh
require_once __DIR__ . '/../models/Compare.php';
$compareModelNav = new Compare();
$compareCount = $compareModelNav->getCount();

// Lấy số lượng thông báo chưa đọc
require_once __DIR__ . '/../models/Notification.php';
$notificationModelNav = new Notification();
$notificationCount = $notificationModelNav->getUnreadCount();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QLSách - Cửa hàng sách</title>
    <link rel="stylesheet" href="/qlsach/public/css/style.css">
    <link rel="stylesheet" href="/qlsach/public/css/wishlist-button.css">
    <?php if (strpos($_SERVER['REQUEST_URI'], 'checkout') !== false): ?>
        <link rel="stylesheet" href="/qlsach/public/css/checkout.css">
    <?php endif; ?>
    <?php if (strpos($_SERVER['REQUEST_URI'], 'wishlist') !== false): ?>
        <link rel="stylesheet" href="/qlsach/public/css/wishlist.css">
    <?php endif; ?>
           <?php if (strpos($_SERVER['REQUEST_URI'], 'book_detail') !== false): ?>
               <link rel="stylesheet" href="/qlsach/public/css/comment.css">
           <?php endif; ?>
           <?php if (strpos($_SERVER['REQUEST_URI'], 'orders') !== false): ?>
               <link rel="stylesheet" href="/qlsach/public/css/orders.css">
           <?php endif; ?>
           <?php if (strpos($_SERVER['REQUEST_URI'], 'profile') !== false): ?>
               <link rel="stylesheet" href="/qlsach/public/css/profile.css">
           <?php endif; ?>
           <?php if (strpos($_SERVER['REQUEST_URI'], 'search') !== false): ?>
               <link rel="stylesheet" href="/qlsach/public/css/search.css">
           <?php endif; ?>
        <?php
        // Load homepage-specific assets only on the homepage
        $currentScript = basename($_SERVER['SCRIPT_NAME']);
        if ($currentScript === 'index.php' || strpos($_SERVER['REQUEST_URI'], '/index.php') !== false): ?>
            <link rel="stylesheet" href="/qlsach/public/css/home.css">
            <script defer src="/qlsach/public/js/home.js"></script>
        <?php endif; ?>
</head>

<body>
<div class="page-container">

<header class="main-header">
    <div class="header-container">
        <!-- Logo -->
        <div class="header-logo">
            <a href="/qlsach/public/index.php" class="logo-link">
                <span class="logo-icon">📚</span>
                <span class="logo-text">QLSách</span>
            </a>
        </div>

        <!-- Search Bar -->
        <div class="header-search">
            <form action="/qlsach/public/search.php" method="GET" class="search-form">
                <input type="text" name="keyword" placeholder="Tìm kiếm sách, tác giả, thể loại..." class="search-input">
                <button type="submit" class="search-btn">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
            </form>
        </div>

        <!-- User Actions -->
        <div class="header-actions">
            <!-- So sánh -->
            <a href="/qlsach/user/compare.php" class="action-item compare-item" title="So sánh sách">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                </svg>
                <span class="action-text">So sánh</span>
                <?php if ($compareCount > 0): ?>
                    <span class="action-badge"><?= $compareCount ?></span>
                <?php endif; ?>
            </a>

            <!-- Thông báo -->
            <?php if (isset($_SESSION['id_tk'])): ?>
                <div class="notification-wrapper">
                    <a href="/qlsach/user/notifications.php" class="action-item notification-item" title="Thông báo">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        <span class="action-text">Thông báo</span>
                        <?php if ($notificationCount > 0): ?>
                            <span class="action-badge"><?= $notificationCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            <?php endif; ?>

            <!-- Giỏ hàng -->
            <a href="/qlsach/user/cart.php" class="action-item cart-item" title="Giỏ hàng">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                <span class="action-text">Giỏ hàng</span>
                <?php if ($cartCount > 0): ?>
                    <span class="action-badge"><?= $cartCount ?></span>
                <?php endif; ?>
            </a>

            <!-- User Menu -->
            <?php if (isset($_SESSION['id_tk'])): ?>
                <div class="user-menu-wrapper">
                    <div class="user-menu-trigger">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span class="user-name"><?= htmlspecialchars(mb_substr($_SESSION['ho_ten'], 0, 15)) ?></span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="dropdown-arrow">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="user-menu-dropdown">
                        <a href="/qlsach/user/profile.php" class="menu-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Tài khoản
                        </a>
                        <a href="/qlsach/user/orders.php" class="menu-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                            Đơn hàng
                        </a>
                        <a href="/qlsach/user/wishlist.php" class="menu-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                            Yêu thích
                        </a>
                        <a href="/qlsach/user/compare.php" class="menu-item">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M8 3H5a2 2 0 0 0-2 2v3m18 0V5a2 2 0 0 0-2-2h-3m0 18h3a2 2 0 0 0 2-2v-3M3 16v3a2 2 0 0 0 2 2h3"></path>
                            </svg>
                            So sánh
                        </a>
                        <?php if (!empty($_SESSION['phan_quyen']) && $_SESSION['phan_quyen'] === 'admin'): ?>
                            <div class="menu-divider"></div>
                            <a href="/qlsach/admin/dashboard.php" class="menu-item">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                </svg>
                                Quản trị
                            </a>
                        <?php endif; ?>
                        <div class="menu-divider"></div>
                        <a href="/qlsach/controllers/authController.php?action=logout" class="menu-item menu-item-danger">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Đăng xuất
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="auth-buttons">
                    <a href="/qlsach/guest/login.php" class="btn-auth btn-login">Đăng nhập</a>
                    <a href="/qlsach/guest/register.php" class="btn-auth btn-register">Đăng ký</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- ===================== THANH DANH MỤC ===================== -->
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
                <?php foreach ($parentCategoriesNav as $parent): 
                    $parentCount = $countsByParent[$parent->id_loai] ?? 0;
                ?>

                    <div class="dropdown-column">
                        <a href="/qlsach/public/search.php?category=<?= $parent->id_loai ?>" 
                           class="dropdown-header">
                            <?= htmlspecialchars($parent->ten_loai) ?> <span class="menu-count">(<?= $parentCount ?>)</span>
                        </a>

                        <?php foreach ($subCategoriesNav as $sub): ?>
                            <?php if ($sub->id_loai == $parent->id_loai): ?>
                                <?php $scnt = $countsBySub[$sub->id_the_loai] ?? 0; ?>
                                <a href="/qlsach/public/search.php?subcategory=<?= $sub->id_the_loai ?>">
                                    <?= htmlspecialchars($sub->ten_the_loai) ?> <span class="menu-count">(<?= $scnt ?>)</span>
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
<script>
// Toggle mega-menu on click for better mobile support
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.category-nav li.dropdown-trigger > a').forEach(a=>{
        a.addEventListener('click', function(e){
            e.preventDefault();
            const parent = a.parentElement;
            parent.classList.toggle('open');
        });
    });

    // Close when clicking outside
    document.addEventListener('click', function(e){
        const openEl = document.querySelector('.category-nav li.dropdown-trigger.open');
        if (!openEl) return;
        if (e.target.closest('.category-nav')) return; // click inside
        openEl.classList.remove('open');
    });
});
</script>
