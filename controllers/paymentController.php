<?php
session_start();
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Notification.php';

$paymentModel = new Payment();
$orderModel = new Order();
$notificationModel = new Notification();

$method = $_GET['method'] ?? '';
$action = $_GET['action'] ?? '';

// Xử lý callback từ payment gateway
switch ($method) {
    case 'momo':
        handleMoMoCallback();
        break;
    
    case 'vnpay':
        handleVNPayCallback();
        break;
    
    case 'zalopay':
        handleZaloPayCallback();
        break;
    
    default:
        header("Location: ../user/orders.php");
        exit;
}

/**
 * Xử lý callback từ MoMo
 */
function handleMoMoCallback() {
    global $paymentModel, $notificationModel;
    
    // TODO: Xác thực callback từ MoMo
    // Trong môi trường thực tế, cần xác thực signature từ MoMo
    
    $orderId = $_GET['orderId'] ?? $_POST['orderId'] ?? '';
    $amount = $_GET['amount'] ?? $_POST['amount'] ?? 0;
    $resultCode = $_GET['resultCode'] ?? $_POST['resultCode'] ?? '0';
    
    if (empty($orderId)) {
        header("Location: ../user/orders.php?payment=error");
        exit;
    }
    
    // resultCode = 0: Thành công
    if ($resultCode == '0') {
        // Cập nhật trạng thái thanh toán
        $paymentModel->updatePaymentStatus($orderId, 1); // 1 = đã thanh toán
        
        // Thông báo
        if (isset($_SESSION['id_tk'])) {
            $notificationModel->add(
                'Thanh toán thành công',
                'Đơn hàng ' . $orderId . ' đã được thanh toán thành công qua MoMo.',
                'success',
                '/qlsach/user/orders.php',
                $_SESSION['id_tk']
            );
        }
        
        // Xóa session pending payment
        unset($_SESSION['pending_payment']);
        
        header("Location: ../user/thankyou.php?id_don_hang=" . $orderId . "&payment=success");
    } else {
        // Thanh toán thất bại
        if (isset($_SESSION['id_tk'])) {
            $notificationModel->add(
                'Thanh toán thất bại',
                'Thanh toán đơn hàng ' . $orderId . ' qua MoMo không thành công. Vui lòng thử lại.',
                'error',
                '/qlsach/user/orders.php',
                $_SESSION['id_tk']
            );
        }
        
        header("Location: ../user/orders.php?payment=failed&order=" . $orderId);
    }
    exit;
}

/**
 * Xử lý callback từ VNPay
 */
function handleVNPayCallback() {
    global $paymentModel, $notificationModel;
    
    // TODO: Xác thực callback từ VNPay
    $vnp_TxnRef = $_GET['vnp_TxnRef'] ?? '';
    $vnp_ResponseCode = $_GET['vnp_ResponseCode'] ?? '';
    $vnp_Amount = $_GET['vnp_Amount'] ?? 0;
    
    if (empty($vnp_TxnRef)) {
        header("Location: ../user/orders.php?payment=error");
        exit;
    }
    
    // vnp_ResponseCode = '00': Thành công
    if ($vnp_ResponseCode == '00') {
        $paymentModel->updatePaymentStatus($vnp_TxnRef, 1);
        
        if (isset($_SESSION['id_tk'])) {
            $notificationModel->add(
                'Thanh toán thành công',
                'Đơn hàng ' . $vnp_TxnRef . ' đã được thanh toán thành công qua VNPay.',
                'success',
                '/qlsach/user/orders.php',
                $_SESSION['id_tk']
            );
        }
        
        unset($_SESSION['pending_payment']);
        header("Location: ../user/thankyou.php?id_don_hang=" . $vnp_TxnRef . "&payment=success");
    } else {
        if (isset($_SESSION['id_tk'])) {
            $notificationModel->add(
                'Thanh toán thất bại',
                'Thanh toán đơn hàng ' . $vnp_TxnRef . ' qua VNPay không thành công.',
                'error',
                '/qlsach/user/orders.php',
                $_SESSION['id_tk']
            );
        }
        
        header("Location: ../user/orders.php?payment=failed&order=" . $vnp_TxnRef);
    }
    exit;
}

/**
 * Xử lý callback từ ZaloPay
 */
function handleZaloPayCallback() {
    global $paymentModel, $notificationModel;
    
    // TODO: Xác thực callback từ ZaloPay
    $orderId = $_GET['orderId'] ?? $_POST['orderId'] ?? '';
    $status = $_GET['status'] ?? $_POST['status'] ?? '';
    
    if (empty($orderId)) {
        header("Location: ../user/orders.php?payment=error");
        exit;
    }
    
    // status = 1: Thành công
    if ($status == '1') {
        $paymentModel->updatePaymentStatus($orderId, 1);
        
        if (isset($_SESSION['id_tk'])) {
            $notificationModel->add(
                'Thanh toán thành công',
                'Đơn hàng ' . $orderId . ' đã được thanh toán thành công qua ZaloPay.',
                'success',
                '/qlsach/user/orders.php',
                $_SESSION['id_tk']
            );
        }
        
        unset($_SESSION['pending_payment']);
        header("Location: ../user/thankyou.php?id_don_hang=" . $orderId . "&payment=success");
    } else {
        if (isset($_SESSION['id_tk'])) {
            $notificationModel->add(
                'Thanh toán thất bại',
                'Thanh toán đơn hàng ' . $orderId . ' qua ZaloPay không thành công.',
                'error',
                '/qlsach/user/orders.php',
                $_SESSION['id_tk']
            );
        }
        
        header("Location: ../user/orders.php?payment=failed&order=" . $orderId);
    }
    exit;
}
?>

