<?php
include '../includes/header.php';
include '../includes/db.php';

// Hàm tạo slug
function create_slug($string) {
    $search = array('á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'í', 'ì', 'ỉ', 'ĩ', 'ị', 'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'đ', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Đ');
    $replace = array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'y', 'y', 'y', 'y', 'y', 'd', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'Y', 'Y', 'Y', 'Y', 'Y', 'D');
    $string = str_replace($search, $replace, $string);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

$error = '';
$success = '';
$edit_mode = false;
$tag_edit = null;

// XỬ LÝ POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action = $_POST['action'];
    $ten_tag = $_POST['ten_tag'];
    $slug = create_slug($ten_tag);

    try {
        if ($action == 'add') {
            $stmt = $pdo->prepare("INSERT INTO tag (ten_tag, slug) VALUES (?, ?)");
            $stmt->execute([$ten_tag, $slug]);
            $_SESSION['success_message'] = "Thêm thẻ thành công!";
        } elseif ($action == 'edit') {
            $id = $_POST['id_tag'];
            $stmt = $pdo->prepare("UPDATE tag SET ten_tag=?, slug=? WHERE id_tag=?");
            $stmt->execute([$ten_tag, $slug, $id]);
            $_SESSION['success_message'] = "Cập nhật thẻ thành công!";
        }
        header('Location: tags.php'); exit;
    } catch (Exception $e) {
        $error = "Lỗi: " . $e->getMessage();
    }
}

// XỬ LÝ GET
if (isset($_GET['action'])) {
    if ($_GET['action'] == 'edit' && isset($_GET['id'])) {
        $edit_mode = true;
        $tag_edit = $pdo->prepare("SELECT * FROM tag WHERE id_tag = ?");
        $tag_edit->execute([$_GET['id']]);
        $tag_edit = $tag_edit->fetch();
    }
    if ($_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        // Kiểm tra tag này có đang được dùng không
        $check = $pdo->prepare("SELECT COUNT(*) FROM bai_viet_tag WHERE id_tag = ?");
        $check->execute([$id]);
        if ($check->fetchColumn() > 0) {
            $_SESSION['error_message'] = "Cảnh báo: Thẻ này đang được dùng trong bài viết. Xóa thẻ sẽ xóa luôn liên kết trong bài viết đó (Cascade).";
            // Nếu muốn chặn xóa thì dùng dòng trên và redirect.
            // Ở đây mình cho phép xóa (Cascade) nhưng hiện thông báo thành công sau đó.
            $pdo->prepare("DELETE FROM tag WHERE id_tag = ?")->execute([$id]);
            $_SESSION['success_message'] = "Đã xóa thẻ.";
        } else {
            $pdo->prepare("DELETE FROM tag WHERE id_tag = ?")->execute([$id]);
            $_SESSION['success_message'] = "Đã xóa thẻ.";
        }
        header('Location: tags.php'); exit;
    }
}

$tags = $pdo->query("SELECT * FROM tag ORDER BY id_tag DESC")->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Quản Lý Thẻ (Tags)</h1></section>
    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success_message'])) { echo "<div class='alert alert-success'>{$_SESSION['success_message']}</div>"; unset($_SESSION['success_message']); } ?>
            <?php if (isset($_SESSION['error_message'])) { echo "<div class='alert alert-warning'>{$_SESSION['error_message']}</div>"; unset($_SESSION['error_message']); } ?>
            <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card card-<?php echo $edit_mode?'warning':'primary'; ?>">
                        <div class="card-header"><h3 class="card-title"><?php echo $edit_mode?'Sửa Thẻ':'Thêm Thẻ Mới'; ?></h3></div>
                        <form method="POST">
                            <div class="card-body">
                                <input type="hidden" name="action" value="<?php echo $edit_mode?'edit':'add'; ?>">
                                <?php if($edit_mode): ?><input type="hidden" name="id_tag" value="<?php echo $tag_edit['id_tag']; ?>"><?php endif; ?>
                                <div class="form-group">
                                    <label>Tên Thẻ</label>
                                    <input type="text" class="form-control" name="ten_tag" required value="<?php echo $edit_mode?$tag_edit['ten_tag']:''; ?>" placeholder="Ví dụ: Văn học, Kinh dị...">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-<?php echo $edit_mode?'warning':'primary'; ?>">Lưu</button>
                                <?php if($edit_mode): ?><a href="tags.php" class="btn btn-default float-right">Hủy</a><?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead><tr><th>ID</th><th>Tên Thẻ</th><th>Slug</th><th>Thao Tác</th></tr></thead>
                                <tbody>
                                    <?php foreach ($tags as $t): ?>
                                    <tr>
                                        <td><?php echo $t['id_tag']; ?></td>
                                        <td><span class="badge badge-info"><?php echo htmlspecialchars($t['ten_tag']); ?></span></td>
                                        <td><?php echo $t['slug']; ?></td>
                                        <td>
                                            <a href="tags.php?action=edit&id=<?php echo $t['id_tag']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="tags.php?action=delete&id=<?php echo $t['id_tag']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa thẻ này?')"><i class="fas fa-trash"></i></a>
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