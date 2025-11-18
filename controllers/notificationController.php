<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Notification.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['id_tk'])) {
    header("Location: /qlsach/guest/login.php");
    exit;
}

$notificationModel = new Notification();
$action = $_GET['action'] ?? '';

switch ($action) {
    case 'markAsRead':
        $id = $_GET['id'] ?? '';
        if ($id) {
            $notificationModel->markAsRead($id);
        }
        header("Location: /qlsach/user/notifications.php");
        exit;

    case 'markAllAsRead':
        $notificationModel->markAllAsRead();
        header("Location: /qlsach/user/notifications.php");
        exit;

    case 'delete':
        $id = $_GET['id'] ?? '';
        if ($id) {
            $notificationModel->delete($id);
        }
        header("Location: /qlsach/user/notifications.php");
        exit;

    case 'clearRead':
        $notificationModel->clearRead();
        header("Location: /qlsach/user/notifications.php");
        exit;

    default:
        header("Location: /qlsach/user/notifications.php");
        exit;
}
?>

