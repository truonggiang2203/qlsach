<?php
session_start();
require_once '../models/User.php';
$userModel = new User();

$action = $_GET['action'] ?? '';

switch ($action) {
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
                
                header('Location: ../user/profile.php?success=1');
                exit;
            } else {
                header('Location: ../user/profile.php?error=1');
                exit;
            }
        }
        break;

    case 'changePassword':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id_tk = $_SESSION['id_tk'];
            $old_pw = $_POST['old_password'];
            $new_pw = $_POST['new_password'];

            if ($userModel->changePassword($id_tk, $old_pw, $new_pw)) {
                header('Location: ../user/change_password.php?success=1');
            } else {
                echo "Mật khẩu cũ không đúng!";
            }
        }
        break;

    default:
        header('Location: ../user/profile.php');
        break;
}
?>
