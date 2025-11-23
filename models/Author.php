<?php
require_once __DIR__ . '/Database.php';

class Author {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy thông tin tác giả theo ID (bao gồm thông tin chi tiết)
    public function getAuthorById($id_tac_gia) {
        $query = "SELECT tg.*, ttg.*
                  FROM tac_gia tg
                  LEFT JOIN thong_tin_tac_gia ttg ON tg.id_tac_gia = ttg.id_tac_gia
                  WHERE tg.id_tac_gia = :id_tac_gia";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_tac_gia', $id_tac_gia);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Lấy tất cả sách của tác giả
    public function getBooksByAuthor($id_tac_gia) {
        $query = "SELECT s.*, tg.*, nxb.ten_nxb, km.phan_tram_km, g.gia_sach_ban
                  FROM sach s
                  INNER JOIN s_tg stg ON s.id_sach = stg.id_sach
                  INNER JOIN tac_gia tg ON stg.id_tac_gia = tg.id_tac_gia
                  LEFT JOIN nxb ON s.id_nxb = nxb.id_nxb
                  LEFT JOIN khuyen_mai km ON s.id_km = km.id_km
                  LEFT JOIN gia_sach g ON s.id_sach = g.id_sach
                  LEFT JOIN thoi_diem td ON g.tg_gia_bd = td.tg_gia_bd
                  WHERE tg.id_tac_gia = :id_tac_gia
                  AND s.trang_thai_sach = 1
                  AND NOW() BETWEEN td.tg_gia_bd AND COALESCE(td.tg_gia_kt, '2099-12-31 23:59:59')
                  GROUP BY s.id_sach
                  ORDER BY s.id_sach DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_tac_gia', $id_tac_gia);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Đếm số lượng sách của tác giả
    public function countBooksByAuthor($id_tac_gia) {
        $query = "SELECT COUNT(*) as total 
                  FROM sach s
                  INNER JOIN s_tg stg ON s.id_sach = stg.id_sach
                  WHERE stg.id_tac_gia = :id_tac_gia
                  AND s.trang_thai_sach = 1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_tac_gia', $id_tac_gia);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    // Lấy tất cả tác giả
    public function getAllAuthors() {
        $query = "SELECT tg.*, COUNT(DISTINCT stg.id_sach) as book_count
                  FROM tac_gia tg
                  LEFT JOIN s_tg stg ON tg.id_tac_gia = stg.id_tac_gia
                  LEFT JOIN sach s ON stg.id_sach = s.id_sach AND s.trang_thai_sach = 1
                  GROUP BY tg.id_tac_gia
                  ORDER BY tg.ten_tac_gia ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Tìm kiếm tác giả theo tên
    public function searchAuthors($keyword) {
        $query = "SELECT tg.*, COUNT(DISTINCT stg.id_sach) as book_count
                  FROM tac_gia tg
                  LEFT JOIN s_tg stg ON tg.id_tac_gia = stg.id_tac_gia
                  LEFT JOIN sach s ON stg.id_sach = s.id_sach AND s.trang_thai_sach = 1
                  WHERE tg.ten_tac_gia LIKE :keyword
                  GROUP BY tg.id_tac_gia
                  ORDER BY tg.ten_tac_gia ASC";
        
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy tác giả phổ biến (có nhiều sách nhất)
    public function getPopularAuthors($limit = 10) {
        $query = "SELECT tg.*, COUNT(DISTINCT stg.id_sach) as book_count
                  FROM tac_gia tg
                  INNER JOIN s_tg stg ON tg.id_tac_gia = stg.id_tac_gia
                  INNER JOIN sach s ON stg.id_sach = s.id_sach
                  WHERE s.trang_thai_sach = 1
                  GROUP BY tg.id_tac_gia
                  ORDER BY book_count DESC
                  LIMIT :limit";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
