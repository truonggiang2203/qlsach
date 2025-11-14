<?php
include '../includes/header.php';
include '../includes/db.php';

$error = '';
$success = '';
$edit_mode = false;
$item_to_edit = null;

// XỬ LÝ POST (Thêm mới hoặc Cập nhật)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_tac_gia = $_POST['ten_tac_gia'];
    $action_type = $_POST['action_type'];

    try {
        $pdo->beginTransaction();

        if ($action_type == 'add') {
            // 1. Lấy ID cuối cùng
            $stmt_id = $pdo->query("SELECT id_tac_gia FROM tac_gia ORDER BY id_tac_gia DESC LIMIT 1 FOR UPDATE");
            $last_item = $stmt_id->fetch();
            $new_id_num = 1;
            if ($last_item) {
                // Lấy phần số từ ID (ví dụ TG001 -> 1)
                $new_id_num = (int) substr($last_item['id_tac_gia'], 2) + 1;
            }
            // Format: TG001 (TG + 3 chữ số)
            $id_tac_gia = 'TG' . str_pad($new_id_num, 3, '0', STR_PAD_LEFT); 

            // 2. Thêm mới
            $stmt = $pdo->prepare("INSERT INTO tac_gia (id_tac_gia, ten_tac_gia) VALUES (?, ?)");
            $stmt->execute([$id_tac_gia, $ten_tac_gia]);
            $success = "Thêm tác giả '$ten_tac_gia' (ID: $id_tac_gia) thành công!";

        } elseif ($action_type == 'edit') {
            $id_tac_gia = $_POST['id_tac_gia'];
            $stmt = $pdo->prepare("UPDATE tac_gia SET ten_tac_gia = ? WHERE id_tac_gia = ?");
            $stmt->execute([$ten_tac_gia, $id_tac_gia]);
            $success = "Cập nhật tác giả thành công!";
        }
        
        $pdo->commit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Đã xảy ra lỗi: " . $e->getMessage();
    }
}

// XỬ LÝ GET (Sửa hoặc Xóa)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id_tac_gia = $_GET['id'];

    if ($action == 'edit') {
        $edit_mode = true;
        $stmt = $pdo->prepare("SELECT * FROM tac_gia WHERE id_tac_gia = ?");
        $stmt->execute([$id_tac_gia]);
        $item_to_edit = $stmt->fetch();
    }

    if ($action == 'delete') {
        try {
            // Kiểm tra ràng buộc: Tác giả này có đang được gán cho sách nào không?
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM s_tg WHERE id_tac_gia = ?");
            $stmt_check->execute([$id_tac_gia]);
            if ($stmt_check->fetchColumn() > 0) {
                $error = "Không thể xóa! Vẫn còn sách được gán cho tác giả này.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM tac_gia WHERE id_tac_gia = ?");
                $stmt->execute([$id_tac_gia]);
                $success = "Xóa tác giả thành công!";
            }
        } catch (Exception $e) {
            $error = "Lỗi khi xóa: " . $e->getMessage();
        }
    }
}

// Lấy danh sách tất cả
$all_items = $pdo->query("SELECT * FROM tac_gia ORDER BY id_tac_gia")->fetchAll();

?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quản Lý Tác Giả</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tác Giả</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card <?php echo $edit_mode ? 'card-warning' : 'card-primary'; ?>">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $edit_mode ? 'Chỉnh Sửa Tác Giả' : 'Thêm Tác Giả Mới'; ?></h3>
                        </div>
                        <form action="authors.php" method="POST">
                            <div class="card-body">
                                <input type="hidden" name="action_type" value="<?php echo $edit_mode ? 'edit' : 'add'; ?>">
                                <?php if ($edit_mode): ?>
                                    <input type="hidden" name="id_tac_gia" value="<?php echo $item_to_edit['id_tac_gia']; ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="ten_tac_gia">Tên Tác Giả</label>
                                    <input type="text" class="form-control" name="ten_tac_gia" 
                                           value="<?php echo $edit_mode ? htmlspecialchars($item_to_edit['ten_tac_gia']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn <?php echo $edit_mode ? 'btn-warning' : 'btn-primary'; ?>">
                                    <?php echo $edit_mode ? 'Cập nhật' : 'Thêm Mới'; ?>
                                </button>
                                <?php if ($edit_mode): ?>
                                    <a href="authors.php" class="btn btn-secondary">Hủy</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Danh Sách Tác Giả</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">ID</th>
                                        <th>Tên Tác Giả</th>
                                        <th style="width: 120px;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['id_tac_gia']); ?></td>
                                        <td><?php echo htmlspecialchars($item['ten_tac_gia']); ?></td>
                                        <td>
                                            <a href="authors.php?action=edit&id=<?php echo $item['id_tac_gia']; ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="authors.php?action=delete&id=<?php echo $item['id_tac_gia']; ?>" class="btn btn-danger btn-sm"
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
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