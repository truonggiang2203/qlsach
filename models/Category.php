<?php
require_once 'Database.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Đổi tên từ: getAllLoaiSach
    public function getAllParentCategories() {
        $sql = "SELECT * FROM loai_sach ORDER BY ten_loai ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Đổi tên từ: getAllTheLoai
    public function getAllSubCategories() {
        $sql = "SELECT * FROM the_loai ORDER BY ten_the_loai ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
?>