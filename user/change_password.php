<?php
include_once '../includes/header.php';
session_start();

if (!isset($_SESSION['id_tk'])) {
    header("Location: ../guest/login.php");
    exit;
}
?>

<div class="form-container">
    <h2>🔐 Đổi mật khẩu</h2>

    <form action="../controllers/userController.php?action=changePassword" method="POST">
        <div class="form-group">
            <label>Mật khẩu cũ:</label>
            <input type="password" name="old_password" required>
        </div>
        <div class="form-group">
            <label>Mật khẩu mới:</label>
            <input type="password" name="new_password" required>
        </div>
        <button type="submit" class="btn">Cập nhật mật khẩu</button>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
