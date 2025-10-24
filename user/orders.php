<?php
session_start();
include('../config/db.php');
include('../includes/header.php');

$user_id = $_SESSION['user_id'];
$res = mysqli_query($conn, "SELECT * FROM don_hang WHERE id_tk='$user_id' ORDER BY ngay_gio_tao_don DESC");
?>
<h2>Đơn hàng của bạn</h2>
<table border="1" cellpadding="8">
<tr><th>Mã đơn</th><th>Ngày đặt</th><th>Địa chỉ</th><th>Chi tiết</th></tr>
<?php while($r=mysqli_fetch_assoc($res)): ?>
<tr>
<td><?=$r['id_don_hang']?></td>
<td><?=$r['ngay_gio_tao_don']?></td>
<td><?=$r['dia_chi_nhan_hang']?></td>
<td><a href="order_detail.php?id=<?=$r['id_don_hang']?>">Xem</a></td>
</tr>
<?php endwhile; ?>
</table>
<?php include('../includes/footer.php'); ?>
