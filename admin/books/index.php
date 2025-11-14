<?php
include '../includes/header.php'; 
include '../includes/db.php';


$stmt = $pdo->query("
    SELECT 
        s.id_sach, 
        s.ten_sach, 
        s.so_luong_ton, 
        n.ten_nxb, 
        tl.ten_the_loai,
        ls.ten_loai,
        
        -- Gộp nhiều tác giả, phân cách bằng dấu phẩy
        GROUP_CONCAT(DISTINCT tg.ten_tac_gia SEPARATOR ', ') AS danh_sach_tac_gia,
        
        -- Gộp nhiều ngôn ngữ, phân cách bằng dấu phẩy
        GROUP_CONCAT(DISTINCT nn.ngon_ngu SEPARATOR ', ') AS danh_sach_ngon_ngu
        
    FROM sach s
    
    -- Join cơ bản
    JOIN nxb n ON s.id_nxb = n.id_nxb
    LEFT JOIN the_loai tl ON s.id_the_loai = tl.id_the_loai
    LEFT JOIN loai_sach ls ON tl.id_loai = ls.id_loai 
    
    -- Join để lấy Tác Giả
    LEFT JOIN s_tg stg ON s.id_sach = stg.id_sach
    LEFT JOIN tac_gia tg ON stg.id_tac_gia = tg.id_tac_gia
    
    -- Join để lấy Ngôn Ngữ
    LEFT JOIN s_nns snn ON s.id_sach = snn.id_sach
    LEFT JOIN ngon_ngu nn ON snn.id_ngon_ngu = nn.id_ngon_ngu
    
    WHERE s.trang_thai_sach = 1 -- Chỉ hiển thị sách chưa bị xóa mềm
    
    GROUP BY s.id_sach -- Nhóm lại theo từng sách
    
    ORDER BY s.id_sach
");
$books = $stmt->fetchAll();
// --- KẾT THÚC THAY ĐỔI ---

?>

<?php include '../includes/sidebar.php'; ?>

<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Quản Lý Sách</h1>
                </div>
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
                    <h3 class="card-title">Tất cả sách (Đang hiển thị)</h3>
                    <div class="card-tools">
                        <a href="create.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Thêm Sách Mới
                        </a>
                    </div>
                </div>
                <div class="card-body table-responsive"> <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Tên Sách</th>
                                <th>Tác Giả</th> <th>Ngôn Ngữ</th> <th>Thể Loại</th>
                                <th>Nhà Xuất Bản</th>
                                <th style="width: 80px;">Tồn Kho</th>
                                <th style="width: 120px;">Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($books as $book): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($book['id_sach']); ?></td>
                                    <td><?php echo htmlspecialchars($book['ten_sach']); ?></td>
                                    
                                    <td><?php echo htmlspecialchars($book['danh_sach_tac_gia'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($book['danh_sach_ngon_ngu'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($book['ten_the_loai'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($book['ten_nxb']); ?></td>
                                    <td><?php echo htmlspecialchars($book['so_luong_ton']); ?></td>
                                    <td>
                                        <a href="edit.php?id=<?php echo $book['id_sach']; ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <a href="delete.php?id=<?php echo $book['id_sach']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn XÓA MỀM (ẩn) sách này?');">
                                            <i class="fas fa-trash"></i> Xóa
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include '../includes/footer.php'; // Gọi footer ?>