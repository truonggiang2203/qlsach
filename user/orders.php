<?php
include_once '../includes/header.php';
require_once '../models/Order.php';

if (!isset($_SESSION['id_tk'])) {
    header("Location: ../guest/login.php");
    exit;
}

$orderModel = new Order();
$orders = $orderModel->getOrdersByUser($_SESSION['id_tk']);

// Helper function để lấy đường dẫn hình ảnh
function getBookImagePath($id_sach) {
    $imagePath = "/qlsach/public/uploads/" . $id_sach . ".jpg";
    $fullPath = __DIR__ . "/../public/uploads/" . $id_sach . ".jpg";
    if (file_exists($fullPath)) {
        return $imagePath;
    }
    return "/qlsach/public/uploads/default-book.png";
}

// Helper chuyển dữ liệu đơn hàng sang JSON cho panel
function buildOrderPayload($order, $details, $paymentInfo) {
    $statusConfig = [
        'Chờ xử lý' => ['color' => '#0d6efd', 'icon' => '⏳', 'bg' => '#e7f1ff'],
        'Đã xác nhận' => ['color' => '#0d9488', 'icon' => '✓', 'bg' => '#e0f7fa'],
        'Đang giao hàng' => ['color' => '#f59e0b', 'icon' => '🚚', 'bg' => '#fff7e6'],
        'Đã hoàn thành' => ['color' => '#22c55e', 'icon' => '✅', 'bg' => '#e8f9ef'],
        'Đã hủy' => ['color' => '#ef4444', 'icon' => '❌', 'bg' => '#ffe5e5'],
    ];
    $status = $order->trang_thai_dh;
    $statusStyle = $statusConfig[$status] ?? ['color' => '#4b5563', 'icon' => '•', 'bg' => '#f3f4f6'];

    $items = [];
    foreach ($details as $item) {
        $items[] = [
            'id' => $item->id_sach,
            'title' => $item->ten_sach,
            'quantity' => (int)$item->so_luong_ban,
            'price' => number_format($item->don_gia_ban, 0, ',', '.') . 'đ',
            'total' => number_format($item->thanh_tien, 0, ',', '.') . 'đ',
            'image' => getBookImagePath($item->id_sach),
            'link' => "/qlsach/public/book_detail.php?id_sach=" . $item->id_sach,
        ];
    }

    $paymentMethod = $paymentInfo->ten_pttt ?? 'Thanh toán khi nhận hàng';
    $isPaid = isset($paymentInfo->trang_thai_tt) ? (int)$paymentInfo->trang_thai_tt === 1 : false;
    $paymentTime = !empty($paymentInfo->ngay_gio_thanh_toan ?? null)
        ? date('d/m/Y H:i', strtotime($paymentInfo->ngay_gio_thanh_toan))
        : null;

    return [
        'id' => $order->id_don_hang,
        'created_at' => date('d/m/Y H:i', strtotime($order->ngay_gio_tao_don)),
        'address' => $order->dia_chi_nhan_hang,
        'total' => number_format($order->tong_tien, 0, ',', '.') . 'đ',
        'items' => $items,
        'item_count' => (int)($order->so_san_pham ?? count($items)),
        'status_badge' => [
            'text' => $status,
            'color' => $statusStyle['color'],
            'bg' => $statusStyle['bg'],
            'icon' => $statusStyle['icon'],
        ],
        'payment' => [
            'method' => $paymentMethod,
            'paid' => $isPaid,
            'time' => $paymentTime,
        ],
        'can_cancel' => (int)$order->id_trang_thai === 1,
    ];
}

// Hiển thị thông báo
$message = '';
if (isset($_GET['cancel'])) {
    $message = '<div class="alert-success">✅ Đã hủy đơn hàng thành công!</div>';
}
?>

<link rel="stylesheet" href="/qlsach/public/css/orders.css">

<div class="orders-page">
    <div class="orders-header">
        <h1>📦 Đơn hàng của tôi</h1>
        <p>Quản lý và theo dõi đơn hàng của bạn</p>
    </div>

    <?php if ($message): ?>
        <div style="margin-bottom: 20px; animation: slideDown 0.3s ease-out;">
            <?= $message ?>
        </div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <!-- Empty State -->
        <div class="orders-empty">
            <div class="empty-icon">📦</div>
            <h2>Bạn chưa có đơn hàng nào</h2>
            <p>Hãy khám phá và mua sắm những cuốn sách bạn yêu thích!</p>
            <a href="/qlsach/public/index.php" class="btn-browse-books">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Khám phá sách ngay
            </a>
        </div>
    <?php else: ?>
        <div class="orders-layout">
            <div class="orders-table-wrapper">
                <div class="orders-toolbar">
                    <div>
                        <strong><?= count($orders) ?></strong> đơn hàng
                    </div>
                    <span class="orders-hint">Chọn một dòng để xem chi tiết</span>
                </div>
                <div class="table-responsive">
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): 
                                $details = $orderModel->getOrderDetails($order->id_don_hang);
                                $paymentInfo = $orderModel->getPaymentMethod($order->id_don_hang);
                                $payload = buildOrderPayload($order, $details, $paymentInfo);
                                $paymentStatus = $payload['payment']['paid'] ? 'Đã thanh toán' : 'Chưa thanh toán';
                            ?>
                                <tr class="orders-row" data-order='<?= htmlspecialchars(json_encode($payload, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8') ?>'>
                                    <td>
                                        <div class="order-code">
                                            <strong><?= htmlspecialchars($order->id_don_hang) ?></strong>
                                        </div>
                                    </td>
                                    <td><?= date('d/m/Y H:i', strtotime($order->ngay_gio_tao_don)) ?></td>
                                    <td><?= $payload['item_count'] ?> sản phẩm</td>
                                    <td><?= number_format($order->tong_tien, 0, ',', '.') ?>đ</td>
                                    <td>
                                        <span class="payment-chip <?= $payload['payment']['paid'] ? 'paid' : 'unpaid' ?>">
                                            <?= $paymentStatus ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-pill" style="color: <?= $payload['status_badge']['color'] ?>; background: <?= $payload['status_badge']['bg'] ?>;">
                                            <?= $payload['status_badge']['icon'] ?> <?= htmlspecialchars($payload['status_badge']['text']) ?>
                                        </span>
                                    </td>
                                    <td class="text-right">
                                        <button class="btn-view-order" type="button">Xem</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="order-detail-panel" id="orderDetailPanel">
                <div class="panel-empty" id="orderPanelEmpty">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path>
                    </svg>
                    <p>Chọn một đơn hàng để xem thông tin chi tiết</p>
                </div>

                <div class="panel-content hidden" id="orderPanelContent">
                    <div class="panel-header">
                        <div>
                            <p>Đơn hàng</p>
                            <h3 id="panelOrderId"></h3>
                            <span class="panel-date" id="panelOrderDate"></span>
                        </div>
                        <div class="status-pill" id="panelOrderStatus">
                            <span id="panelStatusIcon"></span>
                            <span id="panelStatusText"></span>
                        </div>
                    </div>

                    <div class="panel-section">
                        <h4>Sản phẩm</h4>
                        <div class="panel-items" id="panelItems"></div>
                    </div>

                    <div class="panel-section two-columns">
                        <div>
                            <h4>Thông tin nhận hàng</h4>
                            <p id="panelAddress"></p>
                        </div>
                        <div>
                            <h4>Thanh toán</h4>
                            <p id="panelPaymentMethod"></p>
                            <span class="payment-status" id="panelPaymentStatus"></span>
                            <p class="payment-time" id="panelPaymentTime"></p>
                        </div>
                    </div>

                    <div class="panel-section panel-total-row">
                        <span>Tổng tiền</span>
                        <strong id="panelTotal"></strong>
                    </div>

                    <div class="panel-actions">
                        <a href="#" class="btn-cancel-order hidden" id="panelCancelBtn"
                           onclick="return confirm('Bạn có chắc muốn hủy đơn hàng này không?');">
                            Hủy đơn hàng
                        </a>
                    </div>
                </div>
            </aside>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('.orders-table tbody tr');
    if (!rows.length) {
        return;
    }

    const panelEmpty = document.getElementById('orderPanelEmpty');
    const panelContent = document.getElementById('orderPanelContent');
    const panelOrderId = document.getElementById('panelOrderId');
    const panelOrderDate = document.getElementById('panelOrderDate');
    const panelOrderStatus = document.getElementById('panelOrderStatus');
    const panelStatusIcon = document.getElementById('panelStatusIcon');
    const panelStatusText = document.getElementById('panelStatusText');
    const panelItems = document.getElementById('panelItems');
    const panelAddress = document.getElementById('panelAddress');
    const panelTotal = document.getElementById('panelTotal');
    const panelPaymentMethod = document.getElementById('panelPaymentMethod');
    const panelPaymentStatus = document.getElementById('panelPaymentStatus');
    const panelPaymentTime = document.getElementById('panelPaymentTime');
    const panelCancelBtn = document.getElementById('panelCancelBtn');
    const detailPanel = document.getElementById('orderDetailPanel');
    let activeRow = null;

    function renderPanel(data) {
        panelEmpty.classList.add('hidden');
        panelContent.classList.remove('hidden');

        panelOrderId.textContent = data.id;
        panelOrderDate.textContent = data.created_at;
        panelStatusIcon.textContent = data.status_badge.icon;
        panelStatusText.textContent = data.status_badge.text;
        panelOrderStatus.style.background = data.status_badge.bg;
        panelOrderStatus.style.color = data.status_badge.color;

        panelAddress.textContent = data.address;
        panelTotal.textContent = data.total;

        panelItems.innerHTML = '';
        data.items.forEach(function(item) {
            const div = document.createElement('div');
            div.className = 'panel-item';
            div.innerHTML = `
                <div class="panel-item-image">
                    <img src="${item.image}" alt="${item.title}">
                </div>
                <div class="panel-item-info">
                    <a href="${item.link}" target="_blank">${item.title}</a>
                    <div class="panel-item-meta">
                        <span>Số lượng: <strong>${item.quantity}</strong></span>
                        <span>Giá: <strong>${item.price}</strong></span>
                    </div>
                </div>
                <div class="panel-item-total">${item.total}</div>
            `;
            panelItems.appendChild(div);
        });

        panelPaymentMethod.textContent = data.payment.method;
        panelPaymentStatus.textContent = data.payment.paid ? 'Đã thanh toán' : 'Chưa thanh toán';
        panelPaymentStatus.classList.toggle('paid', data.payment.paid);
        panelPaymentStatus.classList.toggle('unpaid', !data.payment.paid);
        panelPaymentTime.textContent = data.payment.time ? `Thanh toán lúc: ${data.payment.time}` : '';

        if (data.can_cancel) {
            panelCancelBtn.classList.remove('hidden');
            panelCancelBtn.href = `/qlsach/controllers/orderController.php?action=cancel&id_don_hang=${encodeURIComponent(data.id)}`;
        } else {
            panelCancelBtn.classList.add('hidden');
        }
    }

    function handleSelectRow(row) {
        const payload = row.dataset.order ? JSON.parse(row.dataset.order) : null;
        if (!payload) return;

        if (activeRow) {
            activeRow.classList.remove('active');
        }
        row.classList.add('active');
        activeRow = row;
        renderPanel(payload);
    }

    rows.forEach(function(row) {
        row.addEventListener('click', function() {
            handleSelectRow(row);
        });
        const viewBtn = row.querySelector('.btn-view-order');
        if (viewBtn) {
            viewBtn.addEventListener('click', function(event) {
                event.stopPropagation();
                handleSelectRow(row);
                detailPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        }
    });

    // Tự chọn đơn hàng đầu tiên
    handleSelectRow(rows[0]);
});
</script>

<?php include_once '../includes/footer.php'; ?>
