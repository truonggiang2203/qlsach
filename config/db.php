<?php
// CẤU HÌNH DATABASE
define("DB_HOST", "localhost");   // Hoặc 127.0.0.1
define("DB_USER", "root");        // User mặc định XAMPP
define("DB_PASS", "");            // Mật khẩu XAMPP thường để trống
define("DB_NAME", "qlsach");      // Tên database của bạn

// CLASS KẾT NỐI DÙNG CHO TOÀN WEBSITE
class Database {
    private $host = DB_HOST;
    private $user = DB_USER;
    private $pass = DB_PASS;
    private $name = DB_NAME;
    public $conn;

    public function __construct() {
        $this->connectDB();
    }

    public function connectDB() {
        $this->conn = new mysqli(
            $this->host,
            $this->user,
            $this->pass,
            $this->name
        );

        if ($this->conn->connect_error) {
            die("Kết nối thất bại: " . $this->conn->connect_error);
        }

        // SET UTF8 để đọc tiếng Việt
        $this->conn->set_charset("utf8mb4");
    }

    public function getConnection() {
        return $this->conn;
    }
}
