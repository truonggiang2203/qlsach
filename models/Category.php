<?php
require_once 'Database.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllCategories() {
        $sql = "SELECT * FROM loai_sach";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getCategoryById($id_loai) {
        $sql = "SELECT * FROM loai_sach WHERE id_loai = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_loai]);
        return $stmt->fetch();
    }
}
?>
