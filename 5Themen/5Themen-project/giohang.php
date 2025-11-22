<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/include/session.php";
require_once __DIR__ . "/include/database.php";
require_once __DIR__ . "/admin/class/product_class.php";

$productModel = new Product();

/* ================== HÀM FIX PATH ẢNH ================== */
function fixImagePath($path)
{
    if (!$path) return '';

    // Xử lý double uploads/uploads
    $path = str_replace('uploads/uploads/', 'uploads/', $path);

    // DB lưu "uploads/xxx.jpg"
    if (strpos($path, 'uploads/') === 0) {
        return 'admin/' . $path;
    }

    // DB lưu "admin/uploads/xxx.jpg"
    if (strpos($path, 'admin/uploads/') === 0) {
        return $path;
    }

    // Chỉ có tên file
    if (!str_contains($path, '/')) {
        return 'admin/uploads/' . $path;
    }

    return $path;
}

/* ================== GIỎ HÀNG SESSION ================== */
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$cart = $_SESSION['cart'];

/* ================== ADD PRODUCT QUA GET (FALLBACK) ================== */
if (isset($_GET['action']) && $_GET['action'] === "add") {

    $id   = (int)($_GET['id'] ?? 0);
    $size = $_GET['size'] ?? 'L';
    $qty  = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    if ($qty <= 0) $qty = 1;

    if ($id > 0) {
        $product = $productModel->get_product($id);

        if ($product) {
            $imgPath = fixImagePath($product['product_img']);

            if (isset($cart[$id])) {
                $cart[$id]['qty']  += $qty;
                $cart[$id]['size'] = $size;
            } else {
                $cart[$id] = [
                    "id"    => $id,
                    "name"  => $product["product_name"],
                    "price" => (float)$product["product_price"],
                    "qty"   => $qty,
                    "size"  => $size,
                    "img"   => $imgPath,
                    "image" => $imgPath
                ];
            }
            $_SESSION['cart'] = $cart;
        }
    }

    header("Location: giohang.php");
    exit;
}

/* ================== DELETE ITEM ================== */
if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    if (isset($cart[$id])) {
        unset($cart[$id]);
        $_SESSION['cart'] = $cart;
    }
    header("Location: giohang.php");
    exit;
}

/* ================== BREADCRUMB ================== */
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
    ['text' => 'Giỏ hàng']
];

/* ================== TÍNH TỔNG ================== */
$total     = 0;
$itemCount = 0;
foreach ($cart as $item) {
    $total     += $item['price'] * $item['qty'];
    $itemCount += $item['qty'];
}
?>

<?php 
require __DIR__ . "/partials/header.php"; 
require __DIR__ . "/partials/breadcrumb.php"; 
?>

<!-- ======================= CSS TÍCH HỢP TRỰC TIẾP ======================= -->
<style>
:root {
    --color-bg: #f5f5f7;
    --color-bg-soft: #ffffff;
    --color-primary: #111827;
    --color-accent: #fbbf24;
    --color-accent-soft: #fef3c7;
    --color-text: #111827;
    --color-text-muted: #6b7280;
    --color-border: #e5e7eb;
    --radius-lg: 18px;
    --shadow-soft: 0 18px 40px rgba(15,23,42,0.08);
    --transition-fast: all .18s ease-out;
    --font-main: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}

*,
*::before,
*::after {
    box-sizing: border-box;
}

body {
    background: var(--color-bg);
    font-family: var(--font-main);
}

/* WRAPPER */
.cart-icondenim {
    padding: 32px 0 52px;
}

.cart-container {
    max-width: 1160px;
    margin: 0 auto;
    padding: 0 16px;
    display: flex;
    gap: 26px;
    align-items: flex-start;
}

/* CARD CHUNG */
.cart-left,
.cart-right {
    background: var(--color-bg-soft);
    border-radius: var(--radius-lg);
    padding: 20px 18px 22px;
    box-shadow: var(--shadow-soft);
}

.cart-left  { flex: 1.05; }
.cart-right { flex: 1; }

/* TIÊU ĐỀ */
.section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--color-primary);
    margin: 0 0 10px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.section-title::before {
    content: "";
    width: 6px;
    height: 18px;
    border-radius: 999px;
    background: var(--color-accent);
}

/* FORM THÔNG TIN */
.cart-form {
    margin-bottom: 16px;
}

.cart-form .form-group {
    margin-bottom: 12px;
}

.input,
.cart-form input[type="text"] {
    width: 100%;
    padding: 11px 12px;
    border-radius: 12px;
    border: 1px solid var(--color-border);
    background: #fff;
    font-size: 14px;
    outline: none;
    transition: var(--transition-fast);
}

.input:focus,
.cart-form input[type="text"]:focus {
    border-color: var(--color-accent);
    box-shadow: 0 0 0 1px rgba(251,191,36,0.4);
}

.input-error {
    border-color: #f97373 !important;
    box-shadow: 0 0 0 1px rgba(248,113,113,0.35);
}

.error-msg {
    display: block;
    font-size: 12px;
    color: #dc2626;
    margin-top: 4px;
}

/* SHIPPING / PAYMENT */
.cart-left h2 + .ship-option,
.cart-left h2 + .pay-item {
    margin-top: 6px;
}

.ship-option,
.pay-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 11px 12px;
    border-radius: 14px;
    background: #fff;
    border: 1px solid var(--color-border);
    margin-bottom: 8px;
    cursor: pointer;
    transition: var(--transition-fast);
    font-size: 14px;
    color: var(--color-text-muted);
}

.ship-option input,
.pay-item input {
    accent-color: var(--color-accent);
}

.ship-option img,
.pay-item img {
    height: 20px;
    width: auto;
    object-fit: contain;
}

.ship-option.active,
.pay-item.active,
.ship-option:hover,
.pay-item:hover {
    border-color: var(--color-accent);
    background: var(--color-accent-soft);
    color: var(--color-text);
}

/* CART RIGHT */
.cart-right-header {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 4px;
}

.cart-right-subtitle {
    font-size: 13px;
    color: var(--color-text-muted);
}

/* CART ITEM */
.cart-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.cart-item:last-of-type {
    border-bottom: none;
}

.cart-thumb {
    width: 84px;
    height: 104px;
    border-radius: 12px;
    object-fit: cover;
    border: 1px solid var(--color-border);
    background: #f9fafb;
}

.item-info {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    gap: 6px;
}

.item-name {
    font-size: 14px;
    font-weight: 600;
    color: var(--color-primary);
}

.item-meta {
    font-size: 12px;
    color: var(--color-text-muted);
}

/* SIZE SELECT */
.item-size select {
    padding: 6px 30px 6px 10px;
    border-radius: 999px;
    border: 1px solid var(--color-border);
    font-size: 13px;
    background: #f9fafb;
    outline: none;
    cursor: pointer;
    transition: var(--transition-fast);
}

.item-size select:focus {
    border-color: var(--color-accent);
    background: #fff7e6;
}

/* QTY */
.qty-box {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
}

.qty-box a {
    width: 26px;
    height: 26px;
    border-radius: 999px;
    border: 1px solid var(--color-border);
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    font-size: 17px;
    font-weight: 500;
    text-decoration: none;
    color: var(--color-text);
    transition: var(--transition-fast);
}

.qty-box a:hover {
    border-color: var(--color-accent);
    background: var(--color-accent-soft);
}

/* PRICE + REMOVE */
.price {
    font-size: 14px;
    font-weight: 600;
    text-align: right;
    min-width: 90px;
    color: var(--color-primary);
}

.remove {
    font-size: 22px;
    color: #9ca3af;
    cursor: pointer;
    text-decoration: none;
    margin-left: 6px;
    transition: var(--transition-fast);
}

.remove:hover {
    color: #e11d48;
}

/* EMPTY CART */
.cart-empty {
    padding: 10px 0 6px;
    font-size: 14px;
    color: var(--color-text-muted);
}

/* TOTAL */
.cart-total {
    margin-top: 16px;
    border-top: 1px solid var(--color-border);
    padding-top: 12px;
}

.total-row {
    display: flex;
    justify-content: space-between;
    font-size: 14px;
    margin-bottom: 4px;
}

.total-row strong {
    font-size: 15px;
}

/* CHECKOUT BUTTON */
.btn-checkout {
    margin-top: 14px;
    width: 100%;
    padding: 12px;
    border-radius: 999px;
    background: var(--color-primary);
    color: #fff;
    font-weight: 600;
    font-size: 15px;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: var(--transition-fast);
}

.btn-checkout:hover {
    background: #000;
    transform: translateY(-1px);
}

.btn-checkout span.icon {
    font-size: 16px;
}

/* RESPONSIVE */
@media (max-width: 900px) {
    .cart-container {
        flex-direction: column;
    }
}

@media (max-width: 640px) {
    .cart-left,
    .cart-right {
        padding: 16px 14px 18px;
    }

    .cart-item {
        align-items: flex-start;
    }

    .cart-thumb {
        width: 78px;
        height: 96px;
    }

    .price {
        min-width: 70px;
        font-size: 13px;
    }

    .section-title {
        font-size: 16px;
    }
}
</style>

<section class="cart-icondenim">
    <div class="cart-container">

        <!-- LEFT – THÔNG TIN & THANH TOÁN -->
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
                Thanh toán COD
            </label>

            <label class="pay-item">
                <input type="radio" name="payment" value="vnpay">
                <img src="images/vnpay.png" alt="VNPay">
                Thanh toán VNPay
            </label>

            <label class="pay-item">
                <input type="radio" name="payment" value="momo">
                <img src="images/momo.png" alt="MoMo">
                Thanh toán MoMo
            </label>

        </div>

        <!-- RIGHT – GIỎ HÀNG -->
        <div class="cart-right">
            <div class="cart-right-header">
                <h2 class="section-title">Giỏ hàng</h2>
                <div class="cart-right-subtitle">
                    <?= $itemCount > 0 ? ("Đã chọn " . $itemCount . " sản phẩm") : "Chưa có sản phẩm nào" ?>
                </div>
            </div>

<?php if (!empty($cart)): ?>
    <?php foreach ($cart as $id => $item): ?>
        <?php
            $name  = $item['name'];
            $price = (float)$item['price'];
            $qty   = (int)$item['qty'];
            $size  = $item['size'];

            $imagePath = fixImagePath($item['image'] ?? $item['img']);
            $lineTotal = $price * $qty;
        ?>

        <div class="cart-item">
            <img src="<?= htmlspecialchars($imagePath) ?>" class="cart-thumb" alt="<?= htmlspecialchars($name) ?>">

            <div class="item-info">
                <div>
                    <div class="item-name"><?= htmlspecialchars($name) ?></div>
                    <div class="item-meta">Size đang chọn: <strong><?= htmlspecialchars($size) ?></strong></div>
                </div>

                <!-- SIZE -->
                <div class="item-size">
                    <form action="them_giohang.php" method="GET">
                        <input type="hidden" name="action" value="changesize">
                        <input type="hidden" name="id" value="<?= (int)$id ?>">
                        <select name="size" onchange="this.form.submit()">
                            <option value="S"  <?= $size==='S'?'selected':'' ?>>S</option>
                            <option value="M"  <?= $size==='M'?'selected':'' ?>>M</option>
                            <option value="L"  <?= $size==='L'?'selected':'' ?>>L</option>
                            <option value="XL" <?= $size==='XL'?'selected':'' ?>>XL</option>
                        </select>
                    </form>
                </div>

                <!-- QTY -->
                <div class="qty-box">
                    <a href="them_giohang.php?action=update&id=<?= (int)$id ?>&qty=<?= max(1,$qty-1) ?>">−</a>
                    <span><?= $qty ?></span>
                    <a href="them_giohang.php?action=update&id=<?= (int)$id ?>&qty=<?= $qty+1 ?>">+</a>
                </div>
            </div>

            <div class="price"><?= number_format($lineTotal,0,',','.') ?>đ</div>

            <a href="giohang.php?delete=<?= (int)$id ?>" class="remove" title="Xóa sản phẩm">&times;</a>
        </div>

    <?php endforeach; ?>
<?php else: ?>
    <div class="cart-empty">
        Giỏ hàng của bạn đang trống. Hãy thêm sản phẩm để tiến hành đặt hàng nhé!
    </div>
<?php endif; ?>

            <div class="cart-total">
                <div class="total-row">
                    <span>Tạm tính:</span>
                    <span><?= number_format($total,0,',','.') ?>đ</span>
                </div>
                <div class="total-row">
                    <strong>Tổng cộng:</strong>
                    <strong><?= number_format($total,0,',','.') ?>đ</strong>
                </div>

                <button type="button" class="btn-checkout" onclick="processCheckout()">
                    <span class="icon">🧾</span>
                    <span>Thanh toán</span>
                </button>
            </div>

        </div>
    </div>
</section>

<script>
// Toggle active style cho ship & payment
document.addEventListener('DOMContentLoaded', function() {
    // Payment
    const payLabels = document.querySelectorAll('.pay-item');
    payLabels.forEach(label => {
        const input = label.querySelector('input[type="radio"]');
        input.addEventListener('change', () => {
            payLabels.forEach(l => l.classList.remove('active'));
            label.classList.add('active');
        });
    });

    // Ship (nếu sau này có nhiều option)
    const shipLabels = document.querySelectorAll('.ship-option');
    shipLabels.forEach(label => {
        const input = label.querySelector('input[type="radio"]');
        input.addEventListener('change', () => {
            shipLabels.forEach(l => l.classList.remove('active'));
            label.classList.add('active');
        });
    });
});

function processCheckout() {

    let fullname = document.querySelector('input[name="fullname"]');
    let phone    = document.querySelector('input[name="phone"]');
    let address  = document.querySelector('input[name="address"]');

    // Reset lỗi
    document.querySelectorAll('.error-msg').forEach(e => e.innerText = "");
    document.querySelectorAll('.input').forEach(e => e.classList.remove('input-error'));

    let ok = true;

    if (fullname.value.trim() === "") {
        ok = false;
        fullname.classList.add('input-error');
        document.getElementById('err-fullname').innerText = "Vui lòng nhập họ tên!";
    }

    const phoneRegex = /^(0[0-9]{9})$/;
    if (!phoneRegex.test(phone.value.trim())) {
        ok = false;
        phone.classList.add('input-error');
        document.getElementById('err-phone').innerText = "Số điện thoại không hợp lệ!";
    }

    if (address.value.trim() === "") {
        ok = false;
        address.classList.add('input-error');
        document.getElementById('err-address').innerText = "Địa chỉ không được trống!";
    }

    if (!ok) return;

    let checked = document.querySelector('input[name="payment"]:checked');
    let method  = checked ? checked.value : 'cod';

    // Tạo form submit sang thanhtoan.php
    let form = document.createElement("form");
    form.method = "POST";
    form.action = "thanhtoan.php?method=" + encodeURIComponent(method);

    form.innerHTML = `
        <input type="hidden" name="fullname" value="${fullname.value.replace(/"/g,'&quot;')}">
        <input type="hidden" name="phone" value="${phone.value.replace(/"/g,'&quot;')}">
        <input type="hidden" name="address" value="${address.value.replace(/"/g,'&quot;')}">
    `;

    document.body.appendChild(form);
    form.submit();
}
</script>

<?php require __DIR__ . "/partials/footer.php"; ?>
