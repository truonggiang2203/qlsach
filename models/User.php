<?php
require_once 'Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /* =====================================================
       🧩 ĐĂNG KÝ TÀI KHOẢN
    ===================================================== */
    public function register($id_tk, $ho_ten, $email, $sdt, $password, $dia_chi) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $id_nd_khach_hang = 'KH'; // Mặc định gán quyền khách hàng

        $sql = "INSERT INTO tai_khoan 
                (id_tk, id_nd, ho_ten, email, sdt, mat_khau, dia_chi_giao_hang, ngay_gio_tao_tk, gioi_tinh) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), 'Khác')";

        $stmt = $this->db->prepare($sql);

        try {
            return $stmt->execute([$id_tk, $id_nd_khach_hang, $ho_ten, $email, $sdt, $hashed_password, $dia_chi]);
        } catch (PDOException $e) {
            error_log("Register Error: " . $e->getMessage());
            return false;
        }
    }


    /* =====================================================
       🔐 ĐĂNG NHẬP
       → Trả về đối tượng user (bao gồm phân quyền)
    ===================================================== */
    public function login($email, $password) {
        $sql = "SELECT tk.*, nd.phan_quyen 
                FROM tai_khoan tk
                JOIN nguoi_dung nd ON tk.id_nd = nd.id_nd
                WHERE tk.email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if ($user && password_verify($password, $user->mat_khau)) {
            return $user;
        }
        return false;
    }


    /* =====================================================
       🔍 TÌM NGƯỜI DÙNG THEO EMAIL
    ===================================================== */
    public function findUserByEmail($email) {
        $sql = "SELECT * FROM tai_khoan WHERE email = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }


    /* =====================================================
       🔍 LẤY THÔNG TIN USER THEO ID_TK
    ===================================================== */
    public function getUserById($id_tk) {
        $sql = "SELECT tk.*, nd.phan_quyen 
                FROM tai_khoan tk
                JOIN nguoi_dung nd ON tk.id_nd = nd.id_nd
                WHERE tk.id_tk = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }


    /* =====================================================
       🧾 CẬP NHẬT THÔNG TIN USER
    ===================================================== */
    public function updateUser($id_tk, $ho_ten, $email, $sdt, $dia_chi) {
        $sql = "UPDATE tai_khoan 
                SET ho_ten = ?, email = ?, sdt = ?, dia_chi_giao_hang = ? 
                WHERE id_tk = ?";
        $stmt = $this->db->prepare($sql);

        try {
            return $stmt->execute([$ho_ten, $email, $sdt, $dia_chi, $id_tk]);
        } catch (PDOException $e) {
            error_log("Update Error: " . $e->getMessage());
            return false;
        }
    }


    /* =====================================================
       🔑 ĐỔI MẬT KHẨU
    ===================================================== */
    public function changePassword($id_tk, $old_password, $new_password) {
        $sql = "SELECT mat_khau FROM tai_khoan WHERE id_tk = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);

        if (!$user || !password_verify($old_password, $user->mat_khau)) {
            return false;
        }

        $hashed_new = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE tai_khoan SET mat_khau = ? WHERE id_tk = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$hashed_new, $id_tk]);
    }
}
?>
