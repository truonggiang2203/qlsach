<?php
include '../includes/db.php';

if (!isset($_SESSION['phan_quyen']) || $_SESSION['phan_quyen'] != 'admin') {
    header('Location: ../dashboard.php'); exit;
}

if (isset($_GET['id'])) {
    $id_sach = $_GET['id'];
    try {
        // Kiểm tra đơn hàng
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM chi_tiet_don_hang ctdh JOIN don_hang dh ON ctdh.id_don_hang = dh.id_don_hang WHERE ctdh.id_sach = ? AND dh.id_trang_thai IN (1, 2, 3)");
        $stmt->execute([$id_sach]);
        
        if ($stmt->fetchColumn() > 0) {
            $_SESSION['error_message'] = "Không thể xóa! Sách đang nằm trong đơn hàng chưa hoàn thành.";
        } else {
            // XÓA MỀM
            $pdo->prepare("UPDATE sach SET trang_thai_sach = 0 WHERE id_sach = ?")->execute([$id_sach]);
            $_SESSION['success_message'] = "Đã xóa mềm sách $id_sach thành công.";
        }
    } catch (Exception $e) {
        $_SESSION['error_message'] = "Lỗi: " . $e->getMessage();
    }
}
header('Location: index.php');
exit;
?>