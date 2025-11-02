<?php
session_start();
require_once '../models/Order.php';
require_once '../models/Book.php';

$orderModel = new Order();
$bookModel = new Book();

$action = $_GET['action'] ?? '';

switch ($action) {

    /* 🧾 TẠO ĐƠN HÀNG (KHI NGƯỜI DÙNG ĐẶT HÀNG) */
    case 'checkout':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            // Kiểm tra đăng nhập
            if (!isset($_SESSION['id_tk'])) {
                header("Location: ../guest/login.php");
                exit;
            }

            // Lấy dữ liệu từ form thanh toán
            $id_tk   = $_SESSION['id_tk'];
            $dia_chi = trim($_POST['dia_chi']);
            $id_pttt = $_POST['id_pttt'] ?? 1; // phương thức thanh toán (1 = COD, mặc định)

            // Giỏ hàng
            $cartItems = $_SESSION['cart'] ?? [];
            if (empty($cartItems)) {
                echo "<script>alert('Giỏ hàng trống!'); window.location.href='../user/cart.php';</script>";
                exit;
            }

            // Sinh mã đơn hàng
            $id_don_hang = 'DH' . rand(100, 999);

            // Tạo đơn hàng
            $result = $orderModel->createOrder($id_don_hang, $id_tk, $dia_chi, $cartItems, $id_pttt);

            if ($result) {
                // Xóa giỏ hàng
                unset($_SESSION['cart']);
                header("Location: ../user/orders.php?success=1");
            } else {
                echo "<script>alert('Đặt hàng thất bại!'); window.location.href='../user/cart.php';</script>";
            }
        }
        break;


    /* ❌ HỦY ĐƠN HÀNG */
    case 'cancel':
        if (isset($_GET['id_don_hang'])) {
            $id_don_hang = $_GET['id_don_hang'];
            $orderModel->cancelOrder($id_don_hang);
            header("Location: ../user/orders.php?cancel=success");
        }
        break;


    /* 🔍 XEM CHI TIẾT ĐƠN HÀNG (nếu có giao diện riêng sau này) */
    case 'detail':
        if (isset($_GET['id_don_hang'])) {
            $id_don_hang = $_GET['id_don_hang'];
            $details = $orderModel->getOrderDetails($id_don_hang);
            echo "<pre>";
            print_r($details);
            echo "</pre>";
        }
        break;


    /* 🚪 Mặc định: Quay lại danh sách đơn hàng */
    default:
        header("Location: ../user/orders.php");
        break;
}
?>
