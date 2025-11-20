<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/admin/class/product_class.php';

$productModel = new Product();

/***********************************************
 * 1. HỖ TRỢ CẢ POST LẪN GET
 ***********************************************/
$id    = 0;
$qty   = 1;
$size  = "";
$color = "";

// POST (từ product_detail.php)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id    = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $qty   = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    $size  = $_POST['option2'] ?? "";
    $color = ""; // bạn chưa có color, tạm để rỗng
}
// GET (từ category.php)
elseif (isset($_GET['action']) && $_GET['action'] === "add") {
    $id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    $qty = 1;
}

if ($id <= 0) {
    header("Location: giohang.php");
    exit;
}

/***********************************************
 * 2. LẤY SẢN PHẨM TỪ DATABASE
 ***********************************************/
$product = $productModel->get_product($id);

if (!$product) {
    header("Location: giohang.php");
    exit;
}

/***********************************************
 * 3. TẠO GIỎ NẾU CHƯA CÓ
 ***********************************************/
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/***********************************************
 * 4. THÊM HOẶC CỘNG DỒN SẢN PHẨM
 ***********************************************/
if (!isset($_SESSION['cart'][$id])) {
    $_SESSION['cart'][$id] = [
        "name"  => $product["product_name"],
        "price" => (float) $product["product_price"],
        "qty"   => $qty,
        "image" => $product["product_img"],
        "color" => $color,
        "size"  => $size ?: "L"
    ];
} else {
    $_SESSION['cart'][$id]['qty'] += $qty;
}

/***********************************************
 * 5. CHUYỂN VỀ TRANG GIỎ
 ***********************************************/
header("Location: giohang.php");
exit;
