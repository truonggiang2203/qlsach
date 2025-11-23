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
     * 📦 Lấy danh sách đơn hàng của người dùng (có phương thức thanh toán)
     */
    public function getOrdersByUser($id_tk) {
        $sql = "SELECT 
                    dh.*, 
                    tt.trang_thai_dh,
                    COALESCE(SUM(ct.so_luong_ban * ct.don_gia_ban), 0) AS tong_tien,
                    MAX(tto.trang_thai_tt) AS trang_thai_tt,
                    (SELECT pttt.ten_pttt 
                     FROM thanh_toan tto2 
                     JOIN phuong_thuc_thanh_toan pttt ON tto2.id_pttt = pttt.id_pttt
                     WHERE tto2.id_don_hang = dh.id_don_hang 
                     LIMIT 1) AS ten_pttt,
                    COUNT(DISTINCT ct.id_sach) AS so_san_pham
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
     *Lấy chi tiết từng đơn hàng (có hình ảnh)
     */
    public function getOrderDetails($id_don_hang) {
        $sql = "SELECT 
                    s.id_sach,
                    s.ten_sach, 
                    ct.so_luong_ban, 
                    ct.don_gia_ban,
                    (ct.so_luong_ban * ct.don_gia_ban) AS thanh_tien
                FROM chi_tiet_don_hang ct
                JOIN sach s ON ct.id_sach = s.id_sach
                WHERE ct.id_don_hang = ?
                ORDER BY s.ten_sach";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_don_hang]);
        return $stmt->fetchAll();
    }

    /**
     * Lấy thông tin phương thức thanh toán của đơn hàng
     */
    public function getPaymentMethod($id_don_hang) {
        $sql = "SELECT pttt.ten_pttt, tt.trang_thai_tt, tt.ngay_gio_thanh_toan
                FROM thanh_toan tt
                JOIN phuong_thuc_thanh_toan pttt ON tt.id_pttt = pttt.id_pttt
                WHERE tt.id_don_hang = ?
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_don_hang]);
        return $stmt->fetch();
    }

    /**
     * Thống kê đơn hàng cho trang hồ sơ
     */
    public function getOrderSummary($id_tk) {
        $sql = "SELECT 
                    COUNT(*) AS total_orders,
                    SUM(CASE WHEN dh.id_trang_thai = 1 THEN 1 ELSE 0 END) AS pending_orders,
                    SUM(CASE WHEN dh.id_trang_thai = 3 THEN 1 ELSE 0 END) AS shipping_orders,
                    SUM(CASE WHEN dh.id_trang_thai = 4 THEN 1 ELSE 0 END) AS completed_orders,
                    SUM(CASE WHEN dh.id_trang_thai = 5 THEN 1 ELSE 0 END) AS cancelled_orders,
                    COALESCE(SUM(CASE WHEN dh.id_trang_thai = 4 THEN (ct.so_luong_ban * ct.don_gia_ban) ELSE 0 END), 0) AS total_spent
                FROM don_hang dh
                LEFT JOIN chi_tiet_don_hang ct ON dh.id_don_hang = ct.id_don_hang
                WHERE dh.id_tk = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        $result = $stmt->fetch();

        return [
            'total_orders' => (int)($result->total_orders ?? 0),
            'pending_orders' => (int)($result->pending_orders ?? 0),
            'shipping_orders' => (int)($result->shipping_orders ?? 0),
            'completed_orders' => (int)($result->completed_orders ?? 0),
            'cancelled_orders' => (int)($result->cancelled_orders ?? 0),
            'total_spent' => (float)($result->total_spent ?? 0),
        ];
    }

    /**
     * Lấy danh sách đơn hàng gần nhất
     */
    public function getRecentOrders($id_tk, $limit = 5) {
        $limit = max(1, (int)$limit);
        $sql = "SELECT 
                    dh.id_don_hang,
                    dh.ngay_gio_tao_don,
                    dh.id_trang_thai,
                    tt.trang_thai_dh,
                    COALESCE(SUM(ct.so_luong_ban * ct.don_gia_ban), 0) AS tong_tien,
                    MAX(tto.trang_thai_tt) AS trang_thai_tt
                FROM don_hang dh
                JOIN trang_thai_don_hang tt ON dh.id_trang_thai = tt.id_trang_thai
                LEFT JOIN chi_tiet_don_hang ct ON dh.id_don_hang = ct.id_don_hang
                LEFT JOIN thanh_toan tto ON dh.id_don_hang = tto.id_don_hang
                WHERE dh.id_tk = ?
                GROUP BY dh.id_don_hang
                ORDER BY dh.ngay_gio_tao_don DESC
                LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $id_tk);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     *Hủy đơn hàng
     */
    public function cancelOrder($id_don_hang) {
        try {
            $this->db->beginTransaction();
            
            // 1. Lấy chi tiết đơn hàng
            $sql = "SELECT id_sach, so_luong_ban FROM chi_tiet_don_hang WHERE id_don_hang = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_don_hang]);
            $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // 2. Khôi phục tồn kho cho từng sản phẩm
            foreach ($items as $item) {
                $sql = "UPDATE sach SET so_luong_ton = so_luong_ton + ? WHERE id_sach = ?";
                $stmt = $this->db->prepare($sql);
                $stmt->execute([$item['so_luong_ban'], $item['id_sach']]);
            }
            
            // 3. Cập nhật trạng thái đơn hàng = 5 (Đã hủy)
            $sql = "UPDATE don_hang SET id_trang_thai = 5 WHERE id_don_hang = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id_don_hang]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Cancel Order Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Lấy timeline trạng thái đơn hàng
     */
    public function getOrderTimeline($id_don_hang) {
        $sql = "SELECT dh.id_trang_thai, ttdh.trang_thai_dh, dh.ngay_gio_tao_don
                FROM don_hang dh
                JOIN trang_thai_don_hang ttdh ON dh.id_trang_thai = ttdh.id_trang_thai
                WHERE dh.id_don_hang = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_don_hang]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>