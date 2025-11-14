<?php
// 1. Tải Header (Full-width)
require_once __DIR__ . '/../includes/header.php'; 
// NẠP MODEL CART ĐỂ LẤY DỮ LIỆU
require_once __DIR__ . '/../models/Cart.php';

// 2. Khởi tạo Model và lấy dữ liệu
$cartModel = new Cart();
$cartItems = $cartModel->getItems(); // <-- Lấy dữ liệu từ Model
$cartIsEmpty = empty($cartItems);

// 3. Kiểm tra người dùng đăng nhập
$isLoggedIn = isset($_SESSION['id_tk']);
?>

<div class="container cart-page-container">
    <h2>Giỏ hàng của bạn</h2>

    <?php if ($cartIsEmpty): ?>
        <p class="cart-empty-message">Giỏ hàng của bạn đang trống. <a href="/qlsach/public/index.php">Tiếp tục mua sắm</a></p>
    <?php else: ?>
        
        <div style="text-align: right; margin-bottom: 15px;">
            <a href="/qlsach/controllers/cartController.php?action=clear" id="btn-clear-cart" class="btn-remove-item" style="text-decoration:none;">
                Làm trống giỏ hàng
            </a>
        </div>
        
        <form action="/qlsach/user/checkout.php" method="POST" id="form-cart-checkout">
            <div class="cart-layout">
            
                <div class="cart-items-list">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th class="cart-checkbox-cell"><input type="checkbox" id="cart-select-all"></th>
                                <th colspan="2">Sản phẩm</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Tạm tính</th>
                                <th>Xóa</th>
                            </tr>
                        </thead>
                        <tbody id="cart-tbody">    
                            <?php foreach ($cartItems as $item): // <-- Vẫn lặp qua $cartItems như cũ   
                                $discountPercent = $item['discount_percent'] ?? 0;
                                $discountedPrice = $item['price'] * (1 - $discountPercent / 100);
                                $originalItemSubtotal = $item['price'] * $item['quantity'];
                                $discountedItemSubtotal = $discountedPrice * $item['quantity'];
                            ?>
                                <tr id="cart-item-<?php echo $item['id']; ?>" 
                                    data-original-price="<?php echo $item['price']; ?>"
                                    data-discounted-price="<?php echo $discountedPrice; ?>">
                                    
                                    <td class="cart-checkbox-cell">
                                        <input type="checkbox" class="cart-item-select" name="selected_items[]" value="<?php echo $item['id']; ?>">
                                    </td>
                                    <td class="cart-item-image">
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </td>
                                    <td class="cart-item-name">
                                        <a href="/qlsach/public/book_detail.php?id_sach=<?php echo $item['id']; ?>">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </a>
                                    </td>
                                    <td class="cart-item-price">
                                        <?php if ($discountPercent > 0): ?>
                                            <span class="product-price-discounted"><?php echo number_format($discountedPrice); ?> đ</span>
                                            <span class="product-price-original"><?php echo number_format($item['price']); ?> đ</span>
                                        <?php else: ?>
                                            <span><?php echo number_format($item['price']); ?> đ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="cart-item-quantity" data-quantity="<?php echo $item['quantity']; ?>">
                                        <?php echo $item['quantity']; ?>
                                    </td>
                                    <td class="cart-item-subtotal">
                                        <?php if ($discountPercent > 0): ?>
                                            <span class="product-price-discounted"><?php echo number_format($discountedItemSubtotal); ?> đ</span>
                                            <span class="product-price-original"><?php echo number_format($originalItemSubtotal); ?> đ</span>
                                        <?php else: ?>
                                            <span class="product-price"><?php echo number_format($originalItemSubtotal); ?> đ</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="cart-item-remove">
                                        <a href="/qlsach/controllers/cartController.php?action=remove&id_sach=<?php echo $item['id']; ?>" class="btn-remove btn-remove-item">X</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="cart-summary">
                    <h4>Tổng cộng giỏ hàng</h4>
                    
                    <div class="summary-row">
                        <span>Tạm tính (Gốc)</span>
                        <span class="summary-price" id="cart-subtotal">0 đ</span>
                    </div>
                    
                    <div class="summary-row">
                        <span>Khuyến mãi</span>
                        <span class="summary-price" id="cart-discount">0 đ</span>
                    </div>
                    
                    <div class="summary-row total-row">
                        <span>Tổng cộng (Đã giảm)</span>
                        <span class="summary-price total-price" id="cart-total">0 đ</span>
                    </div>

                    <?php if ($isLoggedIn): ?>
                        <button type="submit" class="btn-checkout" id="btn-checkout-submit" disabled>
                            Tiến hành Thanh toán
                        </button>
                    <?php else: ?>
                        <a href="/qlsach/guest/login.php" class="btn-checkout" style="display: block; text-align: center; text-decoration: none;">
                            Đăng nhập để thanh toán
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<script src="/qlsach/public/js/main.js"></script>

<?php 
require_once __DIR__ . '/../includes/footer.php'; 
?>