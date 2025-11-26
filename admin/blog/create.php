<?php
include '../includes/header.php';
include '../includes/db.php';

// Hàm tạo slug (Copy lại hàm ở trên hoặc để vào file helper chung)
function create_slug($string) {
    $search = array('á', 'à', 'ả', 'ã', 'ạ', 'ă', 'ắ', 'ằ', 'ẳ', 'ẵ', 'ặ', 'â', 'ấ', 'ầ', 'ẩ', 'ẫ', 'ậ', 'é', 'è', 'ẻ', 'ẽ', 'ẹ', 'ê', 'ế', 'ề', 'ể', 'ễ', 'ệ', 'í', 'ì', 'ỉ', 'ĩ', 'ị', 'ó', 'ò', 'ỏ', 'õ', 'ọ', 'ô', 'ố', 'ồ', 'ổ', 'ỗ', 'ộ', 'ơ', 'ớ', 'ờ', 'ở', 'ỡ', 'ợ', 'ú', 'ù', 'ủ', 'ũ', 'ụ', 'ư', 'ứ', 'ừ', 'ử', 'ữ', 'ự', 'ý', 'ỳ', 'ỷ', 'ỹ', 'ỵ', 'đ', 'Á', 'À', 'Ả', 'Ã', 'Ạ', 'Ă', 'Ắ', 'Ằ', 'Ẳ', 'Ẵ', 'Ặ', 'Â', 'Ấ', 'Ầ', 'Ẩ', 'Ẫ', 'Ậ', 'É', 'È', 'Ẻ', 'Ẽ', 'Ẹ', 'Ê', 'Ế', 'Ề', 'Ể', 'Ễ', 'Ệ', 'Í', 'Ì', 'Ỉ', 'Ĩ', 'Ị', 'Ó', 'Ò', 'Ỏ', 'Õ', 'Ọ', 'Ô', 'Ố', 'Ồ', 'Ổ', 'Ỗ', 'Ộ', 'Ơ', 'Ớ', 'Ờ', 'Ở', 'Ỡ', 'Ợ', 'Ú', 'Ù', 'Ủ', 'Ũ', 'Ụ', 'Ư', 'Ứ', 'Ừ', 'Ử', 'Ữ', 'Ự', 'Ý', 'Ỳ', 'Ỷ', 'Ỹ', 'Ỵ', 'Đ');
    $replace = array('a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'a', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'e', 'i', 'i', 'i', 'i', 'i', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'o', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'u', 'y', 'y', 'y', 'y', 'y', 'd', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'A', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'E', 'I', 'I', 'I', 'I', 'I', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'O', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'U', 'Y', 'Y', 'Y', 'Y', 'Y', 'D');
    $string = str_replace($search, $replace, $string);
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

// Lấy dữ liệu cần thiết
$categories = $pdo->query("SELECT * FROM danh_muc_bai_viet WHERE trang_thai=1")->fetchAll();
$tags = $pdo->query("SELECT * FROM tag")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tieu_de = $_POST['tieu_de'];
    $slug = create_slug($tieu_de);
    $id_danh_muc = $_POST['id_danh_muc'];
    $tom_tat = $_POST['tom_tat'];
    $noi_dung = $_POST['noi_dung'];
    $trang_thai = $_POST['trang_thai'];
    $noi_bat = isset($_POST['noi_bat']) ? 1 : 0;
    
    // Giả sử ID tài khoản admin đang đăng nhập được lưu trong session
    $id_tk = $_SESSION['id_tk'] ?? 'AD001'; // Fallback nếu chưa login

    $pdo->beginTransaction();
    try {
        // 1. Upload ảnh (Lưu vào public/uploads/posts/)
        $anh_dai_dien = null;
        if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] == 0) {
            $path = '../../public/uploads/posts/';
            if (!file_exists($path)) mkdir($path, 0777, true);
            $ext = pathinfo($_FILES['anh_dai_dien']['name'], PATHINFO_EXTENSION);
            $filename = time() . '_' . uniqid() . '.' . $ext; // Tên file ngẫu nhiên để tránh trùng
            move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $path . $filename);
            $anh_dai_dien = $filename;
        }

        // 2. Insert Bài Viết
        $stmt = $pdo->prepare("INSERT INTO bai_viet (id_danh_muc, id_tk, tieu_de, slug, tom_tat, noi_dung, anh_dai_dien, trang_thai, noi_bat) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$id_danh_muc, $id_tk, $tieu_de, $slug, $tom_tat, $noi_dung, $anh_dai_dien, $trang_thai, $noi_bat]);
        $id_bai_viet = $pdo->lastInsertId();

        // 3. Insert Tags (Nếu có)
        if (!empty($_POST['tags'])) {
            $stmt_tag = $pdo->prepare("INSERT INTO bai_viet_tag (id_bai_viet, id_tag) VALUES (?, ?)");
            foreach ($_POST['tags'] as $tag_id) {
                $stmt_tag->execute([$id_bai_viet, $tag_id]);
            }
        }

        $pdo->commit();
        echo "<script>alert('Thêm bài viết thành công!'); window.location='index.php';</script>";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}
?>

<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<?php include '../includes/sidebar.php'; ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Viết Bài Mới</h1></section>
    <section class="content">
        <div class="container-fluid">
            <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-8">
                        <div class="card card-primary">
                            <div class="card-body">
                                <div class="form-group">
                                    <label>Tiêu đề bài viết</label>
                                    <input type="text" class="form-control" name="tieu_de" required>
                                </div>
                                <div class="form-group">
                                    <label>Tóm tắt (Ngắn)</label>
                                    <textarea class="form-control" name="tom_tat" rows="3" required></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Nội dung chi tiết</label>
                                    <textarea id="summernote" name="noi_dung" required></textarea>
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
                                        <option value="published">Công khai (Published)</option>
                                        <option value="draft">Bản nháp (Draft)</option>
                                        <option value="archived">Lưu trữ (Archived)</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" id="noi_bat" name="noi_bat" value="1">
                                        <label for="noi_bat" class="custom-control-label">Bài viết nổi bật</label>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Danh mục</label>
                                    <select class="form-control" name="id_danh_muc" required>
                                        <?php foreach ($categories as $c): ?>
                                            <option value="<?php echo $c['id_danh_muc']; ?>"><?php echo $c['ten_danh_muc']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Thẻ (Tags)</label>
                                    <select class="form-control select2" name="tags[]" multiple="multiple">
                                        <?php foreach ($tags as $t): ?>
                                            <option value="<?php echo $t['id_tag']; ?>"><?php echo $t['ten_tag']; ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Ảnh đại diện</label>
                                    <input type="file" class="form-control-file" name="anh_dai_dien" accept="image/*">
                                </div>
                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn btn-primary btn-block">Đăng Bài</button>
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