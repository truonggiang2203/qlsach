<?php
include '../includes/header.php';
include '../includes/db.php';

// --- XỬ LÝ: KHÓA / MỞ KHÓA TÀI KHOẢN ---
if (isset($_GET['action']) && $_GET['action'] == 'toggle_status' && isset($_GET['id'])) {
    $id_tk = $_GET['id'];
    
    // Lấy trạng thái hiện tại
    $stmt = $pdo->prepare("SELECT trang_thai FROM tai_khoan WHERE id_tk = ?");
    $stmt->execute([$id_tk]);
    $current_status = $stmt->fetchColumn();
    
    if ($current_status !== false) {
        // Đảo ngược trạng thái (1 -> 0, 0 -> 1)
        $new_status = ($current_status == 1) ? 0 : 1;
        
        $stmt_update = $pdo->prepare("UPDATE tai_khoan SET trang_thai = ? WHERE id_tk = ?");
        $stmt_update->execute([$new_status, $id_tk]);
        
        $msg = ($new_status == 0) ? "Đã khóa tài khoản $id_tk" : "Đã mở khóa tài khoản $id_tk";
        $_SESSION['success_message'] = $msg;
    }
    header('Location: index.php');
    exit;
}

// --- LẤY DANH SÁCH KHÁCH HÀNG (id_nd = 'KH') ---
// Cấu hình phân trang
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// Đếm tổng
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM tai_khoan WHERE id_nd = 'KH'");
$stmt_count->execute();
$total_users = $stmt_count->fetchColumn();
$total_pages = ceil($total_users / $limit);

// Lấy dữ liệu
$stmt = $pdo->prepare("
    SELECT * FROM tai_khoan 
    WHERE id_nd = 'KH' 
    ORDER BY ngay_gio_tao_tk DESC 
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$users = $stmt->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Quản Lý Khách Hàng</h1></div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách người dùng</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Mã TK</th>
                                <th>Họ Tên</th>
                                <th>Email</th>
                                <th>SĐT</th>
                                <th>Ngày Tạo</th>
                                <th>Trạng Thái</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($u['id_tk']); ?></td>
                                <td><?php echo htmlspecialchars($u['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($u['email']); ?></td>
                                <td><?php echo htmlspecialchars($u['sdt']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($u['ngay_gio_tao_tk'])); ?></td>
                                <td>
                                    <?php if ($u['trang_thai'] == 1): ?>
                                        <span class="badge badge-success">Hoạt động</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">Đã khóa</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($u['trang_thai'] == 1): ?>
                                        <a href="index.php?action=toggle_status&id=<?php echo $u['id_tk']; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Bạn có chắc chắn muốn CẤM người dùng này? Họ sẽ không thể đăng nhập.');">
                                            <i class="fas fa-ban"></i> Cấm (Khóa)
                                        </a>
                                    <?php else: ?>
                                        <a href="index.php?action=toggle_status&id=<?php echo $u['id_tk']; ?>" 
                                           class="btn btn-success btn-sm"
                                           onclick="return confirm('Mở khóa cho người dùng này?');">
                                            <i class="fas fa-unlock"></i> Mở khóa
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include '../includes/footer.php'; ?>