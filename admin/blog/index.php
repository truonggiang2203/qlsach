<?php
include '../includes/header.php';
include '../includes/db.php';

$limit = 10;
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$total = $pdo->query("SELECT COUNT(*) FROM bai_viet")->fetchColumn();
$pages = ceil($total / $limit);

$sql = "
    SELECT bv.*, dm.ten_danh_muc, tk.ho_ten as tac_gia
    FROM bai_viet bv
    JOIN danh_muc_bai_viet dm ON bv.id_danh_muc = dm.id_danh_muc
    JOIN tai_khoan tk ON bv.id_tk = tk.id_tk
    ORDER BY bv.ngay_tao DESC
    LIMIT $limit OFFSET $offset
";
$posts = $pdo->query($sql)->fetchAll();
?>

<?php include '../includes/sidebar.php'; ?>
<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6"><h1>Tất Cả Bài Viết</h1></div>
                <div class="col-sm-6 text-right"><a href="create.php" class="btn btn-primary"><i class="fas fa-plus"></i> Viết bài mới</a></div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="card">
                <div class="card-body table-responsive p-0">
                    <table class="table table-hover">
                        <thead><tr><th>ID</th><th>Hình</th><th>Tiêu đề</th><th>Danh mục</th><th>Tác giả</th><th>Trạng thái</th><th>Thao tác</th></tr></thead>
                        <tbody>
                            <?php foreach ($posts as $p): ?>
                            <tr>
                                <td><?php echo $p['id_bai_viet']; ?></td>
                                <td>
                                    <?php if($p['anh_dai_dien']): ?>
                                        <img src="../../public/uploads/posts/<?php echo $p['anh_dai_dien']; ?>" width="50">
                                    <?php else: ?>
                                        <span class="text-muted">No img</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($p['tieu_de']); ?></td>
                                <td><?php echo htmlspecialchars($p['ten_danh_muc']); ?></td>
                                <td><?php echo htmlspecialchars($p['tac_gia']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $p['trang_thai']=='published'?'success':($p['trang_thai']=='draft'?'warning':'secondary'); ?>">
                                        <?php echo ucfirst($p['trang_thai']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="edit.php?id=<?php echo $p['id_bai_viet']; ?>" class="btn btn-info btn-sm"><i class="fas fa-edit"></i></a>
                                    <a href="delete.php?id=<?php echo $p['id_bai_viet']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Xóa bài viết này?');"><i class="fas fa-trash"></i></a>
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
<?php include '../includes/footer.php'; ?>