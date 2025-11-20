<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/include/session.php";
require_once __DIR__ . "/include/database.php";

// Nếu giỏ hàng trống thì về lại giỏ hàng
if (empty($_SESSION['cart'])) {
    header("Location: giohang.php");
    exit;
}

// Tính tổng tiền từ session cart
$total = 0;
foreach ($_SESSION['cart'] as $pid => $item) {
    $total += $item['price'] * $item['qty'];
}

// Lấy phương thức thanh toán + step (riêng VNPay)
$method = strtolower($_GET['method'] ?? 'cod');      // cod | vnpay | momo
$step   = strtolower($_GET['step'] ?? 'select');     // select | qr (chỉ dùng cho vnpay)

$error   = null;
$orderId = null;

// Validate method
if (!in_array($method, ['cod', 'vnpay', 'momo'], true)) {
    $error = "Phương thức thanh toán không hợp lệ.";
} else {
    // Kết nối DB (dùng class Database của bạn)
    $db   = new Database();
    $conn = $db->link;

    // ===== HÀM LƯU ĐƠN HÀNG VÀO DB =====
    function saveOrder(mysqli $conn, $total, $method) {
        $methodEscaped = $conn->real_escape_string($method);
        $total         = (int)$total;

        $sqlOrder = "
            INSERT INTO tbl_orders(total_amount, payment_method, status, created_at)
            VALUES ($total, '$methodEscaped', 'pending', NOW())
        ";

        if (!$conn->query($sqlOrder)) {
            return false;
        }

        $orderId = $conn->insert_id;

        // Lưu từng item
        foreach ($_SESSION['cart'] as $pid => $item) {
            $pid   = (int)$pid;
            $name  = $conn->real_escape_string($item['name']);
            $price = (int)$item['price'];
            $qty   = (int)$item['qty'];

            $sqlItem = "
                INSERT INTO tbl_order_items(order_id, product_id, product_name, price, quantity)
                VALUES ($orderId, $pid, '$name', $price, $qty)
            ";

            $conn->query($sqlItem);
        }

        return $orderId;
    }

    // ===== XỬ LÝ TẠO ĐƠN THEO PHƯƠNG THỨC =====
    if ($method === 'cod') {
        // COD: tạo đơn + set status = processing, xoá giỏ
        $orderId = saveOrder($conn, $total, 'cod');

        if ($orderId === false) {
            $error = "Không thể lưu đơn hàng. Vui lòng thử lại.";
        } else {
            $conn->query("UPDATE tbl_orders SET status='processing' WHERE id = $orderId");
            unset($_SESSION['cart']);
            unset($_SESSION['pending_order']); // xoá pending nếu có
        }

    } else {
        // VNPAY & MOMO: tạo đơn 1 lần, lưu trong session 'pending_order'
        if (!isset($_SESSION['pending_order']) ||
            $_SESSION['pending_order']['method'] !== $method) {

            $orderId = saveOrder($conn, $total, $method);

            if ($orderId === false) {
                $error = "Không thể tạo đơn hàng. Vui lòng thử lại.";
            } else {
                $_SESSION['pending_order'] = [
                    'id'     => $orderId,
                    'method' => $method,
                    'total'  => $total
                ];
            }
        } else {
            // Đã có đơn pending cho method này → dùng lại
            $orderId = $_SESSION['pending_order']['id'];
        }
    }
}

require __DIR__ . "/partials/header.php";
?>

<section class="pay-page">
    <div class="container">

        <?php if ($error): ?>

            <div class="pay-box">
                <h2 class="pay-title">Có lỗi xảy ra</h2>
                <div class="pay-msg pay-msg-error">
                    <?= htmlspecialchars($error) ?>
                </div>
                <div class="pay-btn">
                    <a href="giohang.php">Quay lại giỏ hàng</a>
                </div>
            </div>

        <?php elseif ($method === 'cod'): ?>

            <!-- GIAO DIỆN COD: ĐẶT HÀNG THÀNH CÔNG -->
            <div class="pay-box">
                <h2 class="pay-title">Đặt hàng thành công</h2>
                <div class="pay-msg">
                    Cảm ơn bạn đã đặt hàng!<br>
                    Đơn hàng đang được xử lý và sẽ giao trong thời gian sớm nhất.
                </div>
                <div class="pay-total">
                    Tổng tiền: <?= number_format($total, 0, ',', '.') ?>đ
                </div>
                <div class="pay-btn">
                    <a href="trangchu.php">Về trang chủ</a>
                </div>
            </div>

        <?php elseif ($method === 'vnpay' && $step === 'select'): ?>

            <!-- GIAO DIỆN CHỌN PHƯƠNG THỨC VNPAY -->
            <div class="vnpay-card">
                <div class="vnpay-header">
                    <img src="images/vnpay.png" alt="VNPay" class="vnpay-logo">
                    <h2 class="vnpay-title">Chọn phương thức thanh toán</h2>
                </div>

                <div class="vnpay-method-list">
                    <!-- Cả 3 dòng đều link sang step=qr (demo QR giống nhau) -->
                    <a href="thanhtoan.php?method=vnpay&step=qr" class="vnpay-method">
                        <div class="vnpay-method-text">
                            <div class="vnpay-method-main">App Ngân hàng và Ví điện tử (VNPayQR)</div>
                            <div class="vnpay-method-sub">Quét mã VNPAYQR bằng app ngân hàng / ví điện tử</div>
                        </div>
                        <span class="vnpay-arrow">&rsaquo;</span>
                    </a>

                    <a href="thanhtoan.php?method=vnpay&step=qr" class="vnpay-method">
                        <div class="vnpay-method-text">
                            <div class="vnpay-method-main">Thẻ nội địa và tài khoản ngân hàng</div>
                            <div class="vnpay-method-sub">Thanh toán bằng thẻ ATM / Internet Banking</div>
                        </div>
                        <span class="vnpay-arrow">&rsaquo;</span>
                    </a>

                    <a href="thanhtoan.php?method=vnpay&step=qr" class="vnpay-method">
                        <div class="vnpay-method-text">
                            <div class="vnpay-method-main">App VNPAY</div>
                            <div class="vnpay-method-sub">Sử dụng ứng dụng VNPAY để thanh toán</div>
                        </div>
                        <span class="vnpay-arrow">&rsaquo;</span>
                    </a>
                </div>

                <div class="vnpay-footer">
                    Số tiền: <strong><?= number_format($total, 0, ',', '.') ?>đ</strong><br>
                    Đơn hàng #<?= (int)$orderId ?>
                </div>
            </div>

        <?php elseif ($method === 'vnpay' && $step === 'qr'): ?>

            <!-- GIAO DIỆN QR VNPAY DEMO -->
            <div class="pay-box">
                <h2 class="pay-title">Thanh toán qua VNPay</h2>
                <img src="images/vnpay.png" class="gateway-logo" alt="VNPay">

                <div class="pay-msg">
                    Đơn hàng #<?= (int)$orderId ?> đang chờ thanh toán.<br>
                    Quét QR bên dưới bằng app ngân hàng / ví VNPay để thanh toán.
                </div>

                <div class="pay-total">
                    Số tiền: <?= number_format($total, 0, ',', '.') ?>đ
                </div>

                <div class="qr-box">
                    <!-- DEMO: QR tĩnh -->
                    <img src="images/qr_vnpay_demo.png" alt="VNPay QR">
                </div>

                <div class="pay-btn">
                    <a href="trangchu.php">Về trang chủ</a>
                </div>
            </div>

        <?php elseif ($method === 'momo'): ?>

            <!-- GIAO DIỆN QR MOMO -->
            <div class="pay-box">
                <h2 class="pay-title">Thanh toán qua MoMo</h2>
                <img src="images/momo.png" class="gateway-logo" alt="MoMo">

                <div class="pay-msg">
                    Đơn hàng #<?= (int)$orderId ?> đang chờ thanh toán.<br>
                    Quét QR bằng app MoMo để hoàn tất thanh toán.
                </div>

                <div class="pay-total">
                    Số tiền: <?= number_format($total, 0, ',', '.') ?>đ
                </div>

                <div class="qr-box">
                    <!-- DEMO: QR tĩnh -->
                    <img src="images/qr_momo_demo.png" alt="MoMo QR">
                </div>

                <div class="pay-btn">
                    <a href="trangchu.php">Về trang chủ</a>
                </div>
            </div>

        <?php endif; ?>

    </div>
</section>

<?php require __DIR__ . "/partials/footer.php"; ?>
