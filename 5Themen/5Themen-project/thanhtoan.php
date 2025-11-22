<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/include/session.php";
require_once __DIR__ . "/include/database.php";

// ==============================
// 0. GIỎ HÀNG TRỐNG → VỀ GIỎ
// ==============================
if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    header("Location: giohang.php");
    exit;
}

// ==============================
// 1. TÍNH TỔNG TIỀN TỪ GIỎ
// ==============================
$total = 0;
foreach ($_SESSION['cart'] as $pid => $item) {
    $price = isset($item['price']) ? (float)$item['price'] : 0;
    $qty   = isset($item['qty'])   ? (int)$item['qty']   : 0;
    if ($price > 0 && $qty > 0) {
        $total += $price * $qty;
    }
}
$total = (float)$total;

// Không cho đơn 0đ
if ($total <= 0) {
    header("Location: giohang.php");
    exit;
}

// ==============================
// 2. LẤY DỮ LIỆU TỪ FORM
// ==============================
$fullname = trim($_POST['fullname'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$address  = trim($_POST['address'] ?? '');

// phương thức thanh toán & step giao diện
$method = strtolower($_GET['method'] ?? 'cod');     // cod | vnpay | momo
$step   = strtolower($_GET['step']   ?? 'select');  // select | qr

$error   = null;
$orderId = null;

// ==============================
// 3. HÀM LƯU ĐƠN HÀNG VÀ CHI TIẾT
// ==============================
function saveOrder(mysqli $conn, float $total, string $method, string $fullname, string $phone, string $address): int|false
{
    // Lấy user_id nếu đã login, không thì = 0
    $userId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;

    $fullnameEsc = $conn->real_escape_string($fullname);
    $phoneEsc    = $conn->real_escape_string($phone);
    $addressEsc  = $conn->real_escape_string($address);
    $methodEsc   = $conn->real_escape_string($method);

    $sql = "
        INSERT INTO tbl_order (user_id, fullname, phone, address, total_price, payment_method, status, created_at)
        VALUES ($userId, '$fullnameEsc', '$phoneEsc', '$addressEsc', $total, '$methodEsc', 'pending', NOW())
    ";

    if (!$conn->query($sql)) {
        return false;
    }

    $orderId = (int)$conn->insert_id;

    // Lưu từng sản phẩm trong giỏ
    foreach ($_SESSION['cart'] as $pid => $item) {
        $pid   = (int)$pid;
        $price = isset($item['price']) ? (float)$item['price'] : 0;
        $qty   = isset($item['qty'])   ? (int)$item['qty']   : 0;
        $size  = isset($item['size'])  ? $conn->real_escape_string($item['size']) : 'L';

        if ($pid <= 0 || $price <= 0 || $qty <= 0) continue;

        $sqlDetail = "
            INSERT INTO tbl_order_detail (order_id, product_id, price, qty, size)
            VALUES ($orderId, $pid, $price, $qty, '$size')
        ";
        $conn->query($sqlDetail);
    }

    return $orderId;
}

// ==============================
// 4. KIỂM TRA METHOD HỢP LỆ
// ==============================
$allowedMethods = ['cod', 'vnpay', 'momo'];

if (!in_array($method, $allowedMethods, true)) {
    $error = "Phương thức thanh toán không hợp lệ.";
} else {

    // Kết nối DB
    $db   = new Database();
    $conn = $db->link;

    // ==============================
    // 4.1. XÁC ĐỊNH CÓ PHẢI LẦN ĐẦU TẠO ĐƠN KHÔNG
    //     (VNPAY & MOMO sẽ quay lại nhiều lần: step=select, step=qr)
    // ==============================
    $needCreateOrder = false;

    if ($method === 'cod') {
        // COD: mỗi lần submit là tạo đơn mới
        $needCreateOrder = true;

    } else {
        // vnpay / momo
        if (!isset($_SESSION['pending_order']) ||
            $_SESSION['pending_order']['method'] !== $method) {
            // chưa có đơn chờ cho gateway này → tạo mới
            $needCreateOrder = true;
        } else {
            // đã có đơn chờ → dùng lại
            $orderId = (int)$_SESSION['pending_order']['id'];
        }
    }

    // ==============================
    // 4.2. VALIDATE THÔNG TIN ĐƠN HÀNG (chỉ khi TẠO MỚI)
    // ==============================
    if ($needCreateOrder) {

        if ($fullname === '' || $phone === '' || $address === '') {
            $error = "Vui lòng nhập đầy đủ Họ tên, SĐT và Địa chỉ.";
        } elseif (!preg_match('/^(0[0-9]{9})$/', $phone)) {
            $error = "Số điện thoại không hợp lệ.";
        } else {
            // ==============================
            // 4.3. LƯU ĐƠN HÀNG
            // ==============================
            $orderId = saveOrder($conn, $total, $method, $fullname, $phone, $address);

            if (!$orderId) {
                $error = "Không thể lưu đơn hàng. Vui lòng thử lại sau.";
            } else {

                if ($method === 'cod') {

                    // COD: chuyển sang trạng thái processing luôn
                    $conn->query("UPDATE tbl_order SET status='processing' WHERE order_id = $orderId");

                    // Xóa giỏ và pending
                    unset($_SESSION['cart']);
                    unset($_SESSION['pending_order']);

                } else {
                    // VNPay / MoMo: lưu trạng thái chờ thanh toán
                    $_SESSION['pending_order'] = [
                        'id'     => $orderId,
                        'method' => $method,
                        'total'  => $total
                    ];
                }
            }
        }
    }
}

// ==============================
// 5. BẮT ĐẦU XUẤT HTML
// ==============================
require __DIR__ . "/partials/header.php";
?>

<section class="pay-page">
    <div class="container">

        <?php if ($error): ?>

            <!-- LỖI -->
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

            <!-- COD: ĐẶT HÀNG THÀNH CÔNG -->
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

            <!-- CHỌN PHƯƠNG THỨC VNPAY -->
            <div class="vnpay-card">
                <div class="vnpay-header">
                    <img src="images/vnpay.png" alt="VNPay" class="vnpay-logo">
                    <h2 class="vnpay-title">Chọn phương thức thanh toán</h2>
                </div>

                <div class="vnpay-method-list">
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

            <!-- QR VNPAY DEMO -->
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
                    <img src="images/qr_vnpay_demo.png" alt="VNPay QR">
                </div>

                <div class="pay-btn">
                    <a href="trangchu.php">Về trang chủ</a>
                </div>
            </div>

        <?php elseif ($method === 'momo'): ?>

            <!-- QR MOMO DEMO -->
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
