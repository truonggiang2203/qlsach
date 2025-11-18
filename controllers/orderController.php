<?php
session_start();
// NẠP CÁC MODEL CẦN THIẾT
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../models/Payment.php';

$orderModel = new Order();
$cartModel = new Cart();
$notificationModel = new Notification();
$paymentModel = new Payment();

$action = $_GET['action'] ?? '';

switch ($action) {

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

            // Tính tổng tiền
            $totalAmount = 0;
            foreach ($checkoutCart as $item) {
                $price = $item['price'];
                $quantity = $item['quantity'];
                $discount_percent = $item['discount_percent'] ?? 0;
                $totalAmount += ($price * (1 - $discount_percent / 100)) * $quantity;
            }

            // Tạo đơn hàng
            $result = $orderModel->createOrder($id_don_hang, $id_tk, $dia_chi, $checkoutCart, $id_pttt);

            if ($result['success']) {

                // XÓA SẢN PHẨM ĐÃ MUA KHỎI GIỎ HÀNG
                foreach ($checkoutCart as $id_sach => $item) {
                    $cartModel->remove($id_sach); 
                }

                // XÓA GIỎ HÀNG TẠM
                unset($_SESSION['checkout_cart']);

                // CẬP NHẬT SỐ LƯỢNG GIỎ HÀNG
                $_SESSION['cartCount'] = $cartModel->getCount();

                // XỬ LÝ THANH TOÁN
                // Nếu là COD (PT001), chuyển đến trang cảm ơn
                if ($id_pttt === 'PT001') {
                    // COD - Thanh toán khi nhận hàng
                    $paymentModel->updatePaymentStatus($id_don_hang, 0); // 0 = chưa thanh toán
                    
                    // Thông báo
                    $notificationModel->add(
                        'Đặt hàng thành công',
                        'Đơn hàng ' . $id_don_hang . ' của bạn đã được đặt thành công. Bạn sẽ thanh toán khi nhận hàng.',
                        'success',
                        '/qlsach/user/orders.php',
                        $id_tk
                    );

                    header("Location: ../user/thankyou.php?id_don_hang=" . $id_don_hang);
                    exit;
                } 
                // Nếu là thanh toán online, tạo link thanh toán
                else {
                    $orderInfo = 'Thanh toán đơn hàng ' . $id_don_hang;
                    $paymentResult = null;

                    switch ($id_pttt) {
                        case 'PT002': // MoMo
                            $paymentResult = $paymentModel->createMoMoPayment($id_don_hang, $totalAmount, $orderInfo);
                            break;
                        case 'PT003': // VNPay
                            $paymentResult = $paymentModel->createVNPayPayment($id_don_hang, $totalAmount, $orderInfo);
                            break;
                        case 'PT004': // ZaloPay
                            $paymentResult = $paymentModel->createZaloPayPayment($id_don_hang, $totalAmount, $orderInfo);
                            break;
                    }

                    if ($paymentResult && $paymentResult['success']) {
                        // Lưu thông tin thanh toán vào session
                        $_SESSION['pending_payment'] = [
                            'id_don_hang' => $id_don_hang,
                            'id_pttt' => $id_pttt,
                            'amount' => $totalAmount
                        ];

                        // Chuyển đến trang thanh toán
                        header("Location: " . $paymentResult['payment_url']);
                        exit;
                    } else {
                        // Lỗi tạo link thanh toán
                        $error_message = $paymentResult['message'] ?? 'Không thể tạo link thanh toán. Vui lòng thử lại.';
                        echo "<script>alert('" . addslashes($error_message) . "'); window.location.href='../user/checkout.php';</script>";
                        exit;
                    }
                }
                
            } else {
                $error_message = $result['message'] ?? 'Đặt hàng thất bại! Lỗi không xác định.';
                $safe_message = addslashes($error_message);
                
                echo "<script>alert('$safe_message'); window.location.href='../user/checkout.php';</script>";
                exit;
            }
        }
        break;

    case 'cancel':
        if (isset($_GET['id_don_hang'])) {
            $id_don_hang = $_GET['id_don_hang'];
            $orderModel->cancelOrder($id_don_hang);
            header("Location: ../user/orders.php?cancel=success");
        }
        break;

    default:
        header("Location: ../user/orders.php");
        break;
}
?>
