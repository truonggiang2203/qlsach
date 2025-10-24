<?php
require_once 'Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database(); // Khởi tạo kết nối CSDL
    }

    /**
     * Đăng ký tài khoản mới
     * Lưu ý: id_tk cần được tạo duy nhất. 
     * Ví dụ này gán cứng 'KH' cho id_nd (khách hàng).
     */
    public function register($id_tk, $ho_ten, $email, $sdt, $password, $dia_chi) {
        // **Quan trọng: Hash mật khẩu trước khi lưu**
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        // 'KH' là id_nd cho 'khach_hang' mà ta đã thêm ở trên
        $id_nd_khach_hang = 'KH'; 
        
        $sql = "INSERT INTO tai_khoan (id_tk, id_nd, ho_ten, email, sdt, mat_khau, dia_chi_giao_hang, ngay_gio_tao_tk, gioi_tinh) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'Khác')"; // Thêm các trường theo CSDL
        
        $stmt = $this->db->prepare($sql);
        
        try {
            // Bạn cần tự tạo $id_tk duy nhất (ví dụ: 'TK' . rand(100,999))
            $stmt->execute([$id_tk, $id_nd_khach_hang, $ho_ten, $email, $sdt, $hashed_password, $dia_chi]);
            return true;
        } catch (PDOException $e) {
            // Lỗi (có thể do trùng email, sdt hoặc id_tk)
            return false;
        }
    }

    /**
     * Đăng nhập
     * Trả về thông tin user và quyền (phan_quyen) nếu thành công
     */
    public function login($email, $password) {
        // Lấy thông tin tài khoản VÀ quyền của họ
        $sql = "SELECT tk.*, nd.phan_quyen 
                FROM tai_khoan AS tk
                JOIN nguoi_dung AS nd ON tk.id_nd = nd.id_nd
                WHERE tk.email = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user) {
            // **Kiểm tra mật khẩu đã hash**
            if (password_verify($password, $user->mat_khau)) {
                return $user; // Trả về toàn bộ thông tin user (bao gồm cả 'phan_quyen')
            }
        }
        return false; // Sai email hoặc password
    }

    /**
     * Tìm tài khoản bằng email
     */
    public function findUserByEmail($email) {
        $sql = "SELECT * FROM tai_khoan WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
?>