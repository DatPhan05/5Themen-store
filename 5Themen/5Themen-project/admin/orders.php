<?php
include "../include/session.php"; 
include "../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/order_class.php";

$orderModel = new Order();

/* -------------------------------
   XÓA ĐƠN HÀNG (nếu có delete=ID)
--------------------------------*/
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($delId > 0) {
        $orderModel->delete($delId);
        echo "<script>window.location.href='orders.php';</script>";
        exit;
    }
}

/* -------------------------------
   LỌC THEO TRẠNG THÁI
--------------------------------*/
$statusFilter = $_GET['status'] ?? '';
$list         = $orderModel->getAll($statusFilter);
?>

<style>
    .admin-content-right {
        margin-left: 230px;
        flex: 1;
        padding: 30px;
        background-color: #f4f5fb;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .table-container {
        width: 100%;
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 20px;
        color: #333;
        text-transform: uppercase;
    }

    /* Bộ lọc trạng thái */
    .order-filter-form {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 25px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }

    .form-label {
        font-weight: 600;
        color: #555;
        margin: 0;
    }

    .form-control-filter {
        padding: 8px 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        min-width: 200px;
        background: #fff;
    }

    /* Bảng danh sách */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .data-table thead th {
        background: #f1f3f5;
        color: #495057;
        padding: 15px;
        font-weight: 700;
        white-space: nowrap;
        border-bottom: 2px solid #dee2e6;
    }

    .data-table tbody td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        color: #333;
        vertical-align: middle;
    }

    .data-table tbody tr:hover {
        background: #f8f9fa;
    }

    .address-col {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #666;
    }

    /* Badge trạng thái */
    .badge-status {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        display: inline-block;
    }

    .status-pending    { background:#fff3cd; color:#856404; }
    .status-processing { background:#cce5ff; color:#004085; }
    .status-shipping   { background:#d1ecf1; color:#0c5460; }
    .status-success    { background:#d4edda; color:#155724; }
    .status-cancelled  { background:#f8d7da; color:#721c24; }

    .pay-method {
        padding: 4px 8px;
        font-size: 12px;
        font-weight: 700;
        color:#007bff;
        background:#e3f2fd;
        text-transform: uppercase;
        border-radius: 4px;
    }

    /* Nút hành động */
    .order-actions { white-space: nowrap; }

    .btn-action {
        display: inline-flex;
        align-items: center;
        padding: 6px 10px;
        margin: 0 3px;
        font-size: 13px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 500;
        transition: 0.2s;
        border: 1px solid transparent;
    }

    .btn-view {
        background:#e3f2fd; color:#0d47a1;
    }
    .btn-view:hover { background:#bbdefb; transform: translateY(-2px); }

    .btn-invoice {
        background:#e8f5e9; color:#1b5e20;
    }
    .btn-invoice:hover { background:#c8e6c9; transform: translateY(-2px); }

    .btn-delete {
        background:#ffebee; color:#b71c1c;
    }
    .btn-delete:hover { background:#ffcdd2; transform: translateY(-2px); }
</style>

<div class="admin-content-right">

    <div class="table-container">

        <h1 class="page-title"><i class="fa-solid fa-list-check"></i> Danh sách đơn hàng</h1>

        <!-- Form lọc trạng thái -->
        <form method="get" class="order-filter-form">
            <label class="form-label">Trạng thái đơn hàng:</label>
            <select name="status" class="form-control-filter" onchange="this.form.submit()">
                <option value="">-- Tất cả đơn hàng --</option>
                <option value="pending"    <?= $statusFilter=='pending'?'selected':''; ?>>Chờ duyệt</option>
                <option value="processing" <?= $statusFilter=='processing'?'selected':''; ?>>Đang xử lý</option>
                <option value="shipping"   <?= $statusFilter=='shipping'?'selected':''; ?>>Đang giao hàng</option>
                <option value="success"    <?= $statusFilter=='success'?'selected':''; ?>>Thành công</option>
                <option value="cancelled"  <?= $statusFilter=='cancelled'?'selected':''; ?>>Đã huỷ</option>
            </select>
        </form>

        <div style="overflow-x:auto;">
            <table class="data-table">
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
                    <th class="order-actions">Tùy chỉnh</th>
                </tr>
                </thead>

                <tbody>
                <?php if ($list && $list->num_rows > 0): ?>

                    <?php 
                    // Map trạng thái -> text hiển thị
                    $statusTextMap = [
                        'pending' => 'Chờ duyệt',
                        'processing' => 'Đang xử lý',
                        'shipping' => 'Đang giao',
                        'success' => 'Hoàn tất',
                        'cancelled' => 'Đã huỷ'
                    ];

                    while ($o = $list->fetch_assoc()):
                        $sttCode = strtolower($o['status']);
                        $statusDisplay = $statusTextMap[$sttCode] ?? $o['status'];
                    ?>
                    <tr>
                        <td><strong>#<?= (int)$o['order_id']; ?></strong></td>
                        <td><?= htmlspecialchars($o['fullname']); ?></td>
                        <td><?= htmlspecialchars($o['phone']); ?></td>

                        <td class="address-col" title="<?= htmlspecialchars($o['address']); ?>">
                            <?= htmlspecialchars($o['address']); ?>
                        </td>

                        <td style="font-weight:700; color:#d63031;">
                            <?= number_format($o['total_price'], 0, ',', '.'); ?>đ
                        </td>

                        <td>
                            <span class="pay-method"><?= strtoupper(htmlspecialchars($o['payment_method'])); ?></span>
                        </td>

                        <td>
                            <span class="badge-status status-<?= $sttCode; ?>">
                                <?= htmlspecialchars($statusDisplay); ?>
                            </span>
                        </td>

                        <td style="color:#666;">
                            <?= htmlspecialchars($o['created_at']); ?>
                        </td>

                        <td class="order-actions">
                            <a class="btn-action btn-view" 
                               href="order_edit.php?id=<?= (int)$o['order_id']; ?>" 
                               title="Xem chi tiết">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>

                            <a class="btn-action btn-invoice"
                               href="order_invoice.php?id=<?= (int)$o['order_id']; ?>" 
                               target="_blank"
                               title="Xem hóa đơn">
                                <i class="fa-solid fa-print"></i>
                            </a>

                            <a class="btn-action btn-delete" 
                               href="orders.php?delete=<?= (int)$o['order_id']; ?>"
                               onclick="return confirm('Bạn có chắc muốn xóa đơn hàng #<?= (int)$o['order_id']; ?> ?');"
                               title="Xóa đơn">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="9" style="text-align:center; padding:40px;">
                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png"
                                 style="width:60px; opacity:0.5; margin-bottom:10px;">
                            <p style="color:#999; font-style:italic;">
                                <?= $statusFilter 
                                    ? "Không tìm thấy đơn hàng nào có trạng thái “".htmlspecialchars($statusFilter)."”." 
                                    : "Chưa có đơn hàng nào trong hệ thống." ?>
                            </p>
                        </td>
                    </tr>
                <?php endif; ?>
                </tbody>

            </table>
        </div>
    </div>
</div>
