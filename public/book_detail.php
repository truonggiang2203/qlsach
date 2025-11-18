<?php
include_once '../includes/header.php';
require_once '../models/Book.php';
require_once '../models/Comment.php';
// Category.php đã được nạp trong header

$bookModel = new Book();
$commentModel = new Comment();

$id_sach = $_GET['id_sach'] ?? '';

// 1. KIỂM TRA VÀ LẤY DỮ LIỆU SÁCH
if (!$id_sach) {
    echo "<p class='container'>Không tìm thấy sách!</p>";
    include_once '../includes/footer.php';
    exit;
}
$book = $bookModel->getBookById($id_sach);
if (!$book) {
    echo "<p class='container'>Sách không tồn tại!</p>";
    include_once '../includes/footer.php';
    exit;
}
require_once '../models/Wishlist.php';
require_once '../models/Compare.php';

$wishlistModel = new Wishlist();
$compareModel = new Compare();
$isWishlisted = false;
$isInCompare = $compareModel->exists($id_sach);

// Lấy danh sách wishlist của user (nếu đã đăng nhập)
$userWishlist = [];
if (isset($_SESSION['id_tk'])) {
    $id_tk = $_SESSION['id_tk'];
    $isWishlisted = $wishlistModel->exists($id_tk, $id_sach);
    
    // Lấy tất cả sách trong wishlist để hiển thị trạng thái
    $wishlistItems = $wishlistModel->getUserWishlist($id_tk);
    foreach ($wishlistItems as $item) {
        $userWishlist[$item->id_sach] = true;
    }
}


// 2. LẤY DỮ LIỆU BÌNH LUẬN
$comments = $commentModel->getCommentsByBook($id_sach);
$ratingData = $commentModel->getAverageRating($id_sach);
$avg_rating = $ratingData['average'];
$rating_count = $ratingData['count'];
$rating_distribution = $commentModel->getRatingDistribution($id_sach);

// Lấy bình luận của user hiện tại (nếu đã đăng nhập)
$userComment = null;
$canComment = false; // Có thể bình luận không
$hasPurchased = false; // Đã mua sách chưa
$hasOrdered = false; // Có đơn hàng chưa hoàn thành chưa

if (isset($_SESSION['id_tk'])) {
    $id_tk = $_SESSION['id_tk'];
    $userComment = $commentModel->getUserComment($id_sach, $id_tk);
    $hasPurchased = $commentModel->hasUserPurchasedBook($id_sach, $id_tk);
    $hasOrdered = $commentModel->hasUserOrderedBook($id_sach, $id_tk);
    $canComment = $hasPurchased; // Chỉ cho phép bình luận nếu đã mua
}

// 3. LẤY SÁCH CÙNG THỂ LOẠI VÀ SÁCH GỢI Ý
$sameCategoryBooks = $bookModel->getBooksBySameCategory($id_sach, 8);
$id_tk = $_SESSION['id_tk'] ?? null;
$recommendedBooks = $bookModel->getRecommendedBooks($id_tk, 8);

// Helper function để lấy đường dẫn hình ảnh sách
function getBookImagePath($id_sach) {
    $imagePath = "/qlsach/public/uploads/" . $id_sach . ".jpg";
    $fullPath = __DIR__ . "/uploads/" . $id_sach . ".jpg";
    if (file_exists($fullPath)) {
        return $imagePath;
    }
    return "/qlsach/public/uploads/default-book.png";
}
?>

<div class="container" style="margin-top: 30px; margin-bottom: 30px;">
    <div class="product-detail-layout">

        <div class="product-gallery">
            <div class="product-main-image">
                <img src="<?= getBookImagePath($book->id_sach) ?>"
                    alt="<?= htmlspecialchars($book->ten_sach) ?>"
                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 10px;">
            </div>
        </div>

        <div class="product-info-main">
            <h1><?= htmlspecialchars($book->ten_sach) ?></h1>

            <div class="product-meta-info">
                <p><b>Tác giả:</b> <?= htmlspecialchars($book->ten_tac_gia ?? 'Không rõ') ?></p>
                <p><b>Nhà xuất bản:</b> <?= htmlspecialchars($book->ten_nxb ?? 'Không rõ') ?></p>
                <p><b>Thể loại:</b> <?= htmlspecialchars($book->danh_sach_the_loai ?? 'Chưa phân loại') ?></p>
                <div style="display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                    <b>Đánh giá:</b>
                    <div style="display: flex; align-items: center; gap: 4px;">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="<?= $i <= round($avg_rating) ? '#ffc107' : 'none' ?>" stroke="#ffc107" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                        <?php endfor; ?>
                    </div>
                    <span style="font-weight: 600; color: var(--primary);"><?= number_format($avg_rating, 1) ?></span>
                    <?php if ($rating_count > 0): ?>
                        <span style="color: #666;">(<?= $rating_count ?> đánh giá)</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="product-detail-price-wrapper">
                <?php if (isset($book->phan_tram_km) && $book->phan_tram_km > 0): ?>
                    <?php
                    $discountedPrice = $book->gia_sach_ban * (1 - $book->phan_tram_km / 100);
                    ?>
                    <span class="product-detail-price-new">
                        <?= number_format($discountedPrice); ?> đ
                    </span>
                    <span class="product-detail-price-old">
                        <?= number_format($book->gia_sach_ban); ?> đ
                    </span>
                <?php else: ?>
                    <span class="product-detail-price-new">
                        <?= number_format($book->gia_sach_ban); ?> đ
                    </span>
                <?php endif; ?>
            </div>

            <p class="product-stock">
                Tình trạng: <strong>Còn <?= (int)$book->so_luong_ton; ?> sản phẩm</strong>
            </p>

            <!-- FORM + WISHLIST BUTTON WRAP -->
            <div style="display:flex; align-items:center; gap:12px; margin-top:10px;">

                <!-- FORM ADD TO CART -->
                <form action="/qlsach/controllers/cartController.php?action=add" method="POST"
                    style="display:flex; align-items:center; gap:12px;">

                    <input type="hidden" name="id_sach" value="<?= $book->id_sach; ?>">

                    <div class="quantity-selector">
                        <label for="so_luong">Số lượng:</label>
                        <input type="number" id="so_luong" name="so_luong"
                            value="1" min="1" max="<?= (int)$book->so_luong_ton; ?>">
                    </div>

                    <button type="submit"
                        class="btn-primary"
                        style="padding:12px 26px; font-size:16px; border-radius:6px;">
                        🛒 Thêm vào giỏ hàng
                    </button>
                </form>

                <!-- WISHLIST BUTTON -->
                <?php if (!isset($_SESSION['id_tk'])): ?>
                    <a href="/qlsach/guest/login.php"
                        style="background:var(--danger); padding:12px 20px; border-radius:6px;
              color:white; font-size:18px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;"
                        title="Yêu thích">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                        </svg>
                        Thêm vào yêu thích
                    </a>
                <?php else: ?>
                    <?php if ($isWishlisted): ?>
                        <a href="/qlsach/controllers/wishlistController.php?action=remove&id_sach=<?= $id_sach ?>"
                            style="background:var(--danger); padding:12px 20px; border-radius:6px;
                  color:white; font-size:18px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;"
                            title="Bỏ yêu thích">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                            Đã yêu thích - Bỏ yêu thích
                        </a>
                    <?php else: ?>
                        <a href="/qlsach/controllers/wishlistController.php?action=add&id_sach=<?= $id_sach ?>"
                            style="background:var(--danger); padding:12px 20px; border-radius:6px;
                  color:white; font-size:18px; text-decoration:none; display:inline-flex; align-items:center; gap:8px;"
                            title="Thêm vào yêu thích">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                            </svg>
                            Thêm vào yêu thích
                        </a>
                    <?php endif; ?>
                <?php endif; ?>

                <!-- COMPARE BUTTON -->
                <?php if ($isInCompare): ?>
                    <a href="/qlsach/controllers/compareController.php?action=remove&id_sach=<?= $id_sach ?>"
                       class="btn-compare btn-compare-active"
                       title="Xóa khỏi danh sách so sánh">
                        ⚖️ Đã thêm
                    </a>
                <?php else: ?>
                    <a href="/qlsach/controllers/compareController.php?action=add&id_sach=<?= $id_sach ?>"
                       class="btn-compare"
                       title="Thêm vào danh sách so sánh">
                        ⚖️ So sánh
                    </a>
                <?php endif; ?>

            </div>
            <div class="product-accordion">

                <div class="accordion-item active">
                    <div class="accordion-header">
                        <h3>Mô tả sản phẩm</h3>
                        <button type="button" class="accordion-toggle">−</button>
                    </div>
                    <div class="accordion-content" style="max-height: 500px;">
                        <p>
                            <?= nl2br(htmlspecialchars($book->mo_ta)); ?>
                        </p>
                    </div>
                </div>

                <div class="accordion-item">
                    <div class="accordion-header">
                        <h3>Đánh giá & Bình luận (<?= $rating_count ?>)</h3>
                        <button type="button" class="accordion-toggle">+</button>
                    </div>
                    <div class="accordion-content">
                        <div class="comment-section-inner">
                            <!-- Success/Error Messages -->
                            <?php 
                            $commentMessage = '';
                            if (isset($_GET['comment'])) {
                                switch ($_GET['comment']) {
                                    case 'success':
                                        $commentMessage = '<div class="alert-success" style="margin-bottom: 20px; padding: 16px; border-radius: 8px; background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4CAF50;">✅ Đánh giá của bạn đã được gửi thành công!</div>';
                                        break;
                                    case 'updated':
                                        $commentMessage = '<div class="alert-success" style="margin-bottom: 20px; padding: 16px; border-radius: 8px; background: #e8f5e9; color: #2e7d32; border-left: 4px solid #4CAF50;">✅ Đánh giá của bạn đã được cập nhật!</div>';
                                        break;
                                    case 'deleted':
                                        $commentMessage = '<div class="alert-info" style="margin-bottom: 20px; padding: 16px; border-radius: 8px; background: #e3f2fd; color: #1565c0; border-left: 4px solid #2196F3;">🗑️ Đánh giá đã được xóa!</div>';
                                        break;
                                    case 'not_purchased':
                                        $commentMessage = '<div class="alert-warning" style="margin-bottom: 20px; padding: 16px; border-radius: 8px; background: #fff3cd; color: #856404; border-left: 4px solid #ffc107;">⚠️ Bạn chỉ có thể đánh giá sách sau khi đã mua và nhận được sách. Vui lòng mua sách trước!</div>';
                                        break;
                                    case 'error':
                                        $commentMessage = '<div class="alert-danger" style="margin-bottom: 20px; padding: 16px; border-radius: 8px; background: #f8d7da; color: #721c24; border-left: 4px solid #dc3545;">❌ Có lỗi xảy ra. Vui lòng thử lại!</div>';
                                        break;
                                }
                            }
                            if ($commentMessage): ?>
                                <?= $commentMessage ?>
                            <?php endif; ?>
                            
                            <!-- Rating Summary -->
                            <?php if ($rating_count > 0): ?>
                                <div class="rating-summary">
                                    <div class="rating-overview">
                                        <div class="rating-score"><?= number_format($avg_rating, 1) ?></div>
                                        <div class="rating-stars-large">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <svg width="24" height="24" viewBox="0 0 24 24" fill="<?= $i <= round($avg_rating) ? '#ffc107' : 'none' ?>" stroke="#ffc107" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                </svg>
                                            <?php endfor; ?>
                                        </div>
                                        <div class="rating-count"><?= $rating_count ?> đánh giá</div>
                                    </div>
                                    <div class="rating-distribution">
                                        <?php 
                                        $total = array_sum($rating_distribution);
                                        for ($star = 5; $star >= 1; $star--): 
                                            $count = $rating_distribution[$star];
                                            $percentage = $total > 0 ? ($count / $total) * 100 : 0;
                                        ?>
                                            <div class="rating-bar-item">
                                                <div class="rating-bar-label">
                                                    <span><?= $star ?></span>
                                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#ffc107" stroke="#ffc107" stroke-width="2">
                                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                    </svg>
                                                </div>
                                                <div class="rating-bar">
                                                    <div class="rating-bar-fill" style="width: <?= $percentage ?>%"></div>
                                                </div>
                                                <div class="rating-bar-count"><?= $count ?></div>
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <!-- Comment Form -->
                            <?php if (isset($_SESSION['id_tk'])): ?>
                                <?php if ($canComment || $userComment): ?>
                                    <div class="comment-form-wrapper">
                                        <h3 class="comment-form-title">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                            </svg>
                                            <?= $userComment ? 'Chỉnh sửa đánh giá của bạn' : 'Viết đánh giá' ?>
                                        </h3>
                                <?php else: ?>
                                    <div class="comment-form-wrapper" style="background: #fff3cd; border-left: 4px solid #ffc107;">
                                        <div style="display: flex; align-items: start; gap: 16px; padding: 20px;">
                                            <div style="flex-shrink: 0;">
                                                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ffc107" stroke-width="2" style="opacity: 0.8;">
                                                    <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                                                    <path d="M2 17l10 5 10-5"></path>
                                                    <path d="M2 12l10 5 10-5"></path>
                                                </svg>
                                            </div>
                                            <div style="flex: 1;">
                                                <h3 style="margin: 0 0 8px 0; color: #856404; font-size: 18px;">
                                                    <?php if ($hasOrdered): ?>
                                                        Đơn hàng của bạn đang được xử lý
                                                    <?php else: ?>
                                                        Bạn cần mua sách để đánh giá
                                                    <?php endif; ?>
                                                </h3>
                                                <p style="margin: 0; color: #856404; line-height: 1.6;">
                                                    <?php if ($hasOrdered): ?>
                                                        Bạn đã đặt mua cuốn sách này. Sau khi đơn hàng hoàn thành và bạn nhận được sách, bạn sẽ có thể đánh giá sản phẩm.
                                                    <?php else: ?>
                                                        Chỉ khách hàng đã mua và nhận được sách mới có thể đánh giá. Hãy <a href="/qlsach/public/book_detail.php?id_sach=<?= $book->id_sach ?>" style="color: var(--primary); font-weight: 600;">mua sách</a> để chia sẻ cảm nhận của bạn!
                                                    <?php endif; ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <?php if ($canComment || $userComment): ?>
                                    <form action="/qlsach/controllers/commentController.php?action=add" method="POST" class="comment-form" id="commentForm">
                                        <input type="hidden" name="id_sach" value="<?= htmlspecialchars($book->id_sach) ?>">
                                        <?php if ($userComment): ?>
                                            <input type="hidden" name="id_bl" value="<?= htmlspecialchars($userComment->id_bl) ?>">
                                        <?php endif; ?>
                                        
                                        <div class="star-rating-input">
                                            <label class="star-rating-label">Đánh giá của bạn:</label>
                                            <div class="star-rating-container">
                                                <div class="star-rating" id="starRating">
                                                    <input type="radio" name="so_sao" value="5" id="star5" <?= $userComment && $userComment->so_sao == 5 ? 'checked' : '' ?>>
                                                    <label for="star5">
                                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                        </svg>
                                                    </label>
                                                    <input type="radio" name="so_sao" value="4" id="star4" <?= $userComment && $userComment->so_sao == 4 ? 'checked' : '' ?>>
                                                    <label for="star4">
                                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                        </svg>
                                                    </label>
                                                    <input type="radio" name="so_sao" value="3" id="star3" <?= $userComment && $userComment->so_sao == 3 ? 'checked' : '' ?>>
                                                    <label for="star3">
                                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                        </svg>
                                                    </label>
                                                    <input type="radio" name="so_sao" value="2" id="star2" <?= $userComment && $userComment->so_sao == 2 ? 'checked' : '' ?>>
                                                    <label for="star2">
                                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                        </svg>
                                                    </label>
                                                    <input type="radio" name="so_sao" value="1" id="star1" <?= $userComment && $userComment->so_sao == 1 ? 'checked' : '' ?> required>
                                                    <label for="star1">
                                                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                        </svg>
                                                    </label>
                                                </div>
                                                <div class="star-rating-value" id="starRatingValue"><?= $userComment ? number_format($userComment->so_sao, 0) : '0' ?> sao</div>
                                            </div>
                                        </div>
                                        
                                        <textarea name="binh_luan" class="comment-textarea" placeholder="Chia sẻ cảm nhận của bạn về cuốn sách này..." required><?= $userComment ? htmlspecialchars($userComment->binh_luan) : '' ?></textarea>
                                        
                                        <div class="comment-form-actions">
                                            <button type="button" class="btn-cancel-comment" id="cancelCommentBtn" style="display: none;">Hủy</button>
                                            <button type="submit" class="btn-submit-comment" id="submitCommentBtn">
                                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="22" y1="2" x2="11" y2="13"></line>
                                                    <polygon points="22 2 15 22 11 13 2 9 22 2"></polygon>
                                                </svg>
                                                <?= $userComment ? 'Cập nhật đánh giá' : 'Gửi đánh giá' ?>
                                            </button>
                                        </div>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="comment-form-wrapper" style="text-align: center; padding: 40px;">
                                    <p style="font-size: 16px; color: #666; margin-bottom: 16px;">
                                        Bạn cần <a href="/qlsach/guest/login.php" style="color: var(--primary); font-weight: 600;">đăng nhập</a> để viết đánh giá
                                    </p>
                                </div>
                            <?php endif; ?>

                            <!-- Comment List -->
                            <div class="comment-list">
                                <?php if (!empty($comments)): ?>
                                    <?php foreach ($comments as $c): 
                                        $isOwner = isset($_SESSION['id_tk']) && isset($c->id_tk) && $c->id_tk === $_SESSION['id_tk'];
                                        $userInitial = mb_substr($c->ho_ten ?? 'N', 0, 1);
                                        $commentDate = isset($c->ngay_gio_tao) ? date('d/m/Y H:i', strtotime($c->ngay_gio_tao)) : '';
                                    ?>
                                        <div class="comment-item" data-comment-id="<?= $c->id_bl ?>">
                                            <div class="comment-header">
                                                <div class="comment-user-info">
                                                    <div class="comment-avatar"><?= strtoupper($userInitial) ?></div>
                                                    <div class="comment-user-details">
                                                        <div class="comment-user-name"><?= htmlspecialchars($c->ho_ten ?? 'Người dùng ẩn') ?></div>
                                                        <div class="comment-rating">
                                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="<?= $i <= $c->so_sao ? '#ffc107' : 'none' ?>" stroke="#ffc107" stroke-width="2">
                                                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                                                                </svg>
                                                            <?php endfor; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div style="text-align: right;">
                                                    <?php if ($commentDate): ?>
                                                        <div class="comment-date"><?= $commentDate ?></div>
                                                    <?php endif; ?>
                                                    <?php if ($isOwner): ?>
                                                        <div class="comment-actions">
                                                            <button type="button" class="btn-edit-comment" data-comment-id="<?= $c->id_bl ?>" data-rating="<?= $c->so_sao ?>" data-content="<?= htmlspecialchars($c->binh_luan) ?>">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                                                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                                                </svg>
                                                                Sửa
                                                            </button>
                                                            <button type="button" class="btn-delete-comment" data-comment-id="<?= $c->id_bl ?>" data-id-sach="<?= $book->id_sach ?>">
                                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                                    <polyline points="3 6 5 6 21 6"></polyline>
                                                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                                                </svg>
                                                                Xóa
                                                            </button>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="comment-content"><?= nl2br(htmlspecialchars($c->binh_luan)) ?></div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="comment-empty">
                                        <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                                        </svg>
                                        <p>Chưa có đánh giá nào. Hãy là người đầu tiên đánh giá cuốn sách này!</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="accordion-item">
                    <div class="accordion-header">
                        <h3>CHÍNH SÁCH BÁN HÀNG</h3>
                        <button type="button" class="accordion-toggle">+</button>
                    </div>
                    <div class="accordion-content">
                        <p><strong>Cam kết Sách thật:</strong> 100% sách bán ra là sách thật, có bản quyền, nhập trực tiếp từ NXB và các đối tác uy tín.</p>
                        <p><strong>Miễn phí vận chuyển</strong> đối với đơn hàng trên 300,000VND. Phí giao hàng tiêu chuẩn: 25,000VND.</p>
                        <p><strong>Hotline hỗ trợ:</strong> 1900 1009 - <strong>Email:</strong> support@qlsach.com</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- SÁCH CÙNG THỂ LOẠI -->
    <?php if (!empty($sameCategoryBooks)): ?>
        <div class="related-books-section" style="margin-top: 40px;">
            <div class="section-header">
                <h2 class="section-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path>
                        <path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path>
                    </svg>
                    Sách cùng thể loại
                </h2>
            </div>
            <div class="product-grid">
                <?php foreach ($sameCategoryBooks as $relatedBook): 
                    $relatedDiscountPrice = $relatedBook->gia_sach_ban * (1 - ($relatedBook->phan_tram_km ?? 0) / 100);
                    $isRelatedWishlisted = isset($userWishlist[$relatedBook->id_sach]);
                ?>
                    <div class="product-item">
                        <!-- Wishlist Button -->
                        <?php if (isset($_SESSION['id_tk'])): ?>
                            <a href="#" 
                               class="product-item-wishlist-btn <?= $isRelatedWishlisted ? 'active' : '' ?>"
                               data-book-id="<?= $relatedBook->id_sach ?>"
                               title="<?= $isRelatedWishlisted ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="<?= $isRelatedWishlisted ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        
                        <a href="/qlsach/public/book_detail.php?id_sach=<?= $relatedBook->id_sach ?>">
                            <img src="<?= getBookImagePath($relatedBook->id_sach) ?>" 
                                 alt="<?= htmlspecialchars($relatedBook->ten_sach) ?>">
                        </a>
                        <div class="product-info">
                            <h4>
                                <a href="/qlsach/public/book_detail.php?id_sach=<?= $relatedBook->id_sach ?>">
                                    <?= htmlspecialchars($relatedBook->ten_sach) ?>
                                </a>
                            </h4>
                            <div class="product-price">
                                <?php if ($relatedBook->phan_tram_km > 0): ?>
                                    <?= number_format($relatedDiscountPrice, 0, ',', '.') ?>đ
                                    <span class="discount">-<?= $relatedBook->phan_tram_km ?>%</span>
                                <?php else: ?>
                                    <?= number_format($relatedBook->gia_sach_ban, 0, ',', '.') ?>đ
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <form action="/qlsach/controllers/cartController.php?action=add" method="POST" style="flex: 1;">
                                    <input type="hidden" name="id_sach" value="<?= htmlspecialchars($relatedBook->id_sach) ?>">
                                    <input type="hidden" name="so_luong" value="1">
                                    <button type="submit" class="btn">🛒 Thêm vào giỏ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- SÁCH GỢI Ý -->
    <?php if (!empty($recommendedBooks)): ?>
        <div class="recommended-books-section">
            <div class="section-header">
                <h2 class="section-title">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2L2 7l10 5 10-5-10-5z"></path>
                        <path d="M2 17l10 5 10-5"></path>
                        <path d="M2 12l10 5 10-5"></path>
                    </svg>
                    <?= $id_tk ? 'Gợi ý dành cho bạn' : 'Sách bán chạy' ?>
                </h2>
            </div>
            <div class="product-grid">
                <?php foreach ($recommendedBooks as $recBook): 
                    $recDiscountPrice = $recBook->gia_sach_ban * (1 - ($recBook->phan_tram_km ?? 0) / 100);
                    $isRecWishlisted = isset($userWishlist[$recBook->id_sach]);
                ?>
                    <div class="product-item">
                        <!-- Wishlist Button -->
                        <?php if (isset($_SESSION['id_tk'])): ?>
                            <a href="#" 
                               class="product-item-wishlist-btn <?= $isRecWishlisted ? 'active' : '' ?>"
                               data-book-id="<?= $recBook->id_sach ?>"
                               title="<?= $isRecWishlisted ? 'Xóa khỏi yêu thích' : 'Thêm vào yêu thích' ?>">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="<?= $isRecWishlisted ? 'currentColor' : 'none' ?>" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
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
                        
                        <a href="/qlsach/public/book_detail.php?id_sach=<?= $recBook->id_sach ?>">
                            <img src="<?= getBookImagePath($recBook->id_sach) ?>" 
                                 alt="<?= htmlspecialchars($recBook->ten_sach) ?>">
                        </a>
                        <div class="product-info">
                            <h4>
                                <a href="/qlsach/public/book_detail.php?id_sach=<?= $recBook->id_sach ?>">
                                    <?= htmlspecialchars($recBook->ten_sach) ?>
                                </a>
                            </h4>
                            <div class="product-price">
                                <?php if ($recBook->phan_tram_km > 0): ?>
                                    <?= number_format($recDiscountPrice, 0, ',', '.') ?>đ
                                    <span class="discount">-<?= $recBook->phan_tram_km ?>%</span>
                                <?php else: ?>
                                    <?= number_format($recBook->gia_sach_ban, 0, ',', '.') ?>đ
                                <?php endif; ?>
                            </div>
                            <div class="product-actions">
                                <form action="/qlsach/controllers/cartController.php?action=add" method="POST" style="flex: 1;">
                                    <input type="hidden" name="id_sach" value="<?= htmlspecialchars($recBook->id_sach) ?>">
                                    <input type="hidden" name="so_luong" value="1">
                                    <button type="submit" class="btn">🛒 Thêm vào giỏ</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="/qlsach/public/js/wishlist.js"></script>
<script src="/qlsach/public/js/comment.js"></script>
<?php if (isset($_SESSION['id_tk'])): ?>
    <script>
        const userId = '<?= $_SESSION['id_tk'] ?>';
    </script>
<?php endif; ?>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const accordionItems = document.querySelectorAll(".accordion-item");

        accordionItems.forEach(item => {
            const header = item.querySelector(".accordion-header");
            const toggle = item.querySelector(".accordion-toggle");
            const content = item.querySelector(".accordion-content");

            header.addEventListener("click", () => {
                // Đóng tất cả các item khác
                accordionItems.forEach(otherItem => {
                    if (otherItem !== item && otherItem.classList.contains('active')) {
                        otherItem.classList.remove('active');
                        otherItem.querySelector(".accordion-content").style.maxHeight = "0";
                        otherItem.querySelector(".accordion-toggle").textContent = "+";
                    }
                });

                // Mở hoặc đóng item hiện tại
                if (item.classList.contains('active')) {
                    item.classList.remove('active');
                    content.style.maxHeight = "0";
                    toggle.textContent = "+";
                } else {
                    item.classList.add('active');
                    // Cần set max-height bằng chiều cao thật của content
                    content.style.maxHeight = content.scrollHeight + "px";
                    toggle.textContent = "−"; // Ký tự trừ (khác với dấu gạch ngang)
                }
            });
        });
    });
</script>

<?php
include_once '../includes/footer.php';
?>