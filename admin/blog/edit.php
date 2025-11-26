<?php
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_GET['id'])) { header('Location: index.php'); exit; }
$id_bai_viet = $_GET['id'];

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

// XỬ LÝ POST: CẬP NHẬT
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieu_de = $_POST['tieu_de'];
    $slug = create_slug($tieu_de);
    $id_danh_muc = $_POST['id_danh_muc'];
    $tom_tat = $_POST['tom_tat'];
    $noi_dung = $_POST['noi_dung'];
    $trang_thai = $_POST['trang_thai'];
    $noi_bat = isset($_POST['noi_bat']) ? 1 : 0;
    
    // Lấy tên ảnh cũ
    $stmt_old = $pdo->prepare("SELECT anh_dai_dien FROM bai_viet WHERE id_bai_viet = ?");
    $stmt_old->execute([$id_bai_viet]);
    $anh_dai_dien = $stmt_old->fetchColumn();

    $pdo->beginTransaction();
    try {
        // 1. Xử lý Upload Ảnh Mới (Nếu có)
        if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] == 0) {
            $path = '../../public/uploads/posts/';
            if (!file_exists($path)) mkdir($path, 0777, true);
            $ext = pathinfo($_FILES['anh_dai_dien']['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . uniqid() . '.' . $ext;
            
            if (move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $path . $filename)) {
                // Xóa ảnh cũ nếu có
                if ($anh_dai_dien && file_exists($path . $anh_dai_dien)) {
                    unlink($path . $anh_dai_dien);
                }
                $anh_dai_dien = $filename; // Cập nhật tên mới
            }
        }

        // 2. Update Bai Viet
        $stmt = $pdo->prepare("UPDATE bai_viet SET id_danh_muc=?, tieu_de=?, slug=?, tom_tat=?, noi_dung=?, anh_dai_dien=?, trang_thai=?, noi_bat=? WHERE id_bai_viet=?");
        $stmt->execute([$id_danh_muc, $tieu_de, $slug, $tom_tat, $noi_dung, $anh_dai_dien, $trang_thai, $noi_bat, $id_bai_viet]);

        // 3. Update Tags (Xóa hết cũ -> Thêm mới)
        $pdo->prepare("DELETE FROM bai_viet_tag WHERE id_bai_viet=?")->execute([$id_bai_viet]);
        if (!empty($_POST['tags'])) {
            $stmt_tag = $pdo->prepare("INSERT INTO bai_viet_tag (id_bai_viet, id_tag) VALUES (?, ?)");
            foreach ($_POST['tags'] as $tag_id) {
                $stmt_tag->execute([$id_bai_viet, $tag_id]);
            }
        }

        $pdo->commit();
        echo "<script>alert('Cập nhật thành công!'); window.location='edit.php?id=$id_bai_viet';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}

// --- LẤY DỮ LIỆU HIỂN THỊ ---
// Bài viết
$post = $pdo->prepare("SELECT * FROM bai_viet WHERE id_bai_viet = ?");
$post->execute([$id_bai_viet]);
$post = $post->fetch();

// Danh mục & Tags (Dropdown)
$categories = $pdo->query("SELECT * FROM danh_muc_bai_viet WHERE trang_thai=1")->fetchAll();
$tags = $pdo->query("SELECT * FROM tag")->fetchAll();

// Tags đã chọn của bài viết này
$current_tags = $pdo->prepare("SELECT id_tag FROM bai_viet_tag WHERE id_bai_viet = ?");
$current_tags->execute([$id_bai_viet]);
$current_tags = $current_tags->fetchAll(PDO::FETCH_COLUMN);
?>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<?php include '../includes/sidebar.php'; ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Sửa Bài Viết</h1></div>
                <div class="col-sm-6 text-right"><a href="index.php" class="btn btn-secondary">Quay lại</a></div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-warning">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Tiêu đề bài viết</label>
                                    <input type="text" class="form-control" name="tieu_de" required value="<?php echo htmlspecialchars($post['tieu_de']); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Tóm tắt (Ngắn)</label>
                                    <textarea class="form-control" name="tom_tat" rows="3" required><?php echo htmlspecialchars($post['tom_tat']); ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Nội dung chi tiết</label>
                                    <textarea id="summernote" name="noi_dung" required><?php echo $post['noi_dung']; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-info">
                            <div class="card-header"><h3 class="card-title">Thông tin khác</h3></div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Trạng thái</label>
                                    <select class="form-control" name="trang_thai">
                                        <option value="published" <?php if($post['trang_thai']=='published') echo 'selected'; ?>>Công khai (Published)</option>
                                        <option value="draft" <?php if($post['trang_thai']=='draft') echo 'selected'; ?>>Bản nháp (Draft)</option>
                                        <option value="archived" <?php if($post['trang_thai']=='archived') echo 'selected'; ?>>Lưu trữ (Archived)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="noi_bat" name="noi_bat" value="1" <?php if($post['noi_bat']) echo 'checked'; ?>>
                                        <label for="noi_bat" class="custom-control-label">Bài viết nổi bật</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Danh mục</label>
                                    <select class="form-control" name="id_danh_muc" required>
                                        <?php foreach ($categories as $c): ?>
                                            <option value="<?php echo $c['id_danh_muc']; ?>" <?php if($c['id_danh_muc'] == $post['id_danh_muc']) echo 'selected'; ?>>
                                                <?php echo $c['ten_danh_muc']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Thẻ (Tags)</label>
                                    <select class="form-control select2" name="tags[]" multiple="multiple">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?php echo $t['id_tag']; ?>" <?php if(in_array($t['id_tag'], $current_tags)) echo 'selected'; ?>>
                                                <?php echo $t['ten_tag']; ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Ảnh đại diện</label><br>
                                    <?php if($post['anh_dai_dien']): ?>
                                        <img src="../../public/uploads/posts/<?php echo $post['anh_dai_dien']; ?>" width="100%" class="mb-2 rounded">
                                    <?php endif; ?>
                                    <input type="file" class="form-control-file" name="anh_dai_dien" accept="image/*">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-warning btn-block">Cập Nhật</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#summernote').summernote({
            height: 300,
            placeholder: 'Nhập nội dung bài viết tại đây...'
        });
        $('.select2').select2({
            theme: 'bootstrap4',
            placeholder: "Chọn thẻ"
        });
    });
</script>