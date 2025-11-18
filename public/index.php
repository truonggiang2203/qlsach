<?php
include_once '../includes/header.php';
require_once '../models/Book.php';
require_once '../models/Category.php';
require_once '../models/Wishlist.php';

$bookModel = new Book();
$categoryModel = new Category();
$wishlistModel = new Wishlist();

$books = $bookModel->getAllBooks();
$categories = $categoryModel->getAllParentCategories();

// Lấy danh sách wishlist của user (nếu đã đăng nhập)
$userWishlist = [];
if (isset($_SESSION['id_tk'])) {
    $wishlistItems = $wishlistModel->getUserWishlist($_SESSION['id_tk']);
    foreach ($wishlistItems as $item) {
        $userWishlist[$item->id_sach] = true;
    }
}

// Helper function để lấy đường dẫn hình ảnh
function getBookImagePath($id_sach) {
    $imagePath = "/qlsach/public/uploads/" . $id_sach . ".jpg";
    $fullPath = __DIR__ . "/uploads/" . $id_sach . ".jpg";
    if (file_exists($fullPath)) {
        return $imagePath;
    }
    return "/qlsach/public/uploads/default-book.png";
}
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
                <?php foreach ($books as $book): 
                    $isWishlisted = isset($userWishlist[$book->id_sach]);
                    $discountedPrice = $book->gia_sach_ban * (1 - ($book->phan_tram_km ?? 0) / 100);
                ?>
                    <div class="product-item">
                        <!-- Wishlist Button -->
                        <?php if (isset($_SESSION['id_tk'])): ?>
                            <a href="#" 
                               class="product-item-wishlist-btn <?= $isWishlisted ? 'active' : '' ?>"
                               data-book-id="<?= $book->id_sach ?>"
                               title="<?= $isWishlisted ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="<?= $isWishlisted ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </a>
                        <?php else: ?>
                            <a href="/qlsach/guest/login.php" 
                               class="product-item-wishlist-btn"
                               title="Đăng nhập để thêm vào yêu thích">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </a>
                        <?php endif; ?>
                        
                        <!-- Ảnh sách -->
                        <a href="book_detail.php?id_sach=<?= htmlspecialchars($book->id_sach) ?>">
                            <img src="<?= getBookImagePath($book->id_sach) ?>" 
                                 alt="<?= htmlspecialchars($book->ten_sach) ?>">
                        </a>

                        <div class="product-info">
                            <h4>
                                <a href="book_detail.php?id_sach=<?= htmlspecialchars($book->id_sach) ?>">
                                    <?= htmlspecialchars($book->ten_sach) ?>
                                </a>
                            </h4>
                            <div class="product-price">
                                <?php if ($book->phan_tram_km > 0): ?>
                                    <?= number_format($discountedPrice, 0, ',', '.') ?>đ
                                    <span class="discount">-<?= $book->phan_tram_km ?>%</span>
                                <?php else: ?>
                                    <?= number_format($book->gia_sach_ban, 0, ',', '.') ?>đ
                                <?php endif; ?>
                            </div>

                            <!-- Actions -->
                            <div class="product-actions">
                                <form action="../controllers/cartController.php?action=add" method="POST" style="flex: 1;">
                                    <input type="hidden" name="id_sach" value="<?= htmlspecialchars($book->id_sach) ?>">
                                    <input type="hidden" name="so_luong" value="1">
                                    <button type="submit" class="btn">🛒 Thêm vào giỏ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Không có sách nào được hiển thị.</p>
            <?php endif; ?>
        </div>
    </main>
</div>

<script src="/qlsach/public/js/wishlist.js"></script>
<?php if (isset($_SESSION['id_tk'])): ?>
    <script>
        const userId = '<?= $_SESSION['id_tk'] ?>';
    </script>
<?php endif; ?>
<?php include_once '../includes/footer.php'; ?>