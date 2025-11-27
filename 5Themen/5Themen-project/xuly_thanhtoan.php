<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

$db   = new Database();
$conn = $db->link;

// =======================
// 1. BẮT BUỘC ĐĂNG NHẬP
// =======================
if (empty($_SESSION['is_logged_in']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// =======================
// 2. LẤY THÔNG TIN USER
// =======================
$sqlUser = "SELECT * FROM tbl_user WHERE user_id = '$user_id' LIMIT 1";
$result  = $conn->query($sqlUser);

if (!$result || $result->num_rows === 0) {
    header("Location: logout.php");
    exit;
}

$userData = $result->fetch_assoc();

// =======================
// HÀM XỬ LÝ ẢNH
// =======================
function buildProductImgPath($img)
{
    $img = (string)$img;
    if ($img === '') return '';
    if (strpos($img, 'admin/uploads/') === 0) return $img;
    return 'admin/uploads/' . ltrim($img, '/');
}

// =======================
// 3. LẤY order_id
// =======================
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($orderId <= 0) {
    header("Location: account.php?view=orders");
    exit;
}

$payGateway = strtolower($_GET['pay'] ?? '');

// =======================
// 4. LẤY ĐƠN HÀNG + CHI TIẾT
// =======================
$userPhone  = trim($userData['phone'] ?? '');
$wherePhone = "";
if ($userPhone !== '') {
    $phoneEsc   = $conn->real_escape_string($userPhone);
    $wherePhone = " AND o.phone = '$phoneEsc' ";
}

$sql = "
    SELECT 
        o.order_id,
        o.fullname,
        o.phone,
        o.address,
        o.payment_method,
        o.total_price,
        o.status,
        o.created_at,
        od.detail_id,
        od.product_id,
        od.qty,
        od.price      AS detail_price,
        od.size,
        p.product_name,
        p.product_img
    FROM tbl_order o
    LEFT JOIN tbl_order_detail od ON o.order_id = od.order_id
    LEFT JOIN tbl_product p       ON od.product_id = p.product_id
    WHERE o.order_id = $orderId
    $wherePhone
    ORDER BY od.detail_id ASC
";

$rs = $conn->query($sql);

if (!$rs || $rs->num_rows === 0) {
    header("Location: account.php?view=orders");
    exit;
}

$orderInfo  = null;
$orderItems = [];

while ($row = $rs->fetch_assoc()) {
    if ($orderInfo === null) {
        $orderInfo = [
            'order_id'       => $row['order_id'],
            'fullname'       => $row['fullname'],
            'phone'          => $row['phone'],
            'address'        => $row['address'],
            'payment_method' => $row['payment_method'],
            'total_price'    => $row['total_price'],
            'status'         => $row['status'],
            'created_at'     => $row['created_at'],
        ];
    }

    if (!empty($row['product_id'])) {
        $img = buildProductImgPath($row['product_img'] ?? '');
        $orderItems[] = [
            'product_id'   => $row['product_id'],
            'product_name' => $row['product_name'],
            'product_img'  => $img,
            'qty'          => $row['qty'],
            'size'         => $row['size'],
            'price'        => $row['detail_price'],
        ];
    }
}

// =======================
// 5. LABEL STATUS + PAYMENT
// =======================
function odStatusLabel($status)
{
    $status = strtolower((string)$status);
    switch ($status) {
        case 'pending':
            return ['Chờ xác nhận / chờ thanh toán', 'badge-pending'];
        case 'processing':
            return ['Đang xử lý', 'badge-processing'];
        case 'shipping':
            return ['Đang giao hàng', 'badge-shipping'];
        case 'completed':
            return ['Đã giao hàng', 'badge-completed'];
        case 'cancelled':
            return ['Đã hủy', 'badge-cancelled'];
        case 'returned':
            return ['Trả lại', 'badge-returned'];
        default:
            return ['Đang xử lý', 'badge-processing'];
    }
}

function paymentLabel($method)
{
    $method = strtolower((string)$method);
    switch ($method) {
        case 'cod':
            return 'Thanh toán khi nhận hàng (COD)';
        case 'vnpay':
            return 'Thanh toán qua VNPay';
        case 'momo':
            return 'Thanh toán qua MoMo';
        default:
            return strtoupper($method);
    }
}

[$statusText, $statusClass] = odStatusLabel($orderInfo['status'] ?? 'processing');
$paymentText                = paymentLabel($orderInfo['payment_method'] ?? 'cod');

$successTitle = "Đặt hàng thành công!";
$successDesc  = "Cảm ơn bạn đã mua sắm tại 5Themen Store. Chúng tôi đã tiếp nhận đơn và sẽ xử lý trong thời gian sớm nhất.";

if ($orderInfo['payment_method'] === 'vnpay' || $orderInfo['payment_method'] === 'momo') {
    if (strtolower($orderInfo['status']) === 'pending') {
        $successDesc = "Đơn hàng đã được tạo. Vui lòng hoàn tất thanh toán qua " . $paymentText . " (demo: xem như thanh toán đã ghi nhận 🧾).";
    }
}

$breadcrumbs = [
    ['text' => 'Trang chủ',  'url' => 'trangchu.php'],
    ['text' => 'Tài khoản',  'url' => 'account.php'],
    ['text' => 'Đơn hàng #' . (int)$orderInfo['order_id']],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= (int)$orderInfo['order_id'] ?> - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        /* giữ nguyên toàn bộ CSS chi tiết đơn như bạn đang có – mình chỉ để lại */
        .order-detail-page {
            padding: 40px 0 60px;
            background:#f5f5f7;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .order-detail-wrapper {
            max-width: 1100px;
            margin: 0 auto;
        }
        .order-success-banner {
            background:#ecfdf5;
            border-radius:16px;
            padding:14px 16px;
            display:flex;
            gap:10px;
            align-items:flex-start;
            margin-bottom:18px;
            border:1px solid #bbf7d0;
        }
        .order-success-icon { font-size:22px; }
        .order-success-text h2 {
            margin:0 0 4px;
            font-size:16px;
            color:#166534;
        }
        .order-success-text p {
            margin:0;
            font-size:13px;
            color:#166534;
        }
        .order-detail-header {
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom:18px;
        }
        .order-detail-header h1 {
            font-size:22px;
            margin:0 0 6px;
            color:#111827;
        }
        .order-detail-meta {
            font-size:13px;
            color:#6b7280;
        }
        .order-status-badge {
            font-size:12px;
            padding:5px 12px;
            border-radius:999px;
            font-weight:500;
        }
        .badge-processing { background:#fef3c7; color:#92400e;}
        .badge-pending { background:#e5e7eb; color:#374151;}
        .badge-shipping { background:#dbeafe; color:#1d4ed8;}
        .badge-completed { background:#dcfce7; color:#166534;}
        .badge-cancelled { background:#fee2e2; color:#b91c1c;}
        .badge-returned { background:#f3e8ff; color:#6d28d9;}

        .order-detail-grid {
            display:grid;
            grid-template-columns: minmax(0, 2.1fr) minmax(0, 1.1fr);
            gap:20px;
        }
        .order-card {
            background:#fff;
            border-radius:16px;
            box-shadow:0 10px 30px rgba(15,23,42,0.06);
            padding:18px 18px 16px;
        }
        .order-card h2 {
            font-size:16px;
            margin:0 0 10px;
            color:#111827;
        }
        .order-items-header,
        .order-item-row {
            display:grid;
            grid-template-columns: 60px 1fr 80px 80px;
            gap:10px;
            align-items:center;
        }
        .order-items-header {
            font-size:12px;
            color:#6b7280;
            padding-bottom:6px;
            border-bottom:1px solid #e5e7eb;
            margin-bottom:6px;
        }
        .order-item-row {
            padding:8px 0;
            border-bottom:1px solid #f3f4f6;
        }
        .order-item-row:last-child { border-bottom:none; }
        .order-item-thumb img {
            width:52px;
            height:52px;
            border-radius:10px;
            object-fit:cover;
            border:1px solid #e5e7eb;
        }
        .order-item-name {
            font-size:14px;
            color:#111827;
            margin-bottom:2px;
        }
        .order-item-meta {
            font-size:12px;
            color:#6b7280;
        }
        .order-item-price,
        .order-item-sub {
            font-size:13px;
            font-weight:500;
            color:#111827;
            text-align:right;
        }
        .order-summary-row {
            display:flex;
            justify-content:space-between;
            font-size:14px;
            margin-bottom:4px;
        }
        .order-summary-row.total strong { font-size:16px; }
        .order-address {
            font-size:14px;
            color:#374151;
            line-height:1.5;
        }
        .order-actions {
            margin-top:14px;
            display:flex;
            gap:10px;
            flex-wrap:wrap;
        }
        .btn-outline,
        .btn-primary {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:8px 14px;
            border-radius:999px;
            font-size:13px;
            text-decoration:none;
            cursor:pointer;
            border:1px solid transparent;
            transition:all .15s ease;
        }
        .btn-outline {
            border-color:#e5e7eb;
            color:#374151;
            background:#fff;
        }
        .btn-outline:hover { background:#f3f4ff; }
        .btn-primary {
            background:#111827;
            color:#f9fafb;
        }
        .btn-primary:hover { background:#4b5563; }

        @media (max-width: 900px) {
            .order-detail-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .order-items-header { display:none; }
            .order-item-row {
                grid-template-columns: 60px 1fr;
                grid-template-rows:auto auto;
            }
            .order-item-price,
            .order-item-sub { text-align:left; }
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>
<?php require_once __DIR__ . "/partials/breadcrumb.php"; ?>

<section class="order-detail-page">
    <div class="order-detail-wrapper">

        <div class="order-success-banner">
            <div class="order-success-icon">✅</div>
            <div class="order-success-text">
                <h2><?= htmlspecialchars($successTitle) ?></h2>
                <p><?= htmlspecialchars($successDesc) ?></p>
            </div>
        </div>

        <div class="order-detail-header">
            <div>
                <h1>Đơn hàng #<?= (int)$orderInfo['order_id'] ?></h1>
                <div class="order-detail-meta">
                    Ngày đặt: <?= htmlspecialchars($orderInfo['created_at']) ?> ·
                    Thanh toán: <?= htmlspecialchars($paymentText) ?>
                </div>
            </div>
            <span class="order-status-badge <?= $statusClass ?>">
                <?= $statusText ?>
            </span>
        </div>

        <div class="order-detail-grid">

            <div class="order-card">
                <h2>Sản phẩm trong đơn</h2>

                <?php if (empty($orderItems)): ?>
                    <p style="font-size:14px;color:#6b7280;">Không có chi tiết sản phẩm cho đơn hàng này.</p>
                <?php else: ?>
                    <div class="order-items-header">
                        <span></span>
                        <span>Sản phẩm</span>
                        <span style="text-align:right;">Đơn giá</span>
                        <span style="text-align:right;">Thành tiền</span>
                    </div>

                    <?php
                    $subTotal = 0;
                    foreach ($orderItems as $item):
                        $line = (float)$item['price'] * (int)$item['qty'];
                        $subTotal += $line;
                    ?>
                        <div class="order-item-row">
                            <div class="order-item-thumb">
                                <?php if (!empty($item['product_img'])): ?>
                                    <img src="<?= htmlspecialchars($item['product_img']) ?>"
                                         alt="<?= htmlspecialchars($item['product_name']) ?>">
                                <?php endif; ?>
                            </div>
                            <div>
                                <div class="order-item-name">
                                    <?= htmlspecialchars($item['product_name']) ?>
                                </div>
                                <div class="order-item-meta">
                                    Size: <?= htmlspecialchars($item['size']) ?> · SL: <?= (int)$item['qty'] ?>
                                </div>
                            </div>
                            <div class="order-item-price">
                                <?= number_format($item['price'], 0, ',', '.') ?>đ
                            </div>
                            <div class="order-item-sub">
                                <?= number_format($line, 0, ',', '.') ?>đ
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="order-card">
                <h2>Thông tin giao hàng</h2>
                <div class="order-address">
                    <strong><?= htmlspecialchars($orderInfo['fullname']) ?></strong><br>
                    SĐT: <?= htmlspecialchars($orderInfo['phone']) ?><br>
                    Địa chỉ: <?= nl2br(htmlspecialchars($orderInfo['address'])) ?>
                </div>

                <hr style="margin:14px 0;border:none;border-top:1px solid #e5e7eb;">

                <h2>Tổng tiền</h2>
                <div class="order-summary-row">
                    <span>Tạm tính</span>
                    <span><?= number_format($subTotal, 0, ',', '.') ?>đ</span>
                </div>
                <div class="order-summary-row">
                    <span>Phí vận chuyển</span>
                    <span>0đ</span>
                </div>
                <div class="order-summary-row total">
                    <span><strong>Tổng cộng</strong></span>
                    <span><strong><?= number_format($orderInfo['total_price'], 0, ',', '.') ?>đ</strong></span>
                </div>

                <div class="order-actions">
                    <a href="account.php?view=orders" class="btn-outline">← Quay lại danh sách đơn</a>
                    <a href="trangchu.php" class="btn-primary">Tiếp tục mua sắm</a>
                    <a href="reorder.php?id=<?= $orderInfo['order_id'] ?>" class="btn-primary">Mua lại đơn này</a>
                </div>
            </div>

        </div>
    </div>
</section>

<?php require __DIR__ . "/partials/footer.php"; ?>

</body>
</html>
