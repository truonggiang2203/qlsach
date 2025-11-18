<?php
session_start();
require_once '../models/Wishlist.php';

$wishlist = new Wishlist();

// Kiểm tra AJAX request
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if (!isset($_SESSION['id_tk'])) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Bạn cần đăng nhập']);
        exit;
    }
    header("Location: /qlsach/guest/login.php");
    exit;
}

$action = $_GET['action'] ?? '';
$id_tk = $_SESSION['id_tk'];
$id_sach = $_GET['id_sach'] ?? '';
$redirect = $_GET['redirect'] ?? '';

if ($action === 'add' && $id_sach) {
    $result = $wishlist->add($id_tk, $id_sach);
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'action' => 'add', 'message' => 'Đã thêm vào yêu thích']);
        exit;
    }
    
    // Xác định nơi redirect
    if ($redirect === 'wishlist') {
        header("Location: /qlsach/user/wishlist.php?added=1");
    } else {
        header("Location: /qlsach/public/book_detail.php?id_sach=$id_sach&wishlist=added");
    }
    exit;
}

if ($action === 'remove' && $id_sach) {
    $result = $wishlist->remove($id_tk, $id_sach);
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'action' => 'remove', 'message' => 'Đã xóa khỏi yêu thích']);
        exit;
    }
    
    // Xác định nơi redirect
    if ($redirect === 'wishlist') {
        header("Location: /qlsach/user/wishlist.php?removed=1");
    } else {
        header("Location: /qlsach/public/book_detail.php?id_sach=$id_sach&wishlist=removed");
    }
    exit;
}

if ($action === 'clearAll') {
    $wishlist->clearAll($id_tk);
    
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'message' => 'Đã xóa tất cả']);
        exit;
    }
    
    header("Location: /qlsach/user/wishlist.php?cleared=1");
    exit;
}

if ($action === 'view') {
    header("Location: /qlsach/user/wishlist.php");
    exit;
}

if ($isAjax) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
} else {
    echo "Invalid action";
}
