<?php
include '../includes/header.php'; 
include '../includes/db.php';


$limit = 10; // Giới hạn số sách mỗi trang
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1; // Trang hiện tại (mặc định là 1)
$offset = ($page - 1) * $limit; // Vị trí bắt đầu lấy dữ liệu

// 1. Đếm tổng số sách (để tính tổng số trang)
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM sach WHERE trang_thai_sach = 1");
$stmt_count->execute();
$total_books = $stmt_count->fetchColumn();
$total_pages = ceil($total_books / $limit);

// 2. Lấy dữ liệu sách (Có LIMIT và OFFSET)
$sql = "
    SELECT 
        s.id_sach, 
        s.ten_sach, 
        s.so_luong_ton, 
        n.ten_nxb,
        
        GROUP_CONCAT(DISTINCT tl.ten_the_loai SEPARATOR ', ') AS danh_sach_the_loai,
        GROUP_CONCAT(DISTINCT ls.ten_loai SEPARATOR ', ') AS danh_sach_loai_sach,
        GROUP_CONCAT(DISTINCT tg.ten_tac_gia SEPARATOR ', ') AS danh_sach_tac_gia,
        GROUP_CONCAT(DISTINCT nn.ngon_ngu SEPARATOR ', ') AS danh_sach_ngon_ngu
        
    FROM sach s
    JOIN nxb n ON s.id_nxb = n.id_nxb
    LEFT JOIN sach_theloai stl ON s.id_sach = stl.id_sach
    LEFT JOIN the_loai tl ON stl.id_the_loai = tl.id_the_loai
    LEFT JOIN loai_sach ls ON tl.id_loai = ls.id_loai 
    LEFT JOIN s_tg stg ON s.id_sach = stg.id_sach
    LEFT JOIN tac_gia tg ON stg.id_tac_gia = tg.id_tac_gia
    LEFT JOIN s_nns snn ON s.id_sach = snn.id_sach
    LEFT JOIN ngon_ngu nn ON snn.id_ngon_ngu = nn.id_ngon_ngu
    
    WHERE s.trang_thai_sach = 1 
    
    GROUP BY s.id_sach
    ORDER BY s.id_sach DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
// Bind giá trị LIMIT và OFFSET (Bắt buộc dùng PDO::PARAM_INT)
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1 class="m-0">Quản Lý Sách</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Danh Sách Sách</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <?php
            if (isset($_SESSION['success_message'])) {
                echo '<div class="alert alert-success">' . $_SESSION['success_message'] . '</div>';
                unset($_SESSION['success_message']);
            }
            if (isset($_SESSION['error_message'])) {
                echo '<div class="alert alert-danger">' . $_SESSION['error_message'] . '</div>';
                unset($_SESSION['error_message']);
            }
            ?>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        Danh sách sách 
                        <span class="badge badge-info"><?php echo $total_books; ?> quyển</span>
                    </h3>
                    <div class="card-tools">
                        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Sách Mới</a>
                    </div>
                </div>
                
                <div class="card-body table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th style="width: 20%;">Tên Sách</th>
                                <th>Thể Loại</th>
                                <th>Tác Giả</th>
                                <th>NXB</th>
                                <th>Tồn</th>
                                <th style="width: 100px;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($books) > 0): ?>
                                <?php foreach ($books as $book): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($book['id_sach']); ?></td>
                                        <td><?php echo htmlspecialchars($book['ten_sach']); ?></td>
                                        <td><?php echo htmlspecialchars($book['danh_sach_the_loai'] ?? 'Chưa cập nhật'); ?></td>
                                        <td><?php echo htmlspecialchars($book['danh_sach_tac_gia'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($book['ten_nxb']); ?></td>
                                        <td><?php echo htmlspecialchars($book['so_luong_ton']); ?></td>
                                        <td>
                                            <a href="edit.php?id=<?php echo $book['id_sach']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                            <a href="delete.php?id=<?php echo $book['id_sach']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn xóa mềm sách này?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center">Không có dữ liệu</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
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
                    <div class="float-left" style="padding-top: 5px;">
                        Hiển thị trang <?php echo $page; ?> / <?php echo $total_pages; ?>
                    </div>
                </div>
                </div>
        </div>
    </section>
</div>
<?php include '../includes/footer.php'; ?>