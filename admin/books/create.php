<?php
include '../includes/header.php';
include '../includes/db.php';

// Lấy dữ liệu Dropdown
$stmt_genres = $pdo->query("SELECT ls.ten_loai, tl.id_the_loai, tl.ten_the_loai FROM the_loai tl JOIN loai_sach ls ON tl.id_loai = ls.id_loai ORDER BY ls.ten_loai, tl.ten_the_loai");
$genres_grouped = []; foreach ($stmt_genres->fetchAll() as $g) { $genres_grouped[$g['ten_loai']][] = $g; }
$nxb = $pdo->query("SELECT * FROM nxb")->fetchAll();
$tac_gia = $pdo->query("SELECT * FROM tac_gia")->fetchAll();
$ngon_ngu = $pdo->query("SELECT * FROM ngon_ngu")->fetchAll();
$khuyen_mai = $pdo->query("SELECT * FROM khuyen_mai")->fetchAll();
$ncc = $pdo->query("SELECT * FROM nha_cung_cap")->fetchAll();

$error = ''; $success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Dữ liệu bảng 'sach'
    $ten_sach = $_POST['ten_sach'];
    $id_nxb = $_POST['id_nxb'];
    $id_km = $_POST['id_km'];
    $mo_ta = $_POST['mo_ta'];
    $so_luong_ton = $_POST['so_luong_ton'];
    $gia_sach_ban = $_POST['gia_sach_ban'];
    
    // Dữ liệu bảng 'thong_tin_sach' (MỚI)
    $so_trang = !empty($_POST['so_trang']) ? $_POST['so_trang'] : null;
    $trong_luong = !empty($_POST['trong_luong']) ? $_POST['trong_luong'] : null;
    $kich_thuoc = $_POST['kich_thuoc'];
    $hinh_thuc = $_POST['hinh_thuc'];
    $nam_xuat_ban = !empty($_POST['nam_xuat_ban']) ? $_POST['nam_xuat_ban'] : null;

    // Các mảng quan hệ
    $id_the_loais = $_POST['id_the_loai'] ?? [];
    $id_tac_gias = $_POST['id_tac_gia'] ?? []; 
    $id_ngon_ngus = $_POST['id_ngon_ngu'] ?? [];
    $id_nccs = $_POST['id_ncc'] ?? [];

    $pdo->beginTransaction();
    try {
        // 1. TẠO ID
        $stmt_id = $pdo->query("SELECT id_sach FROM sach ORDER BY id_sach DESC LIMIT 1 FOR UPDATE");
        $last_book = $stmt_id->fetch();
        $new_id_num = 1;
        if ($last_book) { $new_id_num = (int) substr($last_book['id_sach'], 1) + 1; }
        $id_sach = 'S' . str_pad($new_id_num, 4, '0', STR_PAD_LEFT);
        
        // 2. UPLOAD ẢNH
        if (isset($_FILES['hinh_anh']) && $_FILES['hinh_anh']['error'] == 0) {
            $upload_dir = '../../public/uploads/';
            if (!file_exists($upload_dir)) mkdir($upload_dir, 0777, true);
            move_uploaded_file($_FILES['hinh_anh']['tmp_name'], $upload_dir . $id_sach . '.jpg');
        }

        // 3. INSERT 'sach'
        $stmt_sach = $pdo->prepare("INSERT INTO sach (id_sach, id_nxb, id_km, ten_sach, mo_ta, trang_thai_sach, so_luong_ton) VALUES (?, ?, ?, ?, ?, 1, ?)");
        $stmt_sach->execute([$id_sach, $id_nxb, $id_km, $ten_sach, $mo_ta, $so_luong_ton]);
        
        // 4. INSERT 'thong_tin_sach' (MỚI)
        $stmt_info = $pdo->prepare("INSERT INTO thong_tin_sach (id_sach, so_trang, trong_luong, kich_thuoc, hinh_thuc, nam_xuat_ban) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt_info->execute([$id_sach, $so_trang, $trong_luong, $kich_thuoc, $hinh_thuc, $nam_xuat_ban]);

        // 5. INSERT các bảng phụ (Thể loại, Giá, Tác giả, Ngôn ngữ, NCC)
        $stmt_stl = $pdo->prepare("INSERT INTO sach_theloai (id_sach, id_the_loai) VALUES (?, ?)");
        foreach ($id_the_loais as $tl) if(!empty($tl)) $stmt_stl->execute([$id_sach, $tl]);

        $now = date('Y-m-d H:i:s'); 
        $pdo->prepare("INSERT IGNORE INTO thoi_diem (tg_gia_bd) VALUES (?)")->execute([$now]);
        $pdo->prepare("INSERT INTO gia_sach (id_sach, tg_gia_bd, gia_sach_ban) VALUES (?, ?, ?)")->execute([$id_sach, $now, $gia_sach_ban]);

        $stmt_tg = $pdo->prepare("INSERT INTO s_tg (id_sach, id_tac_gia) VALUES (?, ?)");
        foreach ($id_tac_gias as $item) if(!empty($item)) $stmt_tg->execute([$id_sach, $item]);

        $stmt_nn = $pdo->prepare("INSERT INTO s_nns (id_sach, id_ngon_ngu) VALUES (?, ?)");
        foreach ($id_ngon_ngus as $item) if(!empty($item)) $stmt_nn->execute([$id_sach, $item]);
        
        $stmt_ncc = $pdo->prepare("INSERT INTO s_ncc (id_sach, id_ncc) VALUES (?, ?)");
        foreach ($id_nccs as $item) if(!empty($item)) $stmt_ncc->execute([$id_sach, $item]);

        $pdo->commit();
        $success = "Thêm sách ($id_sach) thành công!";
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header"><h1>Thêm Sách Mới</h1></section>
    <section class="content">
        <div class="container-fluid">
             <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
             <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
            
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="card card-primary">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label>Tên Sách <span class="text-danger">*</span></label><input type="text" class="form-control" name="ten_sach" required></div>
                                <div class="form-group"><label>Ảnh Bìa</label><div class="custom-file"><input type="file" class="custom-file-input" name="hinh_anh" accept="image/*"><label class="custom-file-label">Chọn file...</label></div></div>
                                <div class="form-group"><label>Thể Loại</label><select class="form-control select2" name="id_the_loai[]" multiple required style="width: 100%;"><?php foreach ($genres_grouped as $cat => $genres): ?><optgroup label="<?php echo htmlspecialchars($cat); ?>"><?php foreach ($genres as $g): ?><option value="<?php echo $g['id_the_loai']; ?>"><?php echo htmlspecialchars($g['ten_the_loai']); ?></option><?php endforeach; ?></optgroup><?php endforeach; ?></select></div>
                                <div class="form-group"><label>Tác Giả</label><select class="form-control select2" name="id_tac_gia[]" multiple style="width: 100%;"><?php foreach ($tac_gia as $item): echo "<option value='{$item['id_tac_gia']}'>{$item['ten_tac_gia']}</option>"; endforeach; ?></select></div>
                                <div class="form-group"><label>Nhà Xuất Bản</label><select class="form-control select2-single" name="id_nxb" style="width: 100%;"><?php foreach ($nxb as $item): echo "<option value='{$item['id_nxb']}'>{$item['ten_nxb']}</option>"; endforeach; ?></select></div>
                                
                                <div class="row">
                                    <div class="col-6"><div class="form-group"><label>Số trang</label><input type="number" class="form-control" name="so_trang" placeholder="VD: 200"></div></div>
                                    <div class="col-6"><div class="form-group"><label>Trọng lượng (g)</label><input type="number" class="form-control" name="trong_luong" placeholder="VD: 300"></div></div>
                                </div>
                                <div class="row">
                                    <div class="col-6"><div class="form-group"><label>Kích thước</label><input type="text" class="form-control" name="kich_thuoc" placeholder="VD: 14x20 cm"></div></div>
                                    <div class="col-6"><div class="form-group"><label>Năm xuất bản</label><input type="number" class="form-control" name="nam_xuat_ban" placeholder="VD: 2024"></div></div>
                                </div>
                                <div class="form-group"><label>Hình thức</label><select class="form-control" name="hinh_thuc"><option value="Bìa mềm">Bìa mềm</option><option value="Bìa cứng">Bìa cứng</option></select></div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group"><label>Mô tả</label><textarea class="form-control" name="mo_ta" rows="3"></textarea></div>
                                <div class="form-group"><label>Ngôn Ngữ</label><select class="form-control select2" name="id_ngon_ngu[]" multiple style="width: 100%;"><?php foreach ($ngon_ngu as $item): echo "<option value='{$item['id_ngon_ngu']}'>{$item['ngon_ngu']}</option>"; endforeach; ?></select></div>
                                <div class="form-group"><label>Nhà Cung Cấp</label><select class="form-control select2" name="id_ncc[]" multiple style="width: 100%;"><?php foreach ($ncc as $item): echo "<option value='{$item['id_ncc']}'>{$item['ten_ncc']}</option>"; endforeach; ?></select></div>
                                <div class="form-group"><label>Khuyến Mãi</label><select class="form-control select2-single" name="id_km" style="width: 100%;"><?php foreach ($khuyen_mai as $item): echo "<option value='{$item['id_km']}'>{$item['ten_km']} (-{$item['phan_tram_km']}%)</option>"; endforeach; ?></select></div>
                                <div class="row">
                                    <div class="col-6"><label>Giá Bán</label><input type="number" class="form-control" name="gia_sach_ban" required value="0"></div>
                                    <div class="col-6"><label>Số Lượng Tồn</label><input type="number" class="form-control" name="so_luong_ton" required value="0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer"><button type="submit" class="btn btn-primary">Lưu Sách</button></div>
                </div>
            </form>
        </div>
    </section>
</div>
<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bs-custom-file-input/dist/bs-custom-file-input.min.js"></script>
<script>$(document).ready(function() { $('.select2').select2({ theme: 'bootstrap4' }); $('.select2-single').select2({ theme: 'bootstrap4' }); bsCustomFileInput.init(); });</script>