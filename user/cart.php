<?php
require_once __DIR__ . '/../includes/header.php'; 
require_once __DIR__ . '/../models/Cart.php';

$cartModel = new Cart();
$cartItems = $cartModel->getItems();
$cartIsEmpty = empty($cartItems);
$isLoggedIn = isset($_SESSION['id_tk']);

// Tính tổng tiền ban đầu
$totals = $cartModel->calculateTotals();
?>

<div class="cart-page">
    <div class="cart-container">
        <div class="cart-header">
            <h1 class="cart-title">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <path d="M16 10a4 4 0 0 1-8 0"></path>
                </svg>
                Giỏ hàng của bạn
            </h1>
            <?php if (!$cartIsEmpty): ?>
                <span class="cart-count-badge"><?= count($cartItems) ?> sản phẩm</span>
            <?php endif; ?>
        </div>

        <?php if ($cartIsEmpty): ?>
            <div class="cart-empty">
                <div class="empty-icon">
                    <svg width="120" height="120" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                </div>
                <h2>Giỏ hàng của bạn đang trống</h2>
                <p>Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                <a href="/qlsach/public/index.php" class="btn-continue-shopping">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M19 12H5M12 19l-7-7 7-7"></path>
                    </svg>
                    Tiếp tục mua sắm
                </a>
            </div>
        <?php else: ?>
            <form action="/qlsach/user/checkout.php" method="POST" id="cart-form">
                <div class="cart-content">
                    <!-- Danh sách sản phẩm -->
                    <div class="cart-items-section">
                        <div class="cart-items-header">
                            <label class="select-all-checkbox">
                                <input type="checkbox" id="cart-select-all" checked>
                                <span>Chọn tất cả (<?= count($cartItems) ?>)</span>
                            </label>
                            <button type="button" class="btn-clear-cart" onclick="clearCart()">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <polyline points="3 6 5 6 21 6"></polyline>
                                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                </svg>
                                Xóa tất cả
                            </button>
                        </div>

                        <div class="cart-items-list">
                            <?php foreach ($cartItems as $item): 
                                $discountPercent = $item['discount_percent'] ?? 0;
                                $discountedPrice = $item['price'] * (1 - $discountPercent / 100);
                                $discountedSubtotal = $discountedPrice * $item['quantity'];
                                $originalSubtotal = $item['price'] * $item['quantity'];
                            ?>
                                <div class="cart-item" id="cart-item-<?= $item['id_sach'] ?>">
                                    <div class="cart-item-checkbox">
                                        <input type="checkbox" 
                                               class="cart-item-select"
                                               name="selected_items[]"
                                               value="<?= $item['id_sach'] ?>"
                                               checked>
                                    </div>

                                    <div class="cart-item-image">
                                        <a href="/qlsach/public/book_detail.php?id_sach=<?= $item['id_sach'] ?>">
                                            <img src="<?= htmlspecialchars($item['image'] ?: 'https://via.placeholder.com/150x200?text=' . urlencode($item['name'])) ?>" 
                                                 alt="<?= htmlspecialchars($item['name']) ?>">
                                        </a>
                                    </div>

                                    <div class="cart-item-info">
                                        <h3 class="cart-item-name">
                                            <a href="/qlsach/public/book_detail.php?id_sach=<?= $item['id_sach'] ?>">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </a>
                                        </h3>
                                        <div class="cart-item-meta">
                                            <span class="item-stock">
                                                <?php if ($item['stock'] > 0): ?>
                                                    <span class="stock-available">Còn <?= $item['stock'] ?> sản phẩm</span>
                                                <?php else: ?>
                                                    <span class="stock-unavailable">Hết hàng</span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="cart-item-price">
                                        <?php if ($discountPercent > 0): ?>
                                            <div class="price-current">
                                                <?= number_format($discountedPrice, 0, ',', '.') ?> đ
                                            </div>
                                            <div class="price-original">
                                                <?= number_format($item['price'], 0, ',', '.') ?> đ
                                            </div>
                                            <div class="price-discount-badge">
                                                -<?= $discountPercent ?>%
                                            </div>
                                        <?php else: ?>
                                            <div class="price-current">
                                                <?= number_format($item['price'], 0, ',', '.') ?> đ
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="cart-item-quantity">
                                        <div class="quantity-controls">
                                            <button type="button" 
                                                    class="qty-btn qty-decrease" 
                                                    data-id="<?= $item['id_sach'] ?>"
                                                    <?= $item['quantity'] <= 1 ? 'disabled' : '' ?>>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                                </svg>
                                            </button>
                                            <input type="number" 
                                                   class="qty-input"
                                                   data-id="<?= $item['id_sach'] ?>"
                                                   value="<?= $item['quantity'] ?>"
                                                   min="1"
                                                   max="<?= $item['stock'] ?>"
                                                   readonly>
                                            <button type="button" 
                                                    class="qty-btn qty-increase" 
                                                    data-id="<?= $item['id_sach'] ?>"
                                                    <?= $item['quantity'] >= $item['stock'] ? 'disabled' : '' ?>>
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <line x1="12" y1="5" x2="12" y2="19"></line>
                                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                                </svg>
                                            </button>
                                        </div>
                                        <div class="quantity-note">
                                            Tối đa: <?= $item['stock'] ?>
                                        </div>
                                    </div>

                                    <div class="cart-item-subtotal">
                                        <div class="subtotal-amount" data-id="<?= $item['id_sach'] ?>">
                                            <?= number_format($discountedSubtotal, 0, ',', '.') ?> đ
                                        </div>
                                        <?php if ($discountPercent > 0): ?>
                                            <div class="subtotal-original">
                                                <?= number_format($originalSubtotal, 0, ',', '.') ?> đ
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div class="cart-item-actions">
                                        <button type="button" 
                                                class="btn-remove-item" 
                                                onclick="removeItem('<?= $item['id_sach'] ?>', '<?= htmlspecialchars($item['name'], ENT_QUOTES) ?>')"
                                                title="Xóa sản phẩm">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <polyline points="3 6 5 6 21 6"></polyline>
                                                <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Tóm tắt đơn hàng -->
                    <div class="cart-summary-section">
                        <div class="cart-summary-card">
                            <h3 class="summary-title">Tóm tắt đơn hàng</h3>
                            
                            <div class="summary-details">
                                <div class="summary-row">
                                    <span>Tạm tính:</span>
                                    <span id="cart-subtotal"><?= number_format($totals['subtotal'], 0, ',', '.') ?> đ</span>
                                </div>
                                
                                <?php if ($totals['totalDiscount'] > 0): ?>
                                    <div class="summary-row summary-discount">
                                        <span>Giảm giá:</span>
                                        <span id="cart-discount">-<?= number_format($totals['totalDiscount'], 0, ',', '.') ?> đ</span>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="summary-divider"></div>
                                
                                <div class="summary-row summary-total">
                                    <span>Tổng cộng:</span>
                                    <span id="cart-total" class="total-amount"><?= number_format($totals['total'], 0, ',', '.') ?> đ</span>
                                </div>
                            </div>

                            <div class="summary-actions">
                                <?php if ($isLoggedIn): ?>
                                    <button type="submit" class="btn-checkout" id="btn-checkout-submit">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M5 12h14M12 5l7 7-7 7"></path>
                                        </svg>
                                        Tiến hành thanh toán
                                    </button>
                                <?php else: ?>
                                    <a href="/qlsach/guest/login.php?redirect=<?= urlencode('/qlsach/user/cart.php') ?>" class="btn-checkout">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4M10 17l5-5-5-5M15 12H3"></path>
                                        </svg>
                                        Đăng nhập để thanh toán
                                    </a>
                                <?php endif; ?>
                                
                                <a href="/qlsach/public/index.php" class="btn-continue">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M19 12H5M12 19l-7-7 7-7"></path>
                                    </svg>
                                    Tiếp tục mua sắm
                                </a>
                            </div>

                            <div class="summary-benefits">
                                <div class="benefit-item">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <span>Miễn phí vận chuyển đơn hàng trên 300.000đ</span>
                                </div>
                                <div class="benefit-item">
                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                                        <polyline points="22 4 12 14.01 9 11.01"></polyline>
                                    </svg>
                                    <span>Đổi trả trong 7 ngày</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<link rel="stylesheet" href="/qlsach/public/css/cart.css">
<script src="/qlsach/public/js/cart.js"></script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
