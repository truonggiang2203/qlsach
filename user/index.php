<?php
session_start();
include('../config/db.php');
include('../includes/header.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../guest/login.php');
    exit;
}

// Lấy tên người dùng
$user_id = $_SESSION['user_id'];
$sql = "SELECT ho_ten FROM tai_khoan WHERE id_tk = '$user_id'";
$user = mysqli_fetch_assoc(mysqli_query($conn, $sql));
?>

<h2>Xin chào, <?= htmlspecialchars($user['ho_ten']) ?> 👋</h2>
<p>Chào mừng bạn đến trang cá nhân.</p>

<nav>
    <a href="profile.php">Hồ sơ</a> |
    <a href="orders.php">Đơn hàng</a> |
    <a href="cart.php">Giỏ hàng</a> |
    <a href="wishlist.php">Yêu thích</a>
</nav>

<hr>

<h3>Gợi ý sách mới</h3>
<?php
$result = mysqli_query($conn, "SELECT * FROM sach ORDER BY RAND() LIMIT 4");
while ($book = mysqli_fetch_assoc($result)) {
    echo "<div style='display:inline-block; margin:10px; border:1px solid #ccc; padding:10px; width:200px;'>
            <b>{$book['ten_sach']}</b><br>
            <a href='../public/product_detail.php?id={$book['id_sach']}'>Xem chi tiết</a>
          </div>";
}
include('../includes/footer.php');
?>
