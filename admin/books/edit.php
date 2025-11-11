<?php
session_start();
include '../includes/header.php';
include '../includes/db.php';

// 1. Kiểm tra ID sách
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Không tìm thấy sách.";
    header('Location: index.php');
    exit;
}
$id_sach = $_GET['id'];

$error = '';
$success = '';

// --- XỬ LÝ POST (CẬP NHẬT SÁCH) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu form
    $ten_sach = $_POST['ten_sach'];
    $id_nxb = $_POST['id_nxb'];
    $id_the_loai = $_POST['id_the_loai'];
    $id_km = $_POST['id_km'];
    $mo_ta = $_POST['mo_ta'];
    $so_luong_ton = $_POST['so_luong_ton'];
    $gia_sach_ban = $_POST['gia_sach_ban'];
    $trang_thai_sach = $_POST['trang_thai_sach'];
    
    // Lấy mảng tác giả và ngôn ngữ (hoặc mảng rỗng nếu không chọn)
    $id_tac_gias = $_POST['id_tac_gia'] ?? []; 
    $id_ngon_ngus = $_POST['id_ngon_ngu'] ?? [];

    $pdo->beginTransaction();
    
    try {
        // 1. Cập nhật bảng `sach`
        $stmt_sach = $pdo->prepare("
            UPDATE sach 
            SET ten_sach = ?, id_nxb = ?, id_the_loai = ?, id_km = ?, mo_ta = ?, so_luong_ton = ?, trang_thai_sach = ?
            WHERE id_sach = ?
        ");
        $stmt_sach->execute([$ten_sach, $id_nxb, $id_the_loai, $id_km, $mo_ta, $so_luong_ton, $trang_thai_sach, $id_sach]);

        // 2. Cập nhật bảng `gia_sach`
        // Logic: Cập nhật giá của bản ghi giá gần đây nhất.
        // (Nếu muốn tạo lịch sử giá mới, bạn sẽ phải INSERT, nhưng edit thường là UPDATE)
        
        // 2a. Tìm ngày giờ của giá gần nhất
        $stmt_latest_price_date = $pdo->prepare("SELECT MAX(ngay_gio_ban) FROM gia_sach WHERE id_sach = ?");
        $stmt_latest_price_date->execute([$id_sach]);
        $latest_price_date = $stmt_latest_price_date->fetchColumn();

        if ($latest_price_date) {
            // 2b. Cập nhật giá tại ngày giờ đó
            $stmt_gia = $pdo->prepare("UPDATE gia_sach SET gia_sach_ban = ? WHERE id_sach = ? AND ngay_gio_ban = ?");
            $stmt_gia->execute([$gia_sach_ban, $id_sach, $latest_price_date]);
        }

        // 3. Cập nhật bảng `s_tg` (tác giả)
        // Xóa cũ -> Thêm mới: Cách đơn giản nhất để xử lý M-M
        $pdo->prepare("DELETE FROM s_tg WHERE id_sach = ?")->execute([$id_sach]);
        $stmt_tg = $pdo->prepare("INSERT INTO s_tg (id_sach, id_tac_gia) VALUES (?, ?)");
        foreach ($id_tac_gias as $id_tac_gia) {
            if (!empty($id_tac_gia)) {
                $stmt_tg->execute([$id_sach, $id_tac_gia]);
            }
        }
        
        // 4. Cập nhật bảng `s_nns` (ngôn ngữ)
        $pdo->prepare("DELETE FROM s_nns WHERE id_sach = ?")->execute([$id_sach]);
        $stmt_nn = $pdo->prepare("INSERT INTO s_nns (id_sach, id_ngon_ngu) VALUES (?, ?)");
        foreach ($id_ngon_ngus as $id_ngon_ngu) {
            if (!empty($id_ngon_ngu)) {
                $stmt_nn->execute([$id_sach, $id_ngon_ngu]);
            }
        }

        // Hoàn tất
        $pdo->commit();
        // Lưu thông báo thành công và tải lại trang để hiển thị
        $_SESSION['success_message'] = "Cập nhật sách (ID: $id_sach) thành công!";
        header('Location: edit.php?id=' . $id_sach);
        exit;
        
    } catch (Exception $e) {
        // Lỗi, rollback
        $pdo->rollBack();
        $error = "Lỗi khi cập nhật sách: " . $e->getMessage();
    }
}

// --- LẤY DỮ LIỆU ĐỂ HIỂN THỊ FORM ---

// 1. Lấy thông tin sách (và giá gần nhất)
$stmt_book = $pdo->prepare("
    SELECT s.*, g.gia_sach_ban
    FROM sach s
    LEFT JOIN gia_sach g ON s.id_sach = g.id_sach
    WHERE s.id_sach = ?
    ORDER BY g.ngay_gio_ban DESC 
    LIMIT 1
");
$stmt_book->execute([$id_sach]);
$book = $stmt_book->fetch();

if (!$book) {
    $_SESSION['error_message'] = "Không tìm thấy sách.";
    header('Location: index.php');
    exit;
}

// 2. Lấy các tác giả hiện tại của sách
$stmt_current_authors = $pdo->prepare("SELECT id_tac_gia FROM s_tg WHERE id_sach = ?");
$stmt_current_authors->execute([$id_sach]);
// fetchAll(PDO::FETCH_COLUMN) tạo một mảng [TG001, TG002]
$current_authors = $stmt_current_authors->fetchAll(PDO::FETCH_COLUMN, 0); 

// 3. Lấy các ngôn ngữ hiện tại của sách
$stmt_current_langs = $pdo->prepare("SELECT id_ngon_ngu FROM s_nns WHERE id_sach = ?");
$stmt_current_langs->execute([$id_sach]);
$current_languages = $stmt_current_langs->fetchAll(PDO::FETCH_COLUMN, 0);

// 4. Lấy dữ liệu cho các dropdown (Giống create.php)
// Lấy Thể Loại, nhóm theo Loại Sách
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

// Lấy thông báo flash (nếu có)
if (isset($_SESSION['success_message'])) {
    $success = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Chỉnh Sửa Sách</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Quản Lý Sách</a></li>
                        <li class="breadcrumb-item active">Chỉnh Sửa (<?php echo htmlspecialchars($id_sach); ?>)</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <div class="card card-warning"> <div class="card-header">
                    <h3 class="card-title">Thông Tin Sách: <?php echo htmlspecialchars($book['ten_sach']); ?></h3>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger m-3"><?php echo $error; ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success m-3"><?php echo $success; ?></div>
                <?php endif; ?>

                <form action="edit.php?id=<?php echo htmlspecialchars($id_sach); ?>" method="POST">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ten_sach">Tên Sách</label>
                                    <input type="text" class="form-control" name="ten_sach" placeholder="Tên sách" 
                                           value="<?php echo htmlspecialchars($book['ten_sach']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="mo_ta">Mô Tả</label>
                                    <textarea class="form-control" name="mo_ta" rows="5"><?php echo htmlspecialchars($book['mo_ta']); ?></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_the_loai">Thể Loại</label>
                                    <select class="form-control" name="id_the_loai" required>
                                        <option value="">-- Chọn thể loại --</option>
                                        <?php foreach ($genres_grouped as $category_name => $genres): ?>
                                            <optgroup label="<?php echo htmlspecialchars($category_name); ?>">
                                                <?php foreach ($genres as $genre): ?>
                                                    <option value="<?php echo $genre['id_the_loai']; ?>"
                                                        <?php if (isset($book['id_the_loai']) && $genre['id_the_loai'] == $book['id_the_loai']) echo 'selected'; ?>>
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
                                            <option value="<?php echo $item['id_nxb']; ?>"
                                                <?php if ($item['id_nxb'] == $book['id_nxb']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($item['ten_nxb']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_tac_gia">Tác Giả (Chọn nhiều)</label>
                                    <select class="form-control" name="id_tac_gia[]" multiple style="height: 150px;">
                                        <?php foreach ($tac_gia as $item): ?>
                                            <option value="<?php echo $item['id_tac_gia']; ?>"
                                                <?php if (in_array($item['id_tac_gia'], $current_authors)) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($item['ten_tac_gia']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều tác giả.</small>
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_ngon_ngu">Ngôn Ngữ (Chọn nhiều)</label>
                                    <select class="form-control" name="id_ngon_ngu[]" multiple style="height: 100px;">
                                        <?php foreach ($ngon_ngu as $item): ?>
                                            <option value="<?php echo $item['id_ngon_ngu']; ?>"
                                                <?php if (in_array($item['id_ngon_ngu'], $current_languages)) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($item['ngon_ngu']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <small class="form-text text-muted">Giữ Ctrl (hoặc Cmd trên Mac) để chọn nhiều ngôn ngữ.</small>
                                </div>

                                 <div class="form-group">
                                    <label for="id_km">Khuyến Mãi</label>
                                    <select class="form-control" name="id_km">
                                        <?php foreach ($khuyen_mai as $item): ?>
                                            <option value="<?php echo $item['id_km']; ?>"
                                                <?php if ($item['id_km'] == $book['id_km']) echo 'selected'; ?>>
                                                <?php echo htmlspecialchars($item['ten_km']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="gia_sach_ban">Giá Bán (VNĐ)</label>
                                            <input type="number" class="form-control" name="gia_sach_ban" 
                                                   value="<?php echo htmlspecialchars($book['gia_sach_ban']); ?>">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form-group">
                                            <label for="so_luong_ton">Số Lượng Tồn</label>
                                            <input type="number" class="form-control" name="so_luong_ton" 
                                                   value="<?php echo htmlspecialchars($book['so_luong_ton']); ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="trang_thai_sach">Trạng Thái</label>
                                    <select class="form-control" name="trang_thai_sach">
                                        <option value="1" <?php if ($book['trang_thai_sach'] == 1) echo 'selected'; ?>>Đang hiển thị</option>
                                        <option value="0" <?php if ($book['trang_thai_sach'] == 0) echo 'selected'; ?>>Đã ẩn (Xóa mềm)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-warning">Cập Nhật</button>
                        <a href="index.php" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>