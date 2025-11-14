<?php
session_start();
require_once '../models/User.php';
$userModel = new User();

$action = $_GET['action'] ?? '';

switch ($action) {

    /* =====================================================
        🧩 ĐĂNG KÝ TÀI KHOẢN
    ===================================================== */
    case 'register':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $ho_ten = $_POST['ho_ten'];
            $email = $_POST['email'];
            $sdt = $_POST['sdt'];
            $password = $_POST['password'];
            $dia_chi = $_POST['dia_chi'];

            // === BẮT ĐẦU SỬA LỖI 1: TẠO ID_TK AN TOÀN ===
            $id_tk = '';
            do {
                // Tạo ID ngẫu nhiên gồm 3 chữ số, ví dụ: TK007, TK123
                $rand_num = str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
                $id_tk = 'TK' . $rand_num;
                
                // Dùng hàm mới vừa thêm vào User.php để kiểm tra
            } while ($userModel->findUserByAccountId($id_tk)); 
            // === KẾT THÚC SỬA LỖI 1 ===


            if ($userModel->findUserByEmail($email)) {
                // === BẮT ĐẦU SỬA LỖI 2: XỬ LÝ LỖI UX ===
                // Không echo, chuyển hướng về trang đăng ký với mã lỗi
                header('Location: ../guest/register.php?error=email_exists');
                exit;
                // === KẾT THÚC SỬA LỖI 2 ===
            }

            if ($userModel->register($id_tk, $ho_ten, $email, $sdt, $password, $dia_chi)) {
                // Đăng ký thành công, chuyển hướng về login với thông báo
                header('Location: ../guest/login.php?register=success');
            } else {
                // Lỗi không xác định
                header('Location: ../guest/register.php?error=failed');
            }
        }
        break;


    /* =====================================================
        🔐 ĐĂNG NHẬP
    ===================================================== */
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $user = $userModel->login($email, $password);

            if ($user) {
                // ... (Toàn bộ phần gán $_SESSION của bạn giữ nguyên)
                $_SESSION['id_tk'] = $user->id_tk;
                $_SESSION['id_nd'] = $user->id_nd;
                $_SESSION['ho_ten'] = $user->ho_ten;
                $_SESSION['phan_quyen'] = $user->phan_quyen;
                $_SESSION['email'] = $user->email;
                $_SESSION['sdt'] = $user->sdt;
                $_SESSION['dia_chi'] = $user->dia_chi_giao_hang;

                // Điều hướng theo vai trò
                if ($user->phan_quyen === 'admin') {
                    header('Location: ../admin/dashboard.php');
                } else {
                    header('Location: ../public/index.php');
                }
                exit;
            } else {
                // === BẮT ĐẦU SỬA LỖI 2: XỬ LÝ LỖI UX ===
                // Sai email/pass, chuyển hướng về login với mã lỗi
                header('Location: ../guest/login.php?error=invalid_credentials');
                exit;
                // === KẾT THÚC SỬA LỖI 2 ===
            }
        }
        break;

    /* =====================================================
       🧾 CẬP NHẬT THÔNG TIN CÁ NHÂN
    ===================================================== */
    case 'updateProfile':
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['id_tk'])) {
            $id_tk = $_SESSION['id_tk'];
            $ho_ten = $_POST['ho_ten'];
            $email = $_POST['email'];
            $sdt = $_POST['sdt'];
            $dia_chi = $_POST['dia_chi'];

            if ($userModel->updateUser($id_tk, $ho_ten, $email, $sdt, $dia_chi)) {
                // ✅ Cập nhật lại session
                $_SESSION['ho_ten'] = $ho_ten;
                $_SESSION['email'] = $email;
                $_SESSION['sdt'] = $sdt;
                $_SESSION['dia_chi'] = $dia_chi;

                header('Location: ../user/profile.php?update=success');
            } else {
                header('Location: ../user/profile.php?update=failed');
            }
        }
        break;


    /* =====================================================
       🚪 ĐĂNG XUẤT
    ===================================================== */
    case 'logout':
        session_unset();
        session_destroy();
        header('Location: ../public/index.php');
        break;

    /* =====================================================
       MẶC ĐỊNH
    ===================================================== */
    default:
        header('Location: ../public/index.php');
        break;
}
?>
