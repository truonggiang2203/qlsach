<?php
include_once '../includes/header.php';
require_once '../models/Wishlist.php';
require_once '../models/Book.php';

if (!isset($_SESSION['id_tk'])) {
    header("Location: /qlsach/guest/login.php");
    exit;
}

$wishlistModel = new Wishlist();
$bookModel = new Book();

$id_tk = $_SESSION['id_tk'];
$wishlistItems = $wishlistModel->getUserWishlist($id_tk);
?>

<div class="container" style="margin-top: 30px;">
    <h2>❤️ Danh sách yêu thích</h2>
    <hr>

    <?php if (empty($wishlistItems)): ?>
        <p>Bạn chưa yêu thích sách nào.</p>
        <a href="/qlsach/public/index.php" class="btn-primary">Tiếp tục xem sách</a>
    <?php else: ?>

        <div class="book-grid">

            <?php foreach ($wishlistItems as $book): ?>
                <div class="book-card">

                    <a href="/qlsach/public/book_detail.php?id_sach=<?= $book->id_sach ?>">
                        <img src="https://via.placeholder.com/200x250?text=<?= urlencode($book->ten_sach) ?>" 
                             alt="<?= htmlspecialchars($book->ten_sach) ?>">
                    </a>

                    <h3><?= htmlspecialchars($book->ten_sach) ?></h3>

                    <p><?= number_format($book->gia_sach_ban) ?> đ</p>

                    <a href="/qlsach/controllers/wishlistController.php?action=remove&id_sach=<?= $book->id_sach ?>"
                       class="btn-danger"
                       style="margin-top:5px; display:inline-block;">
                        Xóa khỏi ❤️
                    </a>
                </div>
            <?php endforeach; ?>

        </div>

    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>
