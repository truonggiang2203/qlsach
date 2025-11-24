<?php
include_once '../includes/header.php';
require_once '../models/Wishlist.php';
require_once '../models/Book.php';
require_once '../models/Cart.php';

if (!isset($_SESSION['id_tk'])) {
    header("Location: /qlsach/guest/login.php");
    exit;
}

$wishlistModel = new Wishlist();
$bookModel = new Book();
$cartModel = new Cart();

$id_tk = $_SESSION['id_tk'];
$wishlistItems = $wishlistModel->getUserWishlist($id_tk);

// Helper function để lấy đường dẫn hình ảnh
function getBookImagePath($id_sach) {
    $imagePath = "/qlsach/public/uploads/" . $id_sach . ".jpg";
    $fullPath = __DIR__ . "/../public/uploads/" . $id_sach . ".jpg";
    if (file_exists($fullPath)) {
        return $imagePath;
    }
    return "/qlsach/public/uploads/default-book.png";
}

// Hiển thị thông báo
$message = '';
if (isset($_GET['added'])) {
    $message = '<div class="alert-success">Đã thêm vào danh sách yêu thích!</div>';
} elseif (isset($_GET['removed'])) {
    $message = '<div class="alert-info">Đã xóa khỏi danh sách yêu thích!</div>';
} elseif (isset($_GET['cleared'])) {
    $message = '<div class="alert-info">Đã xóa tất cả khỏi danh sách yêu thích!</div>';
}
?>

<link rel="stylesheet" href="/qlsach/public/css/wishlist.css">

<div class="wishlist-page">
    <!-- Success/Error Messages -->
    <?php if ($message): ?>
        <div style="margin-bottom: 20px; animation: slideDown 0.3s ease-out;">
            <?= $message ?>
        </div>
    <?php endif; ?>
    
    <!-- Wishlist Header -->
    <div class="wishlist-header">
        <h1>Danh sách yêu thích</h1>
        <p>Quản lý những cuốn sách bạn yêu thích</p>
        <?php if (!empty($wishlistItems)): ?>
            <span class="wishlist-count">
                <?= count($wishlistItems) ?> <?= count($wishlistItems) == 1 ? 'cuốn sách' : 'cuốn sách' ?>
            </span>
        <?php endif; ?>
    </div>

    <?php if (empty($wishlistItems)): ?>
        <!-- Empty State -->
        <div class="wishlist-empty">
            <div class="empty-icon"></div>
            <h2>Danh sách yêu thích của bạn đang trống</h2>
            <p>Hãy thêm những cuốn sách bạn yêu thích vào danh sách để dễ dàng tìm lại sau này!</p>
            <a href="/qlsach/public/index.php" class="btn-browse-books">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Khám phá sách ngay
            </a>
        </div>
    <?php else: ?>
        <!-- Bulk Actions -->
        <div class="wishlist-bulk-actions">
            <div class="bulk-actions-left">
                <span style="font-weight: 600; color: var(--text);">
                    Đã chọn <span id="selectedCount">0</span> sách
                </span>
            </div>
            <div class="bulk-actions-right">
                <button type="button" class="btn-bulk-action btn-add-all-to-cart" id="addAllToCartBtn" disabled>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    Thêm đã chọn vào giỏ
                </button>
                <button type="button" class="btn-bulk-action btn-clear-wishlist" onclick="if(confirm('Bạn có chắc muốn xóa tất cả sách khỏi danh sách yêu thích?')) { window.location.href='/qlsach/controllers/wishlistController.php?action=clearAll'; }">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                    </svg>
                    Xóa tất cả
                </button>
            </div>
        </div>

        <!-- Wishlist Grid -->
        <div class="wishlist-grid">
            <?php foreach ($wishlistItems as $book): 
                $discountedPrice = $book->gia_sach_ban * (1 - ($book->phan_tram_km ?? 0) / 100);
                $stock = (int)$book->so_luong_ton;
                $isOutOfStock = $stock <= 0;
                $isLowStock = $stock > 0 && $stock < 10;
            ?>
                <div class="wishlist-item" data-book-id="<?= $book->id_sach ?>">
                    <!-- Book Image -->
                    <div class="wishlist-item-image-wrapper">
                        <a href="/qlsach/public/book_detail.php?id_sach=<?= $book->id_sach ?>">
                            <img src="<?= getBookImagePath($book->id_sach) ?>" 
                                 alt="<?= htmlspecialchars($book->ten_sach) ?>"
                                 class="wishlist-item-image">
                        </a>
                        
                        <?php if ($book->phan_tram_km > 0): ?>
                            <span class="discount-badge">-<?= number_format($book->phan_tram_km, 0) ?>%</span>
                        <?php endif; ?>
                        
                        <!-- Remove Button -->
                        <button type="button" 
                                class="btn-remove-wishlist"
                                onclick="removeFromWishlist('<?= $book->id_sach ?>', this)"
                                title="Xóa khỏi danh sách yêu thích">
                            ❤️
                        </button>
                        
                        <?php if ($isOutOfStock): ?>
                            <div class="out-of-stock-overlay">
                                Hết hàng
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Book Info -->
                    <div class="wishlist-item-info">
                        <h3 class="wishlist-item-title">
                            <a href="/qlsach/public/book_detail.php?id_sach=<?= $book->id_sach ?>">
                                <?= htmlspecialchars($book->ten_sach) ?>
                            </a>
                        </h3>
                        
                        <div class="wishlist-item-meta">
                            <div class="wishlist-item-author">
                                <?= htmlspecialchars($book->ten_tac_gia ?? 'Không rõ') ?>
                            </div>
                            <?php if (isset($book->ten_nxb)): ?>
                                <div style="font-size: 12px; color: #999;">
                                     <?= htmlspecialchars($book->ten_nxb) ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Price -->
                        <div class="wishlist-item-price-section">
                            <div class="wishlist-item-price">
                                <span class="price-current">
                                    <?= number_format($discountedPrice, 0, ',', '.') ?>đ
                                </span>
                                <?php if ($book->phan_tram_km > 0): ?>
                                    <span class="price-original">
                                        <?= number_format($book->gia_sach_ban, 0, ',', '.') ?>đ
                                    </span>
                                <?php endif; ?>
                            </div>
                            
                            <!-- Stock Status -->
                            <?php if ($isOutOfStock): ?>
                                <span class="stock-status stock-unavailable">Hết hàng</span>
                            <?php elseif ($isLowStock): ?>
                                <span class="stock-status stock-low">Còn <?= $stock ?> quyển</span>
                            <?php else: ?>
                                <span class="stock-status stock-available">Còn hàng</span>
                            <?php endif; ?>

                            <!-- Actions -->
                            <div class="wishlist-item-actions">
                                <form action="/qlsach/controllers/cartController.php?action=add" method="POST" style="flex: 1;">
                                    <input type="hidden" name="id_sach" value="<?= $book->id_sach ?>">
                                    <input type="hidden" name="so_luong" value="1">
                                    <button type="submit" 
                                            class="btn-add-to-cart"
                                            <?= $isOutOfStock ? 'disabled' : '' ?>
                                            onclick="event.preventDefault(); addToCart('<?= $book->id_sach ?>', this);">
                                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                                            <line x1="3" y1="6" x2="21" y2="6"></line>
                                            <path d="M16 10a4 4 0 0 1-8 0"></path>
                                        </svg>
                                        Thêm vào giỏ
                                    </button>
                                </form>
                                <a href="/qlsach/public/book_detail.php?id_sach=<?= $book->id_sach ?>" 
                                   class="btn-view-detail">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                        <circle cx="12" cy="12" r="3"></circle>
                                    </svg>
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Remove from wishlist with animation
function removeFromWishlist(idSach, button) {
    if (!confirm('Bạn có chắc muốn xóa sách này khỏi danh sách yêu thích?')) {
        return;
    }
    
    const item = button.closest('.wishlist-item');
    item.style.transition = 'all 0.3s ease-out';
    item.style.opacity = '0';
    item.style.transform = 'scale(0.8)';
    
    setTimeout(() => {
        window.location.href = '/qlsach/controllers/wishlistController.php?action=remove&id_sach=' + idSach + '&redirect=wishlist';
    }, 300);
}

// Add to cart with notification
function addToCart(idSach, button) {
    const form = button.closest('form');
    const formData = new FormData(form);
    
    button.disabled = true;
    button.innerHTML = '<div class="spinner" style="width: 16px; height: 16px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.6s linear infinite;"></div>';
    
    fetch(form.action, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        // Show success message
        const originalText = button.innerHTML;
        button.innerHTML = 'Đã thêm!';
        button.style.background = '#4CAF50';
        
        // Update cart count (if you have a function for that)
        if (typeof updateCartCount === 'function') {
            updateCartCount();
        }
        
        setTimeout(() => {
            button.disabled = false;
            button.innerHTML = originalText;
            button.style.background = '';
        }, 2000);
    })
    .catch(error => {
        console.error('Error:', error);
        button.disabled = false;
        button.innerHTML = '🛒 Thêm vào giỏ';
        alert('Có lỗi xảy ra. Vui lòng thử lại!');
    });
}

// Bulk add to cart
document.getElementById('addAllToCartBtn')?.addEventListener('click', function() {
    const selectedItems = document.querySelectorAll('.wishlist-item input[type="checkbox"]:checked');
    if (selectedItems.length === 0) return;
    
    const bookIds = Array.from(selectedItems).map(cb => cb.value);
    // Implement bulk add logic
    alert('Tính năng đang được phát triển!');
});
</script>

<style>
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

<?php include_once '../includes/footer.php'; ?>
