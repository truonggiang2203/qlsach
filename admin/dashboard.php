<?php
// 1. GỌI HEADER (đã bao gồm session_start() và kiểm tra đăng nhập)
include 'includes/header.php'; 

// 2. GỌI KẾT NỐI DATABASE
include 'includes/db.php';

// Khởi tạo biến mặc định để tránh lỗi Undefined variable
$stats = [
    'total_books' => 0,
    'total_orders' => 0,
    'total_users' => 0,
    'total_revenue' => 0
];
$recent_orders = []; // <--- KHỞI TẠO TRƯỚC ĐỂ TRÁNH LỖI

try {
    // Tổng số sách
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM sach WHERE trang_thai_sach = 1");
    $stats['total_books'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Tổng số đơn hàng
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM don_hang");
    $stats['total_orders'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Tổng số người dùng
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tai_khoan WHERE id_nd = 'KH'");
    $stats['total_users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // --- ĐÃ SỬA LỖI TÊN CỘT TẠI ĐÂY (ngay_gio_ban -> tg_gia_bd) ---
    $stmt = $pdo->query("
        SELECT SUM(ctdh.so_luong_ban * ctdh.don_gia_ban) as revenue
        FROM chi_tiet_don_hang ctdh
        JOIN don_hang dh ON ctdh.id_don_hang = dh.id_don_hang
        WHERE dh.id_trang_thai = 4 -- Chỉ tính đơn đã hoàn thành
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_revenue'] = $result['revenue'] ?? 0;

    // Lấy đơn hàng gần đây
    $stmt_recent = $pdo->query("
        SELECT 
            dh.id_don_hang, 
            dh.ngay_gio_tao_don,
            tk.ho_ten,
            tt.trang_thai_dh,
            tt.id_trang_thai
        FROM don_hang dh
        JOIN tai_khoan tk ON dh.id_tk = tk.id_tk
        JOIN trang_thai_don_hang tt ON dh.id_trang_thai = tt.id_trang_thai
        ORDER BY dh.ngay_gio_tao_don DESC
        LIMIT 5
    ");
    $recent_orders = $stmt_recent->fetchAll();

} catch (PDOException $e) {
    // Ghi log lỗi nhưng không làm chết trang
    error_log("Database error: " . $e->getMessage());
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
                        <a href="orders/index.php" class="small-box-footer"> Chi tiết <i class="fas fa-arrow-circle-right"></i>
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
                        <a href="users/index.php" class="small-box-footer"> Chi tiết <i class="fas fa-arrow-circle-right"></i>
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
                        <a href="reports/revenue.php" class="small-box-footer">
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
    <?php if (count($recent_orders) > 0): ?>
        <?php foreach ($recent_orders as $order): ?>
            <tr>
                <td>
                    <a href="orders/detail.php?id=<?php echo $order['id_don_hang']; ?>">
                        <?php echo htmlspecialchars($order['id_don_hang']); ?>
                    </a>
                </td>
                <td><?php echo htmlspecialchars($order['ho_ten']); ?></td>
                <td>
                    <?php echo date('d/m/Y H:i', strtotime($order['ngay_gio_tao_don'])); ?>
                </td>
                <td>
                    <?php 
                        // Xử lý màu sắc badge dựa trên id trạng thái
                        $badge_class = 'secondary';
                        switch ($order['id_trang_thai']) {
                            case 1: $badge_class = 'warning'; break; // Chờ xử lý
                            case 2: $badge_class = 'info'; break;    // Đã xác nhận
                            case 3: $badge_class = 'primary'; break; // Đang giao
                            case 4: $badge_class = 'success'; break; // Hoàn thành
                            case 5: $badge_class = 'danger'; break;  // Đã hủy
                        }
                    ?>
                    <span class="badge badge-<?php echo $badge_class; ?>">
                        <?php echo htmlspecialchars($order['trang_thai_dh']); ?>
                    </span>
                </td>
                <td>
                    <a href="orders/detail.php?id=<?php echo $order['id_don_hang']; ?>" class="btn btn-sm btn-primary">
                        <i class="fas fa-eye"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="5" class="text-center">Chưa có đơn hàng nào.</td>
        </tr>
    <?php endif; ?>
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
                            <a href="books/create.php" class="btn btn-primary btn-block mb-3">
                                <i class="fas fa-plus mr-2"></i> Thêm Sách Mới
                            </a>
                            <a href="orders/index.php" class="btn btn-success btn-block mb-3">
                                <i class="fas fa-shopping-cart mr-2"></i> Xem Đơn Hàng
                            </a>
                            <a href="users/index.php" class="btn btn-info btn-block mb-3">
                                <i class="fas fa-users mr-2"></i> Quản Lý User
                            </a>
                            <a href="reports/revenue.php" class="btn btn-warning btn-block">
                                <i class="fas fa-chart-bar mr-2"></i> Báo Cáo
                            </a>
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