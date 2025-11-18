<?php
require_once 'Database.php';

class Comment {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // 🧠 Lấy tất cả bình luận theo sách (đã sửa SQL query)
    public function getCommentsByBook($id_sach) {
        // Kiểm tra xem bảng có cột id_tk không
        $hasIdTk = $this->checkColumnExists('binh_luan', 'id_tk');
        
        if ($hasIdTk) {
            $sql = "SELECT b.*, t.ho_ten, t.id_tk
                    FROM binh_luan b
                    LEFT JOIN tai_khoan t ON b.id_tk = t.id_tk
                    WHERE b.id_sach = ?
                    ORDER BY COALESCE(b.ngay_gio_tao, NOW()) DESC, b.id_bl DESC";
        } else {
            // Fallback nếu chưa có cột id_tk
            $sql = "SELECT b.*, 'Người dùng' as ho_ten, NULL as id_tk
                    FROM binh_luan b
                    WHERE b.id_sach = ?
                    ORDER BY b.id_bl DESC";
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach]);
        return $stmt->fetchAll();
    }
    
    // 🔍 Helper method để kiểm tra cột có tồn tại không
    private function checkColumnExists($table, $column) {
        try {
            $conn = $this->db->getConnection();
            $stmt = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    // 🧾 Thêm bình luận mới
    public function addComment($id_bl, $id_sach, $id_tk, $binh_luan, $so_sao) {
        // Kiểm tra xem bảng có cột id_tk không
        $hasIdTk = $this->checkColumnExists('binh_luan', 'id_tk');
        
        if ($hasIdTk) {
            $sql = "INSERT INTO binh_luan (id_bl, id_sach, id_tk, binh_luan, so_sao, ngay_gio_tao) 
                    VALUES (?, ?, ?, ?, ?, NOW())";
            $params = [$id_bl, $id_sach, $id_tk, $binh_luan, $so_sao];
        } else {
            $sql = "INSERT INTO binh_luan (id_bl, id_sach, binh_luan, so_sao) 
                    VALUES (?, ?, ?, ?)";
            $params = [$id_bl, $id_sach, $binh_luan, $so_sao];
        }
        
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute($params);
            return true;
        } catch (PDOException $e) {
            error_log("Comment Error: " . $e->getMessage());
            return false;
        }
    }

    // 🧮 Tính trung bình sao cho sách
    public function getAverageRating($id_sach) {
        $sql = "SELECT AVG(so_sao) AS diem_tb, COUNT(*) AS so_luong 
                FROM binh_luan 
                WHERE id_sach = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach]);
        $result = $stmt->fetch();
        return [
            'average' => $result ? round($result->diem_tb, 1) : 0,
            'count' => $result ? (int)$result->so_luong : 0
        ];
    }

    // 📊 Lấy phân bố đánh giá (số lượng theo từng sao)
    public function getRatingDistribution($id_sach) {
        $sql = "SELECT so_sao, COUNT(*) as so_luong 
                FROM binh_luan 
                WHERE id_sach = ? 
                GROUP BY so_sao 
                ORDER BY so_sao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach]);
        $results = $stmt->fetchAll();
        
        $distribution = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
        foreach ($results as $row) {
            $star = (int)$row->so_sao;
            if (isset($distribution[$star])) {
                $distribution[$star] = (int)$row->so_luong;
            }
        }
        
        return $distribution;
    }

    // ✏️ Cập nhật bình luận
    public function updateComment($id_bl, $id_tk, $binh_luan, $so_sao) {
        $sql = "UPDATE binh_luan 
                SET binh_luan = ?, so_sao = ? 
                WHERE id_bl = ? AND id_tk = ?";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute([$binh_luan, $so_sao, $id_bl, $id_tk]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Update Comment Error: " . $e->getMessage());
            return false;
        }
    }

    // 🗑️ Xóa bình luận
    public function deleteComment($id_bl, $id_tk) {
        $sql = "DELETE FROM binh_luan 
                WHERE id_bl = ? AND id_tk = ?";
        $stmt = $this->db->prepare($sql);
        try {
            $stmt->execute([$id_bl, $id_tk]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Delete Comment Error: " . $e->getMessage());
            return false;
        }
    }

    // 🔍 Kiểm tra user đã bình luận chưa
    public function hasUserCommented($id_sach, $id_tk) {
        $hasIdTk = $this->checkColumnExists('binh_luan', 'id_tk');
        
        if (!$hasIdTk) {
            return false;
        }
        
        $sql = "SELECT id_bl FROM binh_luan 
                WHERE id_sach = ? AND id_tk = ? 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach, $id_tk]);
        return $stmt->rowCount() > 0;
    }

    // 📝 Lấy bình luận của user cho sách
    public function getUserComment($id_sach, $id_tk) {
        $hasIdTk = $this->checkColumnExists('binh_luan', 'id_tk');
        
        if (!$hasIdTk) {
            return null;
        }
        
        $sql = "SELECT * FROM binh_luan 
                WHERE id_sach = ? AND id_tk = ? 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach, $id_tk]);
        return $stmt->fetch();
    }

    // 🛒 Kiểm tra user đã mua sách chưa (đơn hàng đã hoàn thành)
    public function hasUserPurchasedBook($id_sach, $id_tk) {
        // Kiểm tra xem có bảng don_hang và chi_tiet_don_hang không
        $hasDonHang = $this->checkColumnExists('don_hang', 'id_tk');
        
        if (!$hasDonHang) {
            // Nếu không có bảng đơn hàng, cho phép tất cả user đã đăng nhập đánh giá
            return true;
        }
        
        // Kiểm tra user có đơn hàng đã hoàn thành (id_trang_thai = 4) chứa sách này không
        $sql = "SELECT COUNT(*) as count
                FROM chi_tiet_don_hang ct
                JOIN don_hang dh ON ct.id_don_hang = dh.id_don_hang
                WHERE ct.id_sach = ? 
                AND dh.id_tk = ?
                AND dh.id_trang_thai = 4
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach, $id_tk]);
        $result = $stmt->fetch();
        
        return $result && $result->count > 0;
    }

    // 🛒 Kiểm tra user có đơn hàng đang xử lý chứa sách này không (chưa hoàn thành)
    public function hasUserOrderedBook($id_sach, $id_tk) {
        $hasDonHang = $this->checkColumnExists('don_hang', 'id_tk');
        
        if (!$hasDonHang) {
            return false;
        }
        
        // Kiểm tra user có đơn hàng (bất kỳ trạng thái nào, trừ hủy) chứa sách này không
        $sql = "SELECT COUNT(*) as count
                FROM chi_tiet_don_hang ct
                JOIN don_hang dh ON ct.id_don_hang = dh.id_don_hang
                WHERE ct.id_sach = ? 
                AND dh.id_tk = ?
                AND dh.id_trang_thai != 5
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach, $id_tk]);
        $result = $stmt->fetch();
        
        return $result && $result->count > 0;
    }
}
?>
