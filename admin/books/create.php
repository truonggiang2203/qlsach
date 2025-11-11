<?php
include '../includes/header.php';
include '../includes/db.php';

// --- THAY ĐỔI: Lấy Thể Loại, nhóm theo Loại Sách ---
$stmt_genres = $pdo->query("
    SELECT ls.ten_loai, tl.id_the_loai, tl.ten_the_loai 
    FROM the_loai tl
    JOIN loai_sach ls ON tl.id_loai = ls.id_loai
    ORDER BY ls.ten_loai, tl.ten_the_loai
");

// Nhóm kết quả lại
$genres_grouped = [];
foreach ($stmt_genres->fetchAll() as $genre) {
    $genres_grouped[$genre['ten_loai']][] = $genre;
}
// --- KẾT THÚC THAY ĐỔI ---

// Lấy dữ liệu cho các dropdown
$nxb = $pdo->query("SELECT * FROM nxb")->fetchAll();
$tac_gia = $pdo->query("SELECT * FROM tac_gia")->fetchAll();
$ngon_ngu = $pdo->query("SELECT * FROM ngon_ngu")->fetchAll();
$khuyen_mai = $pdo->query("SELECT * FROM khuyen_mai")->fetchAll();

$error = '';
$success = '';

// Xử lý khi form được submit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Lấy dữ liệu form
    $ten_sach = $_POST['ten_sach'];
    $id_nxb = $_POST['id_nxb'];
    
    // --- THAY ĐỔI: Lấy id_the_loai (thay vì id_loai) ---
    $id_the_loai = $_POST['id_the_loai']; 
    // --- KẾT THÚC THAY ĐỔI ---

    $id_km = $_POST['id_km'];
    $mo_ta = $_POST['mo_ta'];
    $so_luong_ton = $_POST['so_luong_ton'];
    $gia_sach_ban = $_POST['gia_sach_ban'];
    
    $id_tac_gias = $_POST['id_tac_gia'] ?? []; 
    $id_ngon_ngus = $_POST['id_ngon_ngu'] ?? [];

    $pdo->beginTransaction();
    
    try {
        // 1. TẠO ID SÁCH TỰ ĐỘNG
        $stmt_id = $pdo->query("SELECT id_sach FROM sach ORDER BY id_sach DESC LIMIT 1 FOR UPDATE");
        $last_book = $stmt_id->fetch();
        $new_id_num = 1;
        if ($last_book) {
            $last_id_num = (int) substr($last_book['id_sach'], 1);
            $new_id_num = $last_id_num + 1;
        }
        $id_sach = 'S' . str_pad($new_id_num, 4, '0', STR_PAD_LEFT);
        
        // 2. Thêm vào bảng `sach`
        // --- THAY ĐỔI: Chèn id_the_loai (thay vì id_loai) ---
        $stmt_sach = $pdo->prepare("
            INSERT INTO sach (id_sach, id_nxb, id_the_loai, id_km, ten_sach, mo_ta, trang_thai_sach, so_luong_ton)
            VALUES (?, ?, ?, ?, ?, ?, 1, ?)
        ");
        $stmt_sach->execute([$id_sach, $id_nxb, $id_the_loai, $id_km, $ten_sach, $mo_ta, $so_luong_ton]);
        // --- KẾT THÚC THAY ĐỔI ---
        
        // 3. Xử lý ngày giờ và giá bán
        $now = $pdo->query("SELECT NOW()")->fetchColumn();
        $stmt_thoidiem = $pdo->prepare("INSERT IGNORE INTO thoi_diem (ngay_gio_ban) VALUES (?)");
        $stmt_thoidiem->execute([$now]);
        $stmt_gia = $pdo->prepare("INSERT INTO gia_sach (id_sach, ngay_gio_ban, gia_sach_ban) VALUES (?, ?, ?)");
        $stmt_gia->execute([$id_sach, $now, $gia_sach_ban]);

        // 4. Thêm vào bảng `s_tg` (tác giả)
        $stmt_tg = $pdo->prepare("INSERT INTO s_tg (id_sach, id_tac_gia) VALUES (?, ?)");
        foreach ($id_tac_gias as $id_tac_gia) {
            if (!empty($id_tac_gia)) {
                $stmt_tg->execute([$id_sach, $id_tac_gia]);
            }
        }
        
        // 5. Thêm vào bảng `s_nns` (ngôn ngữ)
        $stmt_nn = $pdo->prepare("INSERT INTO s_nns (id_sach, id_ngon_ngu) VALUES (?, ?)");
        foreach ($id_ngon_ngus as $id_ngon_ngu) {
            if (!empty($id_ngon_ngu)) {
                $stmt_nn->execute([$id_sach, $id_ngon_ngu]);
            }
        }

        $pdo->commit();
        $success = "Thêm sách mới (ID: $id_sach) thành công!";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi khi thêm sách: " . $e->getMessage();
    }
}
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Thông Tin Sách</h3>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger m-3"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success m-3"><?php echo $success; ?></div>
                <?php endif; ?>

                <form action="create.php" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ten_sach">Tên Sách</label>
                                    <input type="text" class="form-control" name="ten_sach" placeholder="Tên sách" required>
                                </div>
                                <div class="form-group">
                                    <label for="mo_ta">Mô Tả</label>
                                    <textarea class="form-control" name="mo_ta" rows="5"></textarea>
                                </div>

                                <div class="form-group">
                                    <label for="id_the_loai">Thể Loại</label>
                                    <select class="form-control" name="id_the_loai" required>
                                        <option value="">-- Chọn thể loại --</option>
                                        <?php foreach ($genres_grouped as $category_name => $genres): ?>
                                            <optgroup label="<?php echo htmlspecialchars($category_name); ?>">
                                                <?php foreach ($genres as $genre): ?>
                                                    <option value="<?php echo $genre['id_the_loai']; ?>">
                                                        <?php echo htmlspecialchars($genre['ten_the_loai']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="id_nxb">Nhà Xuất Bản</label>
                                    <select class="form-control" name="id_nxb">
                                        <?php foreach ($nxb as $item): ?>
                                            <option value="<?php echo $item['id_nxb']; ?>"><?php echo htmlspecialchars($item['ten_nxb']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_tac_gia">Tác Giả (Chọn nhiều)</label>
                                    <select class="form-control" name="id_tac_gia[]" multiple style="height: 150px;">
                                        <?php foreach ($tac_gia as $item): ?>
                                            <option value="<?php echo $item['id_tac_gia']; ?>"><?php echo htmlspecialchars($item['ten_tac_gia']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều tác giả.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_ngon_ngu">Ngôn Ngữ (Chọn nhiều)</label>
                                    <select class="form-control" name="id_ngon_ngu[]" multiple style="height: 100px;">
                                        <?php foreach ($ngon_ngu as $item): ?>
                                            <option value="<?php echo $item['id_ngon_ngu']; ?>"><?php echo htmlspecialchars($item['ngon_ngu']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều ngôn ngữ.</small>
                                </div>

                                 <div class="form-group">
                                    <label for="id_km">Khuyến Mãi</label>
                                    <select class="form-control" name="id_km">
                                        <?php foreach ($khuyen_mai as $item): ?>
                                            <option value="<?php echo $item['id_km']; ?>"><?php echo htmlspecialchars($item['ten_km']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="gia_sach_ban">Giá Bán (VNĐ)</label>
                                            <input type="number" class="form-control" name="gia_sach_ban" value="0">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="so_luong_ton">Số Lượng Tồn</label>
                                            <input type="number" class="form-control" name="so_luong_ton" value="0">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">Lưu Sách</button>
                        <a href="index.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>