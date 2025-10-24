<?php
session_start();
if (!isset($_SESSION['phan_quyen']) || $_SESSION['phan_quyen'] != 'admin') {
    header('Location: ../public/index.php');
    exit;
}

// Kết nối database để lấy thống kê
$stats = [
    'total_books' => 0,
    'total_orders' => 0,
    'total_users' => 0,
    'total_revenue' => 0
];

try {
    // Kết nối database
    $host = 'localhost';
    $dbname = 'qlsach';
    $username = 'root';
    $password = '';
    
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Tổng số sách
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM sach");
    $stats['total_books'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Tổng số đơn hàng
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM don_hang");
    $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Tổng số người dùng
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tai_khoan WHERE id_nd = 'KH'");
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Doanh thu (giả định từ đơn hàng)
    $stmt = $pdo->query("SELECT SUM(gs.gia_sach_ban * ctdh.so_luong_ban) as revenue 
                         FROM chi_tiet_don_hang ctdh 
                         JOIN gia_sach gs ON ctdh.id_sach = gs.id_sach");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_revenue'] = $result['revenue'] ?? 0;
} catch (PDOException $e) {
    // Xử lý lỗi - giữ giá trị mặc định
    error_log("Database connection error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị - Quản Lý Sách</title>
    
    <!-- Google Font: Source Sans Pro -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    
    <style>
        .content-wrapper {
            background: #f4f6f9;
        }
        .small-box {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
        }
        .small-box .icon {
            font-size: 70px;
        }
        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,.08);
            border: none;
        }
    </style>
</head>
<body class="hold-transition sidebar-mini layout-fixed">
<div class="wrapper">

    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <!-- Left navbar links -->
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
            <li class="nav-item d-none d-sm-inline-block">
                <a href="../public/index.php" class="nav-link" target="_blank">
                    <i class="fas fa-home"></i> Xem trang chủ
                </a>
            </li>
        </ul>

        <!-- Right navbar links -->
        <ul class="navbar-nav ml-auto">
            <!-- Notifications Dropdown Menu -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-bell"></i>
                    <span class="badge badge-warning navbar-badge">3</span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <span class="dropdown-item dropdown-header">3 Thông báo</span>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-shopping-cart mr-2"></i> 2 đơn hàng mới
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-users mr-2"></i> 1 người dùng mới
                    </a>
                </div>
            </li>
            
            <!-- User Account -->
            <li class="nav-item dropdown">
                <a class="nav-link" data-toggle="dropdown" href="#">
                    <i class="far fa-user"></i>
                    <span class="d-none d-md-inline ml-1"><?php echo htmlspecialchars($_SESSION['ho_ten']); ?></span>
                </a>
                <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Thông tin cá nhân
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="../controllers/AuthController.php?action=logout" class="dropdown-item">
                        <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                    </a>
                </div>
            </li>
        </ul>
    </nav>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Content Header -->
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0">Bảng Điều Khiển</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="#">Trang chủ</a></li>
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main content -->
        <section class="content">
            <div class="container-fluid">
                <!-- Info boxes -->
                <div class="row">
                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-info">
                            <div class="inner">
                                <h3><?php echo number_format($stats['total_books']); ?></h3>
                                <p>Tổng Số Sách</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <a href="quan_ly_sach.php" class="small-box-footer">
                                Chi tiết <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-success">
                            <div class="inner">
                                <h3><?php echo number_format($stats['total_orders']); ?></h3>
                                <p>Đơn Hàng</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-shopping-cart"></i>
                            </div>
                            <a href="quan_ly_don_hang.php" class="small-box-footer">
                                Chi tiết <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-warning">
                            <div class="inner">
                                <h3><?php echo number_format($stats['total_users']); ?></h3>
                                <p>Người Dùng</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <a href="quan_ly_nguoi_dung.php" class="small-box-footer">
                                Chi tiết <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-3 col-6">
                        <div class="small-box bg-danger">
                            <div class="inner">
                                <h3><?php echo number_format($stats['total_revenue']); ?> đ</h3>
                                <p>Doanh Thu</p>
                            </div>
                            <div class="icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <a href="#" class="small-box-footer">
                                Chi tiết <i class="fas fa-arrow-circle-right"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Charts and Tables -->
                <div class="row">
                    <!-- Recent Orders -->
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header border-0">
                                <h3 class="card-title">
                                    <i class="fas fa-shopping-cart mr-1"></i>
                                    Đơn Hàng Gần Đây
                                </h3>
                                <div class="card-tools">
                                    <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="card-body table-responsive p-0">
                                <table class="table table-striped table-valign-middle">
                                    <thead>
                                        <tr>
                                            <th>Mã ĐH</th>
                                            <th>Khách hàng</th>
                                            <th>Ngày tạo</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><a href="#">DH001</a></td>
                                            <td>Nguyễn Văn A</td>
                                            <td>24/10/2025</td>
                                            <td><span class="badge bg-success">Đã giao</span></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><a href="#">DH002</a></td>
                                            <td>Trần Thị B</td>
                                            <td>23/10/2025</td>
                                            <td><span class="badge bg-warning">Đang xử lý</span></td>
                                            <td>
                                                <a href="#" class="btn btn-sm btn-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-bolt mr-1"></i>
                                    Thao Tác Nhanh
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="d-grid gap-2">
                                    <a href="quan_ly_sach.php?action=add" class="btn btn-primary mb-2">
                                        <i class="fas fa-plus mr-2"></i> Thêm Sách Mới
                                    </a>
                                    <a href="quan_ly_don_hang.php" class="btn btn-success mb-2">
                                        <i class="fas fa-shopping-cart mr-2"></i> Xem Đơn Hàng
                                    </a>
                                    <a href="quan_ly_nguoi_dung.php" class="btn btn-info mb-2">
                                        <i class="fas fa-users mr-2"></i> Quản Lý User
                                    </a>
                                    <a href="#" class="btn btn-warning">
                                        <i class="fas fa-chart-bar mr-2"></i> Báo Cáo
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- System Info -->
                        <div class="card">
                            <div class="card-header">
                                <h3 class="card-title">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Thông Tin Hệ Thống
                                </h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Phiên bản:</strong> 1.0.0</p>
                                <p><strong>Database:</strong> MySQL</p>
                                <p><strong>Admin:</strong> <?php echo htmlspecialchars($_SESSION['ho_ten']); ?></p>
                                <p><strong>Đăng nhập lần cuối:</strong> <?php echo date('d/m/Y H:i'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Footer -->
    <footer class="main-footer">
        <strong>Copyright &copy; 2025 <a href="#">Quản Lý Sách</a>.</strong>
        All rights reserved.
        <div class="float-right d-none d-sm-inline-block">
            <b>Version</b> 1.0.0
        </div>
    </footer>
</div>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0-beta2/dist/js/adminlte.min.js"></script>
</body>
</html>