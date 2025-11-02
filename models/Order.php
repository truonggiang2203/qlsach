<?php
require_once 'Database.php';
require_once 'Book.php';

class Order {
    private $db;
    private $bookModel;

    public function __construct() {
        $this->db = new Database();
        $this->bookModel = new Book();
    }

    // 🧾 Tạo đơn hàng mới
    public function createOrder($id_don_hang, $id_tk, $dia_chi, $cartItems, $id_pttt) {
        try {
            $this->db->prepare("START TRANSACTION")->execute();

            // 1️⃣ Tạo đơn hàng
            $sql = "INSERT INTO don_hang (id_don_hang, id_tk, id_trang_thai, ngay_gio_tao_don, dia_chi_nhan_hang)
                    VALUES (?, ?, 1, NOW(), ?)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_don_hang, $id_tk, $dia_chi]);

            // 2️⃣ Lưu chi tiết đơn hàng (chỉ lưu số lượng)
            foreach ($cartItems as $item) {
                $sql = "INSERT INTO chi_tiet_don_hang (id_don_hang, id_sach, so_luong_ban)
                        VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$id_don_hang, $item['id_sach'], $item['so_luong']]);

                // Giảm tồn kho
                $this->bookModel->reduceStock($item['id_sach'], $item['so_luong']);
            }

            // 3️⃣ Thêm bản ghi thanh toán
            $sql = "INSERT INTO thanh_toan (id_pttt, id_don_hang, trang_thai_tt, ngay_gio_thanh_toan)
                    VALUES (?, ?, 0, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_pttt, $id_don_hang]);

            $this->db->prepare("COMMIT")->execute();
            return true;

        } catch (PDOException $e) {
            $this->db->prepare("ROLLBACK")->execute();
            return false;
        }
    }

    // 📦 Lấy danh sách đơn hàng của người dùng
    public function getOrdersByUser($id_tk) {
        $sql = "SELECT 
                    dh.*, 
                    tt.trang_thai_dh,
                    COALESCE(SUM(ct.so_luong_ban * gs.gia_sach_ban), 0) AS tong_tien,
                    MAX(tto.trang_thai_tt) AS trang_thai_tt
                FROM don_hang dh
                JOIN trang_thai_don_hang tt ON dh.id_trang_thai = tt.id_trang_thai
                LEFT JOIN chi_tiet_don_hang ct ON dh.id_don_hang = ct.id_don_hang
                LEFT JOIN gia_sach gs ON ct.id_sach = gs.id_sach
                LEFT JOIN thanh_toan tto ON dh.id_don_hang = tto.id_don_hang
                WHERE dh.id_tk = ?
                GROUP BY dh.id_don_hang, dh.ngay_gio_tao_don, dh.dia_chi_nhan_hang, tt.trang_thai_dh, dh.id_trang_thai
                ORDER BY dh.ngay_gio_tao_don DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        return $stmt->fetchAll();
    }

    // 🔍 Lấy chi tiết từng đơn hàng
    public function getOrderDetails($id_don_hang) {
        $sql = "SELECT s.ten_sach, ct.so_luong_ban, gs.gia_sach_ban
                FROM chi_tiet_don_hang ct
                JOIN sach s ON ct.id_sach = s.id_sach
                JOIN gia_sach gs ON s.id_sach = gs.id_sach
                WHERE ct.id_don_hang = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_don_hang]);
        return $stmt->fetchAll();
    }

    // ❌ Hủy đơn hàng
    public function cancelOrder($id_don_hang) {
        $sql = "UPDATE don_hang SET id_trang_thai = 5 WHERE id_don_hang = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_don_hang]);
    }
}
?>
