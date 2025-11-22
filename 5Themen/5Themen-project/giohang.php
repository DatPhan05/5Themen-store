<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/include/session.php";
require_once __DIR__ . "/include/database.php";
require_once __DIR__ . "/admin/class/product_class.php";

$productModel = new Product();

/* ================== XỬ LÝ THÊM SẢN PHẨM VÀO GIỎ ================== */
if (isset($_GET['action']) && $_GET['action'] === "add") {

    $id = (int)$_GET['id'];

    // Lấy thông tin sản phẩm từ DB
    $product = $productModel->get_product($id);

    if ($product) {

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Nếu SP đã tồn tại thì tăng SL
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty']++;
        } else {
            // Nếu chưa có thì tạo mới
            $_SESSION['cart'][$id] = [
                "name"  => $product["product_name"],
                "price" => $product["product_price"],
                "qty"   => 1,
                "image" => $product["product_img"],   // ảnh từ DB
                "color" => "",                        // tạm
                "size"  => "L"                        // tạm
            ];
        }
    }

    header("Location: giohang.php");
    exit;
}

/* ================== XÓA SẢN PHẨM TRONG GIỎ ================== */
if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    if (isset($_SESSION["cart"][$id])) {
        unset($_SESSION["cart"][$id]);
    }
    header("Location: giohang.php");
    exit;
}

/* ================== BREADCRUMB ================== */
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
    ['text' => 'Giỏ hàng']
];
?>

<?php require __DIR__ . "/partials/header.php"; ?>

<?php require __DIR__ . "/partials/breadcrumb.php"; ?>

<section class="cart-icondenim">
    <div class="container cart-container">

        <!-- CỘT TRÁI -->
        <div class="cart-left">

            <h2 class="section-title">Thông tin đơn hàng</h2>

            <form class="cart-form">
                <div class="form-group">
                    <input class="input" type="text" name="fullname" placeholder="Họ và tên">
                    <span class="error-msg" id="err-fullname"></span>
                </div>

                <div class="form-group">
                    <input class="input" type="text" name="phone" placeholder="Số điện thoại">
                    <span class="error-msg" id="err-phone"></span>
                </div>
                <div class="form-group">
                    <input class="input" type="text" name="address" placeholder="Địa chỉ nhận hàng">
                    <span class="error-msg" id="err-address"></span>
                </div>
            </form>

            <h2 class="section-title">Phương thức vận chuyển</h2>

            <label class="ship-option active">
                <input type="radio" name="ship" checked>
                Freeship đơn hàng
            </label>

            <h2 class="section-title">Hình thức thanh toán</h2>

            <label class="pay-item active">
                <input type="radio" name="payment" value="cod" checked>
                <img src="images/cod.png" alt="COD">
                Thanh toán khi nhận hàng (COD)
            </label>

            <label class="pay-item">
                <input type="radio" name="payment" value="vnpay">
                <img src="images/vnpay.png" alt="VNPay">
                Thanh toán qua VNPay
            </label>

            <label class="pay-item">
                <input type="radio" name="payment" value="momo">
                <img src="images/momo.png" alt="MoMo">
                Thanh toán MoMo
            </label>

        </div>

        <!-- CỘT PHẢI -->
        <div class="cart-right">

            <h2 class="section-title">Giỏ hàng</h2>

            <?php if (!empty($_SESSION['cart'])): ?>
                <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($item['image']) ?>" class="cart-thumb" alt="Ảnh sản phẩm">
                        
                        <div class="item-info">
                            <div class="item-name"><?= htmlspecialchars($item['name']) ?></div>
                            <div class="item-size">
                                <form action="them_giohang.php" method="GET">
                                    <input type="hidden" name="action" value="changesize">
                                    <input type="hidden" name="id" value="<?= $id ?>">

                                    <select name="size" onchange="this.form.submit()">
                                        <option value="S" <?= $item['size'] == 'S' ? 'selected' : '' ?>>S</option>
                                        <option value="M" <?= $item['size'] == 'M' ? 'selected' : '' ?>>M</option>
                                        <option value="L" <?= $item['size'] == 'L' ? 'selected' : '' ?>>L</option>
                                        <option value="XL" <?= $item['size'] == 'XL' ? 'selected' : '' ?>>XL</option>
                                    </select>
                                </form>
                            </div>

                            

                            <div class="qty-box">
                                <a href="them_giohang.php?action=update&id=<?= $id ?>&qty=<?= max(1, $item['qty'] - 1) ?>">−</a>
                                <span><?= $item['qty'] ?></span>
                                <a href="them_giohang.php?action=update&id=<?= $id ?>&qty=<?= $item['qty'] + 1 ?>">+</a>
                            </div>
                        </div>

                        <div class="price"><?= number_format($item['price'] * $item['qty'], 0, ',', '.') ?>đ</div>

                        <a href="giohang.php?delete=<?= $id ?>" class="remove">&times;</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Giỏ hàng trống.</p>
            <?php endif; ?>

            <?php
                $total = 0;
                if (!empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $total += $item['price'] * $item['qty'];
                    }
                }
            ?>

            <div class="cart-total">
                <div class="total-row">
                    <span>Tạm tính:</span>
                    <span><?= number_format($total, 0, ',', '.') ?>đ</span>
                </div>

                <div class="total-row total-final">
                    <strong>Tổng cộng:</strong>
                    <span class="text-main"><strong><?= number_format($total, 0, ',', '.') ?>đ</strong></span>
                </div>

                <button type="button" class="btn-checkout" onclick="processCheckout()">
                    Thanh toán
                </button>
            </div>

        </div>

    </div>
</section>

<script>
function processCheckout() {

    let ok = true;

    // Lấy input
    const fullname = document.querySelector('input[name="fullname"]');
    const phone    = document.querySelector('input[name="phone"]');
    const address  = document.querySelector('input[name="address"]');

    // Reset lỗi cũ
    document.querySelectorAll('.error-msg').forEach(e => e.innerText = "");
    document.querySelectorAll('.input').forEach(e => e.classList.remove('input-error'));

    // Validate fullname
    if (fullname.value.trim() === "") {
        ok = false;
        fullname.classList.add('input-error');
        document.getElementById('err-fullname').innerText = "Vui lòng nhập họ tên!";
    }

    // Validate phone
    const phoneRegex = /^(0[0-9]{9})$/;

    if (phone.value.trim() === "") {
        ok = false;
        phone.classList.add('input-error');
        document.getElementById('err-phone').innerText = "Số điện thoại không được trống!";
    }
    else if (!phoneRegex.test(phone.value.trim())) {
        ok = false;
        phone.classList.add('input-error');
        document.getElementById('err-phone').innerText = "Số điện thoại không hợp lệ!";
    }

    // Validate address
    if (address.value.trim() === "") {
        ok = false;
        address.classList.add('input-error');
        document.getElementById('err-address').innerText = "Địa chỉ không được trống!";
    }

    // Nếu có lỗi → DỪNG
    if (!ok) return;

    // Kiểm tra phương thức thanh toán
    const checked = document.querySelector('input[name="payment"]:checked');
    if (!checked) {
        alert('Vui lòng chọn phương thức thanh toán');
        return;
    }

    const method = checked.value;

    // TẤT CẢ OK → chuyển trang
    window.location.href = 'thanhtoan.php?method=' + method;
}

</script>

<?php require __DIR__ . "/partials/footer.php"; ?>
