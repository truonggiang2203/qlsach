<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Compare.php';
require_once __DIR__ . '/../models/Book.php';

$compareModel = new Compare();
$bookModel = new Book();
$action = $_GET['action'] ?? '';

$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

switch ($action) {
    
    case 'add':
        $id_sach = $_GET['id_sach'] ?? $_POST['id_sach'] ?? null;
        
        if (!$id_sach) {
            if ($is_ajax) {
                echo json_encode(['success' => false, 'message' => 'Thiếu ID sách']);
                exit;
            }
            header("Location: /qlsach/public/index.php");
            exit;
        }

        $book = $bookModel->getBookById($id_sach);
        
        if (!$book) {
            if ($is_ajax) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sách']);
                exit;
            }
            header("Location: /qlsach/public/index.php");
            exit;
        }

        $result = $compareModel->add($book);

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        // Redirect về trang trước đó hoặc trang chi tiết sách
        $redirect = $_SERVER['HTTP_REFERER'] ?? "/qlsach/public/book_detail.php?id_sach=" . $id_sach;
        header("Location: " . $redirect);
        exit;

    case 'remove':
        $id_sach = $_GET['id_sach'] ?? $_POST['id_sach'] ?? null;
        
        if (!$id_sach) {
            if ($is_ajax) {
                echo json_encode(['success' => false, 'message' => 'Thiếu ID sách']);
                exit;
            }
            header("Location: /qlsach/user/compare.php");
            exit;
        }

        $result = $compareModel->remove($id_sach);

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        header("Location: /qlsach/user/compare.php");
        exit;

    case 'clear':
        $result = $compareModel->clear();

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        }

        header("Location: /qlsach/user/compare.php");
        exit;

    default:
        header("Location: /qlsach/user/compare.php");
        exit;
}
?>

