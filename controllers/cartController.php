<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../models/Book.php';
require_once __DIR__ . '/../models/Cart.php';

$bookModel = new Book();
$cartModel = new Cart();

$action = $_GET['action'] ?? '';
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

/* ======================================================
    ⭐ FUNCTION: UPDATE SESSION COUNT AFTER MODIFICATION
====================================================== */
function refreshCartSession($cartModel)
{
    $_SESSION['cart'] = $cartModel->getItems();
    $_SESSION['cartCount'] = $cartModel->getCount();
}

/* ======================================================
    ✦✦✦ CONTROLLER LOGIC ✦✦✦
====================================================== */

switch ($action) {

    /* ------------------------------------------------------
        ⭐ 1. THÊM SẢN PHẨM VÀO GIỎ
    ------------------------------------------------------ */
    case 'add':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') break;

        $id_sach = $_POST['id_sach'] ?? null;
        $so_luong = (int)($_POST['so_luong'] ?? 1);

        if ($id_sach) {
            $book = $bookModel->getBookById($id_sach);
            if ($book) {
                $cartModel->add($book, $so_luong);
            }
        }

        refreshCartSession($cartModel);

        // Response AJAX
        if ($is_ajax) {
            echo json_encode([
                'success' => true,
                'cartCount' => $_SESSION['cartCount']
            ]);
            exit;
        }

        header("Location: /qlsach/user/cart.php");
        exit;


    /* ------------------------------------------------------
        ⭐ 2. UPDATE SỐ LƯỢNG (AJAX: TĂNG / GIẢM)
    ------------------------------------------------------ */
    case 'update_qty':
        header('Content-Type: application/json');
        $id_sach = $_POST['id_sach'] ?? null;
        $type = $_POST['type'] ?? '';

        if (!$id_sach || !isset($_SESSION['cart'][$id_sach])) {
            echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại trong giỏ']);
            exit;
        }

        $currentQty = $_SESSION['cart'][$id_sach]['quantity'];
        $stock = $_SESSION['cart'][$id_sach]['stock'] ?? 99; 

        // tăng / giảm
        if ($type === 'increase') {
            $currentQty++;
        } elseif ($type === 'decrease' && $currentQty > 1) {
            $currentQty--;
        }

        // đảm bảo không vượt quá tồn kho
        if ($currentQty > $stock) {
            $currentQty = $stock;
            echo json_encode([
                'success' => false, 
                'message' => 'Số lượng không được vượt quá tồn kho (' . $stock . ' sản phẩm)',
                'new_qty' => $currentQty
            ]);
            exit;
        }

        // cập nhật
        $cartModel->update($id_sach, $currentQty);

        refreshCartSession($cartModel);

        // tính toán tổng tiền
        $totals = $cartModel->calculateTotals();
        $itemSubtotal = $cartModel->getItemSubtotal($id_sach);

        // trả AJAX
        echo json_encode([
            'success' => true,
            'new_qty' => $currentQty,
            'itemSubtotal' => number_format($itemSubtotal, 0, ',', '.') . ' đ',
            'cart_total' => number_format($totals['total'], 0, ',', '.') . ' đ',
            'cart_subtotal' => number_format($totals['subtotal'], 0, ',', '.') . ' đ',
            'cart_discount' => number_format($totals['totalDiscount'], 0, ',', '.') . ' đ',
            'cartCount' => $_SESSION['cartCount']
        ]);
        exit;


    /* ------------------------------------------------------
        ⭐ 3. XÓA 1 SẢN PHẨM
    ------------------------------------------------------ */
    case 'remove':
        $id_sach = $_GET['id_sach'] ?? null;

        if ($id_sach) {
            $cartModel->remove($id_sach);
        }

        refreshCartSession($cartModel);

        if ($is_ajax) {
            header('Content-Type: application/json');
            $totals = $cartModel->calculateTotals();
            echo json_encode([
                'success' => true,
                'cartCount' => $_SESSION['cartCount'],
                'totals' => [
                    'subtotal' => $totals['subtotal'],
                    'totalDiscount' => $totals['totalDiscount'],
                    'total' => $totals['total']
                ]
            ]);
            exit;
        }

        header("Location: /qlsach/user/cart.php");
        exit;


    /* ------------------------------------------------------
        ⭐ 4. XÓA TOÀN BỘ GIỎ HÀNG
    ------------------------------------------------------ */
    case 'clear':
        $cartModel->clear();
        $_SESSION['cart'] = [];
        $_SESSION['cartCount'] = 0;

        if ($is_ajax) {
            echo json_encode(['success' => true]);
            exit;
        }

        header("Location: /qlsach/user/cart.php");
        exit;


    /* ------------------------------------------------------
        ⭐ 5. MẶC ĐỊNH → QUAY VỀ TRANG GIỎ
    ------------------------------------------------------ */
    default:
        header("Location: /qlsach/user/cart.php");
        exit;
}
?>
