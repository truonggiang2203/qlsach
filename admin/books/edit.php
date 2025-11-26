<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

if (!isset($_GET['id'])) header('Location: index.php');
$id_sach = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu cơ bản
    $ten_sach = $_POST['ten_sach'];
    $id_nxb = $_POST['id_nxb'];
    $id_km = $_POST['id_km'];
    $mo_ta = $_POST['mo_ta'];
    $so_luong_ton = $_POST['so_luong_ton'];
    $gia_sach_ban = $_POST['gia_sach_ban'];
    $trang_thai_sach = $_POST['trang_thai_sach'];
    
    // Dữ liệu thông tin chi tiết (MỚI)
    $so_trang = !empty($_POST['so_trang']) ? $_POST['so_trang'] : null;
    $trong_luong = !empty($_POST['trong_luong']) ? $_POST['trong_luong'] : null;
    $kich_thuoc = $_POST['kich_thuoc'];
    $hinh_thuc = $_POST['hinh_thuc'];
    $nam_xuat_ban = !empty($_POST['nam_xuat_ban']) ? $_POST['nam_xuat_ban'] : null;

    // Các mảng
    $id_the_loais = $_POST['id_the_loai'] ?? [];
    $id_tac_gias = $_POST['id_tac_gia'] ?? [];
    $id_ngon_ngus = $_POST['id_ngon_ngu'] ?? [];
    $id_nccs = $_POST['id_ncc'] ?? [];

    $pdo->beginTransaction();
    try {
        // 1. Upload ảnh (ghi đè)
        if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
            $upload_dir = '../../public/uploads/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $upload_dir . $id_sach . '.jpg');
        }

        // 2. Update bảng Sach
        $stmt = $pdo->prepare("UPDATE sach SET ten_sach=?, id_nxb=?, id_km=?, mo_ta=?, so_luong_ton=?, trang_thai_sach=? WHERE id_sach=?");
        $stmt->execute([$ten_sach, $id_nxb, $id_km, $mo_ta, $so_luong_ton, $trang_thai_sach, $id_sach]);

        // 3. Update/Insert bảng 'thong_tin_sach' (MỚI)
        // Dùng cú pháp ON DUPLICATE KEY UPDATE của MySQL để vừa thêm mới (nếu chưa có) vừa cập nhật
        $stmt_info = $pdo->prepare("
            INSERT INTO thong_tin_sach (id_sach, so_trang, trong_luong, kich_thuoc, hinh_thuc, nam_xuat_ban) 
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
            so_trang = VALUES(so_trang), trong_luong = VALUES(trong_luong), kich_thuoc = VALUES(kich_thuoc), 
            hinh_thuc = VALUES(hinh_thuc), nam_xuat_ban = VALUES(nam_xuat_ban)
        ");
        $stmt_info->execute([$id_sach, $so_trang, $trong_luong, $kich_thuoc, $hinh_thuc, $nam_xuat_ban]);

        // 4. Update Giá
        $stmt_date = $pdo->prepare("SELECT tg_gia_bd FROM gia_sach WHERE id_sach=? ORDER BY tg_gia_bd DESC LIMIT 1");
        $stmt_date->execute([$id_sach]);
        $last_date = $stmt_date->fetchColumn();
        if($last_date) $pdo->prepare("UPDATE gia_sach SET gia_sach_ban=? WHERE id_sach=? AND tg_gia_bd=?")->execute([$gia_sach_ban, $id_sach, $last_date]);

        // 5. Update các bảng phụ (Xóa cũ -> Thêm mới)
        $pdo->prepare("DELETE FROM sach_theloai WHERE id_sach=?")->execute([$id_sach]);
        $stmt_stl = $pdo->prepare("INSERT INTO sach_theloai (id_sach, id_the_loai) VALUES (?, ?)");
        foreach($id_the_loais as $val) if(!empty($val)) $stmt_stl->execute([$id_sach, $val]);

        $pdo->prepare("DELETE FROM s_tg WHERE id_sach=?")->execute([$id_sach]);
        $stmt_tg = $pdo->prepare("INSERT INTO s_tg (id_sach, id_tac_gia) VALUES (?, ?)");
        foreach($id_tac_gias as $val) if(!empty($val)) $stmt_tg->execute([$id_sach, $val]);

        $pdo->prepare("DELETE FROM s_nns WHERE id_sach=?")->execute([$id_sach]);
        $stmt_nn = $pdo->prepare("INSERT INTO s_nns (id_sach, id_ngon_ngu) VALUES (?, ?)");
        foreach($id_ngon_ngus as $val) if(!empty($val)) $stmt_nn->execute([$id_sach, $val]);
        
        $pdo->prepare("DELETE FROM s_ncc WHERE id_sach=?")->execute([$id_sach]);
        $stmt_ncc = $pdo->prepare("INSERT INTO s_ncc (id_sach, id_ncc) VALUES (?, ?)");
        foreach($id_nccs as $val) if(!empty($val)) $stmt_ncc->execute([$id_sach, $val]);

        $pdo->commit();
        $_SESSION['success_message'] = "Cập nhật thành công!";
        header("Location: edit.php?id=$id_sach");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}

// --- LẤY DỮ LIỆU HIỂN THỊ ---
// JOIN thêm bảng thong_tin_sach
$book = $pdo->prepare("
    SELECT s.*, g.gia_sach_ban, tts.* FROM sach s 
    LEFT JOIN gia_sach g ON s.id_sach = g.id_sach 
    LEFT JOIN thong_tin_sach tts ON s.id_sach = tts.id_sach
    WHERE s.id_sach=? 
    ORDER BY g.tg_gia_bd DESC LIMIT 1
");
$book->execute([$id_sach]);
$book = $book->fetch();

// Các mảng ID đã chọn
$cur_genres = $pdo->prepare("SELECT id_the_loai FROM sach_theloai WHERE id_sach=?"); $cur_genres->execute([$id_sach]); $cur_genres = $cur_genres->fetchAll(PDO::FETCH_COLUMN);
$cur_authors = $pdo->prepare("SELECT id_tac_gia FROM s_tg WHERE id_sach=?"); $cur_authors->execute([$id_sach]); $cur_authors = $cur_authors->fetchAll(PDO::FETCH_COLUMN);
$cur_langs = $pdo->prepare("SELECT id_ngon_ngu FROM s_nns WHERE id_sach=?"); $cur_langs->execute([$id_sach]); $cur_langs = $cur_langs->fetchAll(PDO::FETCH_COLUMN);
$cur_nccs = $pdo->prepare("SELECT id_ncc FROM s_ncc WHERE id_sach=?"); $cur_nccs->execute([$id_sach]); $cur_nccs = $cur_nccs->fetchAll(PDO::FETCH_COLUMN);

// Dữ liệu dropdown
$stmt_genres = $pdo->query("SELECT ls.ten_loai, tl.id_the_loai, tl.ten_the_loai FROM the_loai tl JOIN loai_sach ls ON tl.id_loai = ls.id_loai ORDER BY ls.ten_loai, tl.ten_the_loai");
$genres_grouped = []; foreach ($stmt_genres->fetchAll() as $g) $genres_grouped[$g['ten_loai']][] = $g;
$nxb = $pdo->query("SELECT * FROM nxb")->fetchAll();
$tac_gia = $pdo->query("SELECT * FROM tac_gia")->fetchAll();
$ngon_ngu = $pdo->query("SELECT * FROM ngon_ngu")->fetchAll();
$khuyen_mai = $pdo->query("SELECT * FROM khuyen_mai")->fetchAll();
$ncc = $pdo->query("SELECT * FROM nha_cung_cap")->fetchAll();
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<?php include '../includes/sidebar.php'; ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Sửa Sách: <?php echo htmlspecialchars($book['ten_sach']); ?></h1></section>
    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success_message'])) { echo "<div class='alert alert-success'>{$_SESSION['success_message']}</div>"; unset($_SESSION['success_message']); } ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="card card-warning">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label>Tên Sách</label><input type="text" class="form-control" name="ten_sach" value="<?php echo htmlspecialchars($book['ten_sach']); ?>" required></div>
                                
                                <div class="form-group">
                                    <label>Hình Ảnh Hiện Tại</label><br>
                                    <?php $img_path = "../../public/uploads/" . $id_sach . ".jpg"; $img_show = file_exists($img_path) ? $img_path . "?t=" . time() : "https://via.placeholder.com/150?text=No+Image"; ?>
                                    <img src="<?php echo $img_show; ?>" style="max-width: 150px; border: 1px solid #ddd; padding: 2px; margin-bottom: 10px;">
                                    <div class="custom-file"><input type="file" class="custom-file-input" name="hinh_anh" accept="image/*"><label class="custom-file-label">Chọn ảnh mới...</label></div>
                                </div>

                                <div class="form-group"><label>Thể Loại</label><select class="form-control select2" name="id_the_loai[]" multiple required style="width: 100%;"><?php foreach ($genres_grouped as $cat => $genres): ?><optgroup label="<?php echo htmlspecialchars($cat); ?>"><?php foreach ($genres as $g): ?><option value="<?php echo $g['id_the_loai']; ?>" <?php echo in_array($g['id_the_loai'], $cur_genres) ? 'selected' : ''; ?>><?php echo htmlspecialchars($g['ten_the_loai']); ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select></div>
                                <div class="form-group"><label>Tác Giả</label><select class="form-control select2" name="id_tac_gia[]" multiple style="width: 100%;"><?php foreach ($tac_gia as $i) echo "<option value='{$i['id_tac_gia']}' " . (in_array($i['id_tac_gia'], $cur_authors)?'selected':'') . ">{$i['ten_tac_gia']}</option>"; ?></select></div>
                                <div class="form-group"><label>Nhà Xuất Bản</label><select class="form-control select2-single" name="id_nxb" style="width: 100%;"><?php foreach ($nxb as $i) echo "<option value='{$i['id_nxb']}' " . ($i['id_nxb']==$book['id_nxb']?'selected':'') . ">{$i['ten_nxb']}</option>"; ?></select></div>
                                
                                <div class="row">
                                    <div class="col-6"><div class="form-group"><label>Số trang</label><input type="number" class="form-control" name="so_trang" value="<?php echo $book['so_trang']; ?>"></div></div>
                                    <div class="col-6"><div class="form-group"><label>Trọng lượng (g)</label><input type="number" class="form-control" name="trong_luong" value="<?php echo $book['trong_luong']; ?>"></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><div class="form-group"><label>Kích thước</label><input type="text" class="form-control" name="kich_thuoc" value="<?php echo $book['kich_thuoc']; ?>"></div></div>
                                    <div class="col-6"><div class="form-group"><label>Năm xuất bản</label><input type="number" class="form-control" name="nam_xuat_ban" value="<?php echo $book['nam_xuat_ban']; ?>"></div></div>
                                </div>
                                <div class="form-group"><label>Hình thức</label><select class="form-control" name="hinh_thuc"><option value="Bìa mềm" <?php if($book['hinh_thuc']=='Bìa mềm') echo 'selected'; ?>>Bìa mềm</option><option value="Bìa cứng" <?php if($book['hinh_thuc']=='Bìa cứng') echo 'selected'; ?>>Bìa cứng</option></select></div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group"><label>Mô tả</label><textarea class="form-control" name="mo_ta" rows="3"><?php echo htmlspecialchars($book['mo_ta']); ?></textarea></div>
                                <div class="form-group"><label>Ngôn Ngữ</label><select class="form-control select2" name="id_ngon_ngu[]" multiple style="width: 100%;"><?php foreach ($ngon_ngu as $i) echo "<option value='{$i['id_ngon_ngu']}' " . (in_array($i['id_ngon_ngu'], $cur_langs)?'selected':'') . ">{$i['ngon_ngu']}</option>"; ?></select></div>
                                <div class="form-group"><label>Nhà Cung Cấp</label><select class="form-control select2" name="id_ncc[]" multiple style="width: 100%;"><?php foreach ($ncc as $i) echo "<option value='{$i['id_ncc']}' " . (in_array($i['id_ncc'], $cur_nccs)?'selected':'') . ">{$i['ten_ncc']}</option>"; ?></select></div>
                                <div class="form-group"><label>Khuyến Mãi</label><select class="form-control select2-single" name="id_km" style="width: 100%;"><?php foreach ($khuyen_mai as $i) echo "<option value='{$i['id_km']}' " . ($i['id_km']==$book['id_km']?'selected':'') . ">{$i['ten_km']}</option>"; ?></select></div>
                                <div class="row">
                                    <div class="col-6"><label>Giá Bán</label><input type="number" class="form-control" name="gia_sach_ban" value="<?php echo $book['gia_sach_ban']; ?>"></div>
                                    <div class="col-6"><label>Tồn Kho</label><input type="number" class="form-control" name="so_luong_ton" value="<?php echo $book['so_luong_ton']; ?>"></div>
                                </div>
                                <div class="form-group"><label>Trạng Thái</label><select class="form-control" name="trang_thai_sach"><option value="1" <?php echo $book['trang_thai_sach']==1?'selected':''; ?>>Hiển thị</option><option value="0" <?php echo $book['trang_thai_sach']==0?'selected':''; ?>>Ẩn</option></select></div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-warning">Cập Nhật</button></div>
                </div>
            </form>
        </div>
    </section>
</div>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script>$(document).ready(function() { $('.select2').select2({ theme: 'bootstrap4' }); $('.select2-single').select2({ theme: 'bootstrap4' }); bsCustomFileInput.init(); });</script>