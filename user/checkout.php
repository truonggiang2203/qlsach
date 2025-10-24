<?php
session_start();
include('../config/db.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../guest/login.php");
    exit;
}

if (isset($_POST['confirm'])) {
    $id_don_hang = uniqid('DH');
    $id_tk = $_SESSION['user_id'];
    $dia_chi = mysqli_real_escape_string($conn, $_POST['address']);
    $date = date('Y-m-d H:i:s');
    mysqli_query($conn, "INSERT INTO don_hang(id_don_hang, id_tk, ngay_gio_tao_don, dia_chi_nhan_hang) 
                         VALUES('$id_don_hang','$id_tk','$date','$dia_chi')");
    foreach($_SESSION['cart'] as $id=>$i){
        mysqli_query($conn, "INSERT INTO chi_tiet_don_hang VALUES('$id_don_hang','$id',{$i['quantity']})");
    }
    unset($_SESSION['cart']);
    echo "<script>alert('Đặt hàng thành công!'); window.location='orders.php';</script>";
}
?>
<h2>Thanh toán</h2>
<form method="POST">
<label>Địa chỉ nhận hàng:</label><br>
<textarea name="address" required rows="3" cols="40"></textarea><br><br>
<input type="submit" name="confirm" value="Xác nhận đặt hàng">
</form>
<?php include('../includes/footer.php'); ?>
