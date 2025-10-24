<?php
session_start();
include('../config/db.php');
include('../includes/header.php');

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if (isset($_GET['action']) && $_GET['action'] == 'add') {
    $id = $_GET['id'];
    $sql = "SELECT ten_sach, gia_sach_ban FROM sach JOIN gia_sach USING(id_sach)
            WHERE id_sach='$id' ORDER BY ngay_gio_ban DESC LIMIT 1";
    $res = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($res)) {
        if (isset($_SESSION['cart'][$id])) $_SESSION['cart'][$id]['quantity']++;
        else $_SESSION['cart'][$id] = ['name'=>$row['ten_sach'],'price'=>$row['gia_sach_ban'],'quantity'=>1];
    }
    header("Location: cart.php");
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'remove') {
    unset($_SESSION['cart'][$_GET['id']]);
    header("Location: cart.php");
    exit;
}

if (isset($_POST['update'])) {
    foreach ($_POST['quantity'] as $id => $q) $_SESSION['cart'][$id]['quantity'] = max(1,(int)$q);
}
?>
<h2>🛒 Giỏ hàng</h2>
<form method="POST">
<table border="1" cellpadding="8">
<tr><th>Tên sách</th><th>Giá</th><th>Số lượng</th><th>Tổng</th><th>Xóa</th></tr>
<?php
$total=0;
foreach($_SESSION['cart'] as $id=>$i){
    $subtotal=$i['price']*$i['quantity']; $total+=$subtotal;
    echo "<tr>
            <td>{$i['name']}</td>
            <td>".number_format($i['price'])."đ</td>
            <td><input type='number' name='quantity[$id]' value='{$i['quantity']}' min='1'></td>
            <td>".number_format($subtotal)."đ</td>
            <td><a href='cart.php?action=remove&id=$id'>❌</a></td>
          </tr>";
}
?>
<tr><td colspan="3" align="right"><b>Tổng:</b></td><td colspan="2"><?=number_format($total)?> đ</td></tr>
</table>
<br><input type="submit" name="update" value="Cập nhật">
<a href="checkout.php">Thanh toán</a>
</form>
<?php include('../includes/footer.php'); ?>
