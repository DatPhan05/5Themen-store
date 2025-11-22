<?php
// admin/order_invoice.php
require_once __DIR__ . "/class/order_class.php";

$orderModel = new Order();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    die("Thiếu ID đơn hàng.");
}

list($order, $items) = $orderModel->getOrderWithTotal($orderId);
if (!$order) {
    die("Đơn hàng không tồn tại.");
}

$totalCalc = $order['calc_total'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #<?= (int)$orderId; ?> - 5Themen</title>
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
            background:#f4f5fb;
            margin:0;
            padding:20px;
        }
        .invoice-wrapper {
            max-width: 900px;
            margin: 0 auto;
            background:#fff;
            border-radius:12px;
            padding:24px 28px;
            box-shadow:0 4px 16px rgba(0,0,0,0.08);
        }
        .invoice-header {
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom:20px;
        }
        .invoice-logo {
            font-weight:bold;
            font-size:20px;
        }
        .invoice-meta {
            text-align:right;
            font-size:13px;
            color:#555;
        }
        h2 {
            margin-top:0;
        }

        .info-columns {
            display:flex;
            justify-content:space-between;
            margin-bottom:20px;
            font-size:14px;
        }
        .info-block h4 {
            margin:0 0 6px 0;
            font-size:14px;
        }
        .info-block p {
            margin:2px 0;
        }

        table {
            width:100%;
            border-collapse:collapse;
            font-size:14px;
        }
        th, td {
            padding:8px 10px;
            border-bottom:1px solid #eee;
        }
        thead {
            background:#f8fafc;
        }
        tfoot td {
            border:none;
        }
        .total-row td {
            font-weight:bold;
        }

        .footer-note {
            margin-top:20px;
            font-size:13px;
            color:#666;
        }

        .invoice-actions {
            text-align:right;
            margin-bottom:10px;
        }
        .btn-print {
            padding:6px 12px;
            border-radius:6px;
            border:1px solid #4f46e5;
            background:#6366f1;
            color:#fff;
            cursor:pointer;
        }

        @media print {
            body {
                background:#fff;
                padding:0;
            }
            .invoice-wrapper {
                box-shadow:none;
                border-radius:0;
                margin:0;
            }
            .invoice-actions {
                display:none;
            }
        }
    </style>
</head>
<body>

<div class="invoice-wrapper">
    <div class="invoice-actions">
        <button onclick="window.print()" class="btn-print">In hóa đơn</button>
    </div>

    <div class="invoice-header">
        <div class="invoice-logo">
            5Themen Store
            <div style="font-size:12px;color:#777;margin-top:4px;">
                Hóa đơn bán hàng
            </div>
        </div>
        <div class="invoice-meta">
            <div><strong>Mã đơn:</strong> #<?= (int)$orderId; ?></div>
            <div><strong>Ngày tạo:</strong> <?= htmlspecialchars($order['created_at']); ?></div>
            <div><strong>Thanh toán:</strong> <?= strtoupper(htmlspecialchars($order['payment_method'])); ?></div>
            <div><strong>Trạng thái:</strong> <?= htmlspecialchars($order['status']); ?></div>
        </div>
    </div>

    <div class="info-columns">
        <div class="info-block">
            <h4>Khách hàng</h4>
            <p><?= htmlspecialchars($order['fullname']); ?></p>
            <p>Điện thoại: <?= htmlspecialchars($order['phone']); ?></p>
            <p>Địa chỉ: <?= htmlspecialchars($order['address']); ?></p>
        </div>
        <div class="info-block">
            <h4>Cửa hàng</h4>
            <p>5Themen Store</p>
            <p>Hotline: 0123 456 789</p>
            <p>Email: support@5themen.local</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Sản phẩm</th>
                <th>Size</th>
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
                $sub = $row['price'] * $row['qty'];
        ?>
            <tr>
                <td><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['product_name']); ?></td>
                <td><?= htmlspecialchars($row['size']); ?></td>
                <td><?= number_format($row['price'], 0, ',', '.'); ?>đ</td>
                <td><?= (int)$row['qty']; ?></td>
                <td><?= number_format($sub, 0, ',', '.'); ?>đ</td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="6" style="text-align:center;">Không có sản phẩm.</td></tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" style="text-align:right;">Tổng cộng:</td>
                <td><?= number_format($totalCalc, 0, ',', '.'); ?>đ</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer-note">
        Cảm ơn bạn đã mua sắm tại 5Themen Store.  
        Nếu có bất kỳ thắc mắc nào về đơn hàng, vui lòng liên hệ hotline hoặc email hỗ trợ.
    </div>
</div>

</body>
</html>
