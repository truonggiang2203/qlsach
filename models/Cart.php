<?php
require_once 'Database.php';

class Book {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Lấy toàn bộ sách
    public function getAllBooks() {
        $sql = "SELECT * FROM sach";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy thông tin 1 sách theo id
    public function getBookById($id_sach) {
        $sql = "SELECT * FROM sach WHERE id_sach = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_sach]);
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
}
