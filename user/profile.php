<?php
include_once '../includes/header.php';

// KHÔNG cần session_start() ở đây vì header.php đã có
if (!isset($_SESSION['id_tk'])) {
    header("Location: ../guest/login.php");
    exit;
}

// ✅ Load dữ liệu từ database để đảm bảo luôn có dữ liệu mới nhất
require_once '../models/User.php';
$userModel = new User();
$user = $userModel->getUserById($_SESSION['id_tk']);

// Nếu không tìm thấy user, redirect về login
if (!$user) {
    header("Location: ../guest/login.php");
    exit;
}

// ✅ Cập nhật session với dữ liệu từ database
$_SESSION['ho_ten'] = $user->ho_ten;
$_SESSION['email'] = $user->email;
$_SESSION['sdt'] = $user->sdt;
$_SESSION['dia_chi'] = $user->dia_chi_giao_hang;
?>

<div class="profile-container">
    <div class="profile-card">
        <div class="profile-header">
            <span class="profile-icon">👤</span>
            <h2>Hồ sơ của tôi</h2>
            <p>Cập nhật thông tin cá nhân và địa chỉ giao hàng</p>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">Cập nhật thông tin thành công!</div>
        <?php endif; ?>

        <form action="../controllers/userController.php?action=updateProfile" method="POST" class="profile-form">
            <div class="form-group">
                <label>Họ tên:</label>
                <input type="text" name="ho_ten" value="<?= htmlspecialchars($user->ho_ten ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Số điện thoại:</label>
                <input type="text" name="sdt" value="<?= htmlspecialchars($user->sdt ?? '') ?>">
            </div>

            <div class="form-group">
                <label>Địa chỉ giao hàng:</label>
                <input type="text" name="dia_chi" value="<?= htmlspecialchars($user->dia_chi_giao_hang ?? '') ?>">
            </div>

            <button type="submit" class="btn btn-primary">💾 Cập nhật thông tin</button>
        </form>

        <div class="profile-footer">
            <p><a href="change_password.php">🔒 Đổi mật khẩu</a> | 
            <a href="orders.php">🧾 Xem đơn hàng</a></p>
        </div>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>
