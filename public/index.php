<?php
include_once '../includes/header.php';
require_once '../models/Book.php';
require_once '../models/Category.php';

$bookModel = new Book();
$categoryModel = new Category();

$books = $bookModel->getAllBooks();
$categories = $categoryModel->getAllCategories();
?>

<div class="main-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h3>Danh mục sách</h3>
        <ul>
            <?php foreach ($categories as $cat): ?>
                <li>
                    <a href="../public/search.php?category=<?= htmlspecialchars($cat->id_loai) ?>">
                        <?= htmlspecialchars($cat->ten_loai) ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </aside>

    <!-- Main content -->
    <main class="content-area">
        <h2>Sách Mới Nổi Bật</h2>

        <div class="product-grid">
            <?php if (!empty($books)): ?>
                <?php foreach ($books as $book): ?>
                    <div class="product-item">
                        <!-- Ảnh minh họa (nếu có cột hình ảnh thật thì thay link dưới) -->
                        <img src="https://via.placeholder.com/250x350?text=<?= urlencode($book->ten_sach) ?>" alt="<?= htmlspecialchars($book->ten_sach) ?>">

                        <div class="product-info">
                            <h4>
                                <a href="book_detail.php?id_sach=<?= htmlspecialchars($book->id_sach) ?>">
                                    <?= htmlspecialchars($book->ten_sach) ?>
                                </a>
                            </h4>
                            <div class="product-price">
                                <?= number_format($book->gia_sach_ban, 0, ',', '.') ?>đ
                                <?php if ($book->phan_tram_km > 0): ?>
                                    <span class="discount">-<?= $book->phan_tram_km ?>%</span>
                                <?php endif; ?>
                            </div>

                            <!-- Nút thêm vào giỏ -->
                            <form action="../controllers/cartController.php?action=add" method="POST">
                                <input type="hidden" name="id_sach" value="<?= htmlspecialchars($book->id_sach) ?>">
                                <input type="hidden" name="so_luong" value="1">
                                <button type="submit" class="btn">🛒 Thêm vào giỏ</button>
                            </form>

                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Không có sách nào được hiển thị.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<?php include_once '../includes/footer.php'; ?>