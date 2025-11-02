<?php
session_start();
require_once '../models/Database.php';

if (!isset($_SESSION['id_tk'])) {
    header("Location: ../guest/login.php");
    exit;
}

$db = new Database();
$cart = $_SESSION['cart'] ?? [];

if (empty($cart)) {
    echo "<script>alert('Giỏ hàng trống!'); window.location.href='cart.php';</script>";
    exit;
}

$total = 0;
foreach ($cart as $item) {
    $total += $item['gia'] * $item['so_luong'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_don_hang = 'DH' . rand(100, 999);
    $id_tk = $_SESSION['id_tk'];
    $dia_chi = trim($_POST['dia_chi']);
    $id_pttt = $_POST['id_pttt']; // PT001, PT002, ...

    if (empty($dia_chi)) {
        echo "<script>alert('Vui lòng nhập địa chỉ nhận hàng!');</script>";
    } else {
        try {
            $db->prepare("START TRANSACTION")->execute();

            // 1️⃣ Thêm đơn hàng
            $sql1 = "INSERT INTO don_hang (id_don_hang, id_tk, id_trang_thai, ngay_gio_tao_don, dia_chi_nhan_hang)
                     VALUES (?, ?, 1, NOW(), ?)";
            $stmt1 = $db->prepare($sql1);
            $stmt1->execute([$id_don_hang, $id_tk, $dia_chi]);

            // 2️⃣ Thêm chi tiết đơn hàng
            $sql2 = "INSERT INTO chi_tiet_don_hang (id_don_hang, id_sach, so_luong_ban) VALUES (?, ?, ?)";
            $stmt2 = $db->prepare($sql2);
            foreach ($cart as $sp) {
                $stmt2->execute([$id_don_hang, $sp['id_sach'], $sp['so_luong']]);
            }

            // 3️⃣ Thêm thông tin thanh toán (mặc định: chưa thanh toán)
            $sql3 = "INSERT INTO thanh_toan (id_pttt, id_don_hang, trang_thai_tt, ngay_gio_thanh_toan)
                     VALUES (?, ?, 0, NOW())";
            $stmt3 = $db->prepare($sql3);
            $stmt3->execute([$id_pttt, $id_don_hang]);

            // Commit giao dịch
            $db->prepare("COMMIT")->execute();

            // Xóa giỏ hàng sau khi thanh toán
            unset($_SESSION['cart']);

            header("Location: thankyou.php?id_don_hang=$id_don_hang");
            exit;
        } catch (PDOException $e) {
            $db->prepare("ROLLBACK")->execute();
            echo "Lỗi đặt hàng: " . $e->getMessage();
        }
    }
}
?>

<?php include_once '../includes/header.php'; ?>

<div class="container">
    <h2>🧾 Xác nhận đơn hàng</h2>

    <form method="POST" class="checkout-form">
        <h3>Thông tin giao hàng</h3>
        <div class="form-group">
            <label>Họ tên:</label>
            <input type="text" value="<?= htmlspecialchars($_SESSION['ho_ten']) ?>" disabled>
        </div>
        <div class="form-group">
            <label>Địa chỉ nhận hàng:</label>
            <input type="text" name="dia_chi" placeholder="Nhập địa chỉ cụ thể..." required>
        </div>

        <div class="form-group">
            <label>Phương thức thanh toán:</label>
            <select name="id_pttt" required>
                <option value="PT001">Thanh toán khi nhận hàng (COD)</option>
                <option value="PT002">Ví điện tử MoMo</option>
                <option value="PT003">Thẻ ngân hàng</option>
            </select>
        </div>

        <h3>Đơn hàng của bạn</h3>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $sp): ?>
                    <tr>
                        <td><?= htmlspecialchars($sp['ten_sach']) ?></td>
                        <td><?= number_format($sp['gia'], 0, ',', '.') ?>đ</td>
                        <td><?= $sp['so_luong'] ?></td>
                        <td><?= number_format($sp['gia'] * $sp['so_luong'], 0, ',', '.') ?>đ</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <h3>Tổng cộng: <span><?= number_format($total, 0, ',', '.') ?>đ</span></h3>
        </div>

        <button type="submit" class="btn">✅ Xác nhận đặt hàng</button>
        <a href="cart.php" class="btn btn-secondary">⬅ Quay lại giỏ hàng</a>
    </form>
</div>

<?php include_once '../includes/footer.php'; ?>
