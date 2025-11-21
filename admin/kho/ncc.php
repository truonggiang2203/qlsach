<?php
include '../includes/header.php';
include '../includes/db.php';

$error = '';
$success = '';
$edit_mode = false;
$ncc_edit = null;

// --- XỬ LÝ POST (THÊM / SỬA) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action_type = $_POST['action_type'];
    $ten_ncc = $_POST['ten_ncc'];
    $dia_chi_ncc = $_POST['dia_chi_ncc'];
    $sdt_ncc = $_POST['sdt_ncc'];
    $email_ncc = $_POST['email_ncc'];
    $trang_thai_ncc = $_POST['trang_thai_ncc'] ?? 'active';

    try {
        if ($action_type == 'add') {
            // Tạo ID tự động NCCxx
            $stmt_id = $pdo->query("SELECT id_ncc FROM nha_cung_cap ORDER BY id_ncc DESC LIMIT 1");
            $last = $stmt_id->fetch();
            $num = 1;
            if ($last) {
                $num = (int) substr($last['id_ncc'], 3) + 1;
            }
            $id_ncc = 'NCC' . str_pad($num, 2, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO nha_cung_cap (id_ncc, ten_ncc, dia_chi_ncc, sdt_ncc, email_ncc, trang_thai_ncc) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_ncc, $ten_ncc, $dia_chi_ncc, $sdt_ncc, $email_ncc, 'active']);
            $_SESSION['success_message'] = "Thêm nhà cung cấp $ten_ncc ($id_ncc) thành công!";

        } elseif ($action_type == 'edit') {
            $id_ncc = $_POST['id_ncc'];
            $stmt = $pdo->prepare("UPDATE nha_cung_cap SET ten_ncc=?, dia_chi_ncc=?, sdt_ncc=?, email_ncc=?, trang_thai_ncc=? WHERE id_ncc=?");
            $stmt->execute([$ten_ncc, $dia_chi_ncc, $sdt_ncc, $email_ncc, $trang_thai_ncc, $id_ncc]);
            $_SESSION['success_message'] = "Cập nhật nhà cung cấp thành công!";
        }
        header('Location: ncc.php');
        exit;

    } catch (Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// --- XỬ LÝ GET (SỬA / XÓA / LIST) ---
if (isset($_GET['action'])) {
    $id = $_GET['id'] ?? '';
    
    // Chế độ Sửa
    if ($_GET['action'] == 'edit' && $id) {
        $edit_mode = true;
        $stmt = $pdo->prepare("SELECT * FROM nha_cung_cap WHERE id_ncc = ?");
        $stmt->execute([$id]);
        $ncc_edit = $stmt->fetch();
    }

    // Chế độ Xóa (Soft Delete)
    if ($_GET['action'] == 'delete' && $id) {
        // Kiểm tra xem NCC có phiếu nhập nào không
        $check = $pdo->prepare("SELECT COUNT(*) FROM phieu_nhap WHERE id_ncc = ?");
        $check->execute([$id]);
        
        if ($check->fetchColumn() > 0) {
            // Nếu đã có giao dịch -> Chỉ chuyển trạng thái sang inactive
            $pdo->prepare("UPDATE nha_cung_cap SET trang_thai_ncc = 'inactive' WHERE id_ncc = ?")->execute([$id]);
            $_SESSION['success_message'] = "NCC này đã có lịch sử giao dịch. Đã chuyển trạng thái sang 'Ngừng hoạt động' (Inactive) để bảo toàn dữ liệu.";
        } else {
            // Nếu chưa có -> Xóa hẳn
            $pdo->prepare("DELETE FROM nha_cung_cap WHERE id_ncc = ?")->execute([$id]);
            $_SESSION['success_message'] = "Đã xóa vĩnh viễn nhà cung cấp.";
        }
        header('Location: ncc.php');
        exit;
    }
}

// Lấy danh sách
$suppliers = $pdo->query("SELECT * FROM nha_cung_cap ORDER BY id_ncc DESC")->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Nhà Cung Cấp</h1></div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card <?php echo $edit_mode ? 'card-warning' : 'card-primary'; ?>">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $edit_mode ? 'Sửa Nhà Cung Cấp' : 'Thêm Nhà Cung Cấp'; ?></h3>
                        </div>
                        <form action="" method="POST">
                            <div class="card-body">
                                <input type="hidden" name="action_type" value="<?php echo $edit_mode ? 'edit' : 'add'; ?>">
                                <?php if($edit_mode): ?>
                                    <input type="hidden" name="id_ncc" value="<?php echo $ncc_edit['id_ncc']; ?>">
                                <?php endif; ?>

                                <div class="form-group">
                                    <label>Tên NCC <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ten_ncc" required value="<?php echo $edit_mode ? htmlspecialchars($ncc_edit['ten_ncc']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Địa chỉ</label>
                                    <input type="text" class="form-control" name="dia_chi_ncc" value="<?php echo $edit_mode ? htmlspecialchars($ncc_edit['dia_chi_ncc']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Số điện thoại</label>
                                    <input type="text" class="form-control" name="sdt_ncc" value="<?php echo $edit_mode ? htmlspecialchars($ncc_edit['sdt_ncc']) : ''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control" name="email_ncc" value="<?php echo $edit_mode ? htmlspecialchars($ncc_edit['email_ncc']) : ''; ?>">
                                </div>
                                
                                <?php if($edit_mode): ?>
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select class="form-control" name="trang_thai_ncc">
                                        <option value="active" <?php echo $ncc_edit['trang_thai_ncc'] == 'active' ? 'selected' : ''; ?>>Hoạt động</option>
                                        <option value="inactive" <?php echo $ncc_edit['trang_thai_ncc'] == 'inactive' ? 'selected' : ''; ?>>Ngừng hoạt động</option>
                                    </select>
                                </div>
                                <?php endif; ?>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn <?php echo $edit_mode ? 'btn-warning' : 'btn-primary'; ?>">
                                    <?php echo $edit_mode ? 'Cập nhật' : 'Thêm Mới'; ?>
                                </button>
                                <?php if($edit_mode): ?>
                                    <a href="ncc.php" class="btn btn-default float-right">Hủy</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Danh Sách Nhà Cung Cấp</h3>
                        </div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên NCC</th>
                                        <th>Liên Hệ</th>
                                        <th>Trạng Thái</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($suppliers as $ncc): ?>
                                    <tr>
                                        <td><?php echo $ncc['id_ncc']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($ncc['ten_ncc']); ?></strong><br>
                                            <small><?php echo htmlspecialchars($ncc['dia_chi_ncc']); ?></small>
                                        </td>
                                        <td>
                                            <i class="fas fa-phone fa-xs"></i> <?php echo htmlspecialchars($ncc['sdt_ncc']); ?><br>
                                            <i class="fas fa-envelope fa-xs"></i> <?php echo htmlspecialchars($ncc['email_ncc']); ?>
                                        </td>
                                        <td>
                                            <?php if($ncc['trang_thai_ncc'] == 'active'): ?>
                                                <span class="badge badge-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge badge-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="ncc.php?action=edit&id=<?php echo $ncc['id_ncc']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="ncc.php?action=delete&id=<?php echo $ncc['id_ncc']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa/ngừng hoạt động NCC này?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>