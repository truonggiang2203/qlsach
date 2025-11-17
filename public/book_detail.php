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

$wishlistModel = new Wishlist();
$isWishlisted = false;

if (isset($_SESSION['id_tk'])) {
    $id_tk = $_SESSION['id_tk'];
    $isWishlisted = $wishlistModel->exists($id_tk, $id_sach);
}


// 2. LẤY DỮ LIỆU BÌNH LUẬN
$comments = $commentModel->getCommentsByBook($id_sach);
$avg_rating = $commentModel->getAverageRating($id_sach);
?>

<div class="container" style="margin-top: 30px; margin-bottom: 30px;">
    <div class="product-detail-layout">

        <div class="product-gallery">
            <div class="product-main-image">
                <img src="https://via.placeholder.com/500x500?text=<?= urlencode($book->ten_sach) ?>"
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
                <p><b>Đánh giá:</b> ⭐ <?= number_format($avg_rating, 1) ?>/5</p>
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
                        style="background:#ff4d6d; padding:12px 20px; border-radius:6px;
              color:white; font-size:18px; text-decoration:none; display:flex; align-items:center;">
                        🤍
                    </a>

                <?php else: ?>

                    <?php if ($isWishlisted): ?>

                        <a href="/qlsach/controllers/wishlistController.php?action=remove&id_sach=<?= $id_sach ?>"
                            style="background:#ff4d6d; padding:12px 20px; border-radius:6px;
                  color:white; font-size:18px; text-decoration:none; display:flex; align-items:center;">
                            ❤️
                        </a>

                    <?php else: ?>

                        <a href="/qlsach/controllers/wishlistController.php?action=add&id_sach=<?= $id_sach ?>"
                            style="background:#ff4d6d; padding:12px 20px; border-radius:6px;
                  color:white; font-size:18px; text-decoration:none; display:flex; align-items:center;">
                            🤍
                        </a>

                    <?php endif; ?>

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
                        <h3>Đánh giá & Bình luận (<?= count($comments) ?>)</h3>
                        <button type="button" class="accordion-toggle">+</button>
                    </div>
                    <div class="accordion-content">
                        <div class="comment-section-inner">
                            <?php if (isset($_SESSION['id_tk'])): ?>
                                <form action="/qlsach/controllers/commentController.php?action=add" method="POST" class="comment-form">
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
                                    <button type="submit" class="btn-primary">Gửi bình luận</button>
                                </form>
                            <?php else: ?>
                                <p><a href="/qlsach/guest/login.php">Đăng nhập</a> để bình luận.</p>
                            <?php endif; ?>

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
</div>

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