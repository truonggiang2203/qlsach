<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../models/Compare.php';
require_once __DIR__ . '/../models/Book.php';

$compareModel = new Compare();
$bookModel = new Book();
$compareItems = $compareModel->getItems();
$compareCount = $compareModel->getCount();
function getBookImageCompare($id_sach)
{
    $base = "/qlsach/public/uploads/";
    $full = __DIR__ . "/../public/uploads/";
    $exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    foreach ($exts as $ext) {
        if (file_exists($full . $id_sach . "." . $ext)) {
            return $base . $id_sach . "." . $ext;
        }
    }

    return $base . "default-book.png";
}

?>

<link rel="stylesheet" href="/qlsach/public/css/compare.css">

<div class="compare-container">
    <div class="compare-header">
        <h2>So sánh sách (<?= $compareCount ?>/<?= $compareModel->getMaxItems() ?>)</h2>
        <?php if ($compareCount > 0): ?>
            <a href="/qlsach/controllers/compareController.php?action=clear"
                class="btn btn-danger"
                onclick="return confirm('Bạn có chắc muốn xóa toàn bộ danh sách so sánh?');">
                <i class="fas fa-trash-alt"></i> Xóa tất cả
            </a>
        <?php endif; ?>
    </div>

    <?php if ($compareCount == 0): ?>
        <div class="compare-empty">
            <p>
                Danh sách so sánh của bạn đang trống
            </p>
            <a href="/qlsach/public/index.php" class="btn btn-primary">
                Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <div class="compare-table-wrapper">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th>Thông tin</th>
                        <?php foreach ($compareItems as $item): ?>
                            <th>
                                <a href="/qlsach/controllers/compareController.php?action=remove&id_sach=<?= $item['id_sach'] ?>"
                                    class="compare-remove-btn"
                                    onclick="return confirm('Xóa sách này khỏi danh sách so sánh?');"
                                    title="Xóa">
                                    ✕
                                </a>
                                <div class="compare-book-card">
                                    <a href="/qlsach/public/book_detail.php?id_sach=<?= $item['id_sach'] ?>">
                                        <img src="<?= getBookImageCompare($item['id_sach']) ?>"
                                            alt="<?= htmlspecialchars($item['ten_sach']) ?>">
                                    </a>
                                    <h3>
                                        <a href="/qlsach/public/book_detail.php?id_sach=<?= $item['id_sach'] ?>">
                                            <?= htmlspecialchars($item['ten_sach']) ?>
                                        </a>
                                    </h3>
                                </div>
                            </th>
                        <?php endforeach; ?>
                        <?php // Thêm cột trống nếu chưa đủ 4 sách
                        for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <th class="compare-placeholder">
                                Chưa có sách
                            </th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- Tác giả -->
                    <tr>
                        <td><strong>Tác giả</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td><?= htmlspecialchars($item['ten_tac_gia']) ?></td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td class="compare-placeholder">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Nhà xuất bản -->
                    <tr>
                        <td><strong>Nhà xuất bản</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td><?= htmlspecialchars($item['ten_nxb']) ?></td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td class="compare-placeholder">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Thể loại -->
                    <tr>
                        <td><strong>Thể loại</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td><?= htmlspecialchars($item['danh_sach_the_loai']) ?></td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td class="compare-placeholder">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Giá -->
                    <tr>
                        <td><strong>Giá</strong></td>
                        <?php foreach ($compareItems as $item):
                            $discountedPrice = $item['gia_sach_ban'] * (1 - ($item['phan_tram_km'] / 100));
                        ?>
                            <td>
                                <?php if ($item['phan_tram_km'] > 0): ?>
                                    <span class="price-current">
                                        <?= number_format($discountedPrice, 0, ',', '.') ?> đ
                                    </span>
                                    <div>
                                        <span class="price-original">
                                            <?= number_format($item['gia_sach_ban'], 0, ',', '.') ?> đ
                                        </span>
                                        <span class="price-discount">
                                            -<?= $item['phan_tram_km'] ?>%
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <span class="price-current" style="color: var(--primary);">
                                        <?= number_format($item['gia_sach_ban'], 0, ',', '.') ?> đ
                                    </span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td class="compare-placeholder">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Tồn kho -->
                    <tr>
                        <td><strong>Tồn kho</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td>
                                <?php if ($item['so_luong_ton'] > 0): ?>
                                    <span class="stock-status stock-in">
                                        Còn <?= $item['so_luong_ton'] ?> quyển
                                    </span>
                                <?php else: ?>
                                    <span class="stock-status stock-out">
                                        Hết hàng
                                    </span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td class="compare-placeholder">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Mô tả -->
                    <tr>
                        <td><strong>Mô tả</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td>
                                <div class="book-description">
                                    <?= nl2br(htmlspecialchars($item['mo_ta'])) ?>
                                </div>
                            </td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td class="compare-placeholder">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Nút hành động -->
                    <tr>
                        <td><strong>Thao tác</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td>
                                <div class="action-buttons">
                                    <a href="/qlsach/public/book_detail.php?id_sach=<?= $item['id_sach'] ?>"
                                        class="btn-view-detail">
                                        Xem chi tiết
                                    </a>
                                    
                                    <?php if ($item['so_luong_ton'] > 0): ?>
                                        <form action="/qlsach/controllers/cartController.php?action=add" method="POST" style="margin: 0;">
                                            <input type="hidden" name="id_sach" value="<?= $item['id_sach'] ?>">
                                            <input type="hidden" name="so_luong" value="1">
                                            <button type="submit" class="btn-add-cart">
                                                Thêm vào giỏ
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <button disabled class="btn-disabled">
                                            Hết hàng
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td class="compare-placeholder">-</td>
                        <?php endfor; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>