<?php
require_once 'Database.php';
require_once 'Book.php';

class Order {
    private $db;
    private $bookModel;

    public function __construct() {
        $this->db = new Database();
        $this->bookModel = new Book($this->db);
    }

    /**
     * 🧾 Tạo đơn hàng mới
     * (ĐÃ NÂNG CẤP: KIỂM TRA TỒN KHO LẦN CUỐI)
     */
    public function createOrder($id_don_hang, $id_tk, $dia_chi, $cartItems, $id_pttt) {
        try {
            $this->db->beginTransaction();

            // 1️⃣ Tạo đơn hàng
            $sql_dh = "INSERT INTO don_hang (id_don_hang, id_tk, id_trang_thai, ngay_gio_tao_don, dia_chi_nhan_hang)
                       VALUES (?, ?, 1, NOW(), ?)";
            $stmt_dh = $this->db->prepare($sql_dh);
            $stmt_dh->execute([$id_don_hang, $id_tk, $dia_chi]);

            // 2️⃣ Lưu chi tiết đơn hàng
            $sql_ct = "INSERT INTO chi_tiet_don_hang (id_don_hang, id_sach, so_luong_ban, don_gia_ban)
                       VALUES (?, ?, ?, ?)";
            $stmt_ct = $this->db->prepare($sql_ct);
            
            foreach ($cartItems as $item) {
                // SỬ DỤNG KEY TIẾNG ANH
                $id_sach  = $item['id_sach'];
                $quantity = $item['quantity'];
                $price    = $item['price'];
                $discount = $item['discount_percent'] ?? 0;
                
                // === NÂNG CẤP: KIỂM TRA TỒN KHO THỰC TẾ ===
                // Lấy tồn kho mới nhất từ CSDL
                $book_in_db = $this->bookModel->getBookById($id_sach);
                if (!$book_in_db || $book_in_db->so_luong_ton < $quantity) {
                    // Nếu không đủ hàng, hủy toàn bộ giao dịch
                    throw new Exception("Sản phẩm '" . htmlspecialchars($item['name']) . "' không đủ tồn kho. Chỉ còn " . ($book_in_db->so_luong_ton ?? 0) . " quyển.");
                }
                // === KẾT THÚC NÂNG CẤP ===

                // Tính giá bán cuối cùng
                $final_price = $price * (1 - $discount / 100);

                // Thêm vào chi tiết đơn hàng
                $stmt_ct->execute([$id_don_hang, $id_sach, $quantity, $final_price]);

                // Giảm tồn kho (hàm này giờ đã an toàn)
                $this->bookModel->reduceStock($id_sach, $quantity);
            }

            // 3️⃣ Thêm bản ghi thanh toán
            $sql_tt = "INSERT INTO thanh_toan (id_pttt, id_don_hang, trang_thai_tt, ngay_gio_thanh_toan)
                       VALUES (?, ?, 0, NOW())";
            $stmt_tt = $this->db->prepare($sql_tt);
            $stmt_tt->execute([$id_pttt, $id_don_hang]);

            // Hoàn tất
            $this->db->commit();
            
            // Trả về true nếu thành công
            return ['success' => true];

        } catch (Exception $e) { // Bắt lỗi chung
            $this->db->rollBack();
            // Trả về thông báo lỗi cụ thể
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * 📦 Lấy danh sách đơn hàng của người dùng
     */
    public function getOrdersByUser($id_tk) {
        $sql = "SELECT 
                    dh.*, 
                    tt.trang_thai_dh,
                    COALESCE(SUM(ct.so_luong_ban * ct.don_gia_ban), 0) AS tong_tien,
                    MAX(tto.trang_thai_tt) AS trang_thai_tt
                FROM don_hang dh
                JOIN trang_thai_don_hang tt ON dh.id_trang_thai = tt.id_trang_thai
                LEFT JOIN chi_tiet_don_hang ct ON dh.id_don_hang = ct.id_don_hang
                LEFT JOIN thanh_toan tto ON dh.id_don_hang = tto.id_don_hang
                WHERE dh.id_tk = ?
                GROUP BY dh.id_don_hang
                ORDER BY dh.ngay_gio_tao_don DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        return $stmt->fetchAll();
    }

    /**
     *Lấy chi tiết từng đơn hàng
     */
    public function getOrderDetails($id_don_hang) {
        $sql = "SELECT s.ten_sach, ct.so_luong_ban, ct.don_gia_ban
                FROM chi_tiet_don_hang ct
                JOIN sach s ON ct.id_sach = s.id_sach
                WHERE ct.id_don_hang = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_don_hang]);
        return $stmt->fetchAll();
    }

    /**
     *Hủy đơn hàng
     */
    public function cancelOrder($id_don_hang) {
        // TODO: Thêm logic khôi phục tồn kho (restoreStock)
        $sql = "UPDATE don_hang SET id_trang_thai = 5 WHERE id_don_hang = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id_don_hang]);
    }
}
?>