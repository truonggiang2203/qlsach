<?php
include '../includes/header.php';
include '../includes/db.php';

$error = '';
$success = '';
$edit_mode = false;
$item_to_edit = null;

// Hằng số ID đặc biệt không được sửa/xóa
define('DEFAULT_PROMOTION_ID', 'KM000');

// --- XỬ LÝ POST (Thêm mới hoặc Cập nhật) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $action_type = $_POST['action_type'];
    
    // Lấy dữ liệu
    $ten_km = $_POST['ten_km'];
    $phan_tram_km = $_POST['phan_tram_km'];
    $ngay_bat_dau = $_POST['ngay_bat_dau'];
    $ngay_ket_thuc = $_POST['ngay_ket_thuc'];
    $trang_thai_km = $_POST['trang_thai_km'];

    try {
        $pdo->beginTransaction();

        if ($action_type == 'add') {
            // 1. Lấy ID cuối cùng (bỏ qua KM000)
            $stmt_id = $pdo->query("SELECT id_km FROM khuyen_mai WHERE id_km <> '" . DEFAULT_PROMOTION_ID . "' ORDER BY id_km DESC LIMIT 1 FOR UPDATE");
            $last_item = $stmt_id->fetch();
            $new_id_num = 1;
            if ($last_item) {
                // Lấy phần số từ ID (ví dụ KM001 -> 1)
                $new_id_num = (int) substr($last_item['id_km'], 2) + 1;
            }
            // Format: KM001 (KM + 3 chữ số)
            $id_km = 'KM' . str_pad($new_id_num, 3, '0', STR_PAD_LEFT); 

            // 2. Thêm mới
            $stmt = $pdo->prepare("
                INSERT INTO khuyen_mai (id_km, ten_km, phan_tram_km, ngay_bat_dau, ngay_ket_thuc, trang_thai_km) 
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$id_km, $ten_km, $phan_tram_km, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai_km]);
            $success = "Thêm khuyến mãi '$ten_km' (ID: $id_km) thành công!";

        } elseif ($action_type == 'edit') {
            $id_km = $_POST['id_km'];
            // Không cho phép sửa KM000
            if ($id_km == DEFAULT_PROMOTION_ID) {
                throw new Exception("Không thể chỉnh sửa mục mặc định của hệ thống.");
            }
            
            $stmt = $pdo->prepare("
                UPDATE khuyen_mai 
                SET ten_km = ?, phan_tram_km = ?, ngay_bat_dau = ?, ngay_ket_thuc = ?, trang_thai_km = ? 
                WHERE id_km = ?
            ");
            $stmt->execute([$ten_km, $phan_tram_km, $ngay_bat_dau, $ngay_ket_thuc, $trang_thai_km, $id_km]);
            $success = "Cập nhật khuyến mãi thành công!";
        }
        
        $pdo->commit();

    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Đã xảy ra lỗi: " . $e->getMessage();
    }
}

// --- XỬ LÝ GET (Sửa hoặc Xóa) ---
if (isset($_GET['action'])) {
    $action = $_GET['action'];
    $id_km = $_GET['id'];

    // Không cho phép Sửa hoặc Xóa KM000
    if ($id_km == DEFAULT_PROMOTION_ID) {
        $error = "Không thể Sửa hoặc Xóa mục khuyến mãi mặc định của hệ thống.";
    } else {
        if ($action == 'edit') {
            $edit_mode = true;
            $stmt = $pdo->prepare("SELECT * FROM khuyen_mai WHERE id_km = ?");
            $stmt->execute([$id_km]);
            $item_to_edit = $stmt->fetch();
        }

        if ($action == 'delete') {
            try {
                // Kiểm tra ràng buộc: KM này có đang được gán cho sách nào không?
                $stmt_check = $pdo->prepare("SELECT COUNT(*) FROM sach WHERE id_km = ?");
                $stmt_check->execute([$id_km]);
                if ($stmt_check->fetchColumn() > 0) {
                    $error = "Không thể xóa! Vẫn còn sách đang áp dụng khuyến mãi này.";
                } else {
                    $stmt = $pdo->prepare("DELETE FROM khuyen_mai WHERE id_km = ?");
                    $stmt->execute([$id_km]);
                    $success = "Xóa khuyến mãi thành công!";
                }
            } catch (Exception $e) {
                $error = "Lỗi khi xóa: " . $e->getMessage();
            }
        }
    }
}

// --- Lấy danh sách tất cả ---
$all_items = $pdo->query("SELECT * FROM khuyen_mai ORDER BY id_km")->fetchAll();

// Hàm trợ giúp định dạng ngày giờ
function format_datetime_for_input($datetime_str) {
    if (empty($datetime_str)) return '';
    return (new DateTime($datetime_str))->format('Y-m-d\TH:i');
}

function format_datetime_for_display($datetime_str) {
    if (empty($datetime_str)) return '';
    return (new DateTime($datetime_str))->format('d/m/Y H:i');
}
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quản Lý Khuyến Mãi</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Khuyến Mãi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="card <?php echo $edit_mode ? 'card-warning' : 'card-primary'; ?>">
                        <div class="card-header">
                            <h3 class="card-title"><?php echo $edit_mode ? 'Chỉnh Sửa Khuyến Mãi' : 'Thêm Khuyến Mãi Mới'; ?></h3>
                        </div>
                        <form action="index.php" method="POST">
                            <div class="card-body">
                                <input type="hidden" name="action_type" value="<?php echo $edit_mode ? 'edit' : 'add'; ?>">
                                <?php if ($edit_mode): ?>
                                    <input type="hidden" name="id_km" value="<?php echo $item_to_edit['id_km']; ?>">
                                <?php endif; ?>
                                
                                <div class="form-group">
                                    <label for="ten_km">Tên Khuyến Mãi</label>
                                    <input type="text" class="form-control" name="ten_km" 
                                           value="<?php echo $edit_mode ? htmlspecialchars($item_to_edit['ten_km']) : ''; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="phan_tram_km">Phần Trăm (%)</label>
                                    <input type="number" class="form-control" name="phan_tram_km" step="0.01" min="0" max="100"
                                           value="<?php echo $edit_mode ? htmlspecialchars($item_to_edit['phan_tram_km']) : '0.00'; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="ngay_bat_dau">Ngày Bắt Đầu</label>
                                    <input type="datetime-local" class="form-control" name="ngay_bat_dau" 
                                           value="<?php echo $edit_mode ? format_datetime_for_input($item_to_edit['ngay_bat_dau']) : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="ngay_ket_thuc">Ngày Kết Thúc</label>
                                    <input type="datetime-local" class="form-control" name="ngay_ket_thuc" 
                                           value="<?php echo $edit_mode ? format_datetime_for_input($item_to_edit['ngay_ket_thuc']) : ''; ?>" required>
                                </div>

                                <div class="form-group">
                                    <label for="trang_thai_km">Trạng Thái</label>
                                    <select class="form-control" name="trang_thai_km">
                                        <option value="active" <?php echo ($edit_mode && $item_to_edit['trang_thai_km'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo ($edit_mode && $item_to_edit['trang_thai_km'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>

                            </div>
                            <div class="card-footer">
                                <button type="submit" class="btn <?php echo $edit_mode ? 'btn-warning' : 'btn-primary'; ?>">
                                    <?php echo $edit_mode ? 'Cập nhật' : 'Thêm Mới'; ?>
                                </button>
                                <?php if ($edit_mode): ?>
                                    <a href="index.php" class="btn btn-secondary">Hủy</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Danh Sách Khuyến Mãi</h3>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 80px;">ID</th>
                                        <th>Tên Khuyến Mãi</th>
                                        <th style="width: 80px;">% KM</th>
                                        <th>Bắt Đầu</th>
                                        <th>Kết Thúc</th>
                                        <th style="width: 100px;">Trạng Thái</th>
                                        <th style="width: 120px;">Thao Tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($all_items as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['id_km']); ?></td>
                                        <td><?php echo htmlspecialchars($item['ten_km']); ?></td>
                                        <td><?php echo htmlspecialchars($item['phan_tram_km']); ?>%</td>
                                        <td><?php echo format_datetime_for_display($item['ngay_bat_dau']); ?></td>
                                        <td><?php echo format_datetime_for_display($item['ngay_ket_thuc']); ?></td>
                                        <td>
                                            <?php if ($item['trang_thai_km'] == 'active'): ?>
                                                <span class="badge bg-success">Active</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">Inactive</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($item['id_km'] == DEFAULT_PROMOTION_ID): ?>
                                                <span class_ ="text-muted">Hệ thống</span>
                                            <?php else: ?>
                                                <a href="index.php?action=edit&id=<?php echo $item['id_km']; ?>" class="btn btn-info btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="index.php?action=delete&id=<?php echo $item['id_km']; ?>" class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Bạn có chắc chắn muốn xóa?');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            <?php endif; ?>
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