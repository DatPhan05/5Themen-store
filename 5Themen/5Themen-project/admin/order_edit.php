<?php
// admin/order_edit.php

// Bổ sung các file cần thiết
include __DIR__ . "/../include/session.php"; 
include __DIR__ . "/../include/database.php"; 

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/order_class.php";

$orderModel = new Order();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    die("<div class='admin-content-right' style='padding: 20px;'><p>❌ Lỗi: Thiếu ID đơn hàng.</p></div>");
}

// Lấy thông tin đơn hàng và chi tiết sản phẩm (sử dụng getOrderWithTotal để tính tổng lại)
list($order, $items) = $orderModel->getOrderWithTotal($orderId);

if (!$order) {
    die("<div class='admin-content-right' style='padding: 20px;'><p>❌ Lỗi: Đơn hàng không tồn tại.</p></div>");
}

$msg = "";
$msg_type = "";
// Cập nhật trạng thái khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'] ?? 'pending';
    
    // Đảm bảo trạng thái mới khác trạng thái cũ
    if (strtolower($newStatus) !== strtolower($order['status'])) {
        if ($orderModel->updateStatus($orderId, $newStatus)) {
            $msg = "✅ Cập nhật trạng thái đơn hàng thành công!";
            $msg_type = "success";
            // Cập nhật lại biến $order sau khi update
            list($order, $items) = $orderModel->getOrderWithTotal($orderId); 
        } else {
            $msg = "❌ Lỗi: Cập nhật trạng thái thất bại.";
            $msg_type = "error";
        }
    }
    
    // Dù update thành công hay thất bại, chuyển hướng để tránh submit lại form
    echo "<script>
        setTimeout(function() {
            window.location.href='order_edit.php?id=" . $orderId . "';
        }, 1500); // Chờ 1.5s để người dùng đọc thông báo
    </script>";
    // Không dùng header() vì muốn hiển thị thông báo JS
}

// Danh sách trạng thái
$statusList = [
    'pending'    => 'Chờ duyệt',
    'processing' => 'Đang xử lý',
    'shipping'   => 'Đang giao hàng',
    'success'    => 'Hoàn tất',
    'cancelled'  => 'Đã huỷ'
];
$currentStatusCode = strtolower($order['status']);
$currentStatusText = $statusList[$currentStatusCode] ?? $order['status'];
$totalPriceDisplay = number_format((float)($order['total_price'] ?? 0), 0, ',', '.');
$calcTotalDisplay  = number_format((float)($order['calc_total'] ?? 0), 0, ',', '.');

?>

<style>
    /* ================= LAYOUT CHÍNH (ĐỒNG BỘ VỚI orders.php) ================= */
    .admin-content-right {
        margin-left: 230px;
        flex: 1; 
        padding: 30px;
        background-color: #f4f5fb;
        min-height: 100vh;
    }

    .table-container {
        width: 100%;
        background: #fff;
        border-radius: 20px;
        padding: 30px;
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
        margin-bottom: 25px; /* Thêm khoảng cách cho các phần */
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin-bottom: 25px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }

    /* ================= THÔNG BÁO CHUNG ================= */
    .message {
        padding: 12px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    .message.success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .message.error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    /* ================= FORM CẬP NHẬT TRẠNG THÁI ================= */
    .status-update-form {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 30px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
    }
    .form-label-status {
        font-weight: 700;
        color: #333;
        white-space: nowrap;
    }
    .form-control-status {
        padding: 10px 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        min-width: 200px;
    }
    .btn-submit {
        background-color: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .btn-submit:hover {
        background-color: #0056b3;
    }
    .current-status-badge {
        font-size: 14px;
        margin-right: 15px;
    }
    /* Màu sắc trạng thái */
    .badge-status {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
        display: inline-block;
    }
    .status-pending    { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-shipping   { background: #d1ecf1; color: #0c5460; }
    .status-success    { background: #d4edda; color: #155724; }
    .status-cancelled  { background: #f8d7da; color: #721c24; }


    /* ================= THÔNG TIN CHI TIẾT ĐƠN HÀNG ================= */
    .order-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }
    .detail-card {
        padding: 20px;
        border: 1px solid #eee;
        border-radius: 12px;
        background: #fafafa;
    }
    .detail-card h3 {
        font-size: 18px;
        color: #007bff;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #e9ecef;
    }
    .detail-item {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px dashed #e9ecef;
        font-size: 14px;
    }
    .detail-item:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-weight: 600;
        color: #555;
    }
    .detail-value {
        color: #333;
        text-align: right;
        max-width: 60%;
    }
    .total-price-row {
        background-color: #e3f2fd;
        margin-top: 15px;
        padding: 10px;
        border-radius: 8px;
        font-size: 16px;
        font-weight: 700;
    }
    .total-price-value {
        color: #d63031;
    }

    /* ================= BẢNG SẢN PHẨM ================= */
    .data-table-items {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        margin-top: 20px;
    }
    .data-table-items thead th {
        background-color: #f1f3f5;
        color: #495057;
        font-weight: 700;
        text-align: left;
        padding: 12px 15px;
        border-bottom: 2px solid #dee2e6;
    }
    .data-table-items tbody td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
        vertical-align: middle;
    }
    .data-table-items tfoot td {
        padding: 15px;
        background-color: #f8f9fa;
        font-weight: 700;
    }

    /* Các nút hành động khác */
    .action-buttons {
        margin-top: 20px;
        display: flex;
        gap: 15px;
        justify-content: flex-end;
    }
    .btn-action-lg {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.2s;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-action-lg.primary {
        background-color: #28a745;
        color: #fff;
    }
    .btn-action-lg.primary:hover {
        background-color: #1e7e34;
    }
    .btn-action-lg.secondary {
        background-color: #6c757d;
        color: #fff;
    }
    .btn-action-lg.secondary:hover {
        background-color: #5a6268;
    }
    .btn-action-lg.invoice {
        background-color: #007bff;
        color: #fff;
    }
    .btn-action-lg.invoice:hover {
        background-color: #0056b3;
    }


    /* ================= MODAL HÓA ĐƠN ================= */
    .invoice-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 1000;
        display: none;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }
    .invoice-overlay.show {
        display: flex;
        opacity: 1;
    }
    .invoice-overlay-inner {
        position: relative;
        width: 95%;
        max-width: 900px;
        height: 90%;
        background: white;
        border-radius: 15px;
        overflow: hidden;
    }
    .btn-close {
        position: absolute;
        top: 10px;
        right: 15px;
        background: #dc3545;
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        z-index: 1001;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }
    .btn-close:hover {
        background: #c82333;
    }
    #invoice-frame {
        width: 100%;
        height: 100%;
        border: none;
    }
    
    @media (max-width: 900px) {
        .order-detail-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="admin-content-right">
    <div class="table-container">

        <h1 class="page-title">
            Chi tiết Đơn hàng #<?= (int)$orderId; ?> 
            <span class="badge-status current-status-badge status-<?= $currentStatusCode; ?>">
                <?= $currentStatusText; ?>
            </span>
        </h1>

        <?php if (!empty($msg)): ?>
            <div class="message <?= $msg_type; ?>"><?= $msg; ?></div>
        <?php endif; ?>
        
        <form method="post" class="status-update-form">
            <label for="status" class="form-label-status">Cập nhật Trạng thái:</label>
            <select name="status" id="status" class="form-control-status" required>
                <?php foreach ($statusList as $code => $text): ?>
                    <option 
                        value="<?= $code; ?>" 
                        <?= $currentStatusCode === $code ? 'selected' : ''; ?>
                    >
                        <?= $text; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-submit">
                <i class="fa-solid fa-floppy-disk"></i> Lưu
            </button>
        </form>

        <div class="order-detail-grid">
            
            <div class="detail-card">
                <h3><i class="fa-solid fa-user"></i> Thông tin Khách hàng</h3>
                <div class="detail-item">
                    <span class="detail-label">Tên khách hàng:</span>
                    <span class="detail-value"><?= htmlspecialchars($order['fullname'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Số điện thoại:</span>
                    <span class="detail-value"><?= htmlspecialchars($order['phone'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?= htmlspecialchars($order['email'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Địa chỉ giao hàng:</span>
                    <span class="detail-value" style="font-style:italic;">
                        <?= htmlspecialchars($order['address'] ?? 'N/A'); ?>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Ghi chú:</span>
                    <span class="detail-value" style="font-style:italic;">
                        <?= htmlspecialchars($order['note'] ?? 'Không có'); ?>
                    </span>
                </div>
            </div>

            <div class="detail-card">
                <h3><i class="fa-solid fa-file-invoice"></i> Tóm tắt Đơn hàng</h3>
                <div class="detail-item">
                    <span class="detail-label">ID Đơn hàng:</span>
                    <span class="detail-value">#<?= (int)$order['order_id']; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Ngày đặt:</span>
                    <span class="detail-value"><?= htmlspecialchars($order['created_at'] ?? 'N/A'); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Tổng tiền (từ bảng order):</span>
                    <span class="detail-value"><?= $totalPriceDisplay; ?>đ</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Phương thức TT:</span>
                    <span class="detail-value" style="font-weight: 700; color: #007bff;">
                        <?= strtoupper(htmlspecialchars($order['payment_method'] ?? 'N/A')); ?>
                    </span>
                </div>
                <div class="detail-item total-price-row">
                    <span class="detail-label">Tổng tiền chi tiết:</span>
                    <span class="detail-value total-price-value"><?= $calcTotalDisplay; ?>đ</span>
                </div>
            </div>
        </div>

        <h2 style="font-size: 20px; margin-top: 20px; margin-bottom: 15px; color: #333;">
            <i class="fa-solid fa-box-open"></i> Danh sách Sản phẩm
        </h2>
        
        <div style="overflow-x: auto;">
            <table class="data-table-items">
                <thead>
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th>Tên sản phẩm</th>
                        <th style="width: 100px;">Size</th>
                        <th style="width: 100px;">Màu</th>
                        <th style="width: 120px;">Giá (đ)</th>
                        <th style="width: 80px;">SL</th>
                        <th style="width: 150px;">Thành tiền (đ)</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                $hasItems = false;
                if ($items && $items->num_rows > 0):
                    $hasItems = true;
                    while ($row = $items->fetch_assoc()):
                        $sub = (float)($row['price'] ?? 0) * (int)($row['qty'] ?? 0);
                ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= htmlspecialchars($row['product_name'] ?? 'Sản phẩm đã bị xóa'); ?></td>
                        <td><?= htmlspecialchars($row['size'] ?? 'N/A'); ?></td>
                        <td><?= htmlspecialchars($row['color'] ?? 'N/A'); ?></td>
                        <td><?= number_format((float)($row['price'] ?? 0), 0, ',', '.'); ?></td>
                        <td><?= (int)($row['qty'] ?? 0); ?></td>
                        <td style="font-weight: 600; color: #d63031;"><?= number_format($sub, 0, ',', '.'); ?></td>
                    </tr>
                <?php
                    endwhile;
                endif;
                
                if (!$hasItems):
                ?>
                    <tr><td colspan="7" style="text-align:center; padding: 20px; color: #999;">Đơn hàng không có sản phẩm nào.</td></tr>
                <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right;">Tổng giá trị đơn hàng:</td>
                        <td style="color: #d63031;"><?= $calcTotalDisplay; ?>đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="action-buttons">
            <a href="orders.php" class="btn-action-lg secondary">
                <i class="fa-solid fa-arrow-left"></i> Quay lại Danh sách
            </a>
            <button type="button" class="btn-action-lg invoice" id="btnShowInvoice">
                <i class="fa-solid fa-print"></i> Xem Hóa đơn (In)
            </button>
        </div>

    </div>
</div>

<div class="invoice-overlay" id="invoice-overlay">
    <div class="invoice-overlay-inner">
        <button type="button" class="btn-close" id="btnCloseInvoice">×</button>
        <iframe id="invoice-frame" src="" frameborder="0"></iframe>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnShow = document.getElementById('btnShowInvoice');
        const overlay = document.getElementById('invoice-overlay');
        const frame   = document.getElementById('invoice-frame');
        const btnClose = document.getElementById('btnCloseInvoice');

        if (btnShow && overlay && frame && btnClose) {
            // Khi bấm "Hóa đơn" -> mở overlay + load iframe
            btnShow.addEventListener('click', function() {
                // Đảm bảo load đúng ID đơn hàng
                frame.src = 'order_invoice.php?id=<?= (int)$orderId; ?>'; 
                overlay.classList.add('show');
            });

            // Đóng overlay
            btnClose.addEventListener('click', function() {
                overlay.classList.remove('show');
            });

            // Bấm ra nền tối cũng đóng
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.classList.remove('show');
                }
            });
        }
    });
</script>   