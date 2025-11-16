<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Nạp Model
require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Cart.php';

$bookModel = new Book();
$cartModel = new Cart();

$action = $_GET['action'] ?? 'view';
$id_sach = $_REQUEST['id_sach'] ?? null;
$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']);

// =====================================================
// ⭐ HÀM CẬP NHẬT SESSION SAU MỖI THAO TÁC GIỎ HÀNG
// =====================================================
function updateCartSession($cartModel) {
    $_SESSION['cart'] = $cartModel->getItems();
    $_SESSION['cartCount'] = $cartModel->getCount();
}

// =====================================================
// ⭐ ĐIỀU HƯỚNG CHÍNH
// =====================================================
switch ($action) {

    // --------------------------
    // ⭐ THÊM SÁCH VÀO GIỎ
    // --------------------------
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') break;

        $so_luong_post = (int)($_POST['so_luong'] ?? 1);

        if ($id_sach && $so_luong_post > 0) {
            $book = $bookModel->getBookById($id_sach);
            if ($book) {
                $cartModel->add($book, $so_luong_post);
            }
        }

        updateCartSession($cartModel);

        if ($is_ajax) {
            echo json_encode([
                'success' => true,
                'cartCount' => $_SESSION['cartCount']
            ]);
            exit;
        }

        header('Location: /qlsach/user/cart.php');
        exit;


    // --------------------------
    // ⭐ CẬP NHẬT SỐ LƯỢNG
    // --------------------------
    case 'update':
        $quantity = (int)($_REQUEST['quantity'] ?? 1);

        if ($id_sach && $quantity > 0) {
            $cartModel->update($id_sach, $quantity);
        }

        updateCartSession($cartModel);

        if ($is_ajax) {
            $itemSubtotal = $cartModel->getItemSubtotal($id_sach);
            $totals = $cartModel->calculateTotals();

            echo json_encode([
                'success' => true,
                'cartCount' => $_SESSION['cartCount'],
                'itemSubtotal' => number_format($itemSubtotal) . ' đ',
                'totals' => $totals
            ]);
            exit;
        }

        header('Location: /qlsach/user/cart.php');
        exit;


    // --------------------------
    // ⭐ XOÁ 1 SẢN PHẨM
    // --------------------------
    case 'remove':
        if ($id_sach) {
            $cartModel->remove($id_sach);
        }

        updateCartSession($cartModel);

        if ($is_ajax) {
            $totals = $cartModel->calculateTotals();
            echo json_encode([
                'success' => true,
                'cartCount' => $_SESSION['cartCount'],
                'totals' => $totals
            ]);
            exit;
        }

        header('Location: /qlsach/user/cart.php');
        exit;


    // --------------------------
    // ⭐ LÀM TRỐNG GIỎ
    // --------------------------
    case 'clear':
        $cartModel->clear();
        $_SESSION['cart'] = [];
        $_SESSION['cartCount'] = 0;

        if ($is_ajax) {
            echo json_encode([
                'success' => true,
                'cartCount' => 0
            ]);
            exit;
        }

        header('Location: /qlsach/user/cart.php');
        exit;


    // --------------------------
    // ⭐ MẶC ĐỊNH → ĐI TỚI TRANG GIỎ HÀNG
    // --------------------------
    case 'view':
    default:
        header('Location: /qlsach/user/cart.php');
        exit;
}
?>
