<?php
// 1. GỌI HEADER (đã bao gồm session_start() và kiểm tra đăng nhập)
include 'includes/header.php'; 

// 2. GỌI KẾT NỐI DATABASE
include 'includes/db.php';

// 3. LOGIC CŨ CỦA DASHBOARD (giữ nguyên)
// Kết nối database để lấy thống kê
$stats = [
    'total_books' => 0,
    'total_orders' => 0,
    'total_users' => 0,
    'total_revenue' => 0
];

try {
    // Biến $pdo đã có từ file 'includes/db.php'
    
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
    // SỬA LỖI QUERY: Cần join với don_hang và gia_sach tại một thời điểm cố định
    // Query này phức tạp, tạm thời đơn giản hóa dựa trên CSDL của bạn
    $stmt = $pdo->query("
        SELECT SUM(gs.gia_sach_ban * ctdh.so_luong_ban) as revenue
        FROM chi_tiet_don_hang ctdh
        JOIN don_hang dh ON ctdh.id_don_hang = dh.id_don_hang
        JOIN sach s ON ctdh.id_sach = s.id_sach
        -- Giả định lấy giá sách gần nhất (CSDL của bạn lưu giá theo ngày)
        JOIN gia_sach gs ON s.id_sach = gs.id_sach
        WHERE dh.id_trang_thai = 4 -- Giả định 4 là 'Đã giao thành công'
        AND gs.ngay_gio_ban = (SELECT MAX(ngay_gio_ban) FROM gia_sach WHERE id_sach = s.id_sach)
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_revenue'] = $result['revenue'] ?? 0;

} catch (PDOException $e) {
    // Xử lý lỗi - giữ giá trị mặc định
    error_log("Database connection error: " . $e->getMessage());
}

// 4. GỌI SIDEBAR
include 'includes/sidebar.php'; 
?>

<div class="content-wrapper">
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

    <section class="content">
        <div class="container-fluid">
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
                        <a href="books/index.php" class="small-box-footer"> Chi tiết <i class="fas fa-arrow-circle-right"></i>
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
                        <a href="#" class="small-box-footer"> Chi tiết <i class="fas fa-arrow-circle-right"></i>
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
                        <a href="#" class="small-box-footer"> Chi tiết <i class="fas fa-arrow-circle-right"></i>
                        </a>
                    </div>
                </div>

                <div class="col-lg-3 col-6">
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><?php echo number_format($stats['total_revenue']); ?> đ</h3>
                            <p>Doanh Thu (Đã giao)</p>
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

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header border-0">
                            <h3 class="card-title">
                                <i class="fas fa-shopping-cart mr-1"></i>
                                Đơn Hàng Gần Đây
                            </h3>
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
                                        <td>23/10/2025</td>
                                        <td><span class="badge bg-primary">Chờ xử lý</span></td>
                                        <td>
                                            <a href="#" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><a href="#">DH002</a></td>
                                        <td>Trần Thị B</td>
                                        <td>24/10/2025</td>
                                        <td><span class="badge bg-info">Đang giao hàng</span></td>
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
                                <a href="books/create.php" class="btn btn-primary mb-2"> <i class="fas fa-plus mr-2"></i> Thêm Sách Mới
                                </a>
                                <a href="#" class="btn btn-success mb-2"> <i class="fas fa-shopping-cart mr-2"></i> Xem Đơn Hàng
                                </a>
                                <a href="#" class="btn btn-info mb-2"> <i class="fas fa-users mr-2"></i> Quản Lý User
                                </a>
                                <a href="#" class="btn btn-warning">
                                    <i class="fas fa-chart-bar mr-2"></i> Báo Cáo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php
// 5. GỌI FOOTER (đã bao gồm các file .js)
include 'includes/footer.php'; 
?>