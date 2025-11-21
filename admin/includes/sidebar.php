<?php
// Lấy đường dẫn URI hiện tại, ví dụ: /admin/books/index.php
$current_uri = $_SERVER['REQUEST_URI'];

// --- Logic kiểm tra Active Menu ---

// Kiểm tra xem có đang ở trong các trang sách không
$is_books_page = (strpos($current_uri, '/admin/books/') !== false);

// Kiểm tra các trang đơn hàng
$is_orders_page = (strpos($current_uri, '/admin/orders/') !== false);

// Kiểm tra các trang người dùng
$is_users_page = (strpos($current_uri, '/admin/users/') !== false);

// Kiểm tra các trang kho
$is_kho_page = (strpos($current_uri, '/admin/kho/') !== false);

// Kiểm tra các trang Tác giả/NXB (Đề xuất: /admin/meta/)
$is_meta_page = (strpos($current_uri, '/admin/meta/') !== false);

// Kiểm tra các trang Báo cáo
$is_report_page = (strpos($current_uri, '/admin/reports/') !== false);



?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/qlsach/admin/dashboard.php" class="brand-link">
        <i class="fas fa-book-reader brand-image img-circle elevation-3" style="font-size: 2rem; margin-left: 0.8rem;"></i>
        <span class="brand-text font-weight-light">Quản Lý Sách</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white-50"></i>
            </div>
            <div class="info">
                <a href="/qlsach/admin/profile.php" class="d-block"><?php echo htmlspecialchars($_SESSION['ho_ten']); ?></a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="/qlsach/admin/dashboard.php" class="nav-link <?php echo (strpos($current_uri, '/admin/dashboard.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-item <?php echo $is_books_page ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo $is_books_page ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-book"></i>
                        <p>
                            Quản Lý Sách
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/qlsach/admin/books/index.php" class="nav-link <?php echo (strpos($current_uri, '/admin/books/index.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh Sách Sách</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/qlsach/admin/books/create.php" class="nav-link <?php echo (strpos($current_uri, '/admin/books/create.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Thêm Sách Mới</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/qlsach/admin/books/categories.php" class="nav-link <?php echo (strpos($current_uri, '/admin/books/categories.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Loại Sách</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/qlsach/admin/books/genres.php" class="nav-link <?php echo (strpos($current_uri, '/admin/books/genres.php') !== false) ? 'active' : ''; ?>">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Thể Loại</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php echo $is_orders_page ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo $is_orders_page ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Quản Lý Đơn Hàng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/qlsach/admin/orders/index.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Danh Sách Đơn Hàng</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php echo $is_users_page ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo $is_users_page ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Quản Lý Người Dùng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/qlsach/admin/users/index.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Danh Sách Người Dùng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/qlsach/admin/users/admin.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Quản Trị Viên</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php echo $is_kho_page ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo $is_kho_page ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>
                            Quản Lý Kho
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/qlsach/admin/kho/phieu_nhap.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Phiếu Nhập</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/qlsach/admin/kho/ton_kho.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Tồn Kho</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/qlsach/admin/kho/ncc.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Nhà Cung Cấp</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item <?php echo $is_meta_page ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo $is_meta_page ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-pen-fancy"></i>
                        <p>
                            Tác Giả & NXB
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/qlsach/admin/tg_nxb/authors.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Tác Giả</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/qlsach/admin/tg_nxb/publishers.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Nhà Xuất Bản</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="/qlsach/admin/tg_nxb/languages.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Ngôn Ngữ</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="/qlsach/admin/promotions/index.php" class="nav-link <?php echo (strpos($current_uri, '/admin/promotions/') !== false) ? 'active' : ''; ?>"> <i class="nav-icon fas fa-tags"></i>
                        <p>Khuyến Mãi</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/qlsach/admin/reviews/index.php" class="nav-link <?php echo (strpos($current_uri, '/admin/reviews/') !== false) ? 'active' : ''; ?>"> <i class="nav-icon fas fa-comments"></i>
                        <p>
                            Bình Luận
                        </p>
                    </a>
                </li>

                <li class="nav-item <?php echo $is_report_page ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo $is_report_page ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Báo Cáo
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="/qlsach/admin/reports/revenue.php" class="nav-link"> <i class="far fa-circle nav-icon"></i>
                                <p>Doanh Thu</p>
                            </a>
                        </li>
                    </ul>
                </li>


                <li class="nav-header">TÀI KHOẢN</li>

                <li class="nav-item">
                    <a href="/qlsach/admin/profile.php" class="nav-link <?php echo (strpos($current_uri, '/admin/profile.php') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Thông Tin Cá Nhân</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="/qlsach/admin/controllers/AuthController.php?action=logout" class="nav-link"> 
                        <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                        <p class="text-danger">Đăng Xuất</p>
                    </a>
                </li>

            </ul>
        </nav>
        </div>
    </aside>