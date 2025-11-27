<?php
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

// Lấy thông tin đơn hàng + sản phẩm + tổng tiền tính lại
list($order, $items) = $orderModel->getOrderWithTotal($orderId);

if (!$order) {
    die("<div class='admin-content-right' style='padding: 20px;'><p>❌ Lỗi: Đơn hàng không tồn tại.</p></div>");
}

$msg = "";
$msg_type = "";

// Xử lý cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $newStatus = $_POST['status'] ?? 'pending';

    // Chỉ update nếu trạng thái mới khác trạng thái hiện tại
    if (strtolower($newStatus) !== strtolower($order['status'])) {
        if ($orderModel->updateStatus($orderId, $newStatus)) {
            $msg = "✅ Cập nhật trạng thái đơn hàng thành công!";
            $msg_type = "success";
            list($order, $items) = $orderModel->getOrderWithTotal($orderId);
        } else {
            $msg = "❌ Lỗi: Cập nhật trạng thái thất bại.";
            $msg_type = "error";
        }
    }

    // Redirect bằng JavaScript để tránh submit lại form
    echo "<script>
            setTimeout(function() {
                window.location.href='order_edit.php?id={$orderId}';
            }, 1500);
          </script>";
}

// Nhãn trạng thái hiển thị
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
    .admin-content-right {
        margin-left: 230px;
        flex: 1;
        padding: 30px;
        background-color: #f4f5fb;
        min-height: 100vh;
    }

    .table-container {
        background: #fff;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.1);
        margin-bottom: 25px;
    }

    .page-title {
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 25px;
        border-bottom: 1px solid #eee;
        padding-bottom: 15px;
    }

    .message {
        padding: 12px 20px;
        border-radius: 10px;
        margin-bottom: 20px;
        font-weight: 600;
        font-size: 14px;
    }
    .message.success { background: #d4edda; color: #155724; }
    .message.error   { background: #f8d7da; color: #721c24; }

    .status-update-form {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 30px;
    }
    .form-control-status {
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid #ccc;
    }
    .btn-submit {
        background: #007bff;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 30px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-pending    { background: #fff3cd; color: #856404; }
    .status-processing { background: #cce5ff; color: #004085; }
    .status-shipping   { background: #d1ecf1; color: #0c5460; }
    .status-success    { background: #d4edda; color: #155724; }
    .status-cancelled  { background: #f8d7da; color: #721c24; }

    .order-detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .detail-card {
        padding: 20px;
        background: #fafafa;
        border-radius: 12px;
        border: 1px solid #eee;
    }
    .detail-card h3 {
        font-size: 18px;
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

    table.data-table-items {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
        font-size: 14px;
    }
    table.data-table-items th {
        background: #f1f3f5;
        font-weight: 700;
        padding: 12px 15px;
    }
    table.data-table-items td {
        padding: 12px 15px;
        border-bottom: 1px solid #eee;
    }

    .action-buttons {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 25px;
    }

    .btn-action-lg {
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-action-lg.secondary { background: #6c757d; color: #fff; }
    .btn-action-lg.invoice   { background: #007bff; color: #fff; }

    .invoice-overlay {
        position: fixed;
        top: 0; left: 0;
        width: 100%; height: 100%;
        background: rgba(0,0,0,0.7);
        display: none;
        justify-content: center;
        align-items: center;
        opacity: 0;
        transition: 0.3s;
    }
    .invoice-overlay.show {
        display: flex;
        opacity: 1;
    }

    #invoice-frame {
        width: 95%;
        height: 90%;
        border: none;
        background: white;
        border-radius: 15px;
    }

    .btn-close {
        position: absolute;
        top: 10px; right: 15px;
        background: #dc3545;
        color: white;
        border-radius: 50%;
        width: 30px; height: 30px;
        cursor: pointer;
    }

</style>

<div class="admin-content-right">
    <div class="table-container">

        <h1 class="page-title">
            Chi tiết Đơn hàng #<?= $orderId; ?>
            <span class="badge-status status-<?= $currentStatusCode; ?>">
                <?= $currentStatusText; ?>
            </span>
        </h1>

        <?php if (!empty($msg)): ?>
            <div class="message <?= $msg_type; ?>"><?= $msg; ?></div>
        <?php endif; ?>

        <!-- Form cập nhật trạng thái -->
        <form method="post" class="status-update-form">
            <label for="status">Trạng thái:</label>
            <select name="status" id="status" class="form-control-status">
                <?php foreach ($statusList as $code => $text): ?>
                    <option value="<?= $code; ?>" <?= $currentStatusCode === $code ? 'selected' : ''; ?>>
                        <?= $text; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="btn-submit"><i class="fa-solid fa-floppy-disk"></i> Lưu</button>
        </form>

        <!-- Thông tin khách hàng + tóm tắt đơn -->
        <div class="order-detail-grid">

            <div class="detail-card">
                <h3>Thông tin Khách hàng</h3>
                <div class="detail-item"><span>Tên:</span><span><?= htmlspecialchars($order['fullname']); ?></span></div>
                <div class="detail-item"><span>SĐT:</span><span><?= htmlspecialchars($order['phone']); ?></span></div>
                <div class="detail-item"><span>Email:</span><span><?= htmlspecialchars($order['email']); ?></span></div>
                <div class="detail-item"><span>Địa chỉ:</span><span><?= htmlspecialchars($order['address']); ?></span></div>
                <div class="detail-item"><span>Ghi chú:</span><span><?= htmlspecialchars($order['note'] ?? ''); ?></span></div>
            </div>

            <div class="detail-card">
                <h3>Tóm tắt Đơn hàng</h3>
                <div class="detail-item"><span>ID đơn:</span><span>#<?= $order['order_id']; ?></span></div>
                <div class="detail-item"><span>Ngày đặt:</span><span><?= htmlspecialchars($order['created_at']); ?></span></div>
                <div class="detail-item"><span>Tổng tiền:</span><span><?= $totalPriceDisplay; ?>đ</span></div>
                <div class="detail-item"><span>Phương thức TT:</span><span><?= strtoupper($order['payment_method']); ?></span></div>
                <div class="detail-item" style="font-weight:700;"><span>Tổng tiền tính lại:</span><span><?= $calcTotalDisplay; ?>đ</span></div>
            </div>

        </div>

        <h2 style="margin: 20px 0;">Danh sách Sản phẩm</h2>

        <div style="overflow-x: auto;">
            <table class="data-table-items">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên SP</th>
                        <th>Size</th>
                        <th>Màu</th>
                        <th>Giá</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $i = 1;
                    if ($items && $items->num_rows > 0):
                        while ($row = $items->fetch_assoc()):
                            $sub = (float)$row['price'] * (int)$row['qty'];
                    ?>
                    <tr>
                        <td><?= $i++; ?></td>
                        <td><?= htmlspecialchars($row['product_name']); ?></td>
                        <td><?= htmlspecialchars($row['size']); ?></td>
                        <td><?= htmlspecialchars($row['color']); ?></td>
                        <td><?= number_format($row['price'], 0, ',', '.'); ?>đ</td>
                        <td><?= $row['qty']; ?></td>
                        <td style="font-weight:700;color:#d63031;"><?= number_format($sub, 0, ',', '.'); ?>đ</td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="7" style="text-align:center;padding:20px;color:#999;">Không có sản phẩm.</td></tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right;">Tổng:</td>
                        <td style="color:#d63031;font-weight:bold;"><?= $calcTotalDisplay; ?>đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="action-buttons">
            <a href="orders.php" class="btn-action-lg secondary"><i class="fa-solid fa-arrow-left"></i> Quay lại</a>
            <button type="button" class="btn-action-lg invoice" id="btnShowInvoice">
                <i class="fa-solid fa-print"></i> Xem Hóa đơn
            </button>
        </div>

    </div>
</div>

<div class="invoice-overlay" id="invoice-overlay">
    <div style="position: relative; width: 95%; max-width: 900px; height: 90%;">
        <button class="btn-close" id="btnCloseInvoice">×</button>
        <iframe id="invoice-frame"></iframe>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const overlay = document.getElementById('invoice-overlay');
    const frame   = document.getElementById('invoice-frame');

    document.getElementById('btnShowInvoice').onclick = () => {
        frame.src = 'order_invoice.php?id=<?= $orderId; ?>';
        overlay.classList.add('show');
    };

    document.getElementById('btnCloseInvoice').onclick = () => {
        overlay.classList.remove('show');
    };

    overlay.onclick = (e) => {
        if (e.target === overlay) overlay.classList.remove('show');
    };
});
</script>
