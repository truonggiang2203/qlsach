<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class Compare {
    private const MAX_ITEMS = 4; // Tối đa 4 sách để so sánh

    public function __construct() {
        if (!isset($_SESSION['compare']) || !is_array($_SESSION['compare'])) {
            $_SESSION['compare'] = [];
        }
    }

    /**
     * Lấy danh sách sách trong danh sách so sánh
     */
    public function getItems() {
        return $_SESSION['compare'];
    }

    /**
     * Lấy số lượng sách trong danh sách so sánh
     */
    public function getCount() {
        return count($_SESSION['compare']);
    }

    /**
     * Kiểm tra sách có trong danh sách so sánh không
     */
    public function exists($id_sach) {
        return isset($_SESSION['compare'][$id_sach]);
    }

    /**
     * Thêm sách vào danh sách so sánh
     */
    public function add($book) {
        if (!$book || !isset($book->id_sach)) {
            return ['success' => false, 'message' => 'Dữ liệu sách không hợp lệ'];
        }

        $id_sach = $book->id_sach;

        // Kiểm tra đã có trong danh sách chưa
        if ($this->exists($id_sach)) {
            return ['success' => false, 'message' => 'Sách này đã có trong danh sách so sánh'];
        }

        // Kiểm tra số lượng tối đa
        if ($this->getCount() >= self::MAX_ITEMS) {
            return ['success' => false, 'message' => 'Danh sách so sánh đã đầy (tối đa ' . self::MAX_ITEMS . ' sách)'];
        }

        // Thêm sách vào danh sách
        $_SESSION['compare'][$id_sach] = [
            'id_sach' => $book->id_sach,
            'ten_sach' => $book->ten_sach,
            'ten_tac_gia' => $book->ten_tac_gia ?? 'Không rõ',
            'ten_nxb' => $book->ten_nxb ?? 'Không rõ',
            'danh_sach_the_loai' => $book->danh_sach_the_loai ?? 'Chưa phân loại',
            'gia_sach_ban' => $book->gia_sach_ban ?? 0,
            'phan_tram_km' => $book->phan_tram_km ?? 0,
            'so_luong_ton' => $book->so_luong_ton ?? 0,
            'mo_ta' => $book->mo_ta ?? '',
            'hinh_anh' => $book->hinh_anh ?? ''
        ];

        return ['success' => true, 'message' => 'Đã thêm vào danh sách so sánh', 'count' => $this->getCount()];
    }

    /**
     * Xóa sách khỏi danh sách so sánh
     */
    public function remove($id_sach) {
        if (isset($_SESSION['compare'][$id_sach])) {
            unset($_SESSION['compare'][$id_sach]);
            return ['success' => true, 'message' => 'Đã xóa khỏi danh sách so sánh'];
        }
        return ['success' => false, 'message' => 'Sách không có trong danh sách so sánh'];
    }

    /**
     * Xóa toàn bộ danh sách so sánh
     */
    public function clear() {
        $_SESSION['compare'] = [];
        return ['success' => true, 'message' => 'Đã xóa toàn bộ danh sách so sánh'];
    }

    /**
     * Lấy số lượng tối đa
     */
    public function getMaxItems() {
        return self::MAX_ITEMS;
    }
}
?>

