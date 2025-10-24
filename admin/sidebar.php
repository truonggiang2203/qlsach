<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="dashboard.php" class="brand-link">
        <i class="fas fa-book-reader brand-image img-circle elevation-3" style="font-size: 2rem; margin-left: 0.8rem;"></i>
        <span class="brand-text font-weight-light">Quản Lý Sách</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <i class="fas fa-user-circle fa-2x text-white-50"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo htmlspecialchars($_SESSION['ho_ten']); ?></a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- Quản lý Sách -->
                <li class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'quan_ly_sach') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'quan_ly_sach') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-book"></i>
                        <p>
                            Quản Lý Sách
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="quan_ly_sach.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh Sách Sách</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_sach.php?action=add" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Thêm Sách Mới</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_loai_sach.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Loại Sách</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_the_loai.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Thể Loại</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quản lý Đơn Hàng -->
                <li class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'quan_ly_don_hang') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'quan_ly_don_hang') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-shopping-cart"></i>
                        <p>
                            Quản Lý Đơn Hàng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="quan_ly_don_hang.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh Sách Đơn Hàng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_don_hang.php?status=pending" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Đơn Chờ Xử Lý</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_don_hang.php?status=completed" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Đơn Đã Hoàn Thành</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quản lý Người Dùng -->
                <li class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'quan_ly_nguoi_dung') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'quan_ly_nguoi_dung') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-users"></i>
                        <p>
                            Quản Lý Người Dùng
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="quan_ly_nguoi_dung.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Danh Sách Người Dùng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_nguoi_dung.php?action=add" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Thêm Người Dùng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_admin.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Quản Trị Viên</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quản lý Kho -->
                <li class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'kho') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'kho') !== false) ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-warehouse"></i>
                        <p>
                            Quản Lý Kho
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="quan_ly_phieu_nhap.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Phiếu Nhập</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_ton_kho.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tồn Kho</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_nha_cung_cap.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Nhà Cung Cấp</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Quản lý Tác Giả & NXB -->
                <li class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'tac_gia') !== false || strpos($_SERVER['PHP_SELF'], 'nxb') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-pen-fancy"></i>
                        <p>
                            Tác Giả & NXB
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="quan_ly_tac_gia.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Tác Giả</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_nxb.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Nhà Xuất Bản</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="quan_ly_ngon_ngu.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Ngôn Ngữ</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Khuyến Mãi -->
                <li class="nav-item">
                    <a href="quan_ly_khuyen_mai.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'quan_ly_khuyen_mai.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Khuyến Mãi</p>
                    </a>
                </li>

                <!-- Bình Luận & Đánh Giá -->
                <li class="nav-item">
                    <a href="quan_ly_binh_luan.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'quan_ly_binh_luan.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-comments"></i>
                        <p>
                            Bình Luận
                            <span class="badge badge-info right">5</span>
                        </p>
                    </a>
                </li>

                <!-- Báo Cáo & Thống Kê -->
                <li class="nav-item <?php echo (strpos($_SERVER['PHP_SELF'], 'bao_cao') !== false || strpos($_SERVER['PHP_SELF'], 'thong_ke') !== false) ? 'menu-open' : ''; ?>">
                    <a href="#" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>
                            Báo Cáo
                            <i class="fas fa-angle-left right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="bao_cao_doanh_thu.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Doanh Thu</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="bao_cao_ban_hang.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Bán Hàng</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="thong_ke_khach_hang.php" class="nav-link">
                                <i class="far fa-circle nav-icon"></i>
                                <p>Khách Hàng</p>
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- Divider -->
                <li class="nav-header">CÀI ĐẶT</li>

                <!-- Cài Đặt -->
                <li class="nav-item">
                    <a href="cai_dat.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'cai_dat.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-cog"></i>
                        <p>Cài Đặt Hệ Thống</p>
                    </a>
                </li>

                <!-- Phương Thức Thanh Toán -->
                <li class="nav-item">
                    <a href="quan_ly_thanh_toan.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'quan_ly_thanh_toan.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-credit-card"></i>
                        <p>Phương Thức TT</p>
                    </a>
                </li>

                <!-- Divider -->
                <li class="nav-header">TÀI KHOẢN</li>

                <!-- Profile -->
                <li class="nav-item">
                    <a href="profile.php" class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'profile.php') ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Thông Tin Cá Nhân</p>
                    </a>
                </li>

                <!-- Đăng Xuất -->
                <li class="nav-item">
                    <a href="../controllers/AuthController.php?action=logout" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt text-danger"></i>
                        <p class="text-danger">Đăng Xuất</p>
                    </a>
                </li>

            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>