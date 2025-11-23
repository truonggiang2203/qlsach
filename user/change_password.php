<?php
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['id_tk'])) {
    header('Location: ../guest/login.php');
    exit;
}

$error_msg = '';
$success_msg = '';

if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'empty_fields':
            $error_msg = 'Vui lòng điền đầy đủ thông tin.';
            break;
        case 'password_mismatch':
            $error_msg = 'Mật khẩu mới và xác nhận không khớp.';
            break;
        case 'password_short':
            $error_msg = 'Mật khẩu phải có ít nhất 6 ký tự.';
            break;
        case 'wrong_password':
            $error_msg = 'Mật khẩu hiện tại không đúng.';
            break;
        case 'failed':
            $error_msg = 'Không thể đổi mật khẩu. Vui lòng thử lại.';
            break;
    }
}

if (isset($_GET['success']) && $_GET['success'] == 'changed') {
    $success_msg = '✅ Đổi mật khẩu thành công!';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu - Nhà sách Online</title>
    <link rel="stylesheet" href="../public/css/style.css">
    <style>
        body {
            background: var(--light-bg);
        }
        .change-password-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
        }
        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            color: var(--primary);
            text-decoration: none;
            margin-bottom: 20px;
            font-weight: 500;
        }
        .back-link:hover {
            text-decoration: underline;
        }
        .password-card {
            background: white;
            border-radius: 12px;
            box-shadow: var(--shadow);
            overflow: hidden;
        }
        .password-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .password-header h2 {
            margin: 0 0 8px 0;
            font-size: 24px;
        }
        .password-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 14px;
        }
        .password-body {
            padding: 30px;
        }
        .form-message {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 14px;
        }
        .form-message.error {
            background: #fee;
            color: #c33;
            border-left: 4px solid #c33;
        }
        .form-message.success {
            background: #efe;
            color: #3c3;
            border-left: 4px solid #3c3;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            color: #333;
            margin-bottom: 8px;
        }
        .form-group input {
            width: 100%;
            padding: 12px 14px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(93, 162, 213, 0.1);
        }
        .form-info {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            padding: 12px;
            background: #e7f3ff;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 13px;
            color: #0066cc;
        }
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
    </style>
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <div class="change-password-container">
        <a href="profile.php" class="back-link">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Quay lại trang cá nhân
        </a>

        <div class="password-card">
            <div class="password-header">
                <h2>🔐 Đổi mật khẩu</h2>
                <p>Cập nhật mật khẩu cho tài khoản của bạn</p>
            </div>

            <div class="password-body">
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
                <?php if (!empty($success_msg)): ?>
                    <div class="form-message success">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                            <polyline points="22 4 12 14.01 9 11.01"></polyline>
                        </svg>
                        <?php echo $success_msg; ?>
                    </div>
                <?php endif; ?>
                
                <!-- Change Password Form -->
                <form action="../controllers/AuthController.php?action=change_password" method="POST" id="changePasswordForm">
                    <div class="form-group">
                        <label for="old_password">Mật khẩu hiện tại</label>
                        <input type="password" id="old_password" name="old_password" placeholder="Nhập mật khẩu hiện tại" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới</label>
                        <input type="password" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu mới</label>
                        <input type="password" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required minlength="6">
                    </div>
                    
                    <div class="form-info">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="16" x2="12" y2="12"></line>
                            <line x1="12" y1="8" x2="12.01" y2="8"></line>
                        </svg>
                        <span>Mật khẩu mới phải có ít nhất 6 ký tự và khác mật khẩu hiện tại.</span>
                    </div>
                    
                    <button type="submit" class="btn-submit">Đổi mật khẩu</button>
                </form>
            </div>
        </div>
    </div>

    <?php include '../includes/footer.php'; ?>

    <script>
        // Validate password match
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPass = document.getElementById('new_password').value;
            if (this.value && this.value !== newPass) {
                this.setCustomValidity('Mật khẩu xác nhận không khớp');
            } else {
                this.setCustomValidity('');
            }
        });
        
        // Validate new password different from old
        document.getElementById('new_password').addEventListener('input', function() {
            const oldPass = document.getElementById('old_password').value;
            if (this.value && this.value === oldPass) {
                this.setCustomValidity('Mật khẩu mới phải khác mật khẩu hiện tại');
            } else {
                this.setCustomValidity('');
            }
        });
    </script>

</body>
</html>
