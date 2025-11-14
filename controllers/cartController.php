<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Nạp các Model cần thiết
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Cart.php'; // NẠP MODEL CART MỚI

// Khởi tạo các đối tượng Model
$bookModel = new Book();
$cartModel = new Cart(); // KHỞI TẠO CART MODEL

$action = $_GET['action'] ?? 'view';
$id_sach = $_REQUEST['id_sach'] ?? null; 
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']); 

// *** CHÚNG TA KHÔNG CẦN HÀM calculateCartTotals() ở đây nữa ***
// *** Vì nó đã nằm trong Cart.php ***

/* =====================================================
 ĐIỀU HƯỚNG CHỨC NĂNG
===================================================== */
switch ($action) {
    
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') break;

        $so_luong_post = (int)($_POST['so_luong'] ?? 1);
        
        if ($id_sach && $so_luong_post > 0) {
            $book = $bookModel->getBookById($id_sach);
            if ($book) {
                // Chỉ cần gọi hàm add từ Model
                $cartModel->add($book, $so_luong_post);
            }
        }
        
        $totalCount = $cartModel->getCount(); // Gọi hàm getCount từ Model

        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'cartCount' => $totalCount]);
            exit;
        }
        header('Location: /qlsach/user/cart.php');
        exit;

    case 'update':
        $quantity = (int)($_REQUEST['quantity'] ?? 1);
        
        if ($id_sach && $quantity > 0) {
            // Gọi hàm update từ Model
            $cartModel->update($id_sach, $quantity);
        }
        
        if ($is_ajax) {
            $totalCount = $cartModel->getCount(); // Gọi Model
            $totals = $cartModel->calculateTotals(); // Gọi Model
            $itemSubtotal = $cartModel->getItemSubtotal($id_sach); // Gọi Model

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'cartCount' => $totalCount,
                'itemSubtotal' => number_format($itemSubtotal) . ' đ',
                'totals' => $totals
            ]);
            exit;
        }
        header('Location: /qlsach/user/cart.php');
        exit;

    case 'remove':
        if ($id_sach) {
            // Gọi hàm remove từ Model
            $cartModel->remove($id_sach);
        }

        if ($is_ajax) {
            $totalCount = $cartModel->getCount(); // Gọi Model
            $totals = $cartModel->calculateTotals(); // Gọi Model
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true, 
                'cartCount' => $totalCount,
                'totals' => $totals
            ]);
            exit;
        }
        header('Location: /qlsach/user/cart.php');
        exit;

    case 'clear':
        // Gọi hàm clear từ Model
        $cartModel->clear();
        
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Giỏ hàng đã được làm trống.', 'cartCount' => 0]);
            exit;
        }
        header('Location: /qlsach/user/cart.php');
        exit;

    case 'view':
    default:
        header('Location: /qlsach/user/cart.php');
        exit;
}
?>