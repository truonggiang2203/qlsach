<?php
// THÊM VÀO ĐẦU FILE
$error_msg = '';
if (isset($_GET['error'])) {
    if ($_GET['error'] == 'email_exists') {
        $error_msg = 'Email này đã tồn tại. Vui lòng sử dụng email khác!';
    } elseif ($_GET['error'] == 'failed') {
        $error_msg = 'Đăng ký thất bại. Vui lòng thử lại sau.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="../public/css/style.css">

    <style>
        .form-message {
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 15px;
            border: 1px solid transparent;
        }
        .error { background-color: #f8d7da; color: #721c24; border-color: #f5c6cb; }
    </style>
</head>
<body class="form-page-bg">

    <a href="../public/index.php" class="home-link">
        <img src="../images/home-icon.png" alt="Icon trang chủ">
        <span>Trở về Trang chủ</span>
    </a>

    <div class="form-container">
        <h2>Đăng ký tài khoản</h2>
        
        <?php if (!empty($error_msg)): ?>
            <div class="form-message error"><?php echo $error_msg; ?></div>
        <?php endif; ?>

        <form action="../controllers/AuthController.php?action=register" method="POST">
            <div class="form-group">
                <label for="ho_ten">Họ tên:</label>
                <input type="text" id="ho_ten" name="ho_ten" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
             <div class="form-group">
                <label for="sdt">Số điện thoại:</label>
                <input type="text" id="sdt" name="sdt" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
             <div class="form-group">
                <label for="dia_chi">Địa chỉ giao hàng:</label>
                <input type="text" id="dia_chi" name="dia_chi" required>
            </div>
            <button type="submit" class="btn">Đăng ký</button>
        </form>
        <p>Đã có tài khoản? <a href="login.php">Đăng nhập</a></p>
    </div>

</body>
</html>