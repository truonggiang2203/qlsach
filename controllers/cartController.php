<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/Book.php';
$bookModel = new Book();

$action = $_GET['action'] ?? '';

// Đảm bảo $_SESSION['cart'] luôn là mảng
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    case 'add':
        // Nhận dữ liệu từ POST hoặc GET
        $id_sach = $_POST['id_sach'] ?? ($_GET['id_sach'] ?? '');
        $so_luong = isset($_POST['so_luong']) ? (int)$_POST['so_luong'] : 1;

        if (!$id_sach) {
            header("Location: ../public/index.php");
            exit;
        }

        $book = $bookModel->getBookById($id_sach);
        if (!$book) {
            echo "Sách không tồn tại!";
            exit;
        }

        // Nếu sách đã tồn tại → tăng số lượng
        if (isset($_SESSION['cart'][$id_sach]) && is_array($_SESSION['cart'][$id_sach])) {
            $_SESSION['cart'][$id_sach]['so_luong'] += $so_luong;
        } else {
            $_SESSION['cart'][$id_sach] = [
                'id_sach'   => $book->id_sach,
                'ten_sach'  => $book->ten_sach,
                'gia'       => $book->gia_sach_ban,
                'so_luong'  => $so_luong
            ];
        }

        header("Location: ../user/cart.php");
        exit;

    case 'update':
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $id_sach => $so_luong) {
                $so_luong = (int)$so_luong;
                if ($so_luong <= 0) {
                    unset($_SESSION['cart'][$id_sach]);
                } else {
                    if (isset($_SESSION['cart'][$id_sach])) {
                        $_SESSION['cart'][$id_sach]['so_luong'] = $so_luong;
                    }
                }
            }
        }
        header("Location: ../user/cart.php");
        exit;

    case 'remove':
        $id_sach = $_GET['id_sach'] ?? '';
        if (isset($_SESSION['cart'][$id_sach])) {
            unset($_SESSION['cart'][$id_sach]);
        }
        header("Location: ../user/cart.php");
        exit;

    case 'clear':
        unset($_SESSION['cart']);
        $_SESSION['cart'] = [];
        header("Location: ../user/cart.php");
        exit;

    default:
        header("Location: ../public/index.php");
        exit;
}
