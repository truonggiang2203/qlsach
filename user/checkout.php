<?php
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../includes/header.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id_tk'])) {
    header("Location: ../guest/login.php");
    exit;
}

$cartModel = new Cart();
$bookModel = new Book();
$fullCart = $cartModel->getItems();
function getBookImageCheckout($id_sach)
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


// Xử lý lọc sản phẩm đã chọn
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_items'])) {
    $selected_ids = $_POST['selected_items'];
    $checkoutCart = [];

    if (empty($selected_ids)) {
        echo "<script>alert('Bạn chưa chọn sản phẩm nào để thanh toán!'); window.location.href='cart.php';</script>";
        exit;
    }

    foreach ($selected_ids as $id_sach) {
        if (isset($fullCart[$id_sach])) {
            $checkoutCart[$id_sach] = $fullCart[$id_sach];
        }
    }
    $_SESSION['checkout_cart'] = $checkoutCart;
} else if (isset($_SESSION['checkout_cart'])) {
    $checkoutCart = $_SESSION['checkout_cart'];
} else {
    echo "<script>alert('Giỏ hàng trống hoặc phiên đã hết hạn!'); window.location.href='cart.php';</script>";
    exit;
}

if (empty($checkoutCart)) {
    echo "<script>alert('Giỏ hàng thanh toán của bạn bị trống!'); window.location.href='cart.php';</script>";
    exit;
}

// Tính tổng tiền
$subtotal = 0;
$totalDiscount = 0;
$items = [];

foreach ($checkoutCart as $id_sach => $item) {
    $book = $bookModel->getBookById($id_sach);
    $price = $item['price'];
    $quantity = $item['quantity'];
    $discount_percent = $item['discount_percent'] ?? 0;

    $originalPrice = $price * $quantity;
    $discountAmount = ($price * $discount_percent / 100) * $quantity;
    $finalPrice = $originalPrice - $discountAmount;

    $subtotal += $originalPrice;
    $totalDiscount += $discountAmount;

    $items[] = [
        'book' => $book,
        'item' => $item,
        'originalPrice' => $originalPrice,
        'discountAmount' => $discountAmount,
        'finalPrice' => $finalPrice
    ];
}

$total = $subtotal - $totalDiscount;
?>

<link rel="stylesheet" href="/qlsach/public/css/checkout.css">

<div class="checkout-page">
    <div class="checkout-header">
        <h2>Thanh toán đơn hàng</h2>
        <p style="color: #666; margin-top: 8px;">Vui lòng kiểm tra thông tin đơn hàng và thanh toán</p>

        <div class="checkout-steps">
            <div class="checkout-step">
                <div class="checkout-step-number">1</div>
                <div class="checkout-step-label">Giỏ hàng</div>
            </div>
            <div class="checkout-step active">
                <div class="checkout-step-number">2</div>
                <div class="checkout-step-label">Thanh toán</div>
            </div>
            <div class="checkout-step">
                <div class="checkout-step-number">3</div>
                <div class="checkout-step-label">Hoàn tất</div>
            </div>
        </div>
    </div>

    <form method="POST" action="../controllers/orderController.php?action=create" id="checkoutForm" class="checkout-container">
        <!-- Cột trái: Thông tin và thanh toán -->
        <div>
            <!-- Thông tin giao hàng -->
            <div class="checkout-section">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"></path>
                        <circle cx="12" cy="10" r="3"></circle>
                    </svg>
                    Thông tin giao hàng
                </h3>

                <div class="form-group">
                    <label>Họ tên người nhận:</label>
                    <input type="text" value="<?= htmlspecialchars($_SESSION['ho_ten'] ?? 'Chưa cập nhật') ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Số điện thoại:</label>
                    <input type="text" value="<?= htmlspecialchars($_SESSION['sdt'] ?? 'Chưa cập nhật') ?>" disabled>
                </div>

                <div class="form-group">
                    <label>Địa chỉ nhận hàng <span style="color: var(--danger);">*</span>:</label>
                    <textarea name="dia_chi" placeholder="Nhập địa chỉ cụ thể (số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố)" required><?= htmlspecialchars($_SESSION['dia_chi'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Phương thức thanh toán -->
            <div class="checkout-section">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect>
                        <line x1="1" y1="10" x2="23" y2="10"></line>
                    </svg>
                    Phương thức thanh toán
                </h3>

                <div class="payment-methods">
                    <!-- COD -->
                    <label class="payment-method selected" data-payment="PT001">
                        <input type="radio" name="id_pttt" value="PT001" checked required>
                        <div class="payment-method-icon">💰</div>
                        <div class="payment-method-info">
                            <div class="payment-method-name">Thanh toán khi nhận hàng (COD)</div>
                            <div class="payment-method-desc">Thanh toán bằng tiền mặt khi nhận được hàng</div>
                        </div>
                        <span class="payment-method-badge">Phổ biến</span>
                    </label>

                    <!-- MoMo -->
                    <label class="payment-method" data-payment="PT002">
                        <input type="radio" name="id_pttt" value="PT002" required>
                        <div class="payment-method-icon" style="background: #A50064; color: white;">📱</div>
                        <div class="payment-method-info">
                            <div class="payment-method-name">Ví điện tử MoMo</div>
                            <div class="payment-method-desc">Thanh toán nhanh chóng qua ví MoMo</div>
                        </div>
                        <span class="payment-method-badge" style="background: #A50064;">Online</span>
                    </label>

                    <!-- VNPay -->
                    <label class="payment-method" data-payment="PT003">
                        <input type="radio" name="id_pttt" value="PT003" required>
                        <div class="payment-method-icon" style="background: #0052A5; color: white;">🏦</div>
                        <div class="payment-method-info">
                            <div class="payment-method-name">VNPay</div>
                            <div class="payment-method-desc">Thanh toán qua thẻ ngân hàng nội địa</div>
                        </div>
                        <span class="payment-method-badge" style="background: #0052A5;">Online</span>
                    </label>

                    <!-- ZaloPay -->
                    <label class="payment-method" data-payment="PT004">
                        <input type="radio" name="id_pttt" value="PT004" required>
                        <div class="payment-method-icon" style="background: #0068FF; color: white;">💳</div>
                        <div class="payment-method-info">
                            <div class="payment-method-name">ZaloPay</div>
                            <div class="payment-method-desc">Thanh toán qua ví ZaloPay</div>
                        </div>
                        <span class="payment-method-badge" style="background: #0068FF;">Online</span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Cột phải: Tóm tắt đơn hàng -->
        <div>
            <div class="order-summary">
                <h3>
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
                        <line x1="3" y1="6" x2="21" y2="6"></line>
                        <path d="M16 10a4 4 0 0 1-8 0"></path>
                    </svg>
                    Tóm tắt đơn hàng
                </h3>

                <div class="order-items">
                    <?php foreach ($items as $itemData):
                        $book = $itemData['book'];
                        $item = $itemData['item'];
                        $imageUrl = getBookImageCheckout($book->id_sach);
                    ?>
                        <div class="order-item">
                            <img src="<?= htmlspecialchars($imageUrl) ?>"
                                alt="<?= htmlspecialchars($book->ten_sach) ?>"
                                class="order-item-image">

                            <div class="order-item-info">
                                <div class="order-item-name"><?= htmlspecialchars($book->ten_sach) ?></div>
                                <div class="order-item-details">Số lượng: <?= $item['quantity'] ?></div>
                                <?php if ($item['discount_percent'] > 0): ?>
                                    <div class="order-item-details">
                                        <span style="text-decoration: line-through; color: #999;">
                                            <?= number_format($itemData['originalPrice'], 0, ',', '.') ?>đ
                                        </span>
                                        <span style="color: var(--danger); margin-left: 8px;">
                                            -<?= $item['discount_percent'] ?>%
                                        </span>
                                    </div>
                                <?php endif; ?>
                                <div class="order-item-price"><?= number_format($itemData['finalPrice'], 0, ',', '.') ?>đ</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="order-summary-total">
                    <div class="summary-row">
                        <span class="summary-label">Tạm tính:</span>
                        <span class="summary-value"><?= number_format($subtotal, 0, ',', '.') ?>đ</span>
                    </div>
                    <?php if ($totalDiscount > 0): ?>
                        <div class="summary-row summary-discount">
                            <span class="summary-label">Giảm giá:</span>
                            <span class="summary-value">-<?= number_format($totalDiscount, 0, ',', '.') ?>đ</span>
                        </div>
                    <?php endif; ?>
                    <div class="summary-row total">
                        <span class="summary-label">Tổng cộng:</span>
                        <span class="summary-value"><?= number_format($total, 0, ',', '.') ?>đ</span>
                    </div>
                </div>

                <div class="checkout-actions">
                    <button type="submit" class="btn-checkout" id="submitBtn">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 13l4 4L19 7"></path>
                        </svg>
                        Xác nhận đặt hàng
                    </button>
                    <a href="cart.php" class="btn-back">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="15 18 9 12 15 6"></polyline>
                        </svg>
                        Quay lại giỏ hàng
                    </a>
                </div>

                <div class="payment-loading" id="paymentLoading">
                    <div class="spinner"></div>
                    <p>Đang xử lý thanh toán, vui lòng đợi...</p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý chọn phương thức thanh toán
        const paymentMethods = document.querySelectorAll('.payment-method');
        paymentMethods.forEach(method => {
            method.addEventListener('click', function() {
                paymentMethods.forEach(m => m.classList.remove('selected'));
                this.classList.add('selected');
                const radio = this.querySelector('input[type="radio"]');
                radio.checked = true;
            });
        });

        // Xử lý submit form
        const checkoutForm = document.getElementById('checkoutForm');
        const submitBtn = document.getElementById('submitBtn');
        const paymentLoading = document.getElementById('paymentLoading');

        checkoutForm.addEventListener('submit', function(e) {
            const selectedPayment = document.querySelector('input[name="id_pttt"]:checked').value;

            // Nếu là thanh toán online, hiển thị loading
            if (selectedPayment !== 'PT001') {
                submitBtn.disabled = true;
                paymentLoading.classList.add('active');
            }
        });
    });
</script>

<?php include_once '../includes/footer.php'; ?>