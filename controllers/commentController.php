<?php
session_start();
require_once '../models/Comment.php';

$commentModel = new Comment();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_SESSION['id_tk'])) {
                // Nếu chưa đăng nhập, quay về login
                header('Location: ../guest/login.php?error=not_logged_in');
                exit;
            }

            $id_sach = $_POST['id_sach'];
            $binh_luan = trim($_POST['binh_luan']);
            $so_sao = $_POST['so_sao'] ?? 5;

            // Sinh ID_Bình luận ngắn
            $id_bl = 'BL' . rand(100, 999);

            if ($commentModel->addComment($id_bl, $id_sach, $binh_luan, $so_sao)) {
                header("Location: ../public/book_detail.php?id_sach=$id_sach&comment=success");
            } else {
                header("Location: ../public/book_detail.php?id_sach=$id_sach&comment=fail");
            }
        }
        break;

    default:
        header('Location: ../public/index.php');
        break;
}
?>
