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
    <title>Đăng ký tài khoản - Nhà sách Online</title>
    <link rel="stylesheet" href="../public/css/auth.css">
</head>
<body class="auth-page">

    <!-- Home Link -->
    <a href="../public/index.php" class="home-link">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
            <polyline points="9 22 9 12 15 12 15 22"></polyline>
        </svg>
        <span>Trang chủ</span>
    </a>

    <!-- Auth Container -->
    <div class="auth-container">
        <!-- Header -->
        <div class="auth-header">
            <h2>Tạo tài khoản mới</h2>
            <p>Đăng ký để trải nghiệm mua sắm tuyệt vời</p>
        </div>

        <!-- Body -->
        <div class="auth-body">
            <!-- Messages -->
            <?php if (!empty($error_msg)): ?>
                <div class="form-message error">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                    <?php echo $error_msg; ?>
                </div>
            <?php endif; ?>
            
            <!-- Register Form -->
            <form action="../controllers/AuthController.php?action=register" method="POST" id="registerForm">
                <div class="form-group">
                    <label for="ho_ten">Họ và tên</label>
                    <input type="text" id="ho_ten" name="ho_ten" placeholder="Nguyễn Văn A" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="example@email.com" required>
                </div>
                <div class="form-group">
                    <label for="sdt">Số điện thoại</label>
                    <input type="tel" id="sdt" name="sdt" placeholder="0123456789" required>
                </div>
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required minlength="6">
                </div>
                <div class="form-group">
                    <label for="dia_chi">Địa chỉ giao hàng</label>
                    <input type="text" id="dia_chi" name="dia_chi" placeholder="123 Đường ABC, Quận XYZ" required>
                </div>
                <button type="submit" class="btn-submit">Đăng ký</button>
            </form>

            <!-- Divider -->
            <div class="auth-divider">
                <span>Hoặc đăng ký bằng</span>
            </div>

            <!-- Google Sign-Up -->
            <a href="<?php 
                require_once '../config/google_config.php';
                $auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?' . http_build_query([
                    'client_id' => GOOGLE_CLIENT_ID,
                    'redirect_uri' => GOOGLE_REDIRECT_URI,
                    'response_type' => 'code',
                    'scope' => GOOGLE_SCOPES,
                    'access_type' => 'offline',
                    'prompt' => 'consent'
                ]);
                echo $auth_url;
            ?>" class="google-btn">
                <svg width="20" height="20" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Đăng ký với Google
            </a>

            <!-- Footer -->
            <div class="auth-footer">
                <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>

    <script>
        // Loading state khi submit form
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('.btn-submit');
            btn.classList.add('loading');
        });
        
        // Validate password length
        document.getElementById('password').addEventListener('input', function() {
            if (this.value.length > 0 && this.value.length < 6) {
                this.setCustomValidity('Mật khẩu phải có ít nhất 6 ký tự');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>

</body>
</html>