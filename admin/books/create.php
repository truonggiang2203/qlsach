<?php
include '../includes/header.php';
include '../includes/db.php';

// --- PHẦN LOGIC PHP (GIỮ NGUYÊN NHƯ CŨ) ---

// Lấy dữ liệu cho Dropdown
$stmt_genres = $pdo->query("
    SELECT ls.ten_loai, tl.id_the_loai, tl.ten_the_loai 
    FROM the_loai tl
    JOIN loai_sach ls ON tl.id_loai = ls.id_loai
    ORDER BY ls.ten_loai, tl.ten_the_loai
");
$genres_grouped = [];
foreach ($stmt_genres->fetchAll() as $genre) {
    $genres_grouped[$genre['ten_loai']][] = $genre;
}

$nxb = $pdo->query("SELECT * FROM nxb")->fetchAll();
$tac_gia = $pdo->query("SELECT * FROM tac_gia")->fetchAll();
$ngon_ngu = $pdo->query("SELECT * FROM ngon_ngu")->fetchAll();
$khuyen_mai = $pdo->query("SELECT * FROM khuyen_mai")->fetchAll();
$ncc = $pdo->query("SELECT * FROM nha_cung_cap")->fetchAll();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_sach = $_POST['ten_sach'];
    $id_nxb = $_POST['id_nxb'];
    $id_km = $_POST['id_km'];
    $mo_ta = $_POST['mo_ta'];
    $so_luong_ton = $_POST['so_luong_ton'];
    $gia_sach_ban = $_POST['gia_sach_ban'];
    
    $id_the_loais = $_POST['id_the_loai'] ?? [];
    $id_tac_gias = $_POST['id_tac_gia'] ?? []; 
    $id_ngon_ngus = $_POST['id_ngon_ngu'] ?? [];
    $id_nccs = $_POST['id_ncc'] ?? [];

    $pdo->beginTransaction();
    
    try {
        // 1. Tạo ID
        $stmt_id = $pdo->query("SELECT id_sach FROM sach ORDER BY id_sach DESC LIMIT 1 FOR UPDATE");
        $last_book = $stmt_id->fetch();
        $new_id_num = 1;
        if ($last_book) {
            $last_id_num = (int) substr($last_book['id_sach'], 1);
            $new_id_num = $last_id_num + 1;
        }
        $id_sach = 'S' . str_pad($new_id_num, 4, '0', STR_PAD_LEFT);
        
        // 2. Insert Sach
        $stmt_sach = $pdo->prepare("INSERT INTO sach (id_sach, id_nxb, id_km, ten_sach, mo_ta, trang_thai_sach, so_luong_ton) VALUES (?, ?, ?, ?, ?, 1, ?)");
        $stmt_sach->execute([$id_sach, $id_nxb, $id_km, $ten_sach, $mo_ta, $so_luong_ton]);
        
        // 3. Insert Thể loại
        $stmt_stl = $pdo->prepare("INSERT INTO sach_theloai (id_sach, id_the_loai) VALUES (?, ?)");
        foreach ($id_the_loais as $tl) if(!empty($tl)) $stmt_stl->execute([$id_sach, $tl]);

        // 4. Insert Giá & Thời điểm
        $now = date('Y-m-d H:i:s'); 
        $pdo->prepare("INSERT IGNORE INTO thoi_diem (tg_gia_bd) VALUES (?)")->execute([$now]);
        $pdo->prepare("INSERT INTO gia_sach (id_sach, tg_gia_bd, gia_sach_ban) VALUES (?, ?, ?)")->execute([$id_sach, $now, $gia_sach_ban]);

        // 5. Insert các bảng phụ
        $stmt_tg = $pdo->prepare("INSERT INTO s_tg (id_sach, id_tac_gia) VALUES (?, ?)");
        foreach ($id_tac_gias as $item) if(!empty($item)) $stmt_tg->execute([$id_sach, $item]);

        $stmt_nn = $pdo->prepare("INSERT INTO s_nns (id_sach, id_ngon_ngu) VALUES (?, ?)");
        foreach ($id_ngon_ngus as $item) if(!empty($item)) $stmt_nn->execute([$id_sach, $item]);
        
        $stmt_ncc = $pdo->prepare("INSERT INTO s_ncc (id_sach, id_ncc) VALUES (?, ?)");
        foreach ($id_nccs as $item) if(!empty($item)) $stmt_ncc->execute([$id_sach, $item]);

        $pdo->commit();
        $success = "Thêm sách mới ($id_sach) thành công!";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<style>
    /* Tùy chỉnh nhỏ để Select2 đẹp hơn */
    .select2-container--bootstrap4 .select2-selection--multiple .select2-search__field {
        margin-top: 0;
    }
    .select2-selection__choice {
        background-color: #007bff !important;
        border-color: #006fe6 !important;
        color: #fff !important;
    }
    .select2-selection__choice__remove {
        color: #f70606ff !important;
    }
</style>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Thêm Sách Mới</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Sách</a></li>
                        <li class="breadcrumb-item active">Thêm Mới</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
             <?php if ($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
             <?php if ($success) echo "<div class='alert alert-success'>$success</div>"; ?>
            
            <form action="" method="POST">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">Nhập thông tin sách</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tên Sách <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" name="ten_sach" required placeholder="Nhập tên sách...">
                                </div>

                                <div class="form-group">
                                    <label>Thể Loại</label>
                                    <select class="form-control select2" name="id_the_loai[]" multiple="multiple" data-placeholder="Chọn thể loại sách" style="width: 100%;">
                                        <?php foreach ($genres_grouped as $cat => $genres): ?>
                                            <optgroup label="<?php echo htmlspecialchars($cat); ?>">
                                                <?php foreach ($genres as $g): ?>
                                                    <option value="<?php echo $g['id_the_loai']; ?>"><?php echo htmlspecialchars($g['ten_the_loai']); ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Tác Giả</label>
                                    <select class="form-control select2" name="id_tac_gia[]" multiple="multiple" data-placeholder="Chọn tác giả" style="width: 100%;">
                                        <?php foreach ($tac_gia as $item): ?>
                                            <option value="<?php echo $item['id_tac_gia']; ?>"><?php echo htmlspecialchars($item['ten_tac_gia']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Nhà Xuất Bản</label>
                                    <select class="form-control select2-single" name="id_nxb" style="width: 100%;">
                                        <?php foreach ($nxb as $item): ?>
                                            <option value="<?php echo $item['id_nxb']; ?>"><?php echo htmlspecialchars($item['ten_nxb']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Mô tả</label>
                                    <textarea class="form-control" name="mo_ta" rows="4" placeholder="Mô tả nội dung sách..."></textarea>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ngôn Ngữ</label>
                                    <select class="form-control select2" name="id_ngon_ngu[]" multiple="multiple" data-placeholder="Chọn ngôn ngữ" style="width: 100%;">
                                        <?php foreach ($ngon_ngu as $item): ?>
                                            <option value="<?php echo $item['id_ngon_ngu']; ?>"><?php echo htmlspecialchars($item['ngon_ngu']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Nhà Cung Cấp</label>
                                    <select class="form-control select2" name="id_ncc[]" multiple="multiple" data-placeholder="Chọn nhà cung cấp" style="width: 100%;">
                                        <?php foreach ($ncc as $item): ?>
                                            <option value="<?php echo $item['id_ncc']; ?>"><?php echo htmlspecialchars($item['ten_ncc']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>Khuyến Mãi</label>
                                    <select class="form-control select2-single" name="id_km" style="width: 100%;">
                                        <?php foreach ($khuyen_mai as $item): ?>
                                            <option value="<?php echo $item['id_km']; ?>">
                                                <?php echo htmlspecialchars($item['ten_km']); ?> 
                                                (<?php echo $item['phan_tram_km']; ?>%)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Giá Bán (VNĐ)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" name="gia_sach_ban" required value="0">
                                                <div class="input-group-append">
                                                    <span class="input-group-text">đ</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label>Số Lượng Tồn</label>
                                            <input type="number" class="form-control" name="so_luong_ton" required value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-2"></i>Lưu Sách</button>
                        <a href="index.php" class="btn btn-default float-right">Hủy bỏ</a>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        // Kích hoạt Select2 cho các ô chọn nhiều
        $('.select2').select2({
            theme: 'bootstrap4',
            allowClear: true
        });

        // Kích hoạt Select2 cho các ô chọn đơn (NXB, KM) để đồng bộ giao diện
        $('.select2-single').select2({
            theme: 'bootstrap4'
        });
    });
</script>