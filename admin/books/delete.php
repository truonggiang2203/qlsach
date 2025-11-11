<?php
// Bắt đầu session để sử dụng flash messages
session_start();

// Gọi các file cần thiết
include '../includes/db.php';

// 1. Kiểm tra quyền Admin
if (!isset($_SESSION['phan_quyen']) || $_SESSION['phan_quyen'] != 'admin') {
    $_SESSION['error_message'] = "Bạn không có quyền thực hiện hành động này.";
    header('Location: ../dashboard.php');
    exit;
}

// 2. Kiểm tra xem ID có được cung cấp không
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['error_message'] = "Không có ID sách được cung cấp.";
    header('Location: index.php');
    exit;
}

$id_sach = $_GET['id'];

try {
    // --- KIỂM TRA RÀNG BUỘC AN TOÀN ---
    // Kiểm tra xem sách có đang nằm trong đơn hàng nào "Đang hoạt động" không
    // (Giả định 1=Chờ xử lý, 2=Đã xác nhận, 3=Đang giao)
    $stmt_check = $pdo->prepare("
        SELECT COUNT(ctdh.id_don_hang) 
        FROM chi_tiet_don_hang ctdh
        JOIN don_hang dh ON ctdh.id_don_hang = dh.id_don_hang
        WHERE ctdh.id_sach = ? 
        AND dh.id_trang_thai IN (1, 2, 3) 
    ");
    $stmt_check->execute([$id_sach]);
    $active_orders_count = $stmt_check->fetchColumn();

    if ($active_orders_count > 0) {
        // Nếu có, từ chối xóa và báo lỗi
        $_SESSION['error_message'] = "Không thể xóa! Sách này đang nằm trong $active_orders_count đơn hàng đang chờ xử lý.";
        header('Location: index.php');
        exit;
    }

    // --- TIẾN HÀNH XÓA MỀM (SOFT DELETE) ---
    // Nếu không có đơn hàng nào, cập nhật trạng thái sách = 0 (ẩn)
    // (Giả định 1 = Hiển thị, 0 = Ẩn/Đã xóa)
    
    $stmt_delete = $pdo->prepare("UPDATE sach SET trang_thai_sach = 0 WHERE id_sach = ?");
    $stmt_delete->execute([$id_sach]);

    $_SESSION['success_message'] = "Đã ẩn sách (ID: $id_sach) thành công.";
    
} catch (Exception $e) {
    // Báo lỗi nếu có sự cố CSDL
    $_SESSION['error_message'] = "Lỗi khi xóa sách: " . $e->getMessage();
}

// 6. Chuyển hướng về trang danh sách
header('Location: index.php');
exit;
?>