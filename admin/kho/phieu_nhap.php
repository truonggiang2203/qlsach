<?php
include '../includes/header.php';
include '../includes/db.php';

// Các biến dùng chung
$error = '';
$success = '';
$action = $_GET['action'] ?? 'index'; // Mặc định là trang danh sách

// --- 1. XỬ LÝ POST: TẠO PHIẾU NHẬP MỚI ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'create') {
    $id_ncc = $_POST['id_ncc'];
    $vat_percent = (int)$_POST['vat_percent']; // Ví dụ: 10 (%)
    
    // Mảng dữ liệu chi tiết
    $book_ids = $_POST['book_id'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $prices = $_POST['price'] ?? [];

    if (empty($book_ids)) {
        $error = "Vui lòng chọn ít nhất một quyển sách để nhập.";
    } else {
        try {
            $pdo->beginTransaction();

            // A. Tạo ID Phiếu Nhập tự động (PNxxx)
            $stmt_id = $pdo->query("SELECT id_phieu_nhap FROM phieu_nhap ORDER BY id_phieu_nhap DESC LIMIT 1 FOR UPDATE");
            $last_item = $stmt_id->fetch();
            $new_num = 1;
            if ($last_item) {
                $new_num = (int) substr($last_item['id_phieu_nhap'], 2) + 1;
            }
            $id_phieu_nhap = 'PN' . str_pad($new_num, 3, '0', STR_PAD_LEFT);

            // B. Tính toán tổng tiền
            $tong_tien_nhap = 0;
            foreach ($book_ids as $index => $bid) {
                $qty = (int)$quantities[$index];
                $price = (int)$prices[$index];
                $tong_tien_nhap += ($qty * $price);
            }
            
            $vat_amount = $tong_tien_nhap * ($vat_percent / 100);
            $tong_gia_tri = $tong_tien_nhap + $vat_amount;
            $ngay_lap = date('Y-m-d H:i:s');

            // C. Insert bảng `phieu_nhap`
            $stmt_pn = $pdo->prepare("
                INSERT INTO phieu_nhap (id_phieu_nhap, id_ncc, ngay_lap_phieu_nhap, tong_tien_nhap, VAT, tong_gia_tri_phieu_nhap)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt_pn->execute([$id_phieu_nhap, $id_ncc, $ngay_lap, $tong_tien_nhap, $vat_amount, $tong_gia_tri]);

            // D. Insert chi tiết & Cập nhật tồn kho
            $stmt_detail = $pdo->prepare("INSERT INTO chi_tiet_phieu_nhap (id_phieu_nhap, id_sach, so_luong_nhap, don_gia_nhap) VALUES (?, ?, ?, ?)");
            $stmt_stock = $pdo->prepare("UPDATE sach SET so_luong_ton = so_luong_ton + ? WHERE id_sach = ?");

            foreach ($book_ids as $index => $bid) {
                $qty = (int)$quantities[$index];
                $price = (int)$prices[$index];

                if ($qty > 0) {
                    // Lưu chi tiết
                    $stmt_detail->execute([$id_phieu_nhap, $bid, $qty, $price]);
                    
                    // Tăng tồn kho
                    $stmt_stock->execute([$qty, $bid]);
                }
            }

            $pdo->commit();
            $_SESSION['success_message'] = "Tạo phiếu nhập $id_phieu_nhap thành công! Kho đã được cập nhật.";
            header('Location: phieu_nhap.php');
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Lỗi: " . $e->getMessage();
        }
    }
}

// --- LẤY DỮ LIỆU CHO CÁC TRANG ---

// 1. Danh sách NCC (cho form tạo và filter)
$suppliers = $pdo->query("SELECT * FROM nha_cung_cap WHERE trang_thai_ncc = 'active'")->fetchAll();

// 2. Nếu đang ở trang 'create': Lấy danh sách Sách
if ($action == 'create') {
    $books = $pdo->query("SELECT id_sach, ten_sach, so_luong_ton FROM sach WHERE trang_thai_sach = 1")->fetchAll();
}

// 3. Nếu đang ở trang 'view': Lấy chi tiết phiếu nhập
$view_data = null;
$view_details = [];
if ($action == 'view' && isset($_GET['id'])) {
    $id_view = $_GET['id'];
    // Thông tin chung
    $stmt_v = $pdo->prepare("
        SELECT pn.*, ncc.ten_ncc 
        FROM phieu_nhap pn 
        JOIN nha_cung_cap ncc ON pn.id_ncc = ncc.id_ncc 
        WHERE pn.id_phieu_nhap = ?
    ");
    $stmt_v->execute([$id_view]);
    $view_data = $stmt_v->fetch();

    // Chi tiết sách
    $stmt_vd = $pdo->prepare("
        SELECT ct.*, s.ten_sach 
        FROM chi_tiet_phieu_nhap ct 
        JOIN sach s ON ct.id_sach = s.id_sach 
        WHERE ct.id_phieu_nhap = ?
    ");
    $stmt_vd->execute([$id_view]);
    $view_details = $stmt_vd->fetchAll();
}

// 4. Trang Index: Lấy danh sách phiếu nhập (Phân trang)
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

if ($action == 'index') {
    $stmt_count = $pdo->query("SELECT COUNT(*) FROM phieu_nhap");
    $total_records = $stmt_count->fetchColumn();
    $total_pages = ceil($total_records / $limit);

    $stmt_list = $pdo->prepare("
        SELECT pn.*, ncc.ten_ncc 
        FROM phieu_nhap pn 
        JOIN nha_cung_cap ncc ON pn.id_ncc = ncc.id_ncc 
        ORDER BY pn.ngay_lap_phieu_nhap DESC 
        LIMIT :limit OFFSET :offset
    ");
    $stmt_list->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt_list->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt_list->execute();
    $list_phieu_nhap = $stmt_list->fetchAll();
}
?>

<?php include '../includes/sidebar.php'; ?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@ttskch/select2-bootstrap4-theme@x.x.x/dist/select2-bootstrap4.min.css">

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <?php if ($action == 'create'): ?>
                        <h1>Tạo Phiếu Nhập Kho</h1>
                    <?php elseif ($action == 'view'): ?>
                        <h1>Chi Tiết Phiếu Nhập: <?php echo htmlspecialchars($id_view); ?></h1>
                    <?php else: ?>
                        <h1>Quản Lý Phiếu Nhập</h1>
                    <?php endif; ?>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="phieu_nhap.php">Phiếu Nhập</a></li>
                        <?php if ($action != 'index'): ?>
                            <li class="breadcrumb-item active"><?php echo ucfirst($action); ?></li>
                        <?php endif; ?>
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
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <?php if ($action == 'index'): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Lịch sử nhập hàng</h3>
                        <div class="card-tools">
                            <a href="phieu_nhap.php?action=create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Nhập Hàng Mới
                            </a>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover text-nowrap">
                            <thead>
                                <tr>
                                    <th>Mã PN</th>
                                    <th>Nhà Cung Cấp</th>
                                    <th>Ngày Nhập</th>
                                    <th>Tổng Tiền Hàng</th>
                                    <th>VAT</th>
                                    <th>Tổng Thanh Toán</th>
                                    <th>Thao Tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list_phieu_nhap as $pn): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($pn['id_phieu_nhap']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($pn['ten_ncc']); ?></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pn['ngay_lap_phieu_nhap'])); ?></td>
                                    <td><?php echo number_format($pn['tong_tien_nhap'], 0, ',', '.'); ?> đ</td>
                                    <td><?php echo number_format($pn['VAT'], 0, ',', '.'); ?> đ</td>
                                    <td class="text-success font-weight-bold"><?php echo number_format($pn['tong_gia_tri_phieu_nhap'], 0, ',', '.'); ?> đ</td>
                                    <td>
                                        <a href="phieu_nhap.php?action=view&id=<?php echo $pn['id_phieu_nhap']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Xem
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer clearfix">
                        <ul class="pagination pagination-sm m-0 float-right">
                            <?php for($i=1; $i<=$total_pages; $i++): ?>
                                <li class="page-item <?php echo $page==$i?'active':''; ?>"><a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
                            <?php endfor; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($action == 'create'): ?>
                <form method="POST" action="phieu_nhap.php?action=create" id="formNhapHang">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card card-primary">
                                <div class="card-header"><h3 class="card-title">Thông Tin Chung</h3></div>
                                <div class="card-body">
                                    <div class="form-group">
                                        <label>Nhà Cung Cấp <span class="text-danger">*</span></label>
                                        <select class="form-control select2" name="id_ncc" required style="width: 100%;">
                                            <option value="">-- Chọn NCC --</option>
                                            <?php foreach ($suppliers as $sup): ?>
                                                <option value="<?php echo $sup['id_ncc']; ?>"><?php echo htmlspecialchars($sup['ten_ncc']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Thuế VAT (%)</label>
                                        <input type="number" class="form-control" name="vat_percent" id="vat_percent" value="0" min="0" max="100">
                                    </div>
                                    <div class="callout callout-info">
                                        <p>Ngày nhập: <strong><?php echo date('d/m/Y'); ?></strong></p>
                                        <p>Người nhập: <strong><?php echo $_SESSION['ho_ten']; ?></strong></p>
                                    </div>
                                    <button type="submit" class="btn btn-success btn-block btn-lg">
                                        <i class="fas fa-save"></i> Lưu Phiếu Nhập
                                    </button>
                                    <a href="phieu_nhap.php" class="btn btn-secondary btn-block">Hủy bỏ</a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h3 class="card-title">Chi Tiết Hàng Nhập</h3>
                                    <div class="card-tools">
                                        <button type="button" class="btn btn-info btn-sm" id="btnAddRow">
                                            <i class="fas fa-plus"></i> Thêm Dòng
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body table-responsive p-0" style="height: 500px;">
                                    <table class="table table-head-fixed text-nowrap" id="tblChiTiet">
                                        <thead>
                                            <tr>
                                                <th style="width: 40%">Sách</th>
                                                <th style="width: 15%">Tồn Hiện Tại</th>
                                                <th style="width: 15%">SL Nhập</th>
                                                <th style="width: 20%">Đơn Giá Nhập</th>
                                                <th style="width: 10%">Xóa</th>
                                            </tr>
                                        </thead>
                                        <tbody id="tableBody">
                                            </tbody>
                                    </table>
                                </div>
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-6"></div>
                                        <div class="col-6 text-right">
                                            <h5>Tổng tiền hàng: <span id="spanTongTien" class="text-primary">0</span> đ</h5>
                                            <h5>Tiền VAT: <span id="spanVAT" class="text-warning">0</span> đ</h5>
                                            <h3>Tổng cộng: <span id="spanTongCong" class="text-success">0</span> đ</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            <?php endif; ?>

            <?php if ($action == 'view' && $view_data): ?>
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4><i class="fas fa-file-invoice"></i> Phiếu Nhập Kho: <?php echo $view_data['id_phieu_nhap']; ?>
                                <small class="float-right">Ngày: <?php echo date('d/m/Y H:i', strtotime($view_data['ngay_lap_phieu_nhap'])); ?></small>
                            </h4>
                        </div>
                    </div>
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            Từ Nhà Cung Cấp
                            <address><strong><?php echo htmlspecialchars($view_data['ten_ncc']); ?></strong></address>
                        </div>
                        <div class="col-sm-4 invoice-col">
                            <b>Mã PN:</b> <?php echo $view_data['id_phieu_nhap']; ?><br>
                            <b>Tổng giá trị:</b> <?php echo number_format($view_data['tong_gia_tri_phieu_nhap'], 0, ',', '.'); ?> đ
                        </div>
                    </div>
                    <div class="row mt-4">
                        <div class="col-12 table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Sách</th>
                                        <th>Số Lượng Nhập</th>
                                        <th>Đơn Giá Nhập</th>
                                        <th>Thành Tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($view_details as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['ten_sach']); ?> (<?php echo $item['id_sach']; ?>)</td>
                                        <td><?php echo $item['so_luong_nhap']; ?></td>
                                        <td><?php echo number_format($item['don_gia_nhap'], 0, ',', '.'); ?> đ</td>
                                        <td><?php echo number_format($item['so_luong_nhap'] * $item['don_gia_nhap'], 0, ',', '.'); ?> đ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6"></div>
                        <div class="col-6">
                            <div class="table-responsive">
                                <table class="table">
                                    <tr><th style="width:50%">Tổng tiền hàng:</th><td><?php echo number_format($view_data['tong_tien_nhap'], 0, ',', '.'); ?> đ</td></tr>
                                    <tr><th>VAT:</th><td><?php echo number_format($view_data['VAT'], 0, ',', '.'); ?> đ</td></tr>
                                    <tr><th>Tổng cộng:</th><td class="text-danger font-weight-bold"><?php echo number_format($view_data['tong_gia_tri_phieu_nhap'], 0, ',', '.'); ?> đ</td></tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row no-print mt-3">
                        <div class="col-12">
                            <a href="phieu_nhap.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
                            <button onclick="window.print()" class="btn btn-default float-right"><i class="fas fa-print"></i> In Phiếu</button>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>

<?php if ($action == 'create'): ?>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Khởi tạo Select2 cho NCC
    $('.select2').select2({ theme: 'bootstrap4' });

    // Dữ liệu sách từ PHP sang JS
    const booksData = <?php echo json_encode($books); ?>;

    // Hàm thêm dòng mới
    function addRow() {
        let html = `
            <tr>
                <td>
                    <select class="form-control select2-book" name="book_id[]" required style="width: 100%;">
                        <option value="">-- Chọn sách --</option>
                        ${booksData.map(b => `<option value="${b.id_sach}" data-stock="${b.so_luong_ton}">${b.ten_sach} (Tồn: ${b.so_luong_ton})</option>`).join('')}
                    </select>
                </td>
                <td><input type="text" class="form-control txt-stock" readonly></td>
                <td><input type="number" class="form-control txt-qty" name="quantity[]" min="1" value="1" required></td>
                <td><input type="number" class="form-control txt-price" name="price[]" min="0" value="0" required></td>
                <td><button type="button" class="btn btn-danger btn-sm btn-remove"><i class="fas fa-trash"></i></button></td>
            </tr>
        `;
        $('#tableBody').append(html);
        
        // Khởi tạo select2 cho dòng mới
        $('.select2-book').last().select2({ theme: 'bootstrap4' });
    }

    // Thêm dòng đầu tiên mặc định
    addRow();

    // Sự kiện nút Thêm Dòng
    $('#btnAddRow').click(function() { addRow(); });

    // Sự kiện nút Xóa Dòng
    $(document).on('click', '.btn-remove', function() {
        $(this).closest('tr').remove();
        calculateTotal();
    });

    // Sự kiện khi chọn sách -> Hiện tồn kho
    $(document).on('change', '.select2-book', function() {
        let stock = $(this).find(':selected').data('stock');
        $(this).closest('tr').find('.txt-stock').val(stock);
    });

    // Sự kiện thay đổi số lượng, giá hoặc VAT -> Tính lại tiền
    $(document).on('input', '.txt-qty, .txt-price', function() { calculateTotal(); });
    $('#vat_percent').on('input', function() { calculateTotal(); });

    function calculateTotal() {
        let total = 0;
        $('#tableBody tr').each(function() {
            let qty = parseFloat($(this).find('.txt-qty').val()) || 0;
            let price = parseFloat($(this).find('.txt-price').val()) || 0;
            total += (qty * price);
        });

        let vatPercent = parseFloat($('#vat_percent').val()) || 0;
        let vatAmount = total * (vatPercent / 100);
        let grandTotal = total + vatAmount;

        // Format tiền tệ VNĐ
        const fmt = new Intl.NumberFormat('vi-VN');
        $('#spanTongTien').text(fmt.format(total));
        $('#spanVAT').text(fmt.format(vatAmount));
        $('#spanTongCong').text(fmt.format(grandTotal));
    }
});
</script>
<?php endif; ?>