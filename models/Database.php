<?php
// models/Database.php

class Database {
    // Thông số kết nối CSDL
    private $host = '127.0.0.1'; // Vì CSDL của bạn ở 127.0.0.1
    private $db_name = 'qlsach'; // Tên CSDL của bạn
    private $username = 'root'; // Tên đăng nhập CSDL (thường là 'root')
    private $password = ''; // Mật khẩu CSDL (thường là để trống)
    
    private $conn; // Biến giữ kết nối

    // Phương thức kết nối CSDL
    public function __construct() {
        $this->conn = null;
        $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8mb4';

        try {
            // Tạo đối tượng PDO
            $this->conn = new PDO($dsn, $this->username, $this->password);
            
            // Cài đặt chế độ báo lỗi (để hiển thị lỗi SQL)
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            // Cài đặt chế độ fetch mặc định (trả về đối tượng)
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

        } catch (PDOException $e) {
            // Nếu kết nối thất bại, hiển thị lỗi
            echo 'Kết nối thất bại: ' . $e->getMessage();
        }
    }

    // *** Các phương thức này giúp lớp User.php hoạt động ***

    /**
     * Phương thức chuẩn bị câu lệnh SQL
     * (Giống hệt $pdo->prepare())
     */
    public function prepare($sql) {
        return $this->conn->prepare($sql);
    }

    /**
     * Phương thức lấy ID cuối cùng được chèn vào CSDL
     * (Dùng khi đăng ký tài khoản hoặc tạo đơn hàng)
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }
}
?>