<?php
include '../includes/header.php';
include '../includes/db.php';

$error = '';
$success = '';
$edit_mode = false;
$genre_to_edit = null;

// Lấy thông báo flash
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
if (isset($_SESSION['error_message'])) {
    $error = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

// Lấy danh sách Loại Sách (cho dropdown)
$all_categories = $pdo->query("SELECT * FROM loai_sach ORDER BY ten_loai")->fetchAll();

// XỬ LÝ POST (Thêm mới hoặc Cập nhật)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_the_loai = $_POST['ten_the_loai'];
    $id_loai = $_POST['id_loai'];
    $action_type = $_POST['action_type'];

    try {
        if ($action_type == 'add') {
            // Tạo ID tự động TLxxx
            $stmt_id = $pdo->query("SELECT id_the_loai FROM the_loai ORDER BY id_the_loai DESC LIMIT 1 FOR UPDATE");
            $last_item = $stmt_id->fetch();
            $new_id_num = 1;
            if ($last_item) {
                $new_id_num = (int) substr($last_item['id_the_loai'], 2) + 1;
            }
            $id_the_loai = 'TL' . str_pad($new_id_num, 3, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO the_loai (id_the_loai, id_loai, ten_the_loai) VALUES (?, ?, ?)");
            $stmt->execute([$id_the_loai, $id_loai, $ten_the_loai]);
            $_SESSION['success_message'] = "Thêm thể loại '$ten_the_loai' thành công!";

        } elseif ($action_type == 'edit') {
            $id_the_loai = $_POST['id_the_loai'];
            $stmt = $pdo->prepare("UPDATE the_loai SET ten_the_loai = ?, id_loai = ? WHERE id_the_loai = ?");
            $stmt->execute([$ten_the_loai, $id_loai, $id_the_loai]);
            $_SESSION['success_message'] = "Cập nhật thể loại thành công!";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Đã xảy ra lỗi: " . $e->getMessage();
    }
    header('Location: genres.php');
    exit;
}

// XỬ LÝ GET (Sửa hoặc Xóa)
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id_the_loai = $_GET['id'];

    if ($action == 'edit') {
        $edit_mode = true;
        $stmt = $pdo->prepare("SELECT * FROM the_loai WHERE id_the_loai = ?");
        $stmt->execute([$id_the_loai]);
        $genre_to_edit = $stmt->fetch();
    }

    if ($action == 'delete') {
        try {
            // --- [QUAN TRỌNG] SỬA LOGIC XÓA CHO DB MỚI ---
            // Kiểm tra xem có sách nào dùng thể loại này không bằng cách check bảng trung gian `sach_theloai`
            // Thay vì check bảng `sach` như cũ
            $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM sach_theloai WHERE id_the_loai = ?");
            $stmt_check->execute([$id_the_loai]);
            
            if ($stmt_check->fetchColumn() > 0) {
                $_SESSION['error_message'] = "Không thể xóa! Vẫn còn sách đang thuộc về Thể Loại này.";
            } else {
                $stmt = $pdo->prepare("DELETE FROM the_loai WHERE id_the_loai = ?");
                $stmt->execute([$id_the_loai]);
                $_SESSION['success_message'] = "Xóa thể loại thành công!";
            }
            // --- KẾT THÚC SỬA ---
            
        } catch (Exception $e) {
            $_SESSION['error_message'] = "Lỗi khi xóa: " . $e->getMessage();
        }
        header('Location: genres.php');
        exit;
    }
}

// Lấy danh sách tất cả thể loại
$all_genres = $pdo->query("
    SELECT tl.id_the_loai, tl.ten_the_loai, ls.ten_loai 
    FROM the_loai tl
    JOIN loai_sach ls ON tl.id_loai = ls.id_loai
    ORDER BY tl.id_the_loai DESC
")->fetchAll();

?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Quản Lý Thể Loại</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Sách</a></li>
                        <li class="breadcrumb-item active">Thể Loại</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card <?php echo $edit_mode ? 'card-warning' : 'card-primary'; ?>">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $edit_mode ? 'Sửa Thể Loại' : 'Thêm Thể Loại Mới'; ?></h3>
                        </div>
                        <form action="genres.php" method="POST">
                            <div class="card-body">
                                <input type="hidden" name="action_type" value="<?php echo $edit_mode ? 'edit' : 'add'; ?>">
                                <?php if ($edit_mode): ?>
                                    <input type="hidden" name="id_the_loai" value="<?php echo $genre_to_edit['id_the_loai']; ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label>Tên Thể Loại</label>
                                    <input type="text" class="form-control" name="ten_the_loai" 
                                           value="<?php echo $edit_mode ? htmlspecialchars($genre_to_edit['ten_the_loai']) : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label>Thuộc Loại Sách</label>
                                    <select class="form-control" name="id_loai" required>
                                        <option value="">-- Chọn loại sách --</option>
                                        <?php foreach ($all_categories as $cat): ?>
                                            <option value="<?php echo $cat['id_loai']; ?>" 
                                                <?php echo ($edit_mode && $genre_to_edit['id_loai'] == $cat['id_loai']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($cat['ten_loai']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn <?php echo $edit_mode ? 'btn-warning' : 'btn-primary'; ?>">
                                    <?php echo $edit_mode ? 'Cập nhật' : 'Thêm Mới'; ?>
                                </button>
                                <?php if ($edit_mode): ?>
                                    <a href="genres.php" class="btn btn-secondary float-right">Hủy</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Danh Sách Thể Loại</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 500px;">
                            <table class="table table-head-fixed text-nowrap table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Tên Thể Loại</th>
                                        <th>Thuộc Loại Sách</th>
                                        <th>Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_genres as $genre): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($genre['id_the_loai']); ?></td>
                                        <td><?php echo htmlspecialchars($genre['ten_the_loai']); ?></td>
                                        <td><?php echo htmlspecialchars($genre['ten_loai']); ?></td>
                                        <td>
                                            <a href="genres.php?action=edit&id=<?php echo $genre['id_the_loai']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="genres.php?action=delete&id=<?php echo $genre['id_the_loai']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa?');"><i class="fas fa-trash"></i></a>
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