<?php
// admin/orders.php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/order_class.php";

$orderModel = new Order();

// Xử lý xóa đơn nếu có ?delete=ID
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($delId > 0) {
        $orderModel->delete($delId);
    }
    header("Location: orders.php");
    exit;
}

// Lọc theo trạng thái (optional)
$statusFilter = $_GET['status'] ?? '';
$list         = $orderModel->getAll($statusFilter);
?>

<div class="admin-content-right">
    <div class="admin-content-right-category_list">

        <h1>Danh sách đơn hàng</h1>

        <!-- Bộ lọc trạng thái -->
        <form method="get" class="order-filter-form">
            <label for="status">Lọc theo trạng thái: </label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="">-- Tất cả --</option>
                <option value="pending"    <?= $statusFilter=='pending'?'selected':''; ?>>Chờ duyệt</option>
                <option value="processing" <?= $statusFilter=='processing'?'selected':''; ?>>Đang xử lý</option>
                <option value="shipping"   <?= $statusFilter=='shipping'?'selected':''; ?>>Đang giao</option>
                <option value="success"    <?= $statusFilter=='success'?'selected':''; ?>>Hoàn tất</option>
                <option value="cancelled"  <?= $statusFilter=='cancelled'?'selected':''; ?>>Đã huỷ</option>
            </select>
        </form>

        <div class="card order-list-card">
            <table class="order-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Khách hàng</th>
                        <th>SĐT</th>
                        <th>Địa chỉ</th>
                        <th>Tổng tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th style="text-align:center;">Tùy chỉnh</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($list && $list->num_rows > 0): ?>
                    <?php while ($o = $list->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= (int)$o['order_id']; ?></td>
                            <td><?= htmlspecialchars($o['fullname']); ?></td>
                            <td><?= htmlspecialchars($o['phone']); ?></td>
                            <td class="order-address-col">
                                <?= htmlspecialchars($o['address']); ?>
                            </td>
                            <td><?= number_format($o['total_price'], 0, ',', '.'); ?>đ</td>
                            <td>
                                <span class="pay-method"><?= strtoupper(htmlspecialchars($o['payment_method'])); ?></span>
                            </td>
                            <td>
                                <span class="order-status badge-<?= htmlspecialchars($o['status']); ?>">
                                    <?= htmlspecialchars($o['status']); ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($o['created_at']); ?></td>
                            <td class="order-actions">
                                <a class="btn-action btn-view"
                                   href="order_edit.php?id=<?= (int)$o['order_id']; ?>">
                                   Xem / Sửa
                                </a>
                                <a class="btn-action btn-invoice"
                                   href="order_invoice.php?id=<?= (int)$o['order_id']; ?>"
                                   target="_blank">
                                   Hóa đơn
                                </a>
                                <a class="btn-action btn-delete"
                                   href="orders.php?delete=<?= (int)$o['order_id']; ?>"
                                   onclick="return confirm('Xóa đơn #<?= (int)$o['order_id']; ?> ?');">
                                   Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center;">Chưa có đơn hàng nào.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

<style>
    .order-filter-form {
        margin-bottom: 15px;
        font-size: 14px;
    }
    .order-filter-form select {
        padding: 6px 10px;
        border-radius: 4px;
        border: 1px solid #ddd;
        min-width: 180px;
    }

    .order-list-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        padding: 16px;
    }

    .order-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }
    .order-table thead {
        background: #f7f7fb;
    }
    .order-table th,
    .order-table td {
        padding: 10px 12px;
        border-bottom: 1px solid #eee;
    }
    .order-table tbody tr:hover {
        background: #fafafa;
    }
    .order-address-col {
        max-width: 220px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .pay-method {
        font-weight: 600;
        font-size: 12px;
    }

    .order-status {
        padding: 3px 8px;
        border-radius: 999px;
        font-size: 12px;
        text-transform: capitalize;
        display: inline-block;
    }
    .badge-pending {
        background:#fff3cd;
        color:#856404;
    }
    .badge-processing {
        background:#cce5ff;
        color:#004085;
    }
    .badge-shipping {
        background:#d1ecf1;
        color:#0c5460;
    }
    .badge-success {
        background:#d4edda;
        color:#155724;
    }
    .badge-cancelled {
        background:#f8d7da;
        color:#721c24;
    }

    .order-actions {
        text-align: center;
        white-space: nowrap;
    }
    .btn-action {
        display: inline-block;
        padding: 4px 8px;
        margin: 2px 2px;
        border-radius: 6px;
        font-size: 12px;
        text-decoration: none;
        border: 1px solid transparent;
        transition: all .2s;
    }
    .btn-view {
        background:#edf2ff;
        color:#283593;
        border-color:#c5cae9;
    }
    .btn-invoice {
        background:#e8f5e9;
        color:#1b5e20;
        border-color:#c8e6c9;
    }
    .btn-delete {
        background:#ffebee;
        color:#b71c1c;
        border-color:#ffcdd2;
    }
    .btn-action:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    }
</style>
