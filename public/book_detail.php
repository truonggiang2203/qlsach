<?php
include_once '../includes/header.php';
require_once '../models/Book.php';
require_once '../models/Comment.php';
require_once '../models/Category.php';

$bookModel = new Book();
$commentModel = new Comment();
$categoryModel = new Category();

$id_sach = $_GET['id_sach'] ?? '';

if (!$id_sach) {
    echo "<p>Không tìm thấy sách!</p>";
    include_once '../includes/footer.php';
    exit;
}

$book = $bookModel->getBookById($id_sach);
if (!$book) {
    echo "<p>Sách không tồn tại!</p>";
    include_once '../includes/footer.php';
    exit;
}

$comments = $commentModel->getCommentsByBook($id_sach);
$avg_rating = $commentModel->getAverageRating($id_sach);
?>

<div class="container" style="max-width: 1100px; margin-top: 30px;">
    <div class="book-detail">
        <div class="book-image">
            <img src="https://via.placeholder.com/350x500?text=<?= urlencode($book->ten_sach) ?>" alt="<?= htmlspecialchars($book->ten_sach) ?>">
        </div>

        <div class="book-info">
            <h2><?= htmlspecialchars($book->ten_sach) ?></h2>
            <p><b>Thể loại:</b> <?= htmlspecialchars($book->ten_loai ?? 'Chưa phân loại') ?></p>
            <p><b>Nhà xuất bản:</b> <?= htmlspecialchars($book->ten_nxb ?? 'Không rõ') ?></p>
            <p><b>Tác giả:</b> <?= htmlspecialchars($book->ten_tac_gia ?? 'Không rõ') ?></p>
            <p><b>Mô tả:</b> <?= htmlspecialchars($book->mo_ta ?? 'Chưa có mô tả') ?></p>
            <p><b>Điểm đánh giá:</b> ⭐ <?= $avg_rating ?>/5</p>

            <div class="price-box">
                <span class="price">
                    <?= number_format($book->gia_sach_ban, 0, ',', '.') ?>đ
                </span>
                <?php if ($book->phan_tram_km > 0): ?>
                    <span class="discount">-<?= $book->phan_tram_km ?>%</span>
                <?php endif; ?>
            </div>

            <form action="../controllers/cartController.php?action=add&id_sach=<?= htmlspecialchars($book->id_sach) ?>" method="POST">
                <label for="so_luong">Số lượng:</label>
                <input type="number" id="so_luong" name="so_luong" value="1" min="1" style="width:60px;">
                <button type="submit" class="btn">🛒 Thêm vào giỏ</button>
            </form>
        </div>
    </div>

    <hr>

    <!-- Form bình luận -->
    <div class="comment-section">
        <h3>Đánh giá & Bình luận</h3>

        <?php if (isset($_SESSION['id_tk'])): ?>
        <form action="../controllers/commentController.php?action=add" method="POST" class="comment-form">
            <input type="hidden" name="id_sach" value="<?= htmlspecialchars($book->id_sach) ?>">
            <label>Chấm sao:</label>
            <select name="so_sao">
                <option value="5">5 ⭐</option>
                <option value="4">4 ⭐</option>
                <option value="3">3 ⭐</option>
                <option value="2">2 ⭐</option>
                <option value="1">1 ⭐</option>
            </select>
            <textarea name="binh_luan" placeholder="Nhập bình luận của bạn..." required></textarea>
            <button type="submit" class="btn">Gửi bình luận</button>
        </form>
        <?php else: ?>
            <p><a href="../guest/login.php">Đăng nhập</a> để bình luận.</p>
        <?php endif; ?>

        <!-- Danh sách bình luận -->
        <div class="comment-list">
            <?php if (!empty($comments)): ?>
                <?php foreach ($comments as $c): ?>
                    <div class="comment-item">
                        <b><?= htmlspecialchars($c->ho_ten ?? 'Người dùng ẩn') ?></b> 
                        <span> - <?= $c->so_sao ?> ⭐</span>
                        <p><?= htmlspecialchars($c->binh_luan) ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Chưa có bình luận nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
