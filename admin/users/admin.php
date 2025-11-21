<?php
include '../includes/header.php';
include '../includes/db.php';

// --- XỬ LÝ: THÊM ADMIN MỚI ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'add_admin') {
    $ho_ten = $_POST['ho_ten'];
    $email = $_POST['email'];
    $mat_khau = $_POST['mat_khau'];
    $sdt = $_POST['sdt'];
    
    // Mã hóa mật khẩu
    $hashed_password = password_hash($mat_khau, PASSWORD_DEFAULT);
    $now = date('Y-m-d H:i:s');

    try {
        // Tạo ID Admin tự động (ADxxx)
        $stmt_id = $pdo->query("SELECT id_tk FROM tai_khoan WHERE id_nd = 'AD' ORDER BY id_tk DESC LIMIT 1");
        $last_admin = $stmt_id->fetch();
        $new_num = 1;
        if ($last_admin) {
            $new_num = (int) substr($last_admin['id_tk'], 2) + 1;
        }
        $id_tk = 'AD' . str_pad($new_num, 3, '0', STR_PAD_LEFT);

        $stmt = $pdo->prepare("INSERT INTO tai_khoan (id_tk, id_nd, ho_ten, email, mat_khau, sdt, gioi_tinh, dia_chi_giao_hang, ngay_gio_tao_tk, trang_thai) VALUES (?, 'AD', ?, ?, ?, ?, 'Khác', 'Văn phòng Admin', ?, 1)");
        $stmt->execute([$id_tk, $ho_ten, $email, $hashed_password, $sdt, $now]);
        
        $_SESSION['success_message'] = "Thêm Admin mới ($id_tk) thành công!";
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Lỗi: " . $e->getMessage();
    }
    header('Location: admin.php');
    exit;
}

// --- XỬ LÝ: XÓA ADMIN ---
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id_tk_delete = $_GET['id'];
    
    // Lấy ID tài khoản hiện tại đang đăng nhập (Giả sử bạn lưu id_tk trong session khi login)
    // Nếu session của bạn lưu 'id_nd' là 'AD', bạn cần đảm bảo có lưu 'id_tk' (ví dụ 'AD001')
    // Nếu chưa có, bạn nên cập nhật AuthController.
    // Tạm thời kiểm tra đơn giản:
    
    try {
         $stmt = $pdo->prepare("DELETE FROM tai_khoan WHERE id_tk = ? AND id_nd = 'AD'");
         $stmt->execute([$id_tk_delete]);
         $_SESSION['success_message'] = "Đã xóa tài khoản admin: $id_tk_delete";
    } catch (Exception $e) {
         $_SESSION['error_message'] = "Lỗi khi xóa: " . $e->getMessage();
    }
    header('Location: admin.php');
    exit;
}

// Lấy danh sách Admin
$admins = $pdo->query("SELECT * FROM tai_khoan WHERE id_nd = 'AD' ORDER BY id_tk")->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Quản Lý Quản Trị Viên (Admin)</h1></div>
                <div class="col-sm-6 text-right">
                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addAdminModal">
                        <i class="fas fa-user-plus"></i> Thêm Admin Mới
                    </button>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Mã Admin</th>
                                <th>Họ Tên</th>
                                <th>Email</th>
                                <th>SĐT</th>
                                <th>Ngày Tạo</th>
                                <th>Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($admins as $admin): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($admin['id_tk']); ?></td>
                                <td><?php echo htmlspecialchars($admin['ho_ten']); ?></td>
                                <td><?php echo htmlspecialchars($admin['email']); ?></td>
                                <td><?php echo htmlspecialchars($admin['sdt']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($admin['ngay_gio_tao_tk'])); ?></td>
                                <td>
                                    <a href="admin.php?action=delete&id=<?php echo $admin['id_tk']; ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Bạn có chắc chắn muốn xóa quyền Admin của tài khoản này?');">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="addAdminModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Quản Trị Viên Mới</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="admin.php" method="POST">
                <div class="modal-body">
                    <input type="hidden" name="action" value="add_admin">
                    <div class="form-group">
                        <label>Họ Tên</label>
                        <input type="text" class="form-control" name="ho_ten" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" class="form-control" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu</label>
                        <input type="password" class="form-control" name="mat_khau" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" class="form-control" name="sdt" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Lưu Admin</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>