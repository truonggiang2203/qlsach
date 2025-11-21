<?php

include '../includes/header.php';
include '../includes/db.php';

if (!isset($_GET['id'])) header('Location: index.php');
$id_sach = $_GET['id'];

// --- XỬ LÝ LƯU DỮ LIỆU ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ten_sach = $_POST['ten_sach'];
    $id_nxb = $_POST['id_nxb'];
    $id_km = $_POST['id_km'];
    $mo_ta = $_POST['mo_ta'];
    $so_luong_ton = $_POST['so_luong_ton'];
    $gia_sach_ban = $_POST['gia_sach_ban'];
    $trang_thai_sach = $_POST['trang_thai_sach'];
    
    $id_the_loais = $_POST['id_the_loai'] ?? [];
    $id_tac_gias = $_POST['id_tac_gia'] ?? [];
    $id_ngon_ngus = $_POST['id_ngon_ngu'] ?? [];
    $id_nccs = $_POST['id_ncc'] ?? [];

    $pdo->beginTransaction();
    try {
        // 1. Update bảng Sach (Bỏ cột id_the_loai)
        $stmt = $pdo->prepare("UPDATE sach SET ten_sach=?, id_nxb=?, id_km=?, mo_ta=?, so_luong_ton=?, trang_thai_sach=? WHERE id_sach=?");
        $stmt->execute([$ten_sach, $id_nxb, $id_km, $mo_ta, $so_luong_ton, $trang_thai_sach, $id_sach]);

        // 2. Update Giá (Cập nhật giá gần nhất)
        $stmt_date = $pdo->prepare("SELECT tg_gia_bd FROM gia_sach WHERE id_sach=? ORDER BY tg_gia_bd DESC LIMIT 1");
        $stmt_date->execute([$id_sach]);
        $last_date = $stmt_date->fetchColumn();
        if($last_date) {
            $pdo->prepare("UPDATE gia_sach SET gia_sach_ban=? WHERE id_sach=? AND tg_gia_bd=?")->execute([$gia_sach_ban, $id_sach, $last_date]);
        }

        // 3. Update Thể Loại (Xóa cũ -> Thêm mới)
        $pdo->prepare("DELETE FROM sach_theloai WHERE id_sach=?")->execute([$id_sach]);
        $stmt_stl = $pdo->prepare("INSERT INTO sach_theloai (id_sach, id_the_loai) VALUES (?, ?)");
        foreach($id_the_loais as $val) if(!empty($val)) $stmt_stl->execute([$id_sach, $val]);

        // 4. Update Tác Giả
        $pdo->prepare("DELETE FROM s_tg WHERE id_sach=?")->execute([$id_sach]);
        $stmt_tg = $pdo->prepare("INSERT INTO s_tg (id_sach, id_tac_gia) VALUES (?, ?)");
        foreach($id_tac_gias as $val) if(!empty($val)) $stmt_tg->execute([$id_sach, $val]);

        // 5. Update Ngôn Ngữ
        $pdo->prepare("DELETE FROM s_nns WHERE id_sach=?")->execute([$id_sach]);
        $stmt_nn = $pdo->prepare("INSERT INTO s_nns (id_sach, id_ngon_ngu) VALUES (?, ?)");
        foreach($id_ngon_ngus as $val) if(!empty($val)) $stmt_nn->execute([$id_sach, $val]);
        
        // 6. Update NCC
        $pdo->prepare("DELETE FROM s_ncc WHERE id_sach=?")->execute([$id_sach]);
        $stmt_ncc = $pdo->prepare("INSERT INTO s_ncc (id_sach, id_ncc) VALUES (?, ?)");
        foreach($id_nccs as $val) if(!empty($val)) $stmt_ncc->execute([$id_sach, $val]);

        $pdo->commit();
        $_SESSION['success_message'] = "Cập nhật sách thành công!";
        header("Location: edit.php?id=$id_sach");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}

// --- LẤY DỮ LIỆU HIỂN THỊ ---
// Lấy thông tin sách + giá
$book = $pdo->prepare("SELECT s.*, g.gia_sach_ban FROM sach s LEFT JOIN gia_sach g ON s.id_sach = g.id_sach WHERE s.id_sach=? ORDER BY g.tg_gia_bd DESC LIMIT 1");
$book->execute([$id_sach]);
$book = $book->fetch();

// Lấy các mảng ID đã chọn
$cur_genres = $pdo->prepare("SELECT id_the_loai FROM sach_theloai WHERE id_sach=?"); 
$cur_genres->execute([$id_sach]); 
$cur_genres = $cur_genres->fetchAll(PDO::FETCH_COLUMN);

$cur_authors = $pdo->prepare("SELECT id_tac_gia FROM s_tg WHERE id_sach=?");
$cur_authors->execute([$id_sach]);
$cur_authors = $cur_authors->fetchAll(PDO::FETCH_COLUMN);

$cur_langs = $pdo->prepare("SELECT id_ngon_ngu FROM s_nns WHERE id_sach=?");
$cur_langs->execute([$id_sach]);
$cur_langs = $cur_langs->fetchAll(PDO::FETCH_COLUMN);

$cur_nccs = $pdo->prepare("SELECT id_ncc FROM s_ncc WHERE id_sach=?");
$cur_nccs->execute([$id_sach]);
$cur_nccs = $cur_nccs->fetchAll(PDO::FETCH_COLUMN);

// Lấy dữ liệu dropdown (giống create.php)
$stmt_genres = $pdo->query("SELECT ls.ten_loai, tl.id_the_loai, tl.ten_the_loai FROM the_loai tl JOIN loai_sach ls ON tl.id_loai = ls.id_loai ORDER BY ls.ten_loai, tl.ten_the_loai");
$genres_grouped = [];
foreach ($stmt_genres->fetchAll() as $g) $genres_grouped[$g['ten_loai']][] = $g;

$nxb = $pdo->query("SELECT * FROM nxb")->fetchAll();
$tac_gia = $pdo->query("SELECT * FROM tac_gia")->fetchAll();
$ngon_ngu = $pdo->query("SELECT * FROM ngon_ngu")->fetchAll();
$khuyen_mai = $pdo->query("SELECT * FROM khuyen_mai")->fetchAll();
$ncc = $pdo->query("SELECT * FROM nha_cung_cap")->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>
<div class="content-wrapper">
    <section class="content-header"><h1>Sửa Sách: <?php echo htmlspecialchars($book['ten_sach']); ?></h1></section>
    <section class="content">
        <div class="container-fluid">
            <?php if (isset($_SESSION['success_message'])) { echo "<div class='alert alert-success'>{$_SESSION['success_message']}</div>"; unset($_SESSION['success_message']); } ?>
            
            <form method="POST">
                <div class="card card-warning">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group"><label>Tên Sách</label><input type="text" class="form-control" name="ten_sach" value="<?php echo htmlspecialchars($book['ten_sach']); ?>" required></div>
                                <div class="form-group">
                                    <label>Thể Loại</label>
                                    <select class="form-control" name="id_the_loai[]" multiple required style="height: 150px;">
                                        <?php foreach ($genres_grouped as $cat => $genres): ?>
                                            <optgroup label="<?php echo htmlspecialchars($cat); ?>">
                                                <?php foreach ($genres as $g): ?>
                                                    <option value="<?php echo $g['id_the_loai']; ?>" <?php echo in_array($g['id_the_loai'], $cur_genres) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($g['ten_the_loai']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="form-group"><label>NXB</label><select class="form-control" name="id_nxb"><?php foreach ($nxb as $i) echo "<option value='{$i['id_nxb']}' " . ($i['id_nxb']==$book['id_nxb']?'selected':'') . ">{$i['ten_nxb']}</option>"; ?></select></div>
                                <div class="form-group"><label>Mô tả</label><textarea class="form-control" name="mo_ta" rows="3"><?php echo htmlspecialchars($book['mo_ta']); ?></textarea></div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><label>Tác Giả</label><select class="form-control" name="id_tac_gia[]" multiple style="height: 100px;"><?php foreach ($tac_gia as $i) echo "<option value='{$i['id_tac_gia']}' " . (in_array($i['id_tac_gia'], $cur_authors)?'selected':'') . ">{$i['ten_tac_gia']}</option>"; ?></select></div>
                                <div class="form-group"><label>Ngôn Ngữ</label><select class="form-control" name="id_ngon_ngu[]" multiple style="height: 80px;"><?php foreach ($ngon_ngu as $i) echo "<option value='{$i['id_ngon_ngu']}' " . (in_array($i['id_ngon_ngu'], $cur_langs)?'selected':'') . ">{$i['ngon_ngu']}</option>"; ?></select></div>
                                <div class="form-group"><label>Nhà Cung Cấp</label><select class="form-control" name="id_ncc[]" multiple style="height: 80px;"><?php foreach ($ncc as $i) echo "<option value='{$i['id_ncc']}' " . (in_array($i['id_ncc'], $cur_nccs)?'selected':'') . ">{$i['ten_ncc']}</option>"; ?></select></div>
                                <div class="form-group"><label>Khuyến Mãi</label><select class="form-control" name="id_km"><?php foreach ($khuyen_mai as $i) echo "<option value='{$i['id_km']}' " . ($i['id_km']==$book['id_km']?'selected':'') . ">{$i['ten_km']}</option>"; ?></select></div>
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