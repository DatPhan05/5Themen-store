<?php
// admin/orders.php
include "../include/session.php"; 
include "../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/order_class.php";

$orderModel = new Order();

// Xử lý xóa đơn nếu có ?delete=ID
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if ($delId > 0) {
        $orderModel->delete($delId);
        echo "<script>window.location.href='orders.php';</script>";
        exit;
    }
}

// Lọc theo trạng thái
$statusFilter = $_GET['status'] ?? '';
$list         = $orderModel->getAll($statusFilter);
?>

<style>
    /* ================= LAYOUT CHÍNH (ĐỒNG BỘ) ================= */
    .admin-content-right {
        margin-left: 230px;
        flex: 1; 
        padding: 30px;
        background-color: #f4f5fb; /* Nền xám nhạt cho toàn vùng nội dung */
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ================= CARD CONTAINER (KHUNG TRẮNG) ================= */
    .table-container {
        width: 100%;
        background: #fff;
        border-radius: 20px; /* Bo góc lớn như productedit */
        padding: 30px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1); /* Đổ bóng nhẹ */
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin-bottom: 20px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ================= BỘ LỌC ================= */
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
        outline: none;
        min-width: 200px;
        background: #fff;
    }

    /* ================= BẢNG DỮ LIỆU ================= */
    .data-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    .data-table thead th {
        background-color: #f1f3f5;
        color: #495057;
        font-weight: 700;
        text-align: left;
        padding: 15px;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
    }

    .data-table tbody td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        color: #333;
        vertical-align: middle;
    }

    .data-table tbody tr:hover {
        background-color: #f8f9fa;
    }

    /* CỘT ĐỊA CHỈ */
    .address-col {
        max-width: 250px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #666;
    }

    /* TRẠNG THÁI (BADGES) */
    .badge-status {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        display: inline-block;
    }
    
    /* Màu sắc trạng thái */
    .status-pending    { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-shipping   { background: #d1ecf1; color: #0c5460; }
    .status-success    { background: #d4edda; color: #155724; }
    .status-cancelled  { background: #f8d7da; color: #721c24; }

    /* THANH TOÁN */
    .pay-method {
        font-weight: 700;
        font-size: 12px;
        color: #007bff;
        text-transform: uppercase;
        background: #e3f2fd;
        padding: 4px 8px;
        border-radius: 4px;
    }

    /* NÚT TÙY CHỈNH */
    .order-actions {
        white-space: nowrap;
        text-align: center !important;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 10px;
        margin: 0 3px;
        border-radius: 8px;
        font-size: 13px;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        font-weight: 500;
    }

    .btn-view { background-color: #e3f2fd; color: #0d47a1; }
    .btn-view:hover { background-color: #bbdefb; transform: translateY(-2px); }

    .btn-invoice { background-color: #e8f5e9; color: #1b5e20; }
    .btn-invoice:hover { background-color: #c8e6c9; transform: translateY(-2px); }

    .btn-delete { background-color: #ffebee; color: #b71c1c; }
    .btn-delete:hover { background-color: #ffcdd2; transform: translateY(-2px); }

</style>

<div class="admin-content-right">
    <div class="table-container">

        <h1 class="page-title"><i class="fa-solid fa-list-check"></i> Danh sách đơn hàng</h1>

        <form method="get" class="order-filter-form">
            <label for="status" class="form-label">Trạng thái đơn hàng:</label>
            <select name="status" id="status" class="form-control-filter" onchange="this.form.submit()">
                <option value="">-- Tất cả đơn hàng --</option>
                <option value="pending"    <?= $statusFilter=='pending'?'selected':''; ?>>Chờ duyệt</option>
                <option value="processing" <?= $statusFilter=='processing'?'selected':''; ?>>Đang xử lý</option>
                <option value="shipping"   <?= $statusFilter=='shipping'?'selected':''; ?>>Đang giao hàng</option>
                <option value="success"    <?= $statusFilter=='success'?'selected':''; ?>>Thành công</option>
                <option value="cancelled"  <?= $statusFilter=='cancelled'?'selected':''; ?>>Đã huỷ</option>
            </select>
        </form>

        <div style="overflow-x: auto;">
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
                    <?php 
                    if ($list && $list->num_rows > 0): 
                        while ($o = $list->fetch_assoc()): 
                            // Xử lý hiển thị trạng thái (badge color)
                            // Chuyển về chữ thường để so khớp với class CSS
                            $sttCode = strtolower($o['status']);
                            
                            // Text hiển thị tiếng Việt
                            $statusTextMap = [
                                'pending' => 'Chờ duyệt', 
                                'processing' => 'Đang xử lý', 
                                'shipping' => 'Đang giao', 
                                'success' => 'Hoàn tất', 
                                'cancelled' => 'Đã huỷ'
                            ];
                            // Lấy text hiển thị, nếu không có trong map thì lấy nguyên gốc
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
                                <span class="badge-status status-<?= htmlspecialchars($sttCode); ?>">
                                    <?= htmlspecialchars($statusDisplay); ?>
                                </span>
                            </td>
                            
                            <td style="color:#666;">
                                <?= htmlspecialchars($o['created_at']); ?>
                            </td>
                            
                            <td class="order-actions">
                                <a class="btn-action btn-view" href="order_edit.php?id=<?= (int)$o['order_id']; ?>" title="Xem chi tiết">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a class="btn-action btn-invoice" href="order_invoice.php?id=<?= (int)$o['order_id']; ?>" target="_blank" title="In Hóa Đơn">
                                    <i class="fa-solid fa-print"></i>
                                </a>
                                <a class="btn-action btn-delete" href="orders.php?delete=<?= (int)$o['order_id']; ?>" 
                                   onclick="return confirm('Bạn có chắc muốn xóa đơn hàng #<?= (int)$o['order_id']; ?> ?');" title="Xóa đơn hàng">
                                    <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; else: ?>
                        <tr>
                            <td colspan="9" style="text-align:center; padding: 40px;">
                                <img src="https://cdn-icons-png.flaticon.com/512/4076/4076432.png" alt="No data" style="width: 60px; opacity: 0.5; margin-bottom: 10px;">
                                <p style="color: #999; font-style: italic; margin: 0;">
                                    <?php if($statusFilter): ?>
                                        Không tìm thấy đơn hàng nào có trạng thái "<?= htmlspecialchars($statusFilter) ?>".
                                    <?php else: ?>
                                        Chưa có đơn hàng nào trong hệ thống.
                                    <?php endif; ?>
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>