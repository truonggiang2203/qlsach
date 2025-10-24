<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>

    <div class="form-container">
        <h2>Đăng ký tài khoản</h2>
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