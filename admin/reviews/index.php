<?php
include '../includes/header.php';
include '../includes/db.php';

// --- CẤU HÌNH PHÂN TRANG ---
$limit = 10; // Số bình luận mỗi trang
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// 1. Đếm tổng số bình luận
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM binh_luan");
$stmt_count->execute();
$total_reviews = $stmt_count->fetchColumn();
$total_pages = ceil($total_reviews / $limit);

// 2. Lấy dữ liệu bình luận (JOIN với Sách và Tài khoản)
$sql = "
    SELECT 
        bl.id_bl,
        bl.binh_luan,
        bl.so_sao,
        bl.ngay_gio_tao,
        
        s.id_sach,
        s.ten_sach,
        
        tk.ho_ten,
        tk.email
        
    FROM binh_luan bl
    JOIN sach s ON bl.id_sach = s.id_sach
    LEFT JOIN tai_khoan tk ON bl.id_tk = tk.id_tk
    
    ORDER BY bl.ngay_gio_tao DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$reviews = $stmt->fetchAll();

// Hàm hiển thị sao
function renderStars($rating) {
    $stars = '';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $stars .= '<i class="fas fa-star text-warning"></i>'; // Sao đầy
        } elseif ($i - 0.5 == $rating) {
            $stars .= '<i class="fas fa-star-half-alt text-warning"></i>'; // Nửa sao (nếu có logic .5)
        } else {
            $stars .= '<i class="far fa-star text-secondary"></i>'; // Sao rỗng
        }
    }
    return $stars;
}
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quản Lý Bình Luận & Đánh Giá</h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Bình Luận</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Danh sách đánh giá từ khách hàng</h3>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover table-striped">
                        <thead>
                            <tr>
                                <th style="width: 150px;">Người Dùng</th>
                                <th style="width: 250px;">Sách</th>
                                <th style="width: 120px;">Đánh Giá</th>
                                <th>Nội Dung Bình Luận</th>
                                <th style="width: 150px;">Thời Gian</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($reviews) > 0): ?>
                                <?php foreach ($reviews as $row): ?>
                                    <tr>
                                        <td>
                                            <?php if ($row['ho_ten']): ?>
                                                <strong><?php echo htmlspecialchars($row['ho_ten']); ?></strong><br>
                                                <small class="text-muted"><?php echo htmlspecialchars($row['email']); ?></small>
                                            <?php else: ?>
                                                <span class="text-muted font-italic">Người dùng ẩn danh (hoặc đã xóa)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a href="../books/edit.php?id=<?php echo $row['id_sach']; ?>" target="_blank">
                                                <?php echo htmlspecialchars($row['ten_sach']); ?>
                                            </a>
                                            <br>
                                            <small class="text-muted">ID: <?php echo $row['id_sach']; ?></small>
                                        </td>
                                        <td>
                                            <div style="font-size: 0.85rem;">
                                                <?php echo renderStars($row['so_sao']); ?>
                                            </div>
                                            <small class="font-weight-bold"><?php echo $row['so_sao']; ?>/5</small>
                                        </td>
                                        <td>
                                            <p class="mb-0" style="white-space: pre-wrap;"><?php echo htmlspecialchars($row['binh_luan']); ?></p>
                                        </td>
                                        <td>
                                            <?php echo date('d/m/Y', strtotime($row['ngay_gio_tao'])); ?><br>
                                            <small><?php echo date('H:i', strtotime($row['ngay_gio_tao'])); ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">Chưa có bình luận nào.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <div class="float-left" style="padding-top: 5px;">
                        <small>Hiển thị trang <?php echo $page; ?> / <?php echo $total_pages; ?> (Tổng: <?php echo $total_reviews; ?> bình luận)</small>
                    </div>
                    <ul class="pagination pagination-sm m-0 float-right">
                        <li class="page-item <?php echo ($page <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo ($page > 1) ? "?page=" . ($page - 1) : '#'; ?>">«</a>
                        </li>
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        <li class="page-item <?php echo ($page >= $total_pages) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="<?php echo ($page < $total_pages) ? "?page=" . ($page + 1) : '#'; ?>">»</a>
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; ?>