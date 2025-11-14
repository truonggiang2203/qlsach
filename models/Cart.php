<?php
// Đảm bảo session đã được khởi tạo trước khi dùng class này
// (thường là ở header.php hoặc file index gốc)

class Cart {

    /**
     * Khởi tạo giỏ hàng trong SESSION khi class được gọi
     */
    public function __construct() {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Lấy tất cả sản phẩm trong giỏ hàng
     * @return array
     */
    public function getItems() {
        return $_SESSION['cart'];
    }

    /**
     * Thêm sản phẩm vào giỏ hàng
     * @param object $book Đối tượng Sách (từ BookModel)
     * @param int $quantity_to_add Số lượng muốn thêm
     */
    public function add($book, $quantity_to_add = 1) {
        if (!$book || $quantity_to_add <= 0) {
            return;
        }

        $id_sach = $book->id_sach;
        $stock = $book->so_luong_ton;

        // 1. Nếu sản phẩm đã có trong giỏ
        if (isset($_SESSION['cart'][$id_sach])) {
            $current_quantity = $_SESSION['cart'][$id_sach]['quantity'];
            $new_quantity = $current_quantity + $quantity_to_add;

            // Kiểm tra tồn kho
            if ($new_quantity > $stock) {
                $new_quantity = $stock; // Chỉ cho phép thêm tối đa
            }
            $_SESSION['cart'][$id_sach]['quantity'] = $new_quantity;
        
        // 2. Nếu sản phẩm chưa có trong giỏ
        } else {
            // Kiểm tra tồn kho
            if ($quantity_to_add > $stock) {
                $quantity_to_add = $stock;
            }
            
            // Thêm mới với key Tiếng Anh
            $_SESSION['cart'][$id_sach] = [
                'id' => $book->id_sach,
                'name' => $book->ten_sach,
                'price' => $book->gia_sach_ban,
                'image' => $book->hinh_anh ?? 'https://via.placeholder.com/100',
                'quantity' => $quantity_to_add,
                'discount_percent' => $book->phan_tram_km ?? 0,
                'stock' => $stock
            ];
        }
    }

    /**
     * Cập nhật số lượng của 1 sản phẩm
     * @param int $id_sach ID Sách
     * @param int $quantity Số lượng mới
     */
    public function update($id_sach, $quantity) {
        if (!isset($_SESSION['cart'][$id_sach]) || $quantity <= 0) {
            return;
        }

        $stock = $_SESSION['cart'][$id_sach]['stock'] ?? 0;
        
        if ($quantity > $stock) {
            $quantity = $stock; // Không cho phép cập nhật quá tồn kho
        }
        
        $_SESSION['cart'][$id_sach]['quantity'] = $quantity;
    }

    /**
     * Xóa 1 sản phẩm khỏi giỏ
     * @param int $id_sach ID Sách
     */
    public function remove($id_sach) {
        unset($_SESSION['cart'][$id_sach]);
    }

    /**
     * Làm trống toàn bộ giỏ hàng
     */
    public function clear() {
        $_SESSION['cart'] = [];
    }

    /**
     * Đếm tổng số lượng sản phẩm (ví dụ: 2 sách A + 3 sách B = 5)
     * @return int
     */
    public function getCount() {
        return array_sum(array_column($_SESSION['cart'], 'quantity'));
    }

    /**
     * Tính toán tổng tiền
     * @return array
     */
    public function calculateTotals() {
        $subtotal = 0;
        $totalDiscount = 0;
        $cart = $this->getItems(); // Lấy giỏ hàng

        foreach ($cart as $item) {
            $price = $item['price'] ?? 0;
            $quantity = $item['quantity'] ?? 0;
            $discount_percent = $item['discount_percent'] ?? 0;

            $itemTotal = $price * $quantity;
            $subtotal += $itemTotal;
            
            if ($discount_percent > 0) {
                $totalDiscount += ($price * $discount_percent / 100) * $quantity;
            }
        }
        $total = $subtotal - $totalDiscount;
        return ['subtotal' => $subtotal, 'totalDiscount' => $totalDiscount, 'total' => $total];
    }
    
    /**
     * Tính tạm tính của 1 sản phẩm (hữu ích cho AJAX)
     * @return float
     */
    public function getItemSubtotal($id_sach) {
        if (!isset($_SESSION['cart'][$id_sach])) {
            return 0;
        }
        
        $item = $_SESSION['cart'][$id_sach];
        $discountedPrice = $item['price'] * (1 - $item['discount_percent'] / 100);
        return $discountedPrice * $item['quantity'];
    }
}
?>