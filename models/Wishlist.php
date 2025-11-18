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

    // Lấy toàn bộ wishlist của user với đầy đủ thông tin
    public function getUserWishlist($id_tk) {
        $sql = "SELECT 
                    s.*, 
                    n.ten_nxb, 
                    k.phan_tram_km, 
                    g.gia_sach_ban,
                    GROUP_CONCAT(DISTINCT tl.ten_the_loai SEPARATOR ', ') AS danh_sach_the_loai,
                    GROUP_CONCAT(DISTINCT tg.ten_tac_gia SEPARATOR ', ') AS ten_tac_gia
                FROM wishlist w 
                JOIN sach s ON w.id_sach = s.id_sach
                LEFT JOIN sach_theloai stl ON s.id_sach = stl.id_sach
                LEFT JOIN the_loai tl ON stl.id_the_loai = tl.id_the_loai
                LEFT JOIN s_tg st ON s.id_sach = st.id_sach
                LEFT JOIN tac_gia tg ON st.id_tac_gia = tg.id_tac_gia
                JOIN nxb n ON s.id_nxb = n.id_nxb
                JOIN khuyen_mai k ON s.id_km = k.id_km
                JOIN gia_sach g ON g.id_sach = s.id_sach
                JOIN thoi_diem td ON g.tg_gia_bd = td.tg_gia_bd
                WHERE w.id_tk = ?
                AND NOW() BETWEEN td.tg_gia_bd AND COALESCE(td.tg_gia_kt, '2099-12-31 23:59:59')
                GROUP BY s.id_sach
                ORDER BY s.ten_sach ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        return $stmt->fetchAll();
    }

    // Xóa tất cả sách khỏi wishlist
    public function clearAll($id_tk) {
        $sql = "DELETE FROM wishlist WHERE id_tk = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_tk]);
    }

    // Đếm số lượng sách trong wishlist
    public function getCount($id_tk) {
        $sql = "SELECT COUNT(*) as count FROM wishlist WHERE id_tk = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        $result = $stmt->fetch();
        return $result->count ?? 0;
    }
}
