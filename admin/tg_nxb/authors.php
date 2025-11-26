<?php
include '../includes/header.php';
include '../includes/db.php';

$error = '';
$success = '';
$action = $_GET['action'] ?? 'index'; 
$id_edit = $_GET['id'] ?? '';

// --- XỬ LÝ POST: THÊM HOẶC SỬA ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action_post = $_POST['action_post']; 
    $ten_tac_gia = $_POST['ten_tac_gia'];
    $id_tac_gia = $_POST['id_tac_gia'] ?? ''; 
    
    // Dữ liệu chi tiết
    $tieu_su = $_POST['tieu_su'];
    $ngay_sinh = !empty($_POST['ngay_sinh']) ? $_POST['ngay_sinh'] : null;
    $ngay_mat = !empty($_POST['ngay_mat']) ? $_POST['ngay_mat'] : null;
    $quoc_tich = $_POST['quoc_tich'];
    $website = $_POST['website'];
    $facebook = $_POST['facebook'];
    $twitter = $_POST['twitter'];
    $instagram = $_POST['instagram'];
    $giai_thuong = $_POST['giai_thuong'];
    $tac_pham_noi_bat = $_POST['tac_pham_noi_bat'];

    try {
        $pdo->beginTransaction();

        // 1. Xử lý Bảng Chính (tac_gia)
        if ($action_post == 'add') {
            // Tạo ID TGxxx
            $stmt_id = $pdo->query("SELECT id_tac_gia FROM tac_gia ORDER BY id_tac_gia DESC LIMIT 1 FOR UPDATE");
            $last = $stmt_id->fetch();
            $num = 1;
            if ($last) { $num = (int) substr($last['id_tac_gia'], 2) + 1; }
            $id_tac_gia = 'TG' . str_pad($num, 3, '0', STR_PAD_LEFT);

            $pdo->prepare("INSERT INTO tac_gia (id_tac_gia, ten_tac_gia) VALUES (?, ?)")->execute([$id_tac_gia, $ten_tac_gia]);
            $msg = "Thêm tác giả thành công!";
        } else {
            // Kiểm tra ID tồn tại
            $check = $pdo->prepare("SELECT id_tac_gia FROM tac_gia WHERE id_tac_gia = ?");
            $check->execute([$id_tac_gia]);
            if (!$check->fetch()) {
                throw new Exception("Lỗi: Không tìm thấy ID tác giả ($id_tac_gia) để cập nhật.");
            }

            $pdo->prepare("UPDATE tac_gia SET ten_tac_gia = ? WHERE id_tac_gia = ?")->execute([$ten_tac_gia, $id_tac_gia]);
            $msg = "Cập nhật tác giả thành công!";
        }

        // 2. Xử lý Upload Ảnh
        $new_img = null;
        if (isset($_FILES['anh_dai_dien']) && $_FILES['anh_dai_dien']['error'] == 0) {
            $upload_dir = '../../public/uploads/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            
            $ext = strtolower(pathinfo($_FILES['anh_dai_dien']['name'], PATHINFO_EXTENSION));
            if(in_array($ext, ['jpg','jpeg','png','webp'])) {
                $filename = $id_tac_gia . '_avatar.' . $ext; 
                move_uploaded_file($_FILES['anh_dai_dien']['tmp_name'], $upload_dir . $filename);
                $new_img = $filename;
            }
        }

        // 3. Xử lý Bảng Phụ (thong_tin_tac_gia) - UPSERT
        $sql_info = "
            INSERT INTO thong_tin_tac_gia (
                id_tac_gia, tieu_su, ngay_sinh, ngay_mat, quoc_tich, 
                website, facebook, twitter, instagram,
                giai_thuong, tac_pham_noi_bat
                " . ($new_img ? ", anh_dai_dien" : "") . "
            ) VALUES (
                ?, ?, ?, ?, ?, 
                ?, ?, ?, ?, 
                ?, ?
                " . ($new_img ? ", ?" : "") . "
            )
            ON DUPLICATE KEY UPDATE
                tieu_su = VALUES(tieu_su),
                ngay_sinh = VALUES(ngay_sinh),
                ngay_mat = VALUES(ngay_mat),
                quoc_tich = VALUES(quoc_tich),
                website = VALUES(website),
                facebook = VALUES(facebook),
                twitter = VALUES(twitter),
                instagram = VALUES(instagram),
                giai_thuong = VALUES(giai_thuong),
                tac_pham_noi_bat = VALUES(tac_pham_noi_bat)
                " . ($new_img ? ", anh_dai_dien = VALUES(anh_dai_dien)" : "") . "
        ";

        $params = [
            $id_tac_gia, $tieu_su, $ngay_sinh, $ngay_mat, $quoc_tich, 
            $website, $facebook, $twitter, $instagram, 
            $giai_thuong, $tac_pham_noi_bat
        ];
        if ($new_img) $params[] = $new_img;

        $pdo->prepare($sql_info)->execute($params);

        $pdo->commit();
        $_SESSION['success_message'] = $msg;
        
        if ($action_post == 'add') {
            header("Location: authors.php");
        } else {
            header("Location: authors.php?action=edit&id=$id_tac_gia");
        }
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}

// --- XỬ LÝ GET: XÓA ---
if ($action == 'delete' && $id_edit) {
    try {
        $check = $pdo->prepare("SELECT COUNT(*) FROM s_tg WHERE id_tac_gia = ?");
        $check->execute([$id_edit]);
        if ($check->fetchColumn() > 0) {
            $_SESSION['error_message'] = "Không thể xóa! Tác giả này đang có sách.";
        } else {
            $pdo->prepare("DELETE FROM tac_gia WHERE id_tac_gia = ?")->execute([$id_edit]);
            array_map('unlink', glob("../../public/uploads/{$id_edit}_avatar.*"));
            $_SESSION['success_message'] = "Đã xóa tác giả.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Lỗi: " . $e->getMessage();
    }
    header('Location: authors.php'); exit;
}

// --- LẤY DỮ LIỆU HIỂN THỊ ---
$data = [];
if ($action == 'edit') {
    $stmt = $pdo->prepare("
        SELECT t.id_tac_gia, t.ten_tac_gia, tt.* FROM tac_gia t 
        LEFT JOIN thong_tin_tac_gia tt ON t.id_tac_gia = tt.id_tac_gia 
        WHERE t.id_tac_gia = ?
    ");
    $stmt->execute([$id_edit]);
    $data = $stmt->fetch();
} 

// List danh sách
$list = $pdo->query("
    SELECT t.id_tac_gia, t.ten_tac_gia, tt.quoc_tich, tt.anh_dai_dien, tt.ngay_sinh 
    FROM tac_gia t 
    LEFT JOIN thong_tin_tac_gia tt ON t.id_tac_gia = tt.id_tac_gia 
    ORDER BY t.id_tac_gia DESC
")->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Quản Lý Tác Giả</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tác Giả</li>
                    </ol>
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
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($action == 'add' || $action == 'edit'): ?>
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="action_post" value="<?php echo $action; ?>">
                    
                    <?php if($action == 'edit'): ?>
                        <input type="hidden" name="id_tac_gia" value="<?php echo $id_edit; ?>">
                    <?php endif; ?>

                    <div class="card card-primary">
                        <div class="card-header"><h3 class="card-title"><?php echo $action == 'add' ? 'Thêm Tác Giả Mới' : 'Sửa Tác Giả (' . $id_edit . ')'; ?></h3></div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    <label>Ảnh Đại Diện</label>
                                    <div class="mt-2 mb-3">
                                        <?php 
                                            $img_src = "https://via.placeholder.com/150?text=No+Image";
                                            if (!empty($data['anh_dai_dien'])) {
                                                $img_src = "../../public/uploads/" . $data['anh_dai_dien'];
                                            }
                                        ?>
                                        <img src="<?php echo $img_src; ?>" id="preview" class="img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;">
                                    </div>
                                    <div class="custom-file text-left">
                                        <input type="file" class="custom-file-input" name="anh_dai_dien" accept="image/*" onchange="document.getElementById('preview').src = window.URL.createObjectURL(this.files[0])">
                                        <label class="custom-file-label">Chọn ảnh...</label>
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label>Tên Tác Giả <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="ten_tac_gia" value="<?php echo $data['ten_tac_gia'] ?? ''; ?>" required>
                                    </div>
                                    <div class="row">
                                        <div class="col-6"><div class="form-group"><label>Ngày Sinh</label><input type="date" class="form-control" name="ngay_sinh" value="<?php echo $data['ngay_sinh'] ?? ''; ?>"></div></div>
                                        <div class="col-6"><div class="form-group"><label>Ngày Mất (Nếu có)</label><input type="date" class="form-control" name="ngay_mat" value="<?php echo $data['ngay_mat'] ?? ''; ?>"></div></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6"><div class="form-group"><label>Quốc Tịch</label><input type="text" class="form-control" name="quoc_tich" value="<?php echo $data['quoc_tich'] ?? ''; ?>"></div></div>
                                        <div class="col-6"><div class="form-group"><label>Website</label><input type="text" class="form-control" name="website" value="<?php echo $data['website'] ?? ''; ?>"></div></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-4"><div class="form-group"><label><i class="fab fa-facebook"></i> Facebook</label><input type="text" class="form-control" name="facebook" value="<?php echo $data['facebook'] ?? ''; ?>"></div></div>
                                        <div class="col-4"><div class="form-group"><label><i class="fab fa-twitter"></i> Twitter</label><input type="text" class="form-control" name="twitter" value="<?php echo $data['twitter'] ?? ''; ?>"></div></div>
                                        <div class="col-4"><div class="form-group"><label><i class="fab fa-instagram"></i> Instagram</label><input type="text" class="form-control" name="instagram" value="<?php echo $data['instagram'] ?? ''; ?>"></div></div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div class="form-group"><label>Tiểu Sử</label><textarea class="form-control" name="tieu_su" rows="3"><?php echo $data['tieu_su'] ?? ''; ?></textarea></div>
                                    <div class="form-group"><label>Giải Thưởng</label><textarea class="form-control" name="giai_thuong" rows="2"><?php echo $data['giai_thuong'] ?? ''; ?></textarea></div>
                                    <div class="form-group"><label>Tác Phẩm Nổi Bật Khác</label><textarea class="form-control" name="tac_pham_noi_bat" rows="2"><?php echo $data['tac_pham_noi_bat'] ?? ''; ?></textarea></div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-success">Lưu Thông Tin</button>
                            <a href="authors.php" class="btn btn-secondary float-right">Hủy</a>
                        </div>
                    </div>
                </form>

            <?php else: ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Danh sách tác giả</h3>
                        <div class="card-tools">
                            <a href="authors.php?action=add" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Tác Giả</a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Ảnh</th>
                                    <th>Tên Tác Giả</th>
                                    <th>Năm Sinh</th>
                                    <th>Quốc Tịch</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $item): ?>
                                <tr>
                                    <td><?php echo $item['id_tac_gia']; ?></td>
                                    <td>
                                        <?php 
                                            $img = !empty($item['anh_dai_dien']) ? "../../public/uploads/" . $item['anh_dai_dien'] : "https://via.placeholder.com/50?text=User";
                                        ?>
                                        <img src="<?php echo $img; ?>" style="width: 40px; height: 40px; object-fit: cover; border-radius: 50%;">
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($item['ten_tac_gia']); ?></strong></td>
                                    <td><?php echo !empty($item['ngay_sinh']) ? date('Y', strtotime($item['ngay_sinh'])) : ''; ?></td>
                                    <td><?php echo htmlspecialchars($item['quoc_tich'] ?? ''); ?></td>
                                    <td>
                                        <a href="authors.php?action=edit&id=<?php echo $item['id_tac_gia']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                        <a href="authors.php?action=delete&id=<?php echo $item['id_tac_gia']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa tác giả này?');"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script>$(document).ready(function() { bsCustomFileInput.init(); });</script>