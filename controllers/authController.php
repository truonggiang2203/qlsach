<?php
session_start();

require_once '../models/User.php';

$action = $_GET['action'] ?? '';
$userModel = new User();

switch ($action) {
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Lấy dữ liệu từ form
            $ho_ten = $_POST['ho_ten'];
            $email = $_POST['email'];
            $sdt = $_POST['sdt'];
            $password = $_POST['password'];
            $dia_chi = $_POST['dia_chi'];
            
            // **TẠO ID_TK DUY NHẤT (VẤN ĐỀ CỦA VARCHAR(5))**
            // Đây là ví dụ đơn giản, bạn NÊN CÓ giải pháp tốt hơn
            $id_tk = 'TK' . rand(100, 999); 

            if ($userModel->findUserByEmail($email)) {
                echo "Email đã tồn tại!";
            } else {
                if ($userModel->register($id_tk, $ho_ten, $email, $sdt, $password, $dia_chi)) {
                    header('Location: ../guest/login.php?register=success');
                } else {
                    echo "Đăng ký thất bại. Vui lòng thử lại.";
                }
            }
        }
        break;

    case 'login':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $userModel->login($email, $password);

            if ($user) {
                // Đăng nhập thành công, lưu thông tin vào SESSION
                $_SESSION['id_tk'] = $user->id_tk;
                $_SESSION['ho_ten'] = $user->ho_ten;
                $_SESSION['phan_quyen'] = $user->phan_quyen; // Quan trọng

                // Điều hướng dựa trên vai trò (phan_quyen)
                if ($user->phan_quyen == 'admin') {
                    header('Location: ../admin/dashboard.php'); // Tới trang admin
                } else {
                    header('Location: ../public/index.php'); // Về trang chủ
                }
            } else {
                echo "Sai Email hoặc Mật khẩu!";
            }
        }
        break;

    case 'logout':
        session_unset();
        session_destroy();
        header('Location: ../public/index.php');
        break;

    default:
        header('Location: ../public/index.php');
        break;
}
?>