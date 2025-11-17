<?php
require_once __DIR__ . '/Database.php';

class Wishlist {

    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Thêm vào wishlist
    public function add($id_tk, $id_sach) {
        $sql = "INSERT IGNORE INTO wishlist (id_tk, id_sach) VALUES (?, ?)";
        return $this->db->prepare($sql)->execute([$id_tk, $id_sach]);
    }

    // Xóa khỏi wishlist
    public function remove($id_tk, $id_sach) {
        $sql = "DELETE FROM wishlist WHERE id_tk = ? AND id_sach = ?";
        return $this->db->prepare($sql)->execute([$id_tk, $id_sach]);
    }

    // Kiểm tra đã tồn tại chưa
    public function exists($id_tk, $id_sach) {
        $sql = "SELECT * FROM wishlist WHERE id_tk = ? AND id_sach = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk, $id_sach]);
        $result = $stmt->fetch();
        return !empty($result);
    }

    // Lấy toàn bộ wishlist của user
    public function getUserWishlist($id_tk) {
        $sql = "SELECT s.*, g.gia_sach_ban
                FROM wishlist w 
                JOIN sach s ON w.id_sach = s.id_sach
                JOIN gia_sach g ON g.id_sach = s.id_sach
                WHERE w.id_tk = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        return $stmt->fetchAll();
    }
}
