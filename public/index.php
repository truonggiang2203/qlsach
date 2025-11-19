<?php
include_once '../includes/header.php';
require_once '../models/Book.php';
require_once '../models/Category.php';
require_once '../models/Wishlist.php';
require_once '../models/Comment.php';

$bookModel = new Book();
$categoryModel = new Category();
$wishlistModel = new Wishlist();
$commentModel = new Comment();

$books = $bookModel->getAllBooks();
$categories = $categoryModel->getAllParentCategories();

// Các tập sách phục vụ trang chủ
$newBooks = array_slice($books, 0, 8); // sách mới / mặc định lấy top 8 từ danh sách
$bestsellers = $bookModel->getBestsellerBooks(8);
$recommended = $bookModel->getRecommendedBooks($_SESSION['id_tk'] ?? null, 8);

// Sách đang khuyến mãi
$onSaleBooks = array_filter($books, function($b) { return (!empty($b->phan_tram_km) && $b->phan_tram_km > 0); });
$onSaleBooks = array_values($onSaleBooks);
$onSaleBooks = array_slice($onSaleBooks, 0, 8);

// Helper: tìm file uploads với nhiều phần mở rộng
function findUploadFile($basename) {
    $exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    foreach ($exts as $ext) {
        $full = __DIR__ . "/uploads/{$basename}.{$ext}";
        if (file_exists($full)) {
            return "uploads/{$basename}.{$ext}";
        }
    }
    return null;
}

// Helper function để lấy đường dẫn banner (hỗ trợ nhiều ext)
function getBannerPath($n) {
    $basename = "banner{$n}";
    $found = findUploadFile($basename);
    if ($found) return $found;
    return "uploads/default-banner.png";
}

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
    $found = findUploadFile($id_sach);
    if ($found) return $found;
    return "uploads/default-book.png";
}

// Render stars HTML from average rating (e.g. 4.3)
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
?>

<div class="main-container no-sidebar">
    <!-- Main content (sidebar removed on homepage) -->
    <main class="content-area">

        <!-- Banner / Carousel -->
            <?php
            // Debug: in ra các đường dẫn banner và trạng thái tồn tại file trên server (HTML comment)
            $b1 = getBannerPath(1);
            $b2 = getBannerPath(2);
            $b3 = getBannerPath(3);
            // base URL (e.g. '/qlsach/public') derived from current script path
            $baseUrl = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');

            $dbg = "<!-- BANNER PATHS: b1={$b1} (exists=".(file_exists(__DIR__.'/'.str_replace('uploads/','uploads/',$b1))? 'yes':'no')."), b2={$b2} (exists=".(file_exists(__DIR__.'/'.str_replace('uploads/','uploads/',$b2))? 'yes':'no')."), b3={$b3} (exists=".(file_exists(__DIR__.'/'.str_replace('uploads/','uploads/',$b3))? 'yes':'no').") -->";
            echo $dbg;
            ?>
            <div class="home-banner">
            <div class="banner-slides">
                <div class="banner-slide"><img src="<?= $baseUrl . '/' . getBannerPath(1) ?>" alt="Banner 1"></div>
                <div class="banner-slide"><img src="<?= $baseUrl . '/' . getBannerPath(2) ?>" alt="Banner 2"></div>
                <div class="banner-slide"><img src="<?= $baseUrl . '/' . getBannerPath(3) ?>" alt="Banner 3"></div>
            </div>

            <button class="banner-prev" aria-label="Previous banner">‹</button>
            <button class="banner-next" aria-label="Next banner">›</button>

            <div class="banner-indicators" aria-hidden="false"></div>
        </div>

        <!-- Khuyến mãi -->
        <?php if (!empty($onSaleBooks)): ?>
            <section class="home-section">
                <h2>Khuyến mãi nổi bật</h2>
                <div class="product-grid" data-initial="4">
                    <?php foreach ($onSaleBooks as $book):
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
                                    <?= number_format($discountedPrice, 0, ',', '.') ?>đ
                                    <span class="discount">-<?= $book->phan_tram_km ?>%</span>
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
            </section>
        <?php endif; ?>

        <!-- Sách Mới -->
        <section class="home-section">
            <h2>Sách Mới</h2>
            <div class="product-grid" data-initial="4">
                <?php if (!empty($newBooks)): ?>
                    <?php foreach ($newBooks as $book):
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
                                <!-- duplicate rating removed -->
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
                <?php else: ?>
                    <p>Không có sách mới để hiển thị.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Bán chạy -->
        <section class="home-section">
            <h2>Bán chạy</h2>
            <div class="product-grid" data-initial="4">
                <?php if (!empty($bestsellers)): ?>
                    <?php foreach ($bestsellers as $book):
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
                <?php else: ?>
                    <p>Không có sách bán chạy để hiển thị.</p>
                <?php endif; ?>
            </div>
        </section>

        <!-- Gợi ý cho bạn (chỉ hiển thị nếu có dữ liệu) -->
        <?php if (!empty($recommended)): ?>
            <section class="home-section">
                <h2>Gợi ý cho bạn</h2>
                <div class="product-grid" data-initial="4">
                    <?php foreach ($recommended as $book):
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
            </section>
        <?php endif; ?>

    </main>
</div>

<script src="/qlsach/public/js/wishlist.js"></script>
<?php if (isset($_SESSION['id_tk'])): ?>
    <script>
        const userId = '<?= $_SESSION['id_tk'] ?>';
    </script>
<?php endif; ?>
<?php include_once '../includes/footer.php'; ?>