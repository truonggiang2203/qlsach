<?php
include '../includes/header.php'; 
include '../includes/db.php';

// --- CẤU HÌNH PHÂN TRANG ---
$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

// 1. Đếm tổng số sách (chưa xóa)
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM sach WHERE trang_thai_sach = 1");
$stmt_count->execute();
$total_books = $stmt_count->fetchColumn();
$total_pages = ceil($total_books / $limit);

// 2. Lấy dữ liệu sách (Kết hợp bảng trung gian sach_theloai)
$sql = "
    SELECT 
        s.id_sach, 
        s.ten_sach, 
        s.so_luong_ton, 
        n.ten_nxb,
        
        -- Gộp danh sách Thể Loại
        GROUP_CONCAT(DISTINCT tl.ten_the_loai SEPARATOR ', ') AS danh_sach_the_loai,
        
        -- Gộp Tác Giả
        GROUP_CONCAT(DISTINCT tg.ten_tac_gia SEPARATOR ', ') AS danh_sach_tac_gia
        
    FROM sach s
    JOIN nxb n ON s.id_nxb = n.id_nxb
    
    -- Join Thể Loại
    LEFT JOIN sach_theloai stl ON s.id_sach = stl.id_sach
    LEFT JOIN the_loai tl ON stl.id_the_loai = tl.id_the_loai
    
    -- Join Tác Giả
    LEFT JOIN s_tg stg ON s.id_sach = stg.id_sach
    LEFT JOIN tac_gia tg ON stg.id_tac_gia = tg.id_tac_gia
    
    WHERE s.trang_thai_sach = 1 
    
    GROUP BY s.id_sach
    ORDER BY s.id_sach DESC
    LIMIT :limit OFFSET :offset
";

$stmt = $pdo->prepare($sql);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$books = $stmt->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Quản Lý Sách</h1></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="../dashboard.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Sách</li>
                    </ol>
                </div>
            </div>
        </div>
    </section>

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
                    <h3 class="card-title">Danh sách sách (<?php echo $total_books; ?> quyển)</h3>
                    <div class="card-tools">
                        <a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Thêm Mới</a>
                    </div>
                </div>
                
                <div class="card-body table-responsive p-0">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 80px;">ID</th>
                                <th style="width: 80px;">Ảnh</th>
                                <th style="width: 25%;">Tên Sách</th>
                                <th>Thể Loại</th>
                                <th>Tác Giả</th>
                                <th>NXB</th>
                                <th style="width: 80px;">Tồn</th>
                                <th style="width: 100px;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <?php
                                    // --- LOGIC ẢNH KHÔNG CẦN DB ---
                                    // Kiểm tra file Sxxxx.jpg có tồn tại không
                                    $img_path_rel = "../../public/uploads/" . $book['id_sach'] . ".jpg";
                                    // Thêm ?t=time() để tránh cache khi vừa sửa ảnh
                                    $img_src = file_exists($img_path_rel) ? $img_path_rel . "?t=" . time() : "https://via.placeholder.com/50x75?text=No+Img";
                                ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['id_sach']); ?></td>
                                    <td class="text-center">
                                        <img src="<?php echo $img_src; ?>" style="width: 50px; height: 75px; object-fit: cover; border: 1px solid #ddd;">
                                    </td>
                                    <td><?php echo htmlspecialchars($book['ten_sach']); ?></td>
                                    <td><?php echo htmlspecialchars($book['danh_sach_the_loai'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($book['danh_sach_tac_gia'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($book['ten_nxb']); ?></td>
                                    <td><?php echo htmlspecialchars($book['so_luong_ton']); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $book['id_sach']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                        <a href="delete.php?id=<?php echo $book['id_sach']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc muốn XÓA MỀM sách này?');"><i class="fas fa-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-right">
                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                    </ul>
                </div>
            </div>
        </div>
    </section>
</div>
<?php include '../includes/footer.php'; ?>