<?php
session_start();
// Nếu chưa đăng nhập HOẶC quyền không phải 'admin'
if (!isset($_SESSION['phan_quyen']) || $_SESSION['phan_quyen'] != 'admin') {
    header('Location: ../public/index.php'); // Đuổi về trang chủ
    exit;
}
?>