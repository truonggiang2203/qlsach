<?php
session_start();
include('../config/db.php');
include('../includes/header.php');

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM tai_khoan WHERE id_tk='$user_id'";
$user = mysqli_fetch_assoc(mysqli_query($conn, $sql));

if (isset($_POST['save'])) {
    $name = $_POST['ho_ten'];
    $sdt = $_POST['sdt'];
    $email = $_POST['email'];
    $diachi = $_POST['dia_chi_giao_hang'];
    mysqli_query($conn, "UPDATE tai_khoan SET ho_ten='$name', sdt='$sdt', email='$email', dia_chi_giao_hang='$diachi' WHERE id_tk='$user_id'");
    echo "<script>alert('Cập nhật thành công');</script>";
}
?>
<h2>Thông tin cá nhân</h2>
<form method="POST">
Tên: <input type="text" name="ho_ten" value="<?=$user['ho_ten']?>"><br>
Email: <input type="email" name="email" value="<?=$user['email']?>"><br>
SĐT: <input type="text" name="sdt" value="<?=$user['sdt']?>"><br>
Địa chỉ: <input type="text" name="dia_chi_giao_hang" value="<?=$user['dia_chi_giao_hang']?>"><br><br>
<input type="submit" name="save" value="Lưu thay đổi">
</form>
<?php include('../includes/footer.php'); ?>
