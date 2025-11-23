<?php
require_once '../includes/header.php';
require_once '../models/Book.php';
require_once '../models/Wishlist.php';
require_once '../models/Comment.php';

$bookModel = new Book();
$categoryModel = new Category();
$wishlistModel = new Wishlist();
$commentModel = new Comment();

$list_loai_sach = $categoryModel->getAllParentCategories();
$list_the_loai = $categoryModel->getAllSubCategories();
$subCategoriesByParent = [];
foreach ($list_the_loai as $sub) {
    $subCategoriesByParent[$sub->id_loai][] = $sub;
}

// Lấy danh sách wishlist của user (nếu đã đăng nhập)
$userWishlist = [];
if (isset($_SESSION['id_tk'])) {
    $wishlistItems = $wishlistModel->getUserWishlist($_SESSION['id_tk']);
    foreach ($wishlistItems as $item) {
        $userWishlist[$item->id_sach] = true;
    }
}

function getBookImagePath($id_sach)
{
    $imagePath = "/qlsach/public/uploads/" . $id_sach . ".jpg";
    $fullPath = __DIR__ . "/uploads/" . $id_sach . ".jpg";
    if (file_exists($fullPath)) {
        return $imagePath;
    }
    return "/qlsach/public/uploads/default-book.png";
}

// Render stars HTML from average rating (e.g. 4.3)
function renderStars($avg)
{
    $full = floor($avg);
    $half = (($avg - $full) >= 0.5) ? 1 : 0;
    $empty = 5 - $full - $half;
    $html = '<div class="product-rating" aria-hidden="true">';
    for ($i = 0; $i < $full; $i++) $html .= '<span class="star star-full">★</span>';
    if ($half) $html .= '<span class="star star-half">★</span>';
    for ($i = 0; $i < $empty; $i++) $html .= '<span class="star star-empty">☆</span>';
    $html .= '</div>';
    return $html;
}

function buildFilterUrl($overrides = [], $remove = [])
{
    $params = $_GET;
    foreach ($remove as $key) {
        unset($params[$key]);
    }
    foreach ($overrides as $key => $value) {
        if ($value === null || $value === '') {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }
    return 'search.php' . (empty($params) ? '' : ('?' . http_build_query($params)));
}

$keyword = trim($_GET['keyword'] ?? '');
$id_loai = $_GET['category'] ?? '';
$id_the_loai = $_GET['subcategory'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';
$sort = $_GET['sort'] ?? 'relevance';
$in_stock = ($_GET['in_stock'] ?? '') === '1';
$has_discount = ($_GET['has_discount'] ?? '') === '1';

$books = $bookModel->searchBooksAdvanced($keyword, $id_loai, $id_the_loai, $min_price, $max_price);

if ($in_stock) {
    $books = array_filter($books, fn($book) => (int)($book->so_luong_ton ?? 0) > 0);
}

if ($has_discount) {
    $books = array_filter($books, fn($book) => (int)($book->phan_tram_km ?? 0) > 0);
}

$books = array_values($books);

if (!empty($sort) && count($books) > 1) {
    usort($books, function ($a, $b) use ($sort) {
        $priceA = $a->gia_sach_ban * (1 - ($a->phan_tram_km ?? 0) / 100);
        $priceB = $b->gia_sach_ban * (1 - ($b->phan_tram_km ?? 0) / 100);
        switch ($sort) {
            case 'price_asc':
                return $priceA <=> $priceB;
            case 'price_desc':
                return $priceB <=> $priceA;
            case 'discount':
                return ($b->phan_tram_km ?? 0) <=> ($a->phan_tram_km ?? 0);
            case 'name_asc':
                return strcmp($a->ten_sach, $b->ten_sach);
            case 'name_desc':
                return strcmp($b->ten_sach, $a->ten_sach);
            default:
                return 0;
        }
    });
}

$totalResults = count($books);
$discountCount = count(array_filter($books, fn($book) => (int)($book->phan_tram_km ?? 0) > 0));
$inStockCount = count(array_filter($books, fn($book) => (int)($book->so_luong_ton ?? 0) > 0));

$selectedFilters = [];
if ($keyword) $selectedFilters[] = ['label' => "Từ khóa: \"$keyword\"", 'remove_url' => buildFilterUrl([], ['keyword'])];
if ($id_loai) {
    $catName = '';
    foreach ($list_loai_sach as $cat) {
        if ($cat->id_loai == $id_loai) {
            $catName = $cat->ten_loai;
            break;
        }
    }
    $selectedFilters[] = ['label' => "Loại: $catName", 'remove_url' => buildFilterUrl([], ['category', 'subcategory'])];
}
if ($id_the_loai) {
    foreach ($list_the_loai as $sub) {
        if ($sub->id_the_loai == $id_the_loai) {
            $selectedFilters[] = ['label' => "Thể loại: $sub->ten_the_loai", 'remove_url' => buildFilterUrl([], ['subcategory'])];
            break;
        }
    }
}
if ($min_price) $selectedFilters[] = ['label' => "Giá từ " . number_format($min_price, 0, ',', '.') . "đ", 'remove_url' => buildFilterUrl([], ['min_price'])];
if ($max_price) $selectedFilters[] = ['label' => "Giá đến " . number_format($max_price, 0, ',', '.') . "đ", 'remove_url' => buildFilterUrl([], ['max_price'])];
if ($in_stock) $selectedFilters[] = ['label' => "Còn hàng", 'remove_url' => buildFilterUrl([], ['in_stock'])];
if ($has_discount) $selectedFilters[] = ['label' => "Đang giảm giá", 'remove_url' => buildFilterUrl([], ['has_discount'])];

$quickTags = ['Văn học', 'Kinh doanh', 'Tâm lý', 'Thiếu nhi', 'Self-help'];

?>

<div class="search-page">
    <section class="search-hero">
        <div>
            <p>Tìm thấy</p>
            <h1><?= $totalResults ?> kết quả</h1>
            <?php if ($keyword): ?>
                <span>Từ khóa “<?= htmlspecialchars($keyword) ?>”</span>
            <?php else: ?>
                <span>Hãy nhập từ khóa hoặc chọn bộ lọc để tìm sách yêu thích</span>
            <?php endif; ?>
        </div>
        <div class="search-hero-stats">
            <div>
                <strong><?= $inStockCount ?></strong>
                <span>Còn hàng</span>
            </div>
            <div>
                <strong><?= $discountCount ?></strong>
                <span>Đang giảm giá</span>
            </div>
        </div>
    </section>

    <div class="search-quick-tags">
        <span>Từ khóa phổ biến:</span>
        <?php foreach ($quickTags as $tag): ?>
            <a href="<?= buildFilterUrl(['keyword' => $tag]) ?>">#<?= htmlspecialchars($tag) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="search-layout">
        <aside class="search-filters">
            <form action="search.php" method="GET" class="filter-card">
                <h3>🔍 Bộ lọc nâng cao</h3>

                <div class="form-group">
                    <label for="keyword">Từ khóa</label>
                    <input type="text" id="keyword" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Tên sách, tác giả...">
                </div>

                <div class="form-group">
                    <label for="category">Loại sách</label>
                    <select name="category" id="category">
                        <option value="">-- Tất cả --</option>
                        <?php foreach ($list_loai_sach as $cat): ?>
                            <option value="<?= $cat->id_loai ?>" <?= ($cat->id_loai == $id_loai) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat->ten_loai) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <?php if ($id_loai && isset($subCategoriesByParent[$id_loai])): ?>
                    <div class="form-group">
                        <label for="subcategory">Thể loại</label>
                        <select name="subcategory" id="subcategory">
                            <option value="">-- Tất cả --</option>
                            <?php foreach ($subCategoriesByParent[$id_loai] as $sub): ?>
                                <option value="<?= $sub->id_the_loai ?>" <?= ($sub->id_the_loai == $id_the_loai) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($sub->ten_the_loai) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>

                <div class="form-group">
                    <label>Khoảng giá</label>
                    <div class="price-range">
                        <input type="number" name="min_price" value="<?= htmlspecialchars($min_price) ?>" placeholder="Từ 0đ">
                        <input type="number" name="max_price" value="<?= htmlspecialchars($max_price) ?>" placeholder="Đến...">
                    </div>
                </div>

                <div class="filter-toggles">
                    <label class="toggle">
                        <input type="checkbox" name="in_stock" value="1" <?= $in_stock ? 'checked' : '' ?>>
                        <span>Còn hàng</span>
                    </label>
                    <label class="toggle">
                        <input type="checkbox" name="has_discount" value="1" <?= $has_discount ? 'checked' : '' ?>>
                        <span>Đang giảm giá</span>
                    </label>
                </div>

                <div class="form-group">
                    <label for="sort">Sắp xếp</label>
                    <select name="sort" id="sort">
                        <option value="relevance" <?= $sort === 'relevance' ? 'selected' : '' ?>>Mặc định</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="discount" <?= $sort === 'discount' ? 'selected' : '' ?>>Đang giảm mạnh</option>
                        <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
                        <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Tên Z-A</option>
                    </select>
                </div>

                <button type="submit" class="btn-primary w-100">Tìm sách</button>
                <?php if (!empty($_GET)): ?>
                    <a href="search.php" class="reset-link">Đặt lại bộ lọc</a>
                <?php endif; ?>
            </form>
        </aside>

        <main class="search-results">
            <div class="results-toolbar">
                <div>
                    <strong><?= $totalResults ?></strong> kết quả hiển thị
                </div>
                <form method="GET" class="sort-inline">
                    <?php foreach ($_GET as $key => $value): ?>
                        <?php if ($key === 'sort') continue; ?>
                        <input type="hidden" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($value) ?>">
                    <?php endforeach; ?>
                    <label>Sắp xếp:</label>
                    <select name="sort" onchange="this.form.submit()">
                        <option value="relevance" <?= $sort === 'relevance' ? 'selected' : '' ?>>Mặc định</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="discount" <?= $sort === 'discount' ? 'selected' : '' ?>>Đang giảm mạnh</option>
                        <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
                        <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Tên Z-A</option>
                    </select>
                </form>
            </div>

            <?php if (!empty($selectedFilters)): ?>
                <div class="active-filters">
                    <?php foreach ($selectedFilters as $filter): ?>
                        <a href="<?= $filter['remove_url'] ?>" class="filter-chip"><?= htmlspecialchars($filter['label']) ?> ✕</a>
                    <?php endforeach; ?>
                    <a href="search.php" class="filter-chip reset">Xóa tất cả</a>
                </div>
            <?php endif; ?>

            <?php if (empty($books)): ?>
                <div class="empty-results">
                    <h3>Không tìm thấy kết quả</h3>
                    <p>Hãy thử đổi từ khóa hoặc giảm bớt bộ lọc.</p>
                    <ul>
                        <li>Kiểm tra chính tả của từ khóa</li>
                        <li>Thử tên thể loại hoặc tác giả khác</li>
                        <li>Đặt lại bộ lọc giá và trạng thái</li>
                    </ul>
                    <a href="search.php" class="btn-primary">Đặt lại tìm kiếm</a>
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
                                <img src="<?= getBookImagePath($book->id_sach) ?>" alt="<?= htmlspecialchars($book->ten_sach) ?>">
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
        </main>
    </div>
</div>

<script src="/qlsach/public/js/wishlist.js"></script>
<?php if (isset($_SESSION['id_tk'])): ?>
    <script>
        const userId = '<?= $_SESSION['id_tk'] ?>';
    </script>
<?php endif; ?>

<?php
require_once '../includes/footer.php';
?>