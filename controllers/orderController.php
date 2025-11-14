<?php
session_start();
// NẠP CÁC MODEL CẦN THIẾT
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php'; // Nạp Cart Model

$orderModel = new Order();
$cartModel = new Cart(); // Khởi tạo Cart Model

$action = $_GET['action'] ?? '';

switch ($action) {

    /*TẠO ĐƠN HÀNG (ĐÃ SỬA LỖI) */
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!isset($_SESSION['id_tk'])) {
                header("Location: ../guest/login.php");
                exit;
            }

            $checkoutCart = $_SESSION['checkout_cart'] ?? [];
            if (empty($checkoutCart)) {
                echo "<script>alert('Giỏ hàng thanh toán trống!'); window.location.href='../user/cart.php';</script>";
                exit;
            }

            $id_tk   = $_SESSION['id_tk'];
            $dia_chi = trim($_POST['dia_chi']);
            $id_pttt = $_POST['id_pttt'] ?? 'PT001';
            $id_don_hang = 'DH' . time() . rand(10, 99);

            // 5. Tạo đơn hàng
            $result = $orderModel->createOrder($id_don_hang, $id_tk, $dia_chi, $checkoutCart, $id_pttt);

            // SỬA LẠI LOGIC KIỂM TRA KẾT QUẢ
            if ($result['success']) {
                // 6. Xóa sản phẩm đã mua khỏi GIỎ HÀNG CHÍNH
                foreach ($checkoutCart as $id_sach => $item) {
                    $cartModel->remove($id_sach); 
                }
                
                // 7. Xóa giỏ hàng tạm
                unset($_SESSION['checkout_cart']);
                
                // 8. Chuyển đến trang cảm ơn
                header("Location: ../user/thankyou.php?id_don_hang=" . $id_don_hang);
                exit;
                
            } else {
                // 9. HIỂN THỊ LỖI CHI TIẾT TỪ MODEL
                $error_message = $result['message'] ?? 'Đặt hàng thất bại! Lỗi không xác định.';
                // Làm sạch thông báo lỗi để hiển thị an toàn trong JavaScript
                $safe_message = addslashes($error_message);
                
                echo "<script>alert('$safe_message'); window.location.href='../user/checkout.php';</script>";
                exit;
            }
        }
        break;

    // ... (Các case 'cancel' và 'default' không đổi) ...
    /*HỦY ĐƠN HÀNG */
    case 'cancel':
        if (isset($_GET['id_don_hang'])) {
            $id_don_hang = $_GET['id_don_hang'];
            $orderModel->cancelOrder($id_don_hang);
            header("Location: ../user/orders.php?cancel=success");
        }
        break;

    /* Mặc định: Quay lại danh sách đơn hàng */
    default:
        header("Location: ../user/orders.php");
        break;
}
?>