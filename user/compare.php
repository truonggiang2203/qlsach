<?php
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../models/Compare.php';
require_once __DIR__ . '/../models/Book.php';

$compareModel = new Compare();
$bookModel = new Book();
$compareItems = $compareModel->getItems();
$compareCount = $compareModel->getCount();
?>

<div class="container" style="margin-top: 30px; margin-bottom: 50px;">
    <div class="compare-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h2>So sánh sách (<?= $compareCount ?>/<?= $compareModel->getMaxItems() ?>)</h2>
        <?php if ($compareCount > 0): ?>
            <a href="/qlsach/controllers/compareController.php?action=clear" 
               class="btn btn-danger"
               onclick="return confirm('Bạn có chắc muốn xóa toàn bộ danh sách so sánh?');">
                Xóa tất cả
            </a>
        <?php endif; ?>
    </div>

    <?php if ($compareCount == 0): ?>
        <div class="compare-empty" style="text-align: center; padding: 60px 20px; background: #f8f9fa; border-radius: 10px;">
            <p style="font-size: 18px; color: #666; margin-bottom: 20px;">
                Danh sách so sánh của bạn đang trống
            </p>
            <a href="/qlsach/public/index.php" class="btn btn-primary">
                Tiếp tục mua sắm
            </a>
        </div>
    <?php else: ?>
        <div class="compare-table-wrapper" style="overflow-x: auto;">
            <table class="compare-table">
                <thead>
                    <tr>
                        <th style="min-width: 200px;">Thông tin</th>
                        <?php foreach ($compareItems as $item): ?>
                            <th style="min-width: 250px; position: relative;">
                                <a href="/qlsach/controllers/compareController.php?action=remove&id_sach=<?= $item['id_sach'] ?>" 
                                   class="compare-remove-btn"
                                   onclick="return confirm('Xóa sách này khỏi danh sách so sánh?');"
                                   title="Xóa">
                                    ✕
                                </a>
                                <div class="compare-book-card">
                                    <img src="<?= htmlspecialchars($item['hinh_anh'] ?: 'https://via.placeholder.com/200x300?text=' . urlencode($item['ten_sach'])) ?>" 
                                         alt="<?= htmlspecialchars($item['ten_sach']) ?>"
                                         style="width: 100%; max-width: 150px; height: auto; border-radius: 8px; margin-bottom: 10px;">
                                    <h3 style="font-size: 16px; margin: 10px 0; color: var(--primary);">
                                        <a href="/qlsach/public/book_detail.php?id_sach=<?= $item['id_sach'] ?>" 
                                           style="color: var(--primary); text-decoration: none;">
                                            <?= htmlspecialchars($item['ten_sach']) ?>
                                        </a>
                                    </h3>
                                </div>
                            </th>
                        <?php endforeach; ?>
                        <?php // Thêm cột trống nếu chưa đủ 4 sách
                        for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <th style="min-width: 250px; background: #f8f9fa; text-align: center; vertical-align: middle;">
                                <p style="color: #999;">Chưa có sách</p>
                            </th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- Tác giả -->
                    <tr>
                        <td class="compare-label"><strong>Tác giả</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td><?= htmlspecialchars($item['ten_tac_gia']) ?></td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td style="background: #f8f9fa;">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Nhà xuất bản -->
                    <tr>
                        <td class="compare-label"><strong>Nhà xuất bản</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td><?= htmlspecialchars($item['ten_nxb']) ?></td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td style="background: #f8f9fa;">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Thể loại -->
                    <tr>
                        <td class="compare-label"><strong>Thể loại</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td><?= htmlspecialchars($item['danh_sach_the_loai']) ?></td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td style="background: #f8f9fa;">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Giá -->
                    <tr>
                        <td class="compare-label"><strong>Giá</strong></td>
                        <?php foreach ($compareItems as $item): 
                            $discountedPrice = $item['gia_sach_ban'] * (1 - ($item['phan_tram_km'] / 100));
                        ?>
                            <td>
                                <?php if ($item['phan_tram_km'] > 0): ?>
                                    <span style="color: var(--danger); font-weight: bold; font-size: 18px;">
                                        <?= number_format($discountedPrice, 0, ',', '.') ?> đ
                                    </span>
                                    <br>
                                    <span style="text-decoration: line-through; color: #999; font-size: 14px;">
                                        <?= number_format($item['gia_sach_ban'], 0, ',', '.') ?> đ
                                    </span>
                                    <span style="color: var(--danger); font-size: 12px; margin-left: 5px;">
                                        (-<?= $item['phan_tram_km'] ?>%)
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--primary); font-weight: bold; font-size: 18px;">
                                        <?= number_format($item['gia_sach_ban'], 0, ',', '.') ?> đ
                                    </span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td style="background: #f8f9fa;">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Tồn kho -->
                    <tr>
                        <td class="compare-label"><strong>Tồn kho</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td>
                                <?php if ($item['so_luong_ton'] > 0): ?>
                                    <span style="color: #28a745; font-weight: bold;">
                                        Còn <?= $item['so_luong_ton'] ?> quyển
                                    </span>
                                <?php else: ?>
                                    <span style="color: var(--danger); font-weight: bold;">
                                        Hết hàng
                                    </span>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td style="background: #f8f9fa;">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Mô tả -->
                    <tr>
                        <td class="compare-label"><strong>Mô tả</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td>
                                <p style="font-size: 14px; line-height: 1.5; max-height: 150px; overflow-y: auto;">
                                    <?= nl2br(htmlspecialchars(mb_substr($item['mo_ta'], 0, 200))) ?>
                                    <?= mb_strlen($item['mo_ta']) > 200 ? '...' : '' ?>
                                </p>
                            </td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td style="background: #f8f9fa;">-</td>
                        <?php endfor; ?>
                    </tr>

                    <!-- Nút hành động -->
                    <tr>
                        <td class="compare-label"><strong>Thao tác</strong></td>
                        <?php foreach ($compareItems as $item): ?>
                            <td>
                                <a href="/qlsach/public/book_detail.php?id_sach=<?= $item['id_sach'] ?>" 
                                   class="btn btn-primary" 
                                   style="display: inline-block; margin-bottom: 5px; width: 100%;">
                                    Xem chi tiết
                                </a>
                                <br>
                                <?php if ($item['so_luong_ton'] > 0): ?>
                                    <form action="/qlsach/controllers/cartController.php?action=add" method="POST" style="margin: 0;">
                                        <input type="hidden" name="id_sach" value="<?= $item['id_sach'] ?>">
                                        <input type="hidden" name="so_luong" value="1">
                                        <button type="submit" class="btn btn-success" style="width: 100%;">
                                            Thêm vào giỏ
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <button disabled class="btn btn-secondary" style="width: 100%;">
                                        Hết hàng
                                    </button>
                                <?php endif; ?>
                            </td>
                        <?php endforeach; ?>
                        <?php for ($i = $compareCount; $i < $compareModel->getMaxItems(); $i++): ?>
                            <td style="background: #f8f9fa;">-</td>
                        <?php endfor; ?>
                    </tr>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<style>
.compare-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border-radius: 8px;
    overflow: hidden;
}

.compare-table th,
.compare-table td {
    padding: 15px;
    text-align: left;
    border: 1px solid #e0e0e0;
    vertical-align: top;
}

.compare-table th {
    background: var(--primary);
    color: white;
    font-weight: bold;
    text-align: center;
}

.compare-table .compare-label {
    background: #f8f9fa;
    font-weight: 600;
    width: 200px;
}

.compare-remove-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    background: var(--danger);
    color: white;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    font-size: 18px;
    font-weight: bold;
    transition: all 0.3s;
    z-index: 10;
}

.compare-remove-btn:hover {
    background: #c82333;
    transform: scale(1.1);
}

.compare-book-card {
    text-align: center;
    padding: 10px;
}

.compare-book-card img {
    margin: 0 auto;
    display: block;
}

@media (max-width: 768px) {
    .compare-table-wrapper {
        overflow-x: scroll;
    }
    
    .compare-table {
        min-width: 800px;
    }
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

