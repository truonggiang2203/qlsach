<?php
require_once 'Database.php';

class Book {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy tất cả sách đang hoạt động
    public function getAllBooks() {
        $sql = "SELECT s.*, l.ten_loai, n.ten_nxb, k.phan_tram_km, g.gia_sach_ban 
                FROM sach s
                JOIN loai_sach l ON s.id_loai = l.id_loai
                JOIN nxb n ON s.id_nxb = n.id_nxb
                JOIN khuyen_mai k ON s.id_km = k.id_km
                JOIN gia_sach g ON s.id_sach = g.id_sach
                WHERE s.trang_thai_sach = 1
                GROUP BY s.id_sach";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy sách theo ID
    public function getBookById($id_sach) {
        $sql = "SELECT s.*, n.ten_nxb, l.ten_loai, k.phan_tram_km, g.gia_sach_ban 
                FROM sach s
                JOIN nxb n ON s.id_nxb = n.id_nxb
                JOIN loai_sach l ON s.id_loai = l.id_loai
                JOIN khuyen_mai k ON s.id_km = k.id_km
                JOIN gia_sach g ON s.id_sach = g.id_sach
                WHERE s.id_sach = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach]);
        return $stmt->fetch();
    }

    // Tìm kiếm theo tên sách
    public function searchBooks($keyword) {
        $sql = "SELECT s.*, g.gia_sach_ban 
                FROM sach s
                JOIN gia_sach g ON s.id_sach = g.id_sach
                WHERE s.ten_sach LIKE ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['%' . $keyword . '%']);
        return $stmt->fetchAll();
    }

    // Giảm số lượng tồn khi đặt hàng
    public function reduceStock($id_sach, $so_luong) {
        $sql = "UPDATE sach SET so_luong_ton = so_luong_ton - ? WHERE id_sach = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$so_luong, $id_sach]);
    }
}
?>
