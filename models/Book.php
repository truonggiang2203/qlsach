<?php
require_once 'Database.php';

class Book {
    // SỬA 1: Đổi tên biến $db để rõ ràng hơn
    private $db_connection;

    // SỬA 2: Sửa lại hàm __construct để chấp nhận đối số
    public function __construct($db_conn = null) {
        if ($db_conn) {
            // Nếu được truyền vào (từ Order.php), hãy dùng nó
            $this->db_connection = $db_conn;
        } else {
            // Nếu không (hoặc file khác gọi), tự tạo kết nối mới
            $this->db_connection = new Database();
        }
    }

    // Lấy tất cả sách đang hoạt động
    public function getAllBooks() {
        
        // SỬA LẠI: Dùng GROUP_CONCAT để lấy TẤT CẢ thể loại của 1 cuốn sách
        $sql = "SELECT 
                    s.*, l.ten_loai, n.ten_nxb, k.phan_tram_km, g.gia_sach_ban,
                    GROUP_CONCAT(DISTINCT tl.ten_the_loai SEPARATOR ', ') AS danh_sach_the_loai
                FROM sach s
                
                -- SỬA LỖI 1: JOIN qua bảng trung gian sach_theloai
                LEFT JOIN sach_theloai stl ON s.id_sach = stl.id_sach
                LEFT JOIN the_loai tl ON stl.id_the_loai = tl.id_the_loai
                LEFT JOIN loai_sach l ON tl.id_loai = l.id_loai 
                
                -- Các JOIN còn lại
                JOIN nxb n ON s.id_nxb = n.id_nxb
                JOIN khuyen_mai k ON s.id_km = k.id_km
                
                -- SỬA LỖI 2: Logic lấy giá sách HIỆN TẠI
                JOIN gia_sach g ON s.id_sach = g.id_sach
                JOIN thoi_diem td ON g.tg_gia_bd = td.tg_gia_bd
                
                WHERE s.trang_thai_sach = 1
                -- Thêm điều kiện để chỉ lấy giá đang có hiệu lực
                AND NOW() BETWEEN td.tg_gia_bd AND COALESCE(td.tg_gia_kt, '2099-12-31 23:59:59')
                
                -- Phải GROUP BY để GROUP_CONCAT hoạt động
                GROUP BY s.id_sach, l.ten_loai, n.ten_nxb, k.phan_tram_km, g.gia_sach_ban";
                
        // SỬA 3: Dùng biến $db_connection
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy sách theo ID (ĐÃ CẬP NHẬT)
    public function getBookById($id_sach) {
        
        $sql = "SELECT 
                    s.*, n.ten_nxb, l.ten_loai, k.phan_tram_km, g.gia_sach_ban,
                    
                    -- Gộp các thể loại con thành 1 chuỗi
                    GROUP_CONCAT(DISTINCT tl.ten_the_loai SEPARATOR ', ') AS danh_sach_the_loai,
                    
                    -- === THÊM MỚI: Gộp các tác giả thành 1 chuỗi ===
                    GROUP_CONCAT(DISTINCT tg.ten_tac_gia SEPARATOR ', ') AS ten_tac_gia
                    
                FROM sach s
                
                -- Joins cho thể loại
                LEFT JOIN sach_theloai stl ON s.id_sach = stl.id_sach
                LEFT JOIN the_loai tl ON stl.id_the_loai = tl.id_the_loai
                LEFT JOIN loai_sach l ON tl.id_loai = l.id_loai
                
                -- === THÊM MỚI: Joins cho tác giả ===
                LEFT JOIN s_tg st ON s.id_sach = st.id_sach
                LEFT JOIN tac_gia tg ON st.id_tac_gia = tg.id_tac_gia
                
                -- Các JOIN còn lại
                JOIN nxb n ON s.id_nxb = n.id_nxb
                JOIN khuyen_mai k ON s.id_km = k.id_km
                JOIN gia_sach g ON s.id_sach = g.id_sach
                JOIN thoi_diem td ON g.tg_gia_bd = td.tg_gia_bd

                WHERE s.id_sach = ?
                -- Lấy giá hiện tại
                AND NOW() BETWEEN td.tg_gia_bd AND COALESCE(td.tg_gia_kt, '2099-12-31 23:59:59')
                
                -- Chỉ GROUP BY id sách
                GROUP BY s.id_sach
                LIMIT 1";
                
        // SỬA 3: Dùng biến $db_connection
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute([$id_sach]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Tìm kiếm theo tên sách (Cũng bị lỗi logic giá)
    public function searchBooks($keyword) {
        $sql = "SELECT s.*, g.gia_sach_ban 
                FROM sach s
                
                -- SỬA LỖI 2: Logic lấy giá sách HIỆN TẠI
                JOIN gia_sach g ON s.id_sach = g.id_sach
                JOIN thoi_diem td ON g.tg_gia_bd = td.tg_gia_bd
                
                WHERE s.ten_sach LIKE ?
                -- Thêm điều kiện để chỉ lấy giá đang có hiệu lực
                AND NOW() BETWEEN td.tg_gia_bd AND COALESCE(td.tg_gia_kt, '2099-12-31 23:59:59')
                
                -- Thêm GROUP BY để tránh lặp sách
                GROUP BY s.id_sach, g.gia_sach_ban";
                
        // SỬA 3: Dùng biến $db_connection
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute(['%' . $keyword . '%']);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Giảm số lượng tồn khi đặt hàng
    public function reduceStock($id_sach, $so_luong) {
        $sql = "UPDATE sach SET so_luong_ton = so_luong_ton - ? WHERE id_sach = ?";
        // SỬA 3: Dùng biến $db_connection
        $stmt = $this->db_connection->prepare($sql);
        return $stmt->execute([$so_luong, $id_sach]);
    }

    /* =====================================================
     🔍 TÌM KIẾM SÁCH NÂNG CAO
    ===================================================== */
    public function searchBooksAdvanced($keyword, $id_loai, $id_the_loai, $min_price, $max_price) {
        $params = [];
        $sql = "SELECT 
                    s.*, g.gia_sach_ban, k.phan_tram_km, n.ten_nxb, l.ten_loai,
                    GROUP_CONCAT(DISTINCT tl.ten_the_loai SEPARATOR ', ') AS danh_sach_the_loai
                FROM sach s
                
                LEFT JOIN sach_theloai stl ON s.id_sach = stl.id_sach
                LEFT JOIN the_loai tl ON stl.id_the_loai = tl.id_the_loai
                LEFT JOIN loai_sach l ON tl.id_loai = l.id_loai 
                
                JOIN nxb n ON s.id_nxb = n.id_nxb
                JOIN khuyen_mai k ON s.id_km = k.id_km
                JOIN gia_sach g ON s.id_sach = g.id_sach
                JOIN thoi_diem td ON g.tg_gia_bd = td.tg_gia_bd
                
                WHERE s.trang_thai_sach = 1
                AND NOW() BETWEEN td.tg_gia_bd AND COALESCE(td.tg_gia_kt, '2099-12-31 23:59:59')
                ";

        // Thêm điều kiện tìm kiếm động
        if (!empty($keyword)) {
            $sql .= " AND s.ten_sach LIKE ?";
            $params[] = "%$keyword%";
        }
        // Lọc theo Danh mục cha (loai_sach)
        if (!empty($id_loai)) {
            $sql .= " AND l.id_loai = ?";
            $params[] = $id_loai;
        }
        
        // === THÊM MỚI: Lọc theo Thể loại con (the_loai) ===
        if (!empty($id_the_loai)) {
            $sql .= " AND tl.id_the_loai = ?";
            $params[] = $id_the_loai;
        }
        // === KẾT THÚC THÊM MỚI ===

        if (!empty($min_price)) {
            $sql .= " AND g.gia_sach_ban >= ?";
            $params[] = $min_price;
        }
        if (!empty($max_price)) {
            $sql .= " AND g.gia_sach_ban <= ?";
            $params[] = $max_price;
        }

        $sql .= " GROUP BY s.id_sach, g.gia_sach_ban, k.phan_tram_km, n.ten_nxb, l.ten_loai";

        // SỬA 3: Dùng biến $db_connection
        $stmt = $this->db_connection->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_OBJ); // Đảm bảo trả về OBJ
    }
}
?>