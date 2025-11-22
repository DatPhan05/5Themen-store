<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/include/database.php';

// --------------------
// FIX PATH ẢNH (HÀM QUAN TRỌNG)
// --------------------
function fixImagePath($path)
{
    if (!$path) return '';

    // Xóa trường hợp double uploads/uploads
    $path = str_replace('uploads/uploads/', 'uploads/', $path);

    // Nếu DB lưu dạng "uploads/xxx.jpg"
    if (strpos($path, 'uploads/') === 0) {
        return 'admin/' . $path;
    }

    // Nếu DB lưu dạng "admin/uploads/xxx.jpg"
    if (strpos($path, 'admin/uploads/') === 0) {
        return $path;
    }

    // Nếu chỉ là tên file "abc.jpg"
    if (!str_contains($path, '/')) {
        return 'admin/uploads/' . $path;
    }

    return $path;
}

// ----------------------------------------------------
// Đảm bảo luôn có giỏ hàng
// ----------------------------------------------------
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_GET['action'] ?? 'add';

// Lấy product_id
$product_id = isset($_POST['product_id'])
    ? (int)$_POST['product_id']
    : (isset($_GET['id']) ? (int)$_GET['id'] : 0);

if ($product_id <= 0) {
    header("Location: trangchu.php");
    exit;
}

// ================= UPDATE QTY =================
if ($action === 'update') {
    $qty = isset($_GET['qty']) ? (int)$_GET['qty'] : 1;
    if ($qty <= 0) $qty = 1;

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['qty'] = $qty;
    }

    header("Location: giohang.php");
    exit;
}

// ================= CHANGE SIZE =================
if ($action === 'changesize') {
    $size = $_GET['size'] ?? 'L';

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['size'] = $size;
    }

    header("Location: giohang.php");
    exit;
}

// ================= REMOVE =================
if ($action === 'remove') {
    unset($_SESSION['cart'][$product_id]);
    header("Location: giohang.php");
    exit;
}

// ================= ADD PRODUCT =================
$size = $_POST['option2'] ?? ($_GET['size'] ?? 'L');

$qty = isset($_POST['quantity'])
    ? (int)$_POST['quantity']
    : (isset($_GET['qty']) ? (int)$_GET['qty'] : 1);

if ($qty <= 0) $qty = 1;

// Query sản phẩm
$db   = new Database();
$conn = $db->link;

$sql = "SELECT * FROM tbl_product WHERE product_id = $product_id LIMIT 1";
$rs  = $conn->query($sql);

if (!$rs || $rs->num_rows === 0) {
    header("Location: trangchu.php");
    exit;
}

$product = $rs->fetch_assoc();

// PATH ẢNH CHUẨN 100%
$imgPath = fixImagePath($product['product_img']);

if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['qty']  += $qty;
    $_SESSION['cart'][$product_id]['size'] = $size;
} else {
    $_SESSION['cart'][$product_id] = [
        'id'    => $product_id,
        'name'  => $product['product_name'],
        'price' => (float)$product['product_price'],
        'qty'   => $qty,
        'size'  => $size,
        'img'   => $imgPath,
        'image' => $imgPath
    ];
}

// Nếu bấm Mua Ngay → vào giỏ ngay
if (isset($_POST['buy_now'])) {
    header("Location: giohang.php?buynow=1");
    exit;
}

header("Location: giohang.php");
exit;
