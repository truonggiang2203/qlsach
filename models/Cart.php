<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Cart {

    public function __construct() {
        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /* =====================================================
        LẤY TOÀN BỘ GIỎ HÀNG
    ===================================================== */
    public function getItems() {
        return $_SESSION['cart'];
    }

    /* =====================================================
        THÊM SẢN PHẨM
    ===================================================== */
    public function add($book, $quantity_to_add = 1) {

        if (!$book || $quantity_to_add <= 0) return;

        $id_sach = $book->id_sach;
        $stock = $book->so_luong_ton;

        // Nếu SP đã tồn tại trong giỏ
        if (isset($_SESSION['cart'][$id_sach])) {

            $current_qty = $_SESSION['cart'][$id_sach]['quantity'];
            $new_qty = $current_qty + $quantity_to_add;

            if ($new_qty > $stock) $new_qty = $stock;

            $_SESSION['cart'][$id_sach]['quantity'] = $new_qty;
            return;
        }

        // Nếu SP chưa có → thêm mới
        if ($quantity_to_add > $stock) $quantity_to_add = $stock;

        $_SESSION['cart'][$id_sach] = [
            'id_sach'         => $book->id_sach,
            'name'            => $book->ten_sach,
            'price'           => $book->gia_sach_ban,
            'image'           => $book->hinh_anh ?? 'https://via.placeholder.com/100',
            'quantity'        => $quantity_to_add,
            'discount_percent'=> $book->phan_tram_km ?? 0,
            'stock'           => $stock
        ];
    }

    /* =====================================================
        CẬP NHẬT SỐ LƯỢNG
    ===================================================== */
    public function update($id_sach, $quantity) {

        if (!isset($_SESSION['cart'][$id_sach])) return;

        if ($quantity <= 0) {
            unset($_SESSION['cart'][$id_sach]);
            return;
        }

        $stock = $_SESSION['cart'][$id_sach]['stock'];

        if ($quantity > $stock) $quantity = $stock;

        $_SESSION['cart'][$id_sach]['quantity'] = $quantity;
    }

    /* =====================================================
        XÓA 1 SẢN PHẨM
    ===================================================== */
    public function remove($id_sach) {
        unset($_SESSION['cart'][$id_sach]);
    }

    /* =====================================================
        XÓA TOÀN BỘ GIỎ
    ===================================================== */
    public function clear() {
        $_SESSION['cart'] = [];
    }

    /* =====================================================
        ĐẾM TỔNG SỐ LƯỢNG GIỎ HÀNG
    ===================================================== */
    public function getCount() {
        return array_sum(array_column($_SESSION['cart'], 'quantity'));
    }

    /* =====================================================
        TÍNH TỔNG GIỎ HÀNG
    ===================================================== */
    public function calculateTotals() {

        $subtotal = 0;
        $discountTotal = 0;

        foreach ($_SESSION['cart'] as $item) {

            $price = $item['price'];
            $qty   = $item['quantity'];
            $discount = $item['discount_percent'];

            $subtotal += $price * $qty;

            if ($discount > 0) {
                $discountTotal += ($price * $discount / 100) * $qty;
            }
        }

        $total = $subtotal - $discountTotal;

        return [
            'subtotal'      => $subtotal,
            'totalDiscount' => $discountTotal,
            'total'         => $total
        ];
    }

    /* =====================================================
        TÍNH TẠM TÍNH 1 SẢN PHẨM
    ===================================================== */
    public function getItemSubtotal($id_sach) {

        if (!isset($_SESSION['cart'][$id_sach])) return 0;

        $item = $_SESSION['cart'][$id_sach];

        $price = $item['price'];
        $discount = $item['discount_percent'];
        $qty = $item['quantity'];

        $discountedPrice = $price * (1 - $discount / 100);

        return $discountedPrice * $qty;
    }
}
?>
