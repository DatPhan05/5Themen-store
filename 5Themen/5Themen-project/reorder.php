<?php
// MUA LẠI ĐƠN HÀNG: copy các item của order vào giỏ hàng

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

Session::init();

$db   = new Database();
$conn = $db->link;

// Bắt buộc đăng nhập
if (empty($_SESSION['is_logged_in']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id  = (int)$_SESSION['user_id'];
$orderId  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($orderId <= 0) {
    header("Location: account.php?view=orders");
    exit;
}

// Lấy SĐT user để đảm bảo chỉ reorder đơn của chính mình
$sqlUser = "SELECT phone FROM tbl_user WHERE user_id = '$user_id' LIMIT 1";
$rsUser  = $conn->query($sqlUser);
if (!$rsUser || $rsUser->num_rows === 0) {
    header("Location: logout.php");
    exit;
}
$userData  = $rsUser->fetch_assoc();
$userPhone = trim($userData['phone'] ?? '');

// Query lấy các item của order
$wherePhone = "";
if ($userPhone !== '') {
    $phoneEsc   = $conn->real_escape_string($userPhone);
    $wherePhone = " AND o.phone = '$phoneEsc' ";
}

$sql = "
    SELECT 
        od.product_id,
        od.qty,
        od.price,
        od.size,
        p.product_name,
        p.product_img
    FROM tbl_order o
    JOIN tbl_order_detail od ON o.order_id = od.order_id
    JOIN tbl_product p       ON od.product_id = p.product_id
    WHERE o.order_id = $orderId
    $wherePhone
";

$rs = $conn->query($sql);
if (!$rs || $rs->num_rows === 0) {
    // Không có quyền hoặc order không tồn tại
    header("Location: account.php?view=orders");
    exit;
}

// Đảm bảo có giỏ
if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Hàm xử lý path ảnh giống chỗ khác
function buildProductImgPath($img)
{
    $img = (string)$img;
    if ($img === '') return '';
    if (strpos($img, 'admin/uploads/') === 0) return $img;
    return 'admin/uploads/' . ltrim($img, '/');
}

// Thêm từng item vào giỏ
while ($row = $rs->fetch_assoc()) {
    $pid   = (int)$row['product_id'];
    $qty   = (int)$row['qty'];
    $price = (float)$row['price'];
    $size  = $row['size'] ?: 'L';

    $imgPath = buildProductImgPath($row['product_img'] ?? '');

    // Nếu đã có product trong giỏ -> cộng dồn số lượng
    if (isset($_SESSION['cart'][$pid])) {
        $_SESSION['cart'][$pid]['qty']  += $qty;
        $_SESSION['cart'][$pid]['size'] = $size; // cập nhật size theo đơn cũ
    } else {
        $_SESSION['cart'][$pid] = [
            'id'    => $pid,
            'name'  => $row['product_name'],
            'price' => $price,
            'qty'   => $qty,
            'size'  => $size,
            'img'   => $imgPath,
            'image' => $imgPath,
        ];
    }
}

// Sau khi copy xong → quay về giỏ hàng
header("Location: giohang.php");
exit;
