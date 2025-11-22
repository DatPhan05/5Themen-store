<?php
// admin/order_edit.php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/order_class.php";

$orderModel = new Order();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    echo "<div class='admin-content-right'><p>Thiếu ID đơn hàng.</p></div>";
    exit;
}

$order = $orderModel->getById($orderId);
if (!$order) {
    echo "<div class='admin-content-right'><p>Đơn hàng không tồn tại.</p></div>";
    exit;
}

// Cập nhật trạng thái khi submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = $_POST['status'] ?? 'pending';
    $orderModel->updateStatus($orderId, $status);
    header("Location: order_edit.php?id=" . $orderId);
    exit;
}

// Lấy danh sách sản phẩm
$items = $orderModel->getItems($orderId);
?>

<div class="admin-content-right">
    <div class="admin-content-right-category_list order-detail-page">

        <!-- HEADER TRÊN CÙNG -->
        <div class="order-detail-header">
            <div>
                <h1>Đơn hàng #<?= (int)$orderId; ?></h1>
                <p class="order-subtitle">
                    Tạo lúc <?= htmlspecialchars($order['created_at']); ?> • 
                    Thanh toán: <?= strtoupper(htmlspecialchars($order['payment_method'])); ?>
                </p>
            </div>
            <div class="order-detail-header-actions">
                <a href="orders.php" class="btn-order back">← Danh sách</a>

                <!-- NÚT HÓA ĐƠN: KHÔNG MỞ TAB MỚI NỮA -->
                <button type="button" class="btn-order invoice" id="btnShowInvoice">
                    Hóa đơn
                </button>

                <!-- Bạn có thể giữ hoặc bỏ nút Excel sau này -->
                <!-- <a href="order_export.php?id=<?= (int)$orderId; ?>" class="btn-order export">Xuất Excel</a> -->
            </div>
        </div>

        <!-- 2 CỘT: THÔNG TIN KH + TRẠNG THÁI -->
        <div class="order-detail-grid">

            <!-- Thông tin khách hàng -->
            <div class="order-card">
                <h3>Thông tin khách hàng</h3>
                <div class="info-row">
                    <span>Họ tên:</span>
                    <strong><?= htmlspecialchars($order['fullname']); ?></strong>
                </div>
                <div class="info-row">
                    <span>Số điện thoại:</span>
                    <strong><?= htmlspecialchars($order['phone']); ?></strong>
                </div>
                <div class="info-row">
                    <span>Địa chỉ:</span>
                    <strong><?= htmlspecialchars($order['address']); ?></strong>
                </div>
                <div class="info-row">
                    <span>Phương thức thanh toán:</span>
                    <strong><?= strtoupper(htmlspecialchars($order['payment_method'])); ?></strong>
                </div>
                <div class="info-row">
                    <span>Tổng tiền (theo đơn):</span>
                    <strong><?= number_format($order['total_price'], 0, ',', '.'); ?>đ</strong>
                </div>
            </div>

            <!-- Trạng thái đơn -->
            <div class="order-card">
                <h3>Trạng thái đơn hàng</h3>
                <p>Trạng thái hiện tại:</p>
                <p>
                    <span class="order-status-large badge-<?= htmlspecialchars($order['status']); ?>">
                        <?= htmlspecialchars($order['status']); ?>
                    </span>
                </p>

                <form method="post" class="status-form">
                    <label for="status">Cập nhật trạng thái:</label>
                    <select name="status" id="status">
                        <option value="pending"    <?= $order['status']=='pending'?'selected':''; ?>>Chờ duyệt</option>
                        <option value="processing" <?= $order['status']=='processing'?'selected':''; ?>>Đang xử lý</option>
                        <option value="shipping"   <?= $order['status']=='shipping'?'selected':''; ?>>Đang giao</option>
                        <option value="success"    <?= $order['status']=='success'?'selected':''; ?>>Hoàn tất</option>
                        <option value="cancelled"  <?= $order['status']=='cancelled'?'selected':''; ?>>Đã huỷ</option>
                    </select>
                    <button type="submit" class="btn-order primary">Lưu trạng thái</button>
                </form>
            </div>

        </div>

        <!-- DANH SÁCH SẢN PHẨM -->
        <div class="order-card order-items-card">
            <h3>Sản phẩm trong đơn</h3>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Size</th>
                        <th>Giá</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $totalCalc = 0;
                if ($items && $items->num_rows > 0):
                    while ($it = $items->fetch_assoc()):
                        $sub = $it['price'] * $it['qty'];
                        $totalCalc += $sub;
                ?>
                    <tr>
                        <td><?= htmlspecialchars($it['product_name']); ?></td>
                        <td><?= htmlspecialchars($it['size']); ?></td>
                        <td><?= number_format($it['price'], 0, ',', '.'); ?>đ</td>
                        <td><?= (int)$it['qty']; ?></td>
                        <td><?= number_format($sub, 0, ',', '.'); ?>đ</td>
                    </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="5" style="text-align:center;">Không có sản phẩm nào trong đơn.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" style="text-align:right;"><strong>Tổng cộng (tính lại):</strong></td>
                        <td><strong><?= number_format($totalCalc, 0, ',', '.'); ?>đ</strong></td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>

<!-- 🔥 OVERLAY HÓA ĐƠN FULL MÀN HÌNH (DÙNG IFRAME LOAD order_invoice.php?id=...) -->
<div id="invoice-overlay" class="invoice-overlay">
    <div class="invoice-overlay-inner">
        <button type="button" class="invoice-overlay-close" id="btnCloseInvoice">×</button>
        <iframe id="invoice-frame" src="" frameborder="0"></iframe>
    </div>
</div>

<style>
    /* ===== Layout chi tiết đơn ===== */
    .order-detail-page h1 {
        margin-bottom: 4px;
    }
    .order-subtitle {
        font-size: 13px;
        color: #777;
        margin-bottom: 0;
    }
    .order-detail-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 18px;
    }
    .order-detail-header-actions .btn-order {
        margin-left: 6px;
    }

    .order-detail-grid {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        grid-gap: 16px;
        margin-bottom: 16px;
    }

    .order-card {
        background:#fff;
        border-radius:12px;
        padding:16px 18px;
        box-shadow:0 4px 12px rgba(0,0,0,0.04);
    }
    .order-card h3 {
        margin-top:0;
        margin-bottom:10px;
        font-size:16px;
    }

    .info-row {
        display:flex;
        justify-content:space-between;
        margin-bottom:6px;
        font-size:14px;
    }
    .info-row span {
        color:#666;
    }

    .status-form {
        margin-top:10px;
        display:flex;
        flex-direction:column;
        gap:8px;
    }
    .status-form select {
        padding:6px 10px;
        border-radius:4px;
        border:1px solid #ddd;
        max-width:220px;
    }

    .btn-order {
        display:inline-block;
        padding:6px 12px;
        border-radius:8px;
        font-size:13px;
        border:1px solid transparent;
        text-decoration:none;
        cursor:pointer;
        background:#f3f4ff;
        color:#283593;
        transition:.2s;
    }
    .btn-order.primary {
        background:#6366f1;
        color:#fff;
        border-color:#4f46e5;
    }
    .btn-order.back {
        background:#f1f5f9;
        color:#1e293b;
    }
    .btn-order.invoice {
        background:#e8f5e9;
        color:#1b5e20;
    }
    .btn-order.export {
        background:#fff3e0;
        color:#e65100;
    }
    .btn-order:hover {
        transform:translateY(-1px);
        box-shadow:0 2px 6px rgba(0,0,0,0.08);
    }

    .order-status-large {
        padding:4px 10px;
        border-radius:999px;
        font-size:13px;
        text-transform:capitalize;
    }

    .order-items-card {
        margin-top:8px;
    }
    .order-items-card table {
        width:100%;
        border-collapse:collapse;
        font-size:14px;
    }
    .order-items-card th,
    .order-items-card td {
        padding:8px 10px;
        border-bottom:1px solid #eee;
    }
    .order-items-card thead {
        background:#f8fafc;
    }

    /* ===== Overlay HÓA ĐƠN FULL MÀN HÌNH ===== */
    .invoice-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,0.65); /* nền tối mờ */
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }
    .invoice-overlay.show {
        display: flex;
    }
    .invoice-overlay-inner {
        width: 92%;
        max-width: 1100px;
        height: 90vh;
        background: #f4f5fb;
        border-radius: 16px;
        box-shadow: 0 10px 30px rgba(15,23,42,0.35);
        position: relative;
        overflow: hidden;
    }
    .invoice-overlay-inner iframe {
        width: 100%;
        height: 100%;
        border: none;
        background: transparent;
    }
    .invoice-overlay-close {
        position: absolute;
        top: 10px;
        right: 12px;
        width: 32px;
        height: 32px;
        border-radius: 999px;
        border: none;
        background: #e5e7eb;
        color: #111827;
        font-size: 18px;
        cursor: pointer;
        z-index: 10;
        display:flex;
        align-items:center;
        justify-content:center;
    }
    .invoice-overlay-close:hover {
        background:#d1d5db;
    }

    @media (max-width: 900px) {
        .order-detail-grid {
            grid-template-columns: 1fr;
        }
        .invoice-overlay-inner {
            width: 100%;
            height: 100vh;
            border-radius: 0;
        }
    }
</style>

<script>
    (function() {
        const btnShow = document.getElementById('btnShowInvoice');
        const overlay = document.getElementById('invoice-overlay');
        const frame   = document.getElementById('invoice-frame');
        const btnClose = document.getElementById('btnCloseInvoice');

        if (btnShow && overlay && frame && btnClose) {
            // Khi bấm "Hóa đơn" -> mở overlay + load iframe
            btnShow.addEventListener('click', function() {
                frame.src = 'order_invoice.php?id=<?= (int)$orderId; ?>';
                overlay.classList.add('show');
            });

            // Đóng overlay
            btnClose.addEventListener('click', function() {
                overlay.classList.remove('show');
                // Nếu muốn, clear src để giải phóng tài nguyên
                // frame.src = '';
            });

            // Bấm ra nền tối cũng đóng
            overlay.addEventListener('click', function(e) {
                if (e.target === overlay) {
                    overlay.classList.remove('show');
                    // frame.src = '';
                }
            });
        }
    })();
</script>
