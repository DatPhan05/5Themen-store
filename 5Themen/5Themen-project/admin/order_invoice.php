<?php
// admin/order_invoice.php
require_once __DIR__ . "/class/order_class.php";

$orderModel = new Order();

$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    die("Thiếu ID đơn hàng.");
}

// Lấy thông tin đơn hàng và chi tiết sản phẩm
list($order, $items) = $orderModel->getOrderWithTotal($orderId);
if (!$order) {
    die("Đơn hàng không tồn tại.");
}

$totalCalc = $order['calc_total'] ?? 0; // Tổng tiền sản phẩm (đã tính từ chi tiết)
$shippingFee = 30000; // Giả định Phí vận chuyển
$finalTotal = (float)$totalCalc + (float)$shippingFee; // Tổng tiền cuối cùng

// Thông tin người bán (Giả định, bạn có thể thay đổi)
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
        /* ==================== CƠ BẢN VÀ MÀN HÌNH ==================== */
        body {
            font-family: 'Times New Roman', Times, serif; 
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
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        }
        
        /* ==================== HEADER HÓA ĐƠN ==================== */
        .invoice-header {
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        .logo h1 {
            color: #007bff;
            font-size: 28px;
            margin: 0 0 5px 0;
            font-weight: 900;
        }
        .seller-info p {
            margin: 0;
            line-height: 1.4;
            font-size: 13px;
            text-align: right;
        }
        .invoice-title {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin: 0 0 10px 0;
            font-weight: 700;
            text-transform: uppercase;
        }
        .invoice-date {
            text-align: center;
            font-style: italic;
            margin-bottom: 20px;
            font-size: 13px;
            color: #666;
        }

        /* ==================== THÔNG TIN KHÁCH HÀNG ==================== */
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
            font-size: 16px;
            margin-top: 0;
            margin-bottom: 10px;
            color: #007bff;
            border-bottom: 1px dashed #eee;
            padding-bottom: 5px;
        }
        .info-section p {
            margin: 5px 0;
            line-height: 1.6;
            font-size: 14px;
        }
        .info-label {
            font-weight: 700;
            display: inline-block;
            min-width: 100px;
        }

        /* ==================== BẢNG SẢN PHẨM ==================== */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th, .items-table td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        .items-table thead th {
            background-color: #f0f0f0;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 13px;
        }
        .items-table tbody td {
            font-size: 13px;
        }
        .items-table .text-center { text-align: center; }
        .items-table .text-right { text-align: right; }

        /* ==================== FOOTER TÍNH TOÁN ==================== */
        .items-table tfoot td {
            background-color: #f8f8f8;
            font-weight: 600;
            border: 1px solid #ccc;
        }
        .total-row td {
            font-size: 15px;
            font-weight: 700 !important;
            background-color: #e3f2fd !important;
            color: #d63031;
        }
        .total-row .info-label {
            color: #333;
        }

        /* ==================== CHỮ KÝ & GHI CHÚ ==================== */
        .signatures {
            display: flex;
            justify-content: space-around;
            text-align: center;
            margin-top: 40px;
        }
        .signatures div {
            width: 45%;
        }
        .signatures h5 {
            font-size: 15px;
            margin-bottom: 5px;
        }
        .signatures p {
            font-style: italic;
            font-size: 12px;
            color: #888;
        }
        .footer-note {
            text-align: center;
            margin-top: 25px;
            font-size: 12px;
            color: #666;
            padding-top: 10px;
            border-top: 1px dashed #ddd;
        }

        /* ==================== IN ẤN (PRINTER) ==================== */
        @media print {
            body {
                background: none;
                padding: 0;
                margin: 0;
                font-size: 12pt;
                color: #000;
            }
            .invoice-wrapper {
                max-width: none;
                box-shadow: none;
                border-radius: 0;
                padding: 0;
                margin: 0;
            }
            .invoice-header {
                border-bottom-color: #000;
            }
            .customer-info, .order-summary {
                border: none;
                padding: 0;
                margin-bottom: 15px;
            }
            .items-table th, .items-table td {
                border-color: #000 !important;
            }
            .items-table tfoot td {
                background-color: #f0f0f0 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .total-row td {
                background-color: #ccc !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact;
                color-adjust: exact;
            }
            .footer-note {
                page-break-before: auto;
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
            <p>Địa chỉ: <?= htmlspecialchars($seller['address']); ?></p>
            <p>SĐT: <?= htmlspecialchars($seller['phone']); ?> | Email: <?= htmlspecialchars($seller['email']); ?></p>
        </div>
    </div>

    <h2 class="invoice-title">HÓA ĐƠN BÁN HÀNG</h2>
    <p class="invoice-date">Ngày đặt hàng: <?= htmlspecialchars($order['created_at'] ?? date('Y-m-d')); ?></p>

    <div class="info-section">
        <div class="customer-info">
            <h4>Thông tin Khách hàng</h4>
            <p><span class="info-label">Khách hàng:</span> <?= htmlspecialchars($order['fullname'] ?? 'N/A'); ?></p>
            <p><span class="info-label">Số điện thoại:</span> <?= htmlspecialchars($order['phone'] ?? 'N/A'); ?></p>
            <p><span class="info-label">Email:</span> <?= htmlspecialchars($order['email'] ?? 'N/A'); ?></p>
            <p><span class="info-label">Địa chỉ:</span> <?= htmlspecialchars($order['address'] ?? 'N/A'); ?></p>
        </div>
        <div class="order-summary">
            <h4>Chi tiết Đơn hàng</h4>
            <p><span class="info-label">Mã đơn hàng:</span> #<?= (int)$orderId; ?></p>
            <p><span class="info-label">Phương thức TT:</span> <span style="font-weight: 700; color: #007bff;"><?= strtoupper(htmlspecialchars($order['payment_method'] ?? 'N/A')); ?></span></p>
            <p><span class="info-label">Trạng thái:</span> <span style="font-weight: 700; color: #28a745;"><?= htmlspecialchars($order['status'] ?? 'N/A'); ?></span></p>
            <p><span class="info-label">Ghi chú:</span> <?= htmlspecialchars($order['note'] ?? 'Không có'); ?></p>
        </div>
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 30px;" class="text-center">#</th>
                <th>Sản phẩm</th>
                <th style="width: 70px;" class="text-center">Size</th>
                <th style="width: 70px;" class="text-center">Màu</th> <th style="width: 120px;" class="text-right">Giá (đ)</th>
                <th style="width: 50px;" class="text-center">SL</th>
                <th style="width: 150px;" class="text-right">Thành tiền (đ)</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        if ($items && $items->num_rows > 0):
            $items->data_seek(0);
            while ($row = $items->fetch_assoc()):
                $sub = (float)($row['price'] ?? 0) * (int)($row['qty'] ?? 0);
        ?>
            <tr>
                <td class="text-center"><?= $i++; ?></td>
                <td><?= htmlspecialchars($row['product_name'] ?? 'N/A'); ?></td>
                <td class="text-center"><?= htmlspecialchars($row['size'] ?? 'N/A'); ?></td>
                <td class="text-center"><?= htmlspecialchars($row['color'] ?? 'N/A'); ?></td> <td class="text-right"><?= number_format($row['price'] ?? 0, 0, ',', '.'); ?></td>
                <td class="text-center"><?= (int)($row['qty'] ?? 0); ?></td>
                <td class="text-right" style="font-weight: 600;"><?= number_format($sub, 0, ',', '.'); ?>đ</td>
            </tr>
        <?php
            endwhile;
        else:
        ?>
            <tr><td colspan="7" style="text-align:center;">Không có sản phẩm.</td></tr>
        <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6" class="text-right"><span class="info-label">Tạm tính (Tổng giá trị hàng hóa):</span></td>
                <td class="text-right"><?= number_format($totalCalc, 0, ',', '.'); ?>đ</td>
            </tr>
            <tr>
                <td colspan="6" class="text-right"><span class="info-label">Phí vận chuyển:</span></td>
                <td class="text-right"><?= number_format($shippingFee, 0, ',', '.'); ?>đ</td>
            </tr>
            <tr class="total-row">
                <td colspan="6" class="text-right"><span class="info-label">TỔNG CỘNG THANH TOÁN:</span></td>
                <td class="text-right"><?= number_format($finalTotal, 0, ',', '.'); ?>đ</td>
            </tr>
        </tfoot>
    </table>

    <div class="signatures">
        <div>
            <h5>Người Lập Hóa Đơn</h5>
            <p>(Ký, ghi rõ họ tên)</p>
            <br><br><br>
            <p>_________________________</p>
        </div>
        <div>
            <h5>Người Nhận Hàng</h5>
            <p>(Ký, ghi rõ họ tên)</p>
            <br><br><br>
            <p>_________________________</p>
        </div>
    </div>

    <div class="footer-note">
        Cảm ơn Quý khách đã mua sắm tại 5Themen Store. Vui lòng kiểm tra kỹ hàng hóa trước khi ký nhận.
    </div>
</div>

<script>
    // Tự động in khi trang được tải trong iframe (trên order_edit.php)
    window.onload = function() {
        // Chỉ gọi print() nếu cửa sổ hiện tại không phải là cửa sổ chính (đang là iframe)
        if (window.self !== window.top) {
             setTimeout(function() {
                 window.print();
             }, 500); 
        }
    };
</script>

</body>
</html>