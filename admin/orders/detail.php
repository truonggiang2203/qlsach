<?php
include '../includes/header.php';
include '../includes/db.php';

// 1. Kiểm tra ID đơn hàng
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Không tìm thấy mã đơn hàng.";
    header('Location: index.php');
    exit;
}

$id_don_hang = $_GET['id'];
$error = '';
$success = '';

// --- XỬ LÝ POST (CẬP NHẬT TRẠNG THÁI) ---
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_status = $_POST['id_trang_thai'];
    $payment_status = $_POST['trang_thai_tt'];

    try {
        $pdo->beginTransaction();

        // 1. Cập nhật trạng thái đơn hàng
        $stmt_update_dh = $pdo->prepare("UPDATE don_hang SET id_trang_thai = ? WHERE id_don_hang = ?");
        $stmt_update_dh->execute([$new_status, $id_don_hang]);

        // 2. Cập nhật trạng thái thanh toán
        // Kiểm tra xem đơn hàng đã có record trong bảng thanh_toan chưa
        $check_tt = $pdo->prepare("SELECT id_don_hang FROM thanh_toan WHERE id_don_hang = ?");
        $check_tt->execute([$id_don_hang]);
        
        if ($check_tt->rowCount() > 0) {
            // Đã có -> Update
            $stmt_update_tt = $pdo->prepare("UPDATE thanh_toan SET trang_thai_tt = ? WHERE id_don_hang = ?");
            $stmt_update_tt->execute([$payment_status, $id_don_hang]);
        } else {
            // Chưa có (lỗi dữ liệu cũ?) -> Insert mặc định (ví dụ COD)
            $now = date('Y-m-d H:i:s');
            $stmt_insert_tt = $pdo->prepare("INSERT INTO thanh_toan (id_pttt, id_don_hang, trang_thai_tt, ngay_gio_thanh_toan) VALUES ('PT001', ?, ?, ?)");
            $stmt_insert_tt->execute([$id_don_hang, $payment_status, $now]);
        }

        $pdo->commit();
        $success = "Cập nhật đơn hàng thành công!";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Lỗi: " . $e->getMessage();
    }
}

// --- LẤY DỮ LIỆU HIỂN THỊ ---

// 1. Lấy thông tin chung đơn hàng
$sql_order = "
    SELECT 
        dh.*, 
        tk.ho_ten, tk.email, tk.sdt,
        tt.trang_thai_dh,
        pttt.ten_pttt,
        COALESCE(ttoan.trang_thai_tt, 0) as trang_thai_thanh_toan
    FROM don_hang dh
    JOIN tai_khoan tk ON dh.id_tk = tk.id_tk
    JOIN trang_thai_don_hang tt ON dh.id_trang_thai = tt.id_trang_thai
    LEFT JOIN thanh_toan ttoan ON dh.id_don_hang = ttoan.id_don_hang
    LEFT JOIN phuong_thuc_thanh_toan pttt ON ttoan.id_pttt = pttt.id_pttt
    WHERE dh.id_don_hang = ?
";
$stmt_order = $pdo->prepare($sql_order);
$stmt_order->execute([$id_don_hang]);
$order = $stmt_order->fetch();

if (!$order) {
    $_SESSION['error_message'] = "Đơn hàng không tồn tại.";
    header('Location: index.php');
    exit;
}

// 2. Lấy chi tiết sản phẩm (sách)
$sql_items = "
    SELECT 
        ct.*, 
        s.ten_sach, 
        s.id_sach
    FROM chi_tiet_don_hang ct
    JOIN sach s ON ct.id_sach = s.id_sach
    WHERE ct.id_don_hang = ?
";
$stmt_items = $pdo->prepare($sql_items);
$stmt_items->execute([$id_don_hang]);
$items = $stmt_items->fetchAll();

// 3. Lấy danh sách các trạng thái đơn hàng để tạo dropdown
$statuses = $pdo->query("SELECT * FROM trang_thai_don_hang")->fetchAll();

// Tính tổng tiền
$total_money = 0;
foreach ($items as $item) {
    $total_money += ($item['so_luong_ban'] * $item['don_gia_ban']);
}

?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Chi Tiết Đơn Hàng: #<?php echo htmlspecialchars($order['id_don_hang']); ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="index.php">Đơn Hàng</a></li>
                        <li class="breadcrumb-item active">Chi Tiết</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fas fa-book-reader"></i> Quản Lý Sách.
                            <small class="float-right">Ngày đặt: <?php echo date('d/m/Y H:i', strtotime($order['ngay_gio_tao_don'])); ?></small>
                        </h4>
                    </div>
                </div>
                
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        Thông tin khách hàng
                        <address>
                            <strong><?php echo htmlspecialchars($order['ho_ten']); ?></strong><br>
                            Email: <?php echo htmlspecialchars($order['email']); ?><br>
                            SĐT: <?php echo htmlspecialchars($order['sdt']); ?><br>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        Địa chỉ giao hàng
                        <address>
                            <strong><?php echo htmlspecialchars($order['dia_chi_nhan_hang']); ?></strong><br>
                        </address>
                    </div>
                    <div class="col-sm-4 invoice-col">
                        <b>Mã đơn: #<?php echo htmlspecialchars($order['id_don_hang']); ?></b><br>
                        <b>Trạng thái đơn:</b> 
                        <span class="badge badge-info"><?php echo htmlspecialchars($order['trang_thai_dh']); ?></span><br>
                        <b>Thanh toán:</b> 
                        <?php if($order['trang_thai_thanh_toan'] == 1): ?>
                            <span class="badge badge-success">Đã thanh toán</span>
                        <?php else: ?>
                            <span class="badge badge-warning">Chưa thanh toán</span>
                        <?php endif; ?>
                        <br>
                        <b>Phương thức:</b> <?php echo htmlspecialchars($order['ten_pttt'] ?? 'COD'); ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Sách</th>
                                    <th>Đơn Giá</th>
                                    <th>Số Lượng</th>
                                    <th>Thành Tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $stt = 1;
                                foreach ($items as $item): 
                                    $subtotal = $item['so_luong_ban'] * $item['don_gia_ban'];
                                ?>
                                <tr>
                                    <td><?php echo $stt++; ?></td>
                                    <td><?php echo htmlspecialchars($item['ten_sach']); ?> (ID: <?php echo $item['id_sach']; ?>)</td>
                                    <td><?php echo number_format($item['don_gia_ban'], 0, ',', '.'); ?> đ</td>
                                    <td><?php echo $item['so_luong_ban']; ?></td>
                                    <td><?php echo number_format($subtotal, 0, ',', '.'); ?> đ</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <p class="lead">Cập Nhật Trạng Thái:</p>
                        
                        <form action="" method="POST">
                            <div class="form-group">
                                <label>Trạng Thái Đơn Hàng:</label>
                                <select class="form-control" name="id_trang_thai">
                                    <?php foreach ($statuses as $st): ?>
                                        <option value="<?php echo $st['id_trang_thai']; ?>" 
                                            <?php echo ($st['id_trang_thai'] == $order['id_trang_thai']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($st['trang_thai_dh']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label>Trạng Thái Thanh Toán:</label>
                                <select class="form-control" name="trang_thai_tt">
                                    <option value="0" <?php echo ($order['trang_thai_thanh_toan'] == 0) ? 'selected' : ''; ?>>Chưa thanh toán</option>
                                    <option value="1" <?php echo ($order['trang_thai_thanh_toan'] == 1) ? 'selected' : ''; ?>>Đã thanh toán</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success float-left">
                                <i class="far fa-save"></i> Lưu Thay Đổi
                            </button>
                        </form>
                    </div>
                    <div class="col-6">
                        <p class="lead">Tổng Kết</p>

                        <div class="table-responsive">
                            <table class="table">
                                <tr>
                                    <th style="width:50%">Tạm tính:</th>
                                    <td><?php echo number_format($total_money, 0, ',', '.'); ?> đ</td>
                                </tr>
                                <tr>
                                    <th>Phí vận chuyển:</th>
                                    <td>0 đ</td> </tr>
                                <tr>
                                    <th>Tổng Tiền:</th>
                                    <td class="text-danger font-weight-bold" style="font-size: 1.2em;">
                                        <?php echo number_format($total_money, 0, ',', '.'); ?> đ
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row no-print mt-3">
                    <div class="col-12">
                        <a href="javascript:window.print()" class="btn btn-default"><i class="fas fa-print"></i> In Hóa Đơn</a>
                        <a href="index.php" class="btn btn-secondary float-right"><i class="fas fa-arrow-left"></i> Quay Lại Danh Sách</a>
                    </div>
                </div>
            </div>
            </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>