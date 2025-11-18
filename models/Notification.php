<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/Database.php';

class Notification {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Lấy danh sách tất cả thông báo của user
     */
    public function getAll($id_tk = null) {
        if (!$id_tk && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
        }
        
        if (!$id_tk) {
            return [];
        }

        $sql = "SELECT * FROM thong_bao 
                WHERE id_tk = ? 
                ORDER BY ngay_gio_tao DESC 
                LIMIT 50";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        $notifications = $stmt->fetchAll();

        // Chuyển đổi về format tương thích với code cũ
        $result = [];
        foreach ($notifications as $notif) {
            $result[] = [
                'id' => $notif->id_thong_bao,
                'title' => $notif->tieu_de,
                'message' => $notif->noi_dung,
                'type' => $notif->loai,
                'link' => $notif->lien_ket,
                'read' => (bool)$notif->da_doc,
                'created_at' => $notif->ngay_gio_tao
            ];
        }

        return $result;
    }

    /**
     * Lấy danh sách thông báo chưa đọc
     */
    public function getUnread($id_tk = null) {
        if (!$id_tk && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
        }
        
        if (!$id_tk) {
            return [];
        }

        $sql = "SELECT * FROM thong_bao 
                WHERE id_tk = ? AND da_doc = 0 
                ORDER BY ngay_gio_tao DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        $notifications = $stmt->fetchAll();

        $result = [];
        foreach ($notifications as $notif) {
            $result[] = [
                'id' => $notif->id_thong_bao,
                'title' => $notif->tieu_de,
                'message' => $notif->noi_dung,
                'type' => $notif->loai,
                'link' => $notif->lien_ket,
                'read' => false,
                'created_at' => $notif->ngay_gio_tao
            ];
        }

        return $result;
    }

    /**
     * Lấy số lượng thông báo chưa đọc
     */
    public function getUnreadCount($id_tk = null) {
        if (!$id_tk && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
        }
        
        if (!$id_tk) {
            return 0;
        }

        $sql = "SELECT COUNT(*) as count FROM thong_bao 
                WHERE id_tk = ? AND da_doc = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        $result = $stmt->fetch();
        
        return $result->count ?? 0;
    }

    /**
     * Thêm thông báo mới
     */
    public function add($title, $message, $type = 'info', $link = null, $id_tk = null) {
        if (!$id_tk && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
        }
        
        if (!$id_tk) {
            return false;
        }

        // Validate type
        $allowedTypes = ['info', 'success', 'warning', 'error'];
        if (!in_array($type, $allowedTypes)) {
            $type = 'info';
        }

        $sql = "INSERT INTO thong_bao (id_tk, tieu_de, noi_dung, loai, lien_ket, da_doc, ngay_gio_tao) 
                VALUES (?, ?, ?, ?, ?, 0, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk, $title, $message, $type, $link]);

        $id = $this->db->lastInsertId();

        return [
            'id' => $id,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'link' => $link,
            'read' => false,
            'created_at' => date('Y-m-d H:i:s')
        ];
    }

    /**
     * Đánh dấu thông báo là đã đọc
     */
    public function markAsRead($id, $id_tk = null) {
        if (!$id_tk && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
        }
        
        if (!$id_tk) {
            return false;
        }

        // Kiểm tra thông báo thuộc về user này
        $sql = "UPDATE thong_bao 
                SET da_doc = 1 
                WHERE id_thong_bao = ? AND id_tk = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $id_tk]);
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Đánh dấu tất cả thông báo là đã đọc
     */
    public function markAllAsRead($id_tk = null) {
        if (!$id_tk && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
        }
        
        if (!$id_tk) {
            return false;
        }

        $sql = "UPDATE thong_bao 
                SET da_doc = 1 
                WHERE id_tk = ? AND da_doc = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        
        return true;
    }

    /**
     * Xóa một thông báo
     */
    public function delete($id, $id_tk = null) {
        if (!$id_tk && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
        }
        
        if (!$id_tk) {
            return false;
        }

        // Kiểm tra thông báo thuộc về user này
        $sql = "DELETE FROM thong_bao 
                WHERE id_thong_bao = ? AND id_tk = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id, $id_tk]);
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Xóa tất cả thông báo
     */
    public function clear($id_tk = null) {
        if (!$id_tk && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
        }
        
        if (!$id_tk) {
            return false;
        }

        $sql = "DELETE FROM thong_bao WHERE id_tk = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        
        return true;
    }

    /**
     * Xóa tất cả thông báo đã đọc
     */
    public function clearRead($id_tk = null) {
        if (!$id_tk && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
        }
        
        if (!$id_tk) {
            return false;
        }

        $sql = "DELETE FROM thong_bao 
                WHERE id_tk = ? AND da_doc = 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id_tk]);
        
        return true;
    }
}
?>

