<?php
include_once '../includes/header.php';
require_once '../models/Notification.php';

if (!isset($_SESSION['id_tk'])) {
    header("Location: /qlsach/guest/login.php");
    exit;
}

$notificationModel = new Notification();
$notifications = $notificationModel->getAll();
$unreadCount = $notificationModel->getUnreadCount();
?>

<div class="container" style="margin-top: 30px; max-width: 900px;">
    <div class="notifications-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <h2 style="margin: 0;">
             Thông báo
            <?php if ($unreadCount > 0): ?>
                <span style="background: var(--danger); color: white; padding: 4px 8px; border-radius: 12px; font-size: 14px; margin-left: 8px;">
                    <?= $unreadCount ?> chưa đọc
                </span>
            <?php endif; ?>
        </h2>
        <?php if (!empty($notifications)): ?>
            <div style="display: flex; gap: 8px;">
                <a href="../controllers/notificationController.php?action=markAllAsRead" 
                   class="btn" 
                   style="padding: 8px 16px; background: var(--primary); color: white; border-radius: 6px; text-decoration: none; font-size: 14px;">
                    Đánh dấu tất cả đã đọc
                </a>
                <a href="../controllers/notificationController.php?action=clearRead" 
                   class="btn" 
                   style="padding: 8px 16px; background: var(--light-bg); color: var(--text); border-radius: 6px; text-decoration: none; font-size: 14px; border: 1px solid var(--border);"
                   onclick="return confirm('Bạn có chắc muốn xóa tất cả thông báo đã đọc?')">
                    Xóa đã đọc
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (empty($notifications)): ?>
        <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 12px; box-shadow: var(--shadow);">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="color: var(--border); margin: 0 auto 16px;">
                <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
            </svg>
            <p style="color: var(--text); font-size: 16px; margin-bottom: 8px;">Chưa có thông báo nào</p>
            <p style="color: #666; font-size: 14px;">Các thông báo về đơn hàng, khuyến mãi sẽ hiển thị ở đây</p>
        </div>
    <?php else: ?>
        <div class="notifications-list" style="display: flex; flex-direction: column; gap: 12px;">
            <?php foreach ($notifications as $notif): ?>
                <div class="notification-item" 
                     style="background: white; padding: 16px 20px; border-radius: 8px; box-shadow: var(--shadow); 
                            <?= !isset($notif['read']) || !$notif['read'] ? 'border-left: 4px solid var(--primary);' : 'opacity: 0.7;' ?>">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 16px;">
                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 8px;">
                                <?php
                                $typeIcon = '🔔';
                                $typeColor = 'var(--primary)';
                                if (isset($notif['type'])) {
                                    switch ($notif['type']) {
                                        case 'success':
                                            $typeIcon = '';
                                            $typeColor = '#4CAF50';
                                            break;
                                        case 'warning':
                                            $typeIcon = '';
                                            $typeColor = '#FF9800';
                                            break;
                                        case 'error':
                                            $typeIcon = '';
                                            $typeColor = 'var(--danger)';
                                            break;
                                        default:
                                            $typeIcon = '';
                                    }
                                }
                                ?>
                                <span style="font-size: 20px;"><?= $typeIcon ?></span>
                                <h3 style="margin: 0; font-size: 16px; font-weight: 600; color: var(--text);">
                                    <?= htmlspecialchars($notif['title'] ?? 'Thông báo') ?>
                                </h3>
                                <?php if (!isset($notif['read']) || !$notif['read']): ?>
                                    <span style="background: var(--primary); width: 8px; height: 8px; border-radius: 50%; display: inline-block;"></span>
                                <?php endif; ?>
                            </div>
                            <p style="margin: 0 0 8px 0; color: #666; font-size: 14px; line-height: 1.5;">
                                <?= htmlspecialchars($notif['message'] ?? '') ?>
                            </p>
                            <div style="display: flex; align-items: center; gap: 12px; font-size: 12px; color: #999;">
                                <span> <?= isset($notif['created_at']) ? date('d/m/Y H:i', strtotime($notif['created_at'])) : 'Vừa xong' ?></span>
                                <?php if (isset($notif['link']) && $notif['link']): ?>
                                    <a href="<?= htmlspecialchars($notif['link']) ?>" 
                                       style="color: var(--primary); text-decoration: none; font-weight: 500;">
                                        Xem chi tiết →
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <?php if (!isset($notif['read']) || !$notif['read']): ?>
                                <a href="../controllers/notificationController.php?action=markAsRead&id=<?= urlencode($notif['id']) ?>" 
                                   style="padding: 6px 12px; background: var(--light-bg); color: var(--text); border-radius: 6px; text-decoration: none; font-size: 12px; border: 1px solid var(--border);">
                                    Đánh dấu đã đọc
                                </a>
                            <?php endif; ?>
                            <a href="../controllers/notificationController.php?action=delete&id=<?= urlencode($notif['id']) ?>" 
                               style="padding: 6px 12px; background: var(--light-bg); color: var(--danger); border-radius: 6px; text-decoration: none; font-size: 12px; border: 1px solid var(--border);"
                               onclick="return confirm('Bạn có chắc muốn xóa thông báo này?')">
                                ✕
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<?php include_once '../includes/footer.php'; ?>

