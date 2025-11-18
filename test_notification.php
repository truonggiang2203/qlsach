<?php
/**
 * 🔔 FILE TEST THÔNG BÁO
 * 
 * File này giúp bạn test hệ thống thông báo đã hoạt động chưa
 * 
 * CÁCH SỬ DỤNG:
 * 1. Đăng nhập vào hệ thống
 * 2. Truy cập: http://localhost/qlsach/test_notification.php
 * 3. Xem kết quả và kiểm tra thông báo
 */

session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['id_tk'])) {
    die('<h1>❌ Bạn cần đăng nhập trước!</h1><p><a href="guest/login.php">Đăng nhập tại đây</a></p>');
}

require_once __DIR__ . '/models/Notification.php';

$notificationModel = new Notification();
$id_tk = $_SESSION['id_tk'];
$ho_ten = $_SESSION['ho_ten'] ?? 'Người dùng';

// Xử lý thêm thông báo test
if (isset($_GET['action']) && $_GET['action'] === 'add') {
    $type = $_GET['type'] ?? 'info';
    
    $messages = [
        'info' => [
            'title' => 'Thông báo mới',
            'message' => 'Đây là thông báo thông tin mẫu để kiểm tra hệ thống.',
            'type' => 'info'
        ],
        'success' => [
            'title' => 'Thành công!',
            'message' => 'Bạn đã hoàn thành một thao tác nào đó thành công.',
            'type' => 'success'
        ],
        'warning' => [
            'title' => 'Cảnh báo',
            'message' => 'Đây là thông báo cảnh báo, vui lòng chú ý!',
            'type' => 'warning'
        ],
        'error' => [
            'title' => 'Lỗi',
            'message' => 'Đã xảy ra lỗi trong quá trình xử lý.',
            'type' => 'error'
        ]
    ];
    
    $msg = $messages[$type] ?? $messages['info'];
    
    $result = $notificationModel->add(
        $msg['title'],
        $msg['message'],
        $msg['type'],
        '/qlsach/user/notifications.php',
        $id_tk
    );
    
    if ($result) {
        echo '<div style="background: #4CAF50; color: white; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                ✅ Đã thêm thông báo thành công! 
                <a href="user/notifications.php" style="color: white; text-decoration: underline;">Xem thông báo</a>
              </div>';
    } else {
        echo '<div style="background: #f44336; color: white; padding: 10px; margin-bottom: 20px; border-radius: 4px;">
                ❌ Không thể thêm thông báo. Vui lòng kiểm tra lại!
              </div>';
    }
}

// Lấy thông tin
$allNotifications = $notificationModel->getAll($id_tk);
$unreadCount = $notificationModel->getUnreadCount($id_tk);
$totalCount = count($allNotifications);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Thông báo - QLSách</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #5DA2D5;
            padding-bottom: 10px;
        }
        .info-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 6px;
            margin: 20px 0;
            border-left: 4px solid #2196F3;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin: 20px 0;
        }
        .stat-box {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }
        .stat-box strong {
            display: block;
            font-size: 24px;
            color: #5DA2D5;
            margin-bottom: 5px;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin: 20px 0;
            flex-wrap: wrap;
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            text-decoration: none;
            display: inline-block;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
        }
        .btn-info { background: #2196F3; color: white; }
        .btn-success { background: #4CAF50; color: white; }
        .btn-warning { background: #FF9800; color: white; }
        .btn-error { background: #f44336; color: white; }
        .btn:hover { opacity: 0.8; transform: translateY(-2px); }
        .notification-list {
            margin-top: 30px;
        }
        .notification-item {
            background: #f9f9f9;
            padding: 15px;
            margin: 10px 0;
            border-radius: 6px;
            border-left: 4px solid #ddd;
        }
        .notification-item.unread {
            border-left-color: #5DA2D5;
            background: #f0f8ff;
        }
        .notification-item strong {
            display: block;
            margin-bottom: 5px;
        }
        .notification-item small {
            color: #666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔔 Test Hệ thống Thông báo</h1>
        
        <div class="info-box">
            <strong>👤 Người dùng:</strong> <?= htmlspecialchars($ho_ten) ?><br>
            <strong>🆔 ID Tài khoản:</strong> <?= htmlspecialchars($id_tk) ?>
        </div>

        <div class="stats">
            <div class="stat-box">
                <strong><?= $totalCount ?></strong>
                <span>Tổng thông báo</span>
            </div>
            <div class="stat-box">
                <strong><?= $unreadCount ?></strong>
                <span>Chưa đọc</span>
            </div>
            <div class="stat-box">
                <strong><?= $totalCount - $unreadCount ?></strong>
                <span>Đã đọc</span>
            </div>
        </div>

        <h2>Thêm thông báo test:</h2>
        <div class="btn-group">
            <a href="?action=add&type=info" class="btn btn-info">🔔 Thông báo Info</a>
            <a href="?action=add&type=success" class="btn btn-success">✅ Thông báo Success</a>
            <a href="?action=add&type=warning" class="btn btn-warning">⚠️ Thông báo Warning</a>
            <a href="?action=add&type=error" class="btn btn-error">❌ Thông báo Error</a>
        </div>

        <div style="margin-top: 20px;">
            <a href="user/notifications.php" class="btn" style="background: #5DA2D5; color: white;">
                📋 Xem trang Thông báo đầy đủ
            </a>
            <a href="public/index.php" class="btn" style="background: #999; color: white;">
                🏠 Về trang chủ
            </a>
        </div>

        <?php if (!empty($allNotifications)): ?>
            <div class="notification-list">
                <h2>Danh sách thông báo (5 mới nhất):</h2>
                <?php foreach (array_slice($allNotifications, 0, 5) as $notif): ?>
                    <div class="notification-item <?= !$notif['read'] ? 'unread' : '' ?>">
                        <strong>
                            <?php
                            $icons = [
                                'info' => '🔔',
                                'success' => '✅',
                                'warning' => '⚠️',
                                'error' => '❌'
                            ];
                            echo $icons[$notif['type']] ?? '🔔';
                            ?>
                            <?= htmlspecialchars($notif['title']) ?>
                        </strong>
                        <p><?= htmlspecialchars($notif['message']) ?></p>
                        <small>
                            📅 <?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?>
                            <?= !$notif['read'] ? ' • <strong style="color: #5DA2D5;">Chưa đọc</strong>' : '' ?>
                        </small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="info-box" style="background: #fff3cd; border-left-color: #ffc107;">
                <strong>ℹ️ Chưa có thông báo nào</strong><br>
                Hãy click vào các nút bên trên để thêm thông báo test.
            </div>
        <?php endif; ?>
    </div>
</body>
</html>

