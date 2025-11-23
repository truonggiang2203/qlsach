<?php
require_once __DIR__ . '/Database.php';

class Blog {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Lấy tất cả bài viết đã xuất bản
    public function getAllPosts($limit = null, $offset = 0) {
        $sql = "SELECT bv.*, dm.ten_danh_muc, dm.slug as danh_muc_slug, tk.ho_ten as tac_gia
                FROM bai_viet bv
                LEFT JOIN danh_muc_bai_viet dm ON bv.id_danh_muc = dm.id_danh_muc
                LEFT JOIN tai_khoan tk ON bv.id_tk = tk.id_tk
                WHERE bv.trang_thai = 'published'
                AND bv.ngay_xuat_ban <= NOW()
                ORDER BY bv.ngay_xuat_ban DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $this->conn->prepare($sql);
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy bài viết theo slug
    public function getPostBySlug($slug) {
        $sql = "SELECT bv.*, dm.ten_danh_muc, dm.slug as danh_muc_slug, tk.ho_ten as tac_gia
                FROM bai_viet bv
                LEFT JOIN danh_muc_bai_viet dm ON bv.id_danh_muc = dm.id_danh_muc
                LEFT JOIN tai_khoan tk ON bv.id_tk = tk.id_tk
                WHERE bv.slug = :slug
                AND bv.trang_thai = 'published'";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Tăng lượt xem
    public function incrementViews($id_bai_viet) {
        $sql = "UPDATE bai_viet SET luot_xem = luot_xem + 1 WHERE id_bai_viet = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id_bai_viet);
        return $stmt->execute();
    }

    // Lấy bài viết nổi bật
    public function getFeaturedPosts($limit = 3) {
        $sql = "SELECT bv.*, dm.ten_danh_muc, dm.slug as danh_muc_slug
                FROM bai_viet bv
                LEFT JOIN danh_muc_bai_viet dm ON bv.id_danh_muc = dm.id_danh_muc
                WHERE bv.trang_thai = 'published'
                AND bv.noi_bat = 1
                AND bv.ngay_xuat_ban <= NOW()
                ORDER BY bv.ngay_xuat_ban DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy bài viết theo danh mục
    public function getPostsByCategory($id_danh_muc, $limit = null) {
        $sql = "SELECT bv.*, dm.ten_danh_muc, dm.slug as danh_muc_slug, tk.ho_ten as tac_gia
                FROM bai_viet bv
                LEFT JOIN danh_muc_bai_viet dm ON bv.id_danh_muc = dm.id_danh_muc
                LEFT JOIN tai_khoan tk ON bv.id_tk = tk.id_tk
                WHERE bv.id_danh_muc = :id_danh_muc
                AND bv.trang_thai = 'published'
                AND bv.ngay_xuat_ban <= NOW()
                ORDER BY bv.ngay_xuat_ban DESC";
        
        if ($limit) {
            $sql .= " LIMIT :limit";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_danh_muc', $id_danh_muc);
        if ($limit) {
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy bài viết liên quan
    public function getRelatedPosts($id_bai_viet, $id_danh_muc, $limit = 3) {
        $sql = "SELECT bv.*, dm.ten_danh_muc, dm.slug as danh_muc_slug
                FROM bai_viet bv
                LEFT JOIN danh_muc_bai_viet dm ON bv.id_danh_muc = dm.id_danh_muc
                WHERE bv.id_danh_muc = :id_danh_muc
                AND bv.id_bai_viet != :id_bai_viet
                AND bv.trang_thai = 'published'
                AND bv.ngay_xuat_ban <= NOW()
                ORDER BY bv.ngay_xuat_ban DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_danh_muc', $id_danh_muc);
        $stmt->bindParam(':id_bai_viet', $id_bai_viet);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Tìm kiếm bài viết
    public function searchPosts($keyword) {
        $sql = "SELECT bv.*, dm.ten_danh_muc, dm.slug as danh_muc_slug
                FROM bai_viet bv
                LEFT JOIN danh_muc_bai_viet dm ON bv.id_danh_muc = dm.id_danh_muc
                WHERE bv.trang_thai = 'published'
                AND bv.ngay_xuat_ban <= NOW()
                AND (bv.tieu_de LIKE :keyword OR bv.tom_tat LIKE :keyword OR bv.noi_dung LIKE :keyword)
                ORDER BY bv.ngay_xuat_ban DESC";
        
        $stmt = $this->conn->prepare($sql);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy tất cả danh mục
    public function getAllCategories() {
        $sql = "SELECT dm.*, COUNT(bv.id_bai_viet) as so_bai_viet
                FROM danh_muc_bai_viet dm
                LEFT JOIN bai_viet bv ON dm.id_danh_muc = bv.id_danh_muc 
                    AND bv.trang_thai = 'published'
                WHERE dm.trang_thai = 1
                GROUP BY dm.id_danh_muc
                ORDER BY dm.thu_tu ASC";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Lấy danh mục theo slug
    public function getCategoryBySlug($slug) {
        $sql = "SELECT * FROM danh_muc_bai_viet WHERE slug = :slug AND trang_thai = 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':slug', $slug);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Lấy tags của bài viết
    public function getPostTags($id_bai_viet) {
        $sql = "SELECT t.* FROM tag t
                INNER JOIN bai_viet_tag bvt ON t.id_tag = bvt.id_tag
                WHERE bvt.id_bai_viet = :id_bai_viet";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id_bai_viet', $id_bai_viet);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Đếm tổng số bài viết
    public function countPosts() {
        $sql = "SELECT COUNT(*) as total FROM bai_viet 
                WHERE trang_thai = 'published' 
                AND ngay_xuat_ban <= NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total;
    }

    // Lấy bài viết phổ biến (nhiều lượt xem nhất)
    public function getPopularPosts($limit = 5) {
        $sql = "SELECT bv.*, dm.ten_danh_muc, dm.slug as danh_muc_slug
                FROM bai_viet bv
                LEFT JOIN danh_muc_bai_viet dm ON bv.id_danh_muc = dm.id_danh_muc
                WHERE bv.trang_thai = 'published'
                AND bv.ngay_xuat_ban <= NOW()
                ORDER BY bv.luot_xem DESC
                LIMIT :limit";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
