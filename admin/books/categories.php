<?php

include '../includes/header.php';
include '../includes/db.php';

$error = '';
$success = '';
$edit_mode = false;
$category_to_edit = null;


if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}


// XỬ LÝ POST (Thêm mới hoặc Cập nhật)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_loai = $_POST['ten_loai'];
    $action_type = $_POST['action_type'];

    try {
        if ($action_type == 'add') {
            // 1. Lấy ID cuối cùng
            $stmt_id = $pdo->query("SELECT id_loai FROM loai_sach ORDER BY id_loai DESC LIMIT 1 FOR UPDATE");
            $last_item = $stmt_id->fetch();
            $new_id_num = 1;
            if ($last_item) {
                $new_id_num = (int) substr($last_item['id_loai'], 2) + 1;
            }
            $id_loai = 'LS' . str_pad($new_id_num, 3, '0', STR_PAD_LEFT); // Format: LS001

            // 2. Thêm mới
            $stmt = $pdo->prepare("INSERT INTO loai_sach (id_loai, ten_loai) VALUES (?, ?)");
            $stmt->execute([$id_loai, $ten_loai]);
            $_SESSION['success_message'] = "Thêm loại sách '$ten_loai' thành công!";

        } elseif ($action_type == 'edit') {
            $id_loai = $_POST['id_loai'];
            $stmt = $pdo->prepare("UPDATE loai_sach SET ten_loai = ? WHERE id_loai = ?");
            $stmt->execute([$ten_loai, $id_loai]);
            $_SESSION['success_message'] = "Cập nhật loại sách thành công!";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Đã xảy ra lỗi: " . $e->getMessage();
    }
    header('Location: categories.php'); // Chuyển hướng để refresh
    exit;
}

// XỬ LÝ GET (Sửa hoặc Xóa)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id_loai = $_GET['id'];

    if ($action == 'edit') {
        $edit_mode = true;
        $stmt = $pdo->prepare("SELECT * FROM loai_sach WHERE id_loai = ?");
        $stmt->execute([$id_loai]);
        $category_to_edit = $stmt->fetch();
    }

    if ($action == 'delete') {
        try {
            // Kiểm tra xem có "Thể Loại" (genres) nào đang dùng "Loại Sách" (category) này không
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM the_loai WHERE id_loai = ?");
            $stmt_check->execute([$id_loai]);
            
            if ($stmt_check->fetchColumn() > 0) {
                // Nếu có, báo lỗi
                $_SESSION['error_message'] = "Không thể xóa! Vẫn còn Thể Loại (ví dụ: Tiểu thuyết) đang thuộc về Loại Sách này.";
            } else {
                // Nếu không, tiến hành xóa
                $stmt = $pdo->prepare("DELETE FROM loai_sach WHERE id_loai = ?");
                $stmt->execute([$id_loai]);
                $_SESSION['success_message'] = "Xóa loại sách thành công!";
            }
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Lỗi khi xóa: " . $e->getMessage();
        }
        header('Location: categories.php'); // Chuyển hướng để refresh
        exit;
    }
}

// Lấy danh sách tất cả loại sách
$all_categories = $pdo->query("SELECT * FROM loai_sach ORDER BY id_loai")->fetchAll();

?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quản Lý Loại Sách</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Quản Lý Sách</a></li>
                        <li class="breadcrumb-item active">Loại Sách</li>
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
                            <h3 class="card-title"><?php echo $edit_mode ? 'Chỉnh Sửa Loại Sách' : 'Thêm Loại Sách Mới'; ?></h3>
                        </div>
                        <form action="categories.php" method="POST">
                            <div class="card-body">
                                <input type="hidden" name="action_type" value="<?php echo $edit_mode ? 'edit' : 'add'; ?>">
                                <?php if ($edit_mode): ?>
                                    <input type="hidden" name="id_loai" value="<?php echo $category_to_edit['id_loai']; ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="ten_loai">Tên Loại Sách</label>
                                    <input type="text" class="form-control" name="ten_loai" 
                                           value="<?php echo $edit_mode ? htmlspecialchars($category_to_edit['ten_loai']) : ''; ?>" required>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn <?php echo $edit_mode ? 'btn-warning' : 'btn-primary'; ?>">
                                    <?php echo $edit_mode ? 'Cập nhật' : 'Thêm Mới'; ?>
                                </button>
                                <?php if ($edit_mode): ?>
                                    <a href="categories.php" class="btn btn-secondary">Hủy</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Danh Sách Loại Sách</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 100px;">ID</th>
                                        <th>Tên Loại Sách</th>
                                        <th style="width: 120px;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_categories as $cat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($cat['id_loai']); ?></td>
                                        <td><?php echo htmlspecialchars($cat['ten_loai']); ?></td>
                                        <td>
                                            <a href="categories.php?action=edit&id=<?php echo $cat['id_loai']; ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="categories.php?action=delete&id=<?php echo $cat['id_loai']; ?>" class="btn btn-danger btn-sm"
                                               onclick="return confirm('Bạn có chắc chắn muốn xóa? Thao tác này sẽ thất bại nếu vẫn còn Thể Loại con.');">
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