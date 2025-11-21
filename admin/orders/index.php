<?php
include '../includes/header.php';
include '../includes/db.php';

// --- CẤU HÌNH PHÂN TRANG ---
$limit = 10; // Số đơn hàng mỗi trang
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// 1. Đếm tổng số đơn hàng
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM don_hang");
$stmt_count->execute();
$total_orders = $stmt_count->fetchColumn();
$total_pages = ceil($total_orders / $limit);

// 2. Lấy dữ liệu đơn hàng (Kèm tính tổng tiền và trạng thái)
// Chúng ta cần JOIN nhiều bảng để lấy tên khách, trạng thái, và tính tổng tiền
$sql = "
    SELECT 
        dh.id_don_hang,
        dh.ngay_gio_tao_don,
        tk.ho_ten,
        tt.trang_thai_dh,
        tt.id_trang_thai,
        
        -- Lấy thông tin thanh toán (nếu có)
        pttt.ten_pttt,
        COALESCE(ttoan.trang_thai_tt, 0) as trang_thai_thanh_toan,
        
        -- Tính tổng tiền đơn hàng từ bảng chi tiết
        SUM(ct.so_luong_ban * ct.don_gia_ban) as tong_tien
        
    FROM don_hang dh
    JOIN tai_khoan tk ON dh.id_tk = tk.id_tk
    JOIN trang_thai_don_hang tt ON dh.id_trang_thai = tt.id_trang_thai
    JOIN chi_tiet_don_hang ct ON dh.id_don_hang = ct.id_don_hang
    
    -- Join bảng thanh toán (LEFT JOIN vì có thể chưa thanh toán hoặc thanh toán sau)
    LEFT JOIN thanh_toan ttoan ON dh.id_don_hang = ttoan.id_don_hang
    LEFT JOIN phuong_thuc_thanh_toan pttt ON ttoan.id_pttt = pttt.id_pttt
    
    GROUP BY dh.id_don_hang
    ORDER BY dh.ngay_gio_tao_don DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$orders = $stmt->fetchAll();

// Hàm helper để hiển thị màu trạng thái
function getStatusBadge($status_id, $status_name) {
    switch ($status_id) {
        case 1: return '<span class="badge badge-warning">' . $status_name . '</span>'; // Chờ xử lý
        case 2: return '<span class="badge badge-info">' . $status_name . '</span>'; // Đã xác nhận
        case 3: return '<span class="badge badge-primary">' . $status_name . '</span>'; // Đang giao
        case 4: return '<span class="badge badge-success">' . $status_name . '</span>'; // Hoàn thành
        case 5: return '<span class="badge badge-danger">' . $status_name . '</span>'; // Đã hủy
        default: return '<span class="badge badge-secondary">' . $status_name . '</span>';
    }
}
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quản Lý Đơn Hàng</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh Sách Đơn Hàng</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách tất cả đơn hàng</h3>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Mã ĐH</th>
                                <th>Khách Hàng</th>
                                <th>Ngày Đặt</th>
                                <th>Tổng Tiền</th>
                                <th>Thanh Toán</th>
                                <th>Trạng Thái Đơn</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($orders) > 0): ?>
                                <?php foreach ($orders as $order): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($order['id_don_hang']); ?></strong></td>
                                        <td><?php echo htmlspecialchars($order['ho_ten']); ?></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($order['ngay_gio_tao_don'])); ?></td>
                                        <td class="text-danger font-weight-bold">
                                            <?php echo number_format($order['tong_tien'], 0, ',', '.'); ?> đ
                                        </td>
                                        <td>
                                            <?php 
                                            // Trạng thái thanh toán
                                            if ($order['trang_thai_thanh_toan'] == 1) {
                                                echo '<span class="badge badge-success"><i class="fas fa-check"></i> Đã thanh toán</span>';
                                            } else {
                                                echo '<span class="badge badge-secondary">Chưa thanh toán</span>';
                                            }
                                            ?>
                                            <br>
                                            <small class="text-muted"><?php echo $order['ten_pttt'] ?? 'COD'; ?></small>
                                        </td>
                                        <td>
                                            <?php echo getStatusBadge($order['id_trang_thai'], $order['trang_thai_dh']); ?>
                                        </td>
                                        <td>
                                            <a href="detail.php?id=<?php echo $order['id_don_hang']; ?>" class="btn btn-primary btn-sm">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Không có đơn hàng nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo ($page > 1) ? "?page=" . ($page - 1) : '#'; ?>">«</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo ($page < $total_pages) ? "?page=" . ($page + 1) : '#'; ?>">»</a>
                        </li>
                    </ul>
                    <div class="float-left mt-2">
                        <small>Hiển thị trang <?php echo $page; ?> / <?php echo $total_pages; ?></small>
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>