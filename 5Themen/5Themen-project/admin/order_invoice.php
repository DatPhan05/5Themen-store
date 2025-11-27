<?php
require_once __DIR__ . "/class/order_class.php";

$orderModel = new Order();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    die("Thiếu ID đơn hàng.");
}

// Lấy thông tin đơn hàng + sản phẩm + tổng tiền chi tiết
list($order, $items) = $orderModel->getOrderWithTotal($orderId);
if (!$order) {
    die("Đơn hàng không tồn tại.");
}

// Tổng tiền tính từ chi tiết
$totalCalc = $order['calc_total'] ?? 0;

// Phí ship cố định
$shippingFee = 30000;

// Tổng phải thanh toán
$finalTotal = (float)$totalCalc + (float)$shippingFee;

// Thông tin người bán (có thể đổi theo thực tế)
$seller = [
    'name'    => '5Themen Store',
    'address' => '123 Đường ABC, Quận 1, TP.HCM',
    'phone'   => '0987.654.321',
    'email'   => 'cskh@5themen.vn',
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hóa đơn #<?= (int)$orderId; ?> - 5Themen</title>

    <style>
        body {
            font-family: 'Times New Roman', serif;
            background: #f4f5fb;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 14px;
        }
        .invoice-wrapper {
            max-width: 800px;
            margin: 0 auto;
            background: #fff;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        }

        /* Header hóa đơn */
        .invoice-header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .logo h1 {
            color: #007bff;
            font-size: 28px;
            margin: 0 0 5px 0;
            font-weight: 900;
        }
        .seller-info p {
            margin: 0;
            font-size: 13px;
            text-align: right;
        }

        .invoice-title {
            text-align: center;
            font-size: 24px;
            margin: 0 0 10px 0;
            font-weight: 700;
        }
        .invoice-date {
            text-align: center;
            font-style: italic;
            margin-bottom: 20px;
            font-size: 13px;
        }

        /* Thông tin khách hàng + tóm tắt đơn hàng */
        .info-section {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
        }
        .customer-info, .order-summary {
            width: 50%;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }
        .info-section h4 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #007bff;
            font-size: 16px;
        }
        .info-label {
            font-weight: 700;
            display: inline-block;
            min-width: 100px;
        }

        /* Bảng sản phẩm */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #ccc;
            padding: 10px;
        }
        .items-table th {
            background: #f0f0f0;
            font-size: 13px;
            text-transform: uppercase;
        }
        .text-center { text-align: center; }
        .text-right  { text-align: right; }

        /* Tổng tiền */
        .items-table tfoot td {
            background: #f8f8f8;
            font-weight: 600;
        }
        .total-row td {
            background: #e3f2fd !important;
            font-weight: 700;
            color: #d63031;
        }

        /* Chữ ký */
        .signatures {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            text-align: center;
        }
        .signatures h5 {
            margin-bottom: 5px;
        }
        .signatures p {
            font-size: 12px;
            color: #888;
            font-style: italic;
        }

        /* Ghi chú footer */
        .footer-note {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
            color: #666;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
        }

        /* In ấn */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
            }
            .invoice-wrapper {
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                margin: 0;
            }
            .items-table th, .items-table td {
                border-color: #000 !important;
            }
            .total-row td {
                background: #ccc !important;
                color: #000 !important;
            }
        }
    </style>
</head>

<body>

<div class="invoice-wrapper">

    <div class="invoice-header">
        <div class="logo">
            <h1>5Themen</h1>
        </div>
        <div class="seller-info">
            <p><strong><?= htmlspecialchars($seller['name']); ?></strong></p>
            <p><?= htmlspecialchars($seller['address']); ?></p>
            <p><?= htmlspecialchars($seller['phone']); ?> | <?= htmlspecialchars($seller['email']); ?></p>
        </div>
    </div>

    <h2 class="invoice-title">HÓA ĐƠN BÁN HÀNG</h2>
    <p class="invoice-date">
        Ngày đặt hàng: <?= htmlspecialchars($order['created_at'] ?? date('Y-m-d')); ?>
    </p>

    <div class="info-section">

        <div class="customer-info">
            <h4>Thông tin khách hàng</h4>
            <p><span class="info-label">Khách hàng:</span><?= htmlspecialchars($order['fullname']); ?></p>
            <p><span class="info-label">Điện thoại:</span><?= htmlspecialchars($order['phone']); ?></p>
            <p><span class="info-label">Email:</span><?= htmlspecialchars($order['email']); ?></p>
            <p><span class="info-label">Địa chỉ:</span><?= htmlspecialchars($order['address']); ?></p>
        </div>

        <div class="order-summary">
            <h4>Chi tiết đơn hàng</h4>
            <p><span class="info-label">Mã đơn:</span>#<?= $orderId; ?></p>
            <p><span class="info-label">Thanh toán:</span><?= strtoupper(htmlspecialchars($order['payment_method'])); ?></p>
            <p><span class="info-label">Trạng thái:</span><?= htmlspecialchars($order['status']); ?></p>
            <p><span class="info-label">Ghi chú:</span><?= htmlspecialchars($order['note'] ?? 'Không có'); ?></p>
        </div>

    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th class="text-center" style="width:30px;">#</th>
                <th>Sản phẩm</th>
                <th class="text-center" style="width:70px;">Size</th>
                <th class="text-center" style="width:70px;">Màu</th>
                <th class="text-right"  style="width:120px;">Giá (đ)</th>
                <th class="text-center" style="width:50px;">SL</th>
                <th class="text-right"  style="width:150px;">Thành tiền (đ)</th>
            </tr>
        </thead>

        <tbody>
        <?php
        $i = 1;
        if ($items && $items->num_rows > 0):
            $items->data_seek(0);
            while ($row = $items->fetch_assoc()):
                $sub = (float)$row['price'] * (int)$row['qty'];
        ?>
            <tr>
                <td class="text-center"><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['product_name']); ?></td>
                <td class="text-center"><?= htmlspecialchars($row['size']); ?></td>
                <td class="text-center"><?= htmlspecialchars($row['color']); ?></td>
                <td class="text-right"><?= number_format($row['price'], 0, ',', '.'); ?></td>
                <td class="text-center"><?= $row['qty']; ?></td>
                <td class="text-right" style="font-weight:600;">
                    <?= number_format($sub, 0, ',', '.'); ?>đ
                </td>
            </tr>
        <?php endwhile; else: ?>
            <tr><td colspan="7" style="text-align:center;">Không có sản phẩm.</td></tr>
        <?php endif; ?>
        </tbody>

        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><span class="info-label">Tạm tính:</span></td>
                <td class="text-right"><?= number_format($totalCalc, 0, ',', '.'); ?>đ</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"><span class="info-label">Phí vận chuyển:</span></td>
                <td class="text-right"><?= number_format($shippingFee, 0, ',', '.'); ?>đ</td>
            </tr>
            <tr class="total-row">
                <td colspan="6" class="text-right"><span class="info-label">Tổng cộng:</span></td>
                <td class="text-right"><?= number_format($finalTotal, 0, ',', '.'); ?>đ</td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div>
            <h5>Người lập hóa đơn</h5>
            <p>(Ký, ghi rõ họ tên)</p><br><br><br>
            <p>_________________________</p>
        </div>
        <div>
            <h5>Người nhận hàng</h5>
            <p>(Ký, ghi rõ họ tên)</p><br><br><br>
            <p>_________________________</p>
        </div>
    </div>

    <div class="footer-note">
        Cảm ơn Quý khách đã mua sắm tại 5Themen Store.
    </div>
</div>

<script>
window.onload = function() {
    // Chỉ in khi file này nằm trong iframe
    if (window.self !== window.top) {
        setTimeout(() => window.print(), 500);
    }
};
</script>

</body>
</html>
