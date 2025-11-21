<?php
include '../includes/header.php';
include '../includes/db.php';

// --- CẤU HÌNH ---
$limit = 15;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;
$search = $_GET['search'] ?? '';

// --- XÂY DỰNG QUERY ---
$where = "WHERE s.trang_thai_sach = 1";
$params = [];

if (!empty($search)) {
    $where .= " AND (s.ten_sach LIKE :search OR s.id_sach LIKE :search)";
    $params[':search'] = "%$search%";
}

// 1. Đếm tổng (cho phân trang)
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM sach s $where");
$stmt_count->execute($params);
$total_records = $stmt_count->fetchColumn();
$total_pages = ceil($total_records / $limit);

// 2. Lấy dữ liệu
$sql = "
    SELECT 
        s.id_sach, 
        s.ten_sach, 
        s.so_luong_ton, 
        n.ten_nxb,
        g.gia_sach_ban  -- <--- ĐÃ SỬA: Lấy từ bảng 'g' (gia_sach) thay vì 's' (sach)
    FROM sach s
    JOIN nxb n ON s.id_nxb = n.id_nxb
    
    -- Join bảng giá để lấy giá mới nhất
    LEFT JOIN gia_sach g ON s.id_sach = g.id_sach 
        AND g.tg_gia_bd = (SELECT MAX(tg_gia_bd) FROM gia_sach WHERE id_sach = s.id_sach)
    
    $where
    ORDER BY s.so_luong_ton ASC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) $stmt->bindValue($key, $val);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$inventory = $stmt->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Quản Lý Tồn Kho</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Tồn Kho</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Báo cáo tồn kho</h3>
                    <div class="card-tools">
                        <form action="" method="GET">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <input type="text" name="search" class="form-control float-right" placeholder="Tìm tên hoặc mã sách..." value="<?php echo htmlspecialchars($search); ?>">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover text-nowrap">
                        <thead>
                            <tr>
                                <th>Mã Sách</th>
                                <th>Tên Sách</th>
                                <th>Nhà Xuất Bản</th>
                                <th>Giá Bán (Hiện tại)</th>
                                <th class="text-center">Số Lượng Tồn</th>
                                <th>Trạng Thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($inventory as $item): ?>
                                <?php 
                                    $qty = $item['so_luong_ton'];
                                    $badge = 'success';
                                    $status_text = 'Còn hàng';
                                    
                                    if ($qty == 0) {
                                        $badge = 'danger';
                                        $status_text = 'Hết hàng';
                                    } elseif ($qty <= 10) {
                                        $badge = 'warning';
                                        $status_text = 'Sắp hết';
                                    }
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['id_sach']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($item['ten_sach']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['ten_nxb']); ?></td>
                                    <td><?php echo number_format($item['gia_sach_ban'] ?? 0, 0, ',', '.'); ?> đ</td>
                                    <td class="text-center">
                                        <span class="text-bold" style="font-size: 1.2em;"><?php echo $qty; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $badge; ?>"><?php echo $status_text; ?></span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <div class="float-left">
                        <small>Tổng số: <?php echo $total_records; ?> đầu sách</small>
                    </div>
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php for($i=1; $i<=$total_pages; $i++): ?>
                            <li class="page-item <?php echo $page==$i?'active':''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>