<?php
// THÊM VÀO ĐẦU FILE
$error_msg = '';
$success_msg = '';

if (isset($_GET['error'])) {
    if ($_GET['error'] == 'invalid_credentials') {
        $error_msg = 'Sai email hoặc mật khẩu. Vui lòng thử lại!';
    }
}
if (isset($_GET['register']) && $_GET['register'] == 'success') {
    $success_msg = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
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
        .success { background-color: #d4edda; color: #155724; border-color: #c3e6cb; }
    </style>
</head>
<body class="form-page-bg">

    <a href="../public/index.php" class="home-link">
        <img src="../images/home-icon.png" alt="Icon trang chủ">
        <span>Trở về Trang chủ</span>
    </a>

    <div class="form-container">
        <h2>Đăng nhập</h2>

        <?php if (!empty($error_msg)): ?>
            <div class="form-message error"><?php echo $error_msg; ?></div>
        <?php endif; ?>
        <?php if (!empty($success_msg)): ?>
            <div class="form-message success"><?php echo $success_msg; ?></div>
        <?php endif; ?>
        
        <form action="../controllers/AuthController.php?action=login" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Đăng nhập</button>
        </form>
        <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
    </div>

</body>
</html>