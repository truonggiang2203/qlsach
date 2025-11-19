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

    // Lấy các thể loại con cho một danh mục cha
    public function getSubCategoriesByParent($id_loai) {
        $sql = "SELECT * FROM the_loai WHERE id_loai = ? ORDER BY ten_the_loai ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_loai]);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Đếm số sách theo danh mục cha (loai_sach)
    public function countBooksByParent() {
        $sql = "SELECT l.id_loai, COUNT(DISTINCT s.id_sach) AS cnt
                FROM loai_sach l
                LEFT JOIN the_loai tl ON tl.id_loai = l.id_loai
                LEFT JOIN sach_theloai stl ON stl.id_the_loai = tl.id_the_loai
                LEFT JOIN sach s ON s.id_sach = stl.id_sach AND s.trang_thai_sach = 1
                GROUP BY l.id_loai";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $map = [];
        foreach ($rows as $r) $map[$r['id_loai']] = (int)$r['cnt'];
        return $map;
    }

    // Đếm số sách theo thể loại con (the_loai)
    public function countBooksBySubcategory() {
        $sql = "SELECT tl.id_the_loai, COUNT(DISTINCT stl.id_sach) AS cnt
                FROM the_loai tl
                LEFT JOIN sach_theloai stl ON stl.id_the_loai = tl.id_the_loai
                LEFT JOIN sach s ON s.id_sach = stl.id_sach AND s.trang_thai_sach = 1
                GROUP BY tl.id_the_loai";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $map = [];
        foreach ($rows as $r) $map[$r['id_the_loai']] = (int)$r['cnt'];
        return $map;
    }
}
?>