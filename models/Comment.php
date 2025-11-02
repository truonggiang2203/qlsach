<?php
require_once 'Database.php';

class Comment {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // 🧠 Lấy tất cả bình luận theo sách
    public function getCommentsByBook($id_sach) {
        $sql = "SELECT b.*, t.ho_ten 
                FROM binh_luan b
                JOIN tai_khoan t ON b.id_sach = ? AND t.id_tk = t.id_tk
                WHERE b.id_sach = ?
                ORDER BY b.id_bl DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach, $id_sach]);
        return $stmt->fetchAll();
    }

    // 🧾 Thêm bình luận mới
    public function addComment($id_bl, $id_sach, $binh_luan, $so_sao) {
        $sql = "INSERT INTO binh_luan (id_bl, id_sach, binh_luan, so_sao) VALUES (?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute([$id_bl, $id_sach, $binh_luan, $so_sao]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // 🧮 Tính trung bình sao cho sách
    public function getAverageRating($id_sach) {
        $sql = "SELECT AVG(so_sao) AS diem_tb FROM binh_luan WHERE id_sach = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach]);
        $result = $stmt->fetch();
        return $result ? round($result->diem_tb, 1) : 0;
    }
}
?>
