<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';
require_once __DIR__ . '/partials/header.php';

// Nếu giỏ hàng trống → quay lại giỏ hàng
if (empty($_SESSION['cart'])) {
    header("Location: giohang.php");
    exit;
}

$db = new Database();

// Nếu user đã đăng nhập → tự lấy thông tin
$userInfo = null;
if (!empty($_SESSION['user_id'])) {
    $uid = (int)$_SESSION['user_id'];
    $sql = "SELECT * FROM tbl_user WHERE user_id = $uid LIMIT 1";
    $rs  = $db->select($sql);
    if ($rs && $rs->num_rows > 0) {
        $userInfo = $rs->fetch_assoc();
    }
}
?>

<link rel="stylesheet" href="CSS/style.css">

<section class="checkout container">

    <h1 class="checkout-title">THANH TOÁN</h1>

    <form method="POST" action="thanhtoan_xuly.php" class="checkout-form">

        <div class="checkout-left">

            <h2>Thông tin giao hàng</h2>

            <label>Họ và tên *</label>
            <input type="text" name="fullname" required
                   value="<?= $userInfo['fullname'] ?? '' ?>">

            <label>Email *</label>
            <input type="email" name="email" required
                   value="<?= $userInfo['email'] ?? '' ?>">

            <label>Số điện thoại *</label>
            <input type="text" name="phone" required
                   value="<?= $userInfo['phone'] ?? '' ?>">

            <label>Địa chỉ giao hàng *</label>
            <input type="text" name="address" required
                   value="<?= $userInfo['address'] ?? '' ?>">

            <h2>Phương thức thanh toán</h2>

            <div class="payment-method">
                <label>
                    <input type="radio" name="method" value="cod" checked>
                    Thanh toán khi nhận hàng (COD)
                </label>

                <label>
                    <input type="radio" name="method" value="vnpay">
                    VNPAY
                </label>

                <label>
                    <input type="radio" name="method" value="momo">
                    Ví Momo
                </label>
            </div>

        </div>

        <div class="checkout-right">

            <h2>Đơn hàng của bạn</h2>

            <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $pid => $item):
                $total += $item['price'] * $item['qty'];
            ?>

            <div class="cart-item">
                <img src="<?= $item['image'] ?>" class="cart-thumb">

                <div class="cart-info">
                    <p class="cart-name"><?= $item['name'] ?></p>
                    <p>Size: <?= $item['size'] ?></p>
                    <p>Số lượng: <?= $item['qty'] ?></p>
                </div>

                <div class="cart-price">
                    <?= number_format($item['price'] * $item['qty']) ?>đ
                </div>
            </div>

            <?php endforeach; ?>

            <div class="checkout-total">
                <p>Tạm tính:</p>
                <p><?= number_format($total) ?>đ</p>
            </div>

            <div class="checkout-total grand">
                <p><strong>Tổng cộng:</strong></p>
                <p><strong><?= number_format($total) ?>đ</strong></p>
            </div>

            <button type="submit" class="btn-checkout">HOÀN TẤT ĐƠN HÀNG</button>

        </div>

    </form>

</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
