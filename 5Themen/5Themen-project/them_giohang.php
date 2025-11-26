<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/include/database.php';

/* ================== FIX ĐƯỜNG DẪN ẢNH ================== */
function fixImagePath($path)
{
    if (!$path) return '';

    $path = str_replace('uploads/uploads/', 'uploads/', $path);

    if (strpos($path, 'uploads/') === 0) return 'admin/' . $path;
    if (strpos($path, 'admin/uploads/') === 0) return $path;

    if (!str_contains($path, '/')) {
        return 'admin/uploads/' . $path;
    }

    return $path;
}

/* ================== GIỎ HÀNG SESSION ================== */
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
$cart =& $_SESSION['cart'];

/* ================== LẤY ACTION & PRODUCT_ID ================== */
$action = $_GET['action'] ?? 'add';

$product_id = isset($_POST['product_id'])
    ? (int)$_POST['product_id']
    : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if ($product_id <= 0) {
    header("Location: trangchu.php");
    exit;
}

/* =======================================================
   1. UPDATE QTY
   ======================================================= */
if ($action === 'update') {
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    if ($qty <= 0) $qty = 1;

    // CHỈ SỬA SỐ LƯỢNG TRONG SESSION
    if (isset($cart[$product_id])) {
        $cart[$product_id]['qty'] = $qty;
    }

    header("Location: giohang.php");
    exit;
}

/* =======================================================
   2. CHANGE SIZE
   ======================================================= */
if ($action === 'changesize') {
    $size = $_GET['size'] ?? 'L';

    if (isset($cart[$product_id])) {
        $cart[$product_id]['size'] = $size;
    }

    header("Location: giohang.php");
    exit;
}

/* =======================================================
   3. REMOVE
   ======================================================= */
if ($action === 'remove') {
    unset($cart[$product_id]);
    header("Location: giohang.php");
    exit;
}

/* =======================================================
   4. ADD PRODUCT
   ======================================================= */

$size = $_POST['option2'] ?? ($_GET['size'] ?? 'L');

$qty = isset($_POST['quantity'])
    ? (int)$_POST['quantity']
    : (isset($_GET['qty']) ? (int)$_GET['qty'] : 1);

if ($qty <= 0) $qty = 1;

// Lấy sản phẩm trong DB
$db   = new Database();
$conn = $db->link;

$sql = "SELECT * FROM tbl_product WHERE product_id = $product_id LIMIT 1";
$rs  = $conn->query($sql);

if (!$rs || $rs->num_rows === 0) {
    header("Location: trangchu.php");
    exit;
}

$product = $rs->fetch_assoc();

// TÍNH GIÁ SALE ĐÚNG
$finalPrice = ($product['product_sale'] > 0 &&
               $product['product_sale'] < $product['product_price'])
               ? (float)$product['product_sale']
               : (float)$product['product_price'];

$imgPath = fixImagePath($product['product_img']);

if (isset($cart[$product_id])) {
    // Đã có trong giỏ → cộng dồn
    $cart[$product_id]['qty']   += $qty;
    $cart[$product_id]['size']   = $size;
    $cart[$product_id]['price']  = $finalPrice;   // luôn giữ giá đúng
    $cart[$product_id]['img']    = $imgPath;
    $cart[$product_id]['image']  = $imgPath;
} else {
    // Thêm mới
    $cart[$product_id] = [
        'id'    => $product_id,
        'name'  => $product['product_name'],
        'price' => $finalPrice,
        'qty'   => $qty,
        'size'  => $size,
        'img'   => $imgPath,
        'image' => $imgPath
    ];
}

// Nếu bấm Mua Ngay → chuyển luôn tới giỏ
if (isset($_POST['buy_now'])) {
    header("Location: giohang.php?buynow=1");
    exit;
}

header("Location: giohang.php");
exit;
