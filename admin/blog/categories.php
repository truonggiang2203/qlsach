<?php
include '../includes/header.php';
include '../includes/db.php';

function create_slug($string) {
    $search = array('á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'í', 'ì', 'ỉ', 'ĩ', 'ị', 'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'đ', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Đ');
    $replace = array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'y', 'y', 'y', 'y', 'y', 'd', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'Y', 'Y', 'Y', 'Y', 'Y', 'D');
    $string = str_replace($search, $replace, $string);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

$error = '';
$success = '';
$edit_mode = false;
$cat_edit = null;

// XỬ LÝ POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $ten_danh_muc = $_POST['ten_danh_muc'];
    $slug = create_slug($ten_danh_muc); // Tự động tạo slug
    $mo_ta = $_POST['mo_ta'];

    try {
        if ($action == 'add') {
            // Tạo ID DMxxx
            $stmt_id = $pdo->query("SELECT id_danh_muc FROM danh_muc_bai_viet ORDER BY id_danh_muc DESC LIMIT 1");
            $last = $stmt_id->fetch();
            $num = 1;
            if ($last) $num = (int)substr($last['id_danh_muc'], 2) + 1;
            $id = 'DM' . str_pad($num, 3, '0', STR_PAD_LEFT);

            $stmt = $pdo->prepare("INSERT INTO danh_muc_bai_viet (id_danh_muc, ten_danh_muc, slug, mo_ta, trang_thai) VALUES (?, ?, ?, ?, 1)");
            $stmt->execute([$id, $ten_danh_muc, $slug, $mo_ta]);
            $_SESSION['success_message'] = "Thêm danh mục thành công!";
        } elseif ($action == 'edit') {
            $id = $_POST['id_danh_muc'];
            $stmt = $pdo->prepare("UPDATE danh_muc_bai_viet SET ten_danh_muc=?, slug=?, mo_ta=? WHERE id_danh_muc=?");
            $stmt->execute([$ten_danh_muc, $slug, $mo_ta, $id]);
            $_SESSION['success_message'] = "Cập nhật thành công!";
        }
        header('Location: categories.php'); exit;
    } catch (Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// XỬ LÝ GET (Sửa/Xóa)
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $edit_mode = true;
        $cat_edit = $pdo->prepare("SELECT * FROM danh_muc_bai_viet WHERE id_danh_muc = ?");
        $cat_edit->execute([$_GET['id']]);
        $cat_edit = $cat_edit->fetch();
    }
    if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        // Kiểm tra bài viết con
        $check = $pdo->prepare("SELECT COUNT(*) FROM bai_viet WHERE id_danh_muc = ?");
        $check->execute([$_GET['id']]);
        if ($check->fetchColumn() > 0) {
            $_SESSION['error_message'] = "Không thể xóa! Danh mục này đang chứa bài viết.";
        } else {
            $pdo->prepare("DELETE FROM danh_muc_bai_viet WHERE id_danh_muc = ?")->execute([$_GET['id']]);
            $_SESSION['success_message'] = "Đã xóa danh mục.";
        }
        header('Location: categories.php'); exit;
    }
}

$cats = $pdo->query("SELECT * FROM danh_muc_bai_viet ORDER BY id_danh_muc DESC")->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Quản Lý Danh Mục Bài Viết</h1></section>
    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success_message'])) { echo "<div class='alert alert-success'>{$_SESSION['success_message']}</div>"; unset($_SESSION['success_message']); } ?>
            <?php if (isset($_SESSION['error_message'])) { echo "<div class='alert alert-danger'>{$_SESSION['error_message']}</div>"; unset($_SESSION['error_message']); } ?>
            <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card card-<?php echo $edit_mode?'warning':'primary'; ?>">
                        <div class="card-header"><h3 class="card-title"><?php echo $edit_mode?'Sửa Danh Mục':'Thêm Danh Mục'; ?></h3></div>
                        <form method="POST">
                            <div class="card-body">
                                <input type="hidden" name="action" value="<?php echo $edit_mode?'edit':'add'; ?>">
                                <?php if($edit_mode): ?><input type="hidden" name="id_danh_muc" value="<?php echo $cat_edit['id_danh_muc']; ?>"><?php endif; ?>
                                <div class="form-group">
                                    <label>Tên Danh Mục</label>
                                    <input type="text" class="form-control" name="ten_danh_muc" required value="<?php echo $edit_mode?$cat_edit['ten_danh_muc']:''; ?>">
                                </div>
                                <div class="form-group">
                                    <label>Mô Tả</label>
                                    <textarea class="form-control" name="mo_ta" rows="3"><?php echo $edit_mode?$cat_edit['mo_ta']:''; ?></textarea>
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-<?php echo $edit_mode?'warning':'primary'; ?>">Lưu</button>
                                <?php if($edit_mode): ?><a href="categories.php" class="btn btn-default float-right">Hủy</a><?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead><tr><th>ID</th><th>Tên</th><th>Slug</th><th>Thao Tác</th></tr></thead>
                                <tbody>
                                    <?php foreach ($cats as $c): ?>
                                    <tr>
                                        <td><?php echo $c['id_danh_muc']; ?></td>
                                        <td><?php echo htmlspecialchars($c['ten_danh_muc']); ?></td>
                                        <td><?php echo $c['slug']; ?></td>
                                        <td>
                                            <a href="categories.php?action=edit&id=<?php echo $c['id_danh_muc']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="categories.php?action=delete&id=<?php echo $c['id_danh_muc']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa danh mục này?')"><i class="fas fa-trash"></i></a>
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