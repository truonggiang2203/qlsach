<?php

require_once '../includes/header.php';
require_once '../models/Book.php';


$bookModel = new Book();
$categoryModel = new Category();


$list_loai_sach = $categoryModel->getAllParentCategories();


$keyword = $_GET['keyword'] ?? '';
$id_loai = $_GET['category'] ?? ''; // Đổi tên 'category' thành 'id_loai' cho nhất quán
$id_the_loai = $_GET['subcategory'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

// 5. GỌI HÀM TÌM KIẾM
$books = $bookModel->searchBooksAdvanced($keyword, $id_loai, $id_the_loai, $min_price, $max_price);
?>

<div class="main-container">

    <aside class="sidebar">
        <h3>🔍 Tìm kiếm nâng cao</h3>
        
        <form action="search.php" method="GET" class="checkout-form" style="margin:0; padding:10px 0; box-shadow:none;">
            
            <div class="form-group">
                <label for="keyword">Từ khóa:</label>
                <input type="text" id="keyword" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Tên sách...">
            </div>

            <div class="form-group">
                <label for="category">Loại sách:</label>
                <select name="category" id="category">
                    <option value="">-- Tất cả loại sách --</option>
                    <?php foreach ($list_loai_sach as $cat): ?>
                        <option value="<?= $cat->id_loai ?>" <?= ($cat->id_loai == $id_loai) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat->ten_loai) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Khoảng giá:</label>
                <div style="display: flex; gap: 10px;">
                    <input type="number" name="min_price" value="<?= htmlspecialchars($min_price) ?>" placeholder="Từ 0đ">
                    <input type="number" name="max_price" value="<?= htmlspecialchars($max_price) ?>" placeholder="Đến...">
                </div>
            </div>

            <button type="submit" class="btn-primary" style="width:100%;">Tìm kiếm</button>
        </form>
    </aside>

    <main class="content-area">
        <h2>Kết quả tìm kiếm (<?= count($books) ?>)</h2>

        <div class="product-grid">
            <?php if (empty($books)): ?>
                <p>Không tìm thấy sản phẩm nào phù hợp với tiêu chí của bạn.</p>
            <?php else: ?>
                <?php foreach ($books as $book): 
                    // Tính giá sau khi giảm
                    $gia_goc = $book->gia_sach_ban;
                    $phan_tram_km = $book->phan_tram_km;
                    $gia_ban = $gia_goc * (1 - $phan_tram_km / 100);
                ?>
                    <div class="product-item">
                        <a href="book_detail.php?id_sach=<?= $book->id_sach ?>">
                            <img src="../uploads/images/<?= htmlspecialchars($book->ten_sach) ?>.jpg" alt="<?= htmlspecialchars($book->ten_sach) ?>">
                        </a>
                        <div class="product-info">
                            <h4>
                                <a href="book_detail.php?id_sach=<?= $book->id_sach ?>"><?= htmlspecialchars($book->ten_sach) ?></a>
                            </h4>
                            <div class="product-price">
                                <?= number_format($gia_ban, 0, ',', '.') ?>đ
                                <?php if ($phan_tram_km > 0): ?>
                                    <span class="discount" style="background:var(--danger);"><?= $book->phan_tram_km ?>%</span>
                                <?php endif; ?>
                            </div>
                            <small style="color:#777;"><?= htmlspecialchars($book->ten_loai) ?></small>
                            <br>
                            <a href="../controllers/cartController.php?action=add&id=<?= $book->id_sach ?>" class="btn">Thêm vào giỏ</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div></main>

</div><?php
// 6. NẠP FOOTER
require_once '../includes/footer.php';
?>