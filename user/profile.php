<?php
include_once '../includes/header.php';

if (!isset($_SESSION['id_tk'])) {
    header("Location: ../guest/login.php");
    exit;
}

require_once '../models/User.php';
require_once '../models/Order.php';
require_once '../models/Wishlist.php';
require_once '../models/Notification.php';

$userModel = new User();
$orderModel = new Order();
$wishlistModel = new Wishlist();
$notificationModel = new Notification();

$user = $userModel->getUserById($_SESSION['id_tk']);

if (!$user) {
    header("Location: ../guest/login.php");
    exit;
}

// Cập nhật session với dữ liệu mới nhất
$_SESSION['ho_ten'] = $user->ho_ten;
$_SESSION['email'] = $user->email;
$_SESSION['sdt'] = $user->sdt;
$_SESSION['dia_chi'] = $user->dia_chi_giao_hang;

$orderSummary = $orderModel->getOrderSummary($_SESSION['id_tk']);
$recentOrders = $orderModel->getRecentOrders($_SESSION['id_tk'], 5);
$wishlistCount = $wishlistModel->getCount($_SESSION['id_tk']);
$notificationUnread = $notificationModel->getUnreadCount($_SESSION['id_tk']);

$statusBadges = [
    'Chờ xử lý' => ['bg' => '#e3f2fd', 'color' => '#0d6efd', 'icon' => ''],
    'Đã xác nhận' => ['bg' => '#e0f7fa', 'color' => '#0d9488', 'icon' => ''],
    'Đang giao hàng' => ['bg' => '#fff7e6', 'color' => '#f59e0b', 'icon' => ''],
    'Đã hoàn thành' => ['bg' => '#e8f9ef', 'color' => '#22c55e', 'icon' => ''],
    'Đã hủy' => ['bg' => '#ffe5e5', 'color' => '#ef4444', 'icon' => ''],
];

function formatCurrency($number)
{
    return number_format($number, 0, ',', '.') . 'đ';
}
?>

<div class="profile-page">
    <header class="profile-hero">
        <div>
            <p>Xin chào,</p>
            <h1><?= htmlspecialchars($user->ho_ten ?? 'Khách hàng') ?></h1>
            <span><?= htmlspecialchars($user->email ?? '') ?></span>
        </div>
        <a href="orders.php" class="btn-secondary">
            Xem đơn hàng
        </a>
    </header>

    <section class="profile-summary-grid">
        <div class="summary-card">
            <div>
                <p>Tổng đơn hàng</p>
                <h3><?= $orderSummary['total_orders'] ?></h3>
            </div>
            <span class="summary-icon"></span>
        </div>
        <div class="summary-card">
            <div>
                <p>Đơn đang xử lý</p>
                <h3><?= $orderSummary['pending_orders'] + $orderSummary['shipping_orders'] ?></h3>
            </div>
            <span class="summary-icon"></span>
        </div>
        <div class="summary-card">
            <div>
                <p>Đã hoàn thành</p>
                <h3><?= $orderSummary['completed_orders'] ?></h3>
            </div>
            <span class="summary-icon"></span>
        </div>
        <div class="summary-card">
            <div>
                <p>Danh sách yêu thích</p>
                <h3><?= $wishlistCount ?></h3>
            </div>
            <span class="summary-icon"></span>
        </div>
        <div class="summary-card">
            <div>
                <p>Thông báo chưa đọc</p>
                <h3><?= $notificationUnread ?></h3>
            </div>
            <span class="summary-icon"></span>
        </div>
        <div class="summary-card">
            <div>
                <p>Chi tiêu (đã giao)</p>
                <h3><?= formatCurrency($orderSummary['total_spent']) ?></h3>
            </div>
            <span class="summary-icon"></span>
        </div>
    </section>

    <div class="profile-layout">
        <div class="profile-main-column">
            <div class="profile-card">
                <div class="profile-card-header">
                    <div>
                        <h2>Thông tin cá nhân</h2>
                        <p>Cập nhật thông tin liên hệ của bạn</p>
                    </div>
                    <span class="profile-icon"></span>
                </div>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">Cập nhật thông tin thành công!</div>
                <?php endif; ?>

                <form action="../controllers/userController.php?action=updateProfile" method="POST" class="profile-form-grid">
                    <div class="form-group">
                        <label>Họ tên</label>
                        <input type="text" name="ho_ten" value="<?= htmlspecialchars($user->ho_ten ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" value="<?= htmlspecialchars($user->email ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="sdt" value="<?= htmlspecialchars($user->sdt ?? '') ?>">
                    </div>
                    <div class="form-group full-width">
                        <label>Địa chỉ giao hàng</label>
                        <textarea name="dia_chi" rows="2"><?= htmlspecialchars($user->dia_chi_giao_hang ?? '') ?></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Lưu thay đổi</button>
                        <a href="change_password.php" class="btn-link">Đổi mật khẩu</a>
                    </div>
                </form>
            </div>

            <div class="profile-card">
                <div class="profile-card-header">
                    <div>
                        <h2>Sở thích & thông báo</h2>
                        <p>Kiểm soát các tùy chọn nhận thông tin của bạn</p>
                    </div>
                    <span class="profile-icon"></span>
                </div>
                <div class="preferences-grid">
                    <div class="toggle-row">
                        <div>
                            <strong>Email khuyến mãi</strong>
                            <p>Nhận thông báo về ưu đãi, voucher mới</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked disabled>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-row">
                        <div>
                            <strong>Thông báo đơn hàng</strong>
                            <p>Nhận thông báo khi trạng thái đơn thay đổi</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" checked disabled>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <div class="toggle-row">
                        <div>
                            <strong>Cập nhật sách mới</strong>
                            <p>Nhận gợi ý sách phù hợp sở thích của bạn</p>
                        </div>
                        <label class="switch">
                            <input type="checkbox" disabled>
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>
                <p class="note">* Tính năng tùy chỉnh thông báo đang được phát triển.</p>
            </div>
        </div>

        <aside class="profile-sidebar">
            <div class="profile-card">
                <div class="profile-card-header">
                    <div>
                        <h2>Trạng thái đơn hàng</h2>
                        <p>Hiện tại của tất cả đơn hàng</p>
                    </div>
                    <span class="profile-icon"></span>
                </div>
                <ul class="status-list">
                    <li>
                        <span>Chờ xử lý</span>
                        <strong><?= $orderSummary['pending_orders'] ?></strong>
                    </li>
                    <li>
                        <span>Đang giao</span>
                        <strong><?= $orderSummary['shipping_orders'] ?></strong>
                    </li>
                    <li>
                        <span>Đã hoàn thành</span>
                        <strong><?= $orderSummary['completed_orders'] ?></strong>
                    </li>
                    <li>
                        <span>Đã hủy</span>
                        <strong><?= $orderSummary['cancelled_orders'] ?></strong>
                    </li>
                </ul>
            </div>

            <div class="profile-card recent-orders-card">
                <div class="profile-card-header">
                    <div>
                        <h2>Đơn hàng gần đây</h2>
                        <p>5 đơn mới nhất</p>
                    </div>
                    <span class="profile-icon"></span>
                </div>
                <?php if (empty($recentOrders)): ?>
                    <p class="text-muted">Bạn chưa có đơn hàng nào.</p>
                <?php else: ?>
                    <div class="recent-orders-list">
                        <?php foreach ($recentOrders as $order):
                            $badge = $statusBadges[$order->trang_thai_dh] ?? ['bg' => '#f3f4f6', 'color' => '#4b5563', 'icon' => '•'];
                        ?>
                            <div class="recent-order-item">
                                <div>
                                    <strong><?= htmlspecialchars($order->id_don_hang) ?></strong>
                                    <p><?= date('d/m/Y H:i', strtotime($order->ngay_gio_tao_don)) ?></p>
                                </div>
                                <div class="recent-order-meta">
                                    <span><?= formatCurrency($order->tong_tien) ?></span>
                                    <span class="status-pill" style="background: <?= $badge['bg'] ?>; color: <?= $badge['color'] ?>;">
                                        <?= $badge['icon'] ?> <?= htmlspecialchars($order->trang_thai_dh) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <a href="orders.php" class="btn-link">Xem tất cả đơn hàng →</a>
                <?php endif; ?>
            </div>

            <div class="profile-card quick-links-card">
                <h3>Liên kết nhanh</h3>
                <ul>
                    <li><a href="/qlsach/public/index.php">Khám phá sách mới</a></li>
                    <li><a href="/qlsach/user/wishlist.php">Danh sách yêu thích</a></li>
                    <li><a href="/qlsach/user/notifications.php">Trung tâm thông báo</a></li>
                </ul>
            </div>
        </aside>
    </div>
</div>

<?php include_once '../includes/footer.php'; ?>