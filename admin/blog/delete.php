<?php
include '../includes/db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    
    try {
        // 1. Lấy tên ảnh để xóa file
        $stmt = $pdo->prepare("SELECT anh_dai_dien FROM bai_viet WHERE id_bai_viet = ?");
        $stmt->execute([$id]);
        $img = $stmt->fetchColumn();

        // 2. Xóa dữ liệu trong DB
        // Bảng trung gian bai_viet_tag tự xóa do có ràng buộc ON DELETE CASCADE
        $pdo->prepare("DELETE FROM bai_viet WHERE id_bai_viet = ?")->execute([$id]);

        // 3. Xóa file ảnh (Nếu có)
        if ($img && file_exists("../../public/uploads/posts/" . $img)) {
            unlink("../../public/uploads/posts/" . $img);
        }

        // 4. Báo thành công (Sử dụng JavaScript alert rồi chuyển hướng vì file này không có giao diện)
        echo "<script>alert('Đã xóa bài viết thành công!'); window.location='index.php';</script>";
    } catch (Exception $e) {
        echo "<script>alert('Lỗi: " . $e->getMessage() . "'); window.location='index.php';</script>";
    }
} else {
    header('Location: index.php');
}
?>