<?php
require_once '../includes/header.php';
require_once '../models/Author.php';
require_once '../models/Book.php';
require_once '../models/Wishlist.php';
require_once '../models/Comment.php';

$authorModel = new Author();
$bookModel = new Book();
$wishlistModel = new Wishlist();
$commentModel = new Comment();

// Lấy ID tác giả từ URL
$id_tac_gia = $_GET['id'] ?? '';

if (empty($id_tac_gia)) {
    header("Location: authors.php");
    exit;
}

// Lấy thông tin tác giả
$author = $authorModel->getAuthorById($id_tac_gia);

if (!$author) {
    header("Location: authors.php");
    exit;
}

// Lấy danh sách sách của tác giả
$books = $authorModel->getBooksByAuthor($id_tac_gia);
$bookCount = $authorModel->countBooksByAuthor($id_tac_gia);

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
    $exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    foreach ($exts as $ext) {
        $full = __DIR__ . "/uploads/{$id_sach}.{$ext}";
        if (file_exists($full)) {
            return "uploads/{$id_sach}.{$ext}";
        }
    }
    return "uploads/default-book.png";
}

// Render stars HTML from average rating
function renderStars($avg) {
    $full = floor($avg);
    $half = (($avg - $full) >= 0.5) ? 1 : 0;
    $empty = 5 - $full - $half;
    $html = '<div class="product-rating" aria-hidden="true">';
    for ($i=0;$i<$full;$i++) $html .= '<span class="star star-full">★</span>';
    if ($half) $html .= '<span class="star star-half">★</span>';
    for ($i=0;$i<$empty;$i++) $html .= '<span class="star star-empty">☆</span>';
    $html .= '</div>';
    return $html;
}

$baseUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
?>

<link rel="stylesheet" href="<?= $baseUrl ?>/css/author.css">

<div class="author-page">
    <!-- Author Hero Section -->
    <section class="author-hero">
        <div class="author-hero-content">
            <div class="author-avatar">
                <span class="avatar-icon">✍️</span>
            </div>
            <div class="author-info">
                <p class="author-label">Tác giả</p>
                <h1><?= htmlspecialchars($author->ten_tac_gia) ?></h1>
                <div class="author-stats">
                    <span><strong><?= $bookCount ?></strong> tác phẩm</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Author Bio Section -->
    <section class="author-bio">
        <div class="bio-card">
            <h2>Giới thiệu về tác giả</h2>
            
            <?php if (!empty($author->tieu_su)): ?>
                <p class="bio-text"><?= nl2br(htmlspecialchars($author->tieu_su)) ?></p>
            <?php else: ?>
                <p class="bio-text">
                    <?= htmlspecialchars($author->ten_tac_gia) ?> là một trong những tác giả nổi tiếng với nhiều tác phẩm được yêu thích. 
                    Các tác phẩm của tác giả đã để lại dấu ấn sâu đậm trong lòng độc giả.
                </p>
                <p class="bio-note">
                    <em>* Thông tin chi tiết về tiểu sử tác giả sẽ được cập nhật trong thời gian tới.</em>
                </p>
            <?php endif; ?>

            <!-- Thông tin bổ sung -->
            <div class="author-details">
                <?php if (!empty($author->ngay_sinh)): ?>
                    <div class="detail-item">
                        <strong>Ngày sinh:</strong>
                        <span><?= date('d/m/Y', strtotime($author->ngay_sinh)) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($author->quoc_tich)): ?>
                    <div class="detail-item">
                        <strong>Quốc tịch:</strong>
                        <span><?= htmlspecialchars($author->quoc_tich) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($author->giai_thuong)): ?>
                    <div class="detail-item">
                        <strong>Giải thưởng:</strong>
                        <span><?= htmlspecialchars($author->giai_thuong) ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($author->tac_pham_noi_bat)): ?>
                    <div class="detail-item">
                        <strong>Tác phẩm nổi bật:</strong>
                        <span><?= htmlspecialchars($author->tac_pham_noi_bat) ?></span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Social Links -->
            <?php if (!empty($author->website) || !empty($author->facebook) || !empty($author->twitter) || !empty($author->instagram)): ?>
                <div class="author-social">
                    <strong>Liên kết:</strong>
                    <div class="social-links">
                        <?php if (!empty($author->website)): ?>
                            <a href="<?= htmlspecialchars($author->website) ?>" target="_blank" rel="noopener" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"></circle>
                                    <line x1="2" y1="12" x2="22" y2="12"></line>
                                    <path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path>
                                </svg>
                                Website
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($author->facebook)): ?>
                            <a href="<?= htmlspecialchars($author->facebook) ?>" target="_blank" rel="noopener" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                Facebook
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($author->twitter)): ?>
                            <a href="<?= htmlspecialchars($author->twitter) ?>" target="_blank" rel="noopener" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                </svg>
                                Twitter
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($author->instagram)): ?>
                            <a href="<?= htmlspecialchars($author->instagram) ?>" target="_blank" rel="noopener" class="social-link">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                </svg>
                                Instagram
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Books Section -->
    <section class="author-books">
        <div class="section-header">
            <h2>Tác phẩm của <?= htmlspecialchars($author->ten_tac_gia) ?></h2>
            <p>Tìm thấy <?= $bookCount ?> cuốn sách</p>
        </div>

        <?php if (empty($books)): ?>
            <div class="empty-state">
                <p>Hiện tại chưa có tác phẩm nào của tác giả này trong hệ thống.</p>
                <a href="/qlsach/public/index.php" class="btn-primary">Khám phá sách khác</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($books as $book):
                    $isWishlisted = isset($userWishlist[$book->id_sach]);
                    $discountedPrice = $book->gia_sach_ban * (1 - ($book->phan_tram_km ?? 0) / 100);
                ?>
                    <div class="product-item">
                        <?php if (isset($_SESSION['id_tk'])): ?>
                            <a href="#" class="product-item-wishlist-btn <?= $isWishlisted ? 'active' : '' ?>" data-book-id="<?= $book->id_sach ?>" title="<?= $isWishlisted ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="<?= $isWishlisted ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </a>
                        <?php else: ?>
                            <a href="/qlsach/guest/login.php" class="product-item-wishlist-btn" title="Đăng nhập để thêm vào yêu thích">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                </svg>
                            </a>
                        <?php endif; ?>

                        <a href="book_detail.php?id_sach=<?= htmlspecialchars($book->id_sach) ?>">
                            <img src="<?= $baseUrl . '/' . getBookImagePath($book->id_sach) ?>" alt="<?= htmlspecialchars($book->ten_sach) ?>">
                        </a>

                        <div class="product-info">
                            <h4><a href="book_detail.php?id_sach=<?= htmlspecialchars($book->id_sach) ?>"><?= htmlspecialchars($book->ten_sach) ?></a></h4>
                            <div class="product-price">
                                <?php if (!empty($book->phan_tram_km) && $book->phan_tram_km > 0): ?>
                                    <?= number_format($discountedPrice, 0, ',', '.') ?>đ
                                    <span class="discount">-<?= $book->phan_tram_km ?>%</span>
                                <?php else: ?>
                                    <?= number_format($book->gia_sach_ban, 0, ',', '.') ?>đ
                                <?php endif; ?>
                            </div>
                            <?php
                                $rating = $commentModel->getAverageRating($book->id_sach);
                            ?>
                            <div class="product-rating-block">
                                <?= renderStars($rating['average']) ?>
                                <?php if ($rating['count'] > 0): ?>
                                    <span class="rating-number"><?= $rating['average'] ?></span>
                                    <span class="rating-count">(<?= $rating['count'] ?>)</span>
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <form action="../controllers/cartController.php?action=add" method="POST" style="flex:1;">
                                    <input type="hidden" name="id_sach" value="<?= htmlspecialchars($book->id_sach) ?>">
                                    <input type="hidden" name="so_luong" value="1">
                                    <button type="submit" class="btn">🛒 Thêm vào giỏ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<script src="/qlsach/public/js/wishlist.js"></script>
<?php if (isset($_SESSION['id_tk'])): ?>
    <script>
        const userId = '<?= $_SESSION['id_tk'] ?>';
    </script>
<?php endif; ?>

<?php include_once '../includes/footer.php'; ?>
