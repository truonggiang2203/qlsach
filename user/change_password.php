<?php
session_start();
include('../config/db.php');
include('../includes/header.php');

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: ../guest/login.php');
    exit;
}

if (isset($_POST['change'])) {
    $old = $_POST['old_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT mat_khau FROM tai_khoan WHERE id_tk='$user_id'"));

    if (!password_verify($old, $row['mat_khau'])) {
        $msg = "❌ Mật khẩu cũ không đúng!";
    } elseif ($new !== $confirm) {
        $msg = "❌ Mật khẩu mới không trùng khớp!";
    } else {
        $hash = password_hash($new, PASSWORD_DEFAULT);
        mysqli_query($conn, "UPDATE tai_khoan SET mat_khau='$hash' WHERE id_tk='$user_id'");
        $msg = "✅ Đổi mật khẩu thành công!";
    }
}
?>

<h2>Đổi mật khẩu</h2>
<?php if(isset($msg)) echo "<p>$msg</p>"; ?>
<form method="POST">
    Mật khẩu cũ: <input type="password" name="old_password" required><br>
    Mật khẩu mới: <input type="password" name="new_password" required><br>
    Nhập lại mật khẩu mới: <input type="password" name="confirm_password" required><br>
    <input type="submit" name="change" value="Đổi mật khẩu">
</form>

<?php include('../includes/footer.php'); ?>
