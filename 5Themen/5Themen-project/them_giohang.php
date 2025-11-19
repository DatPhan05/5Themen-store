<?php
// Dùng session chung
require_once __DIR__ . '/include/session.php';

/***********************************************
 * 1. KIỂM TRA DỮ LIỆU POST
 ***********************************************/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: giohang.php");
    exit;
}

$id    = isset($_POST['id'])    ? (int)$_POST['id'] : 0;
$name  = $_POST['name']   ?? '';
$image = $_POST['image']  ?? '';
$price = isset($_POST['price']) ? (int)$_POST['price'] : 0;
$color = $_POST['color']  ?? '';
$size  = $_POST['size']   ?? '';
$qty   = isset($_POST['qty'])   ? (int)$_POST['qty'] : 1;

/***********************************************
 * 2. KIỂM TRA GIÁ TRỊ HỢP LỆ
 ***********************************************/
if ($id <= 0 || $qty <= 0) {
    header("Location: giohang.php");
    exit;
}

/***********************************************
 * 3. KHỞI TẠO GIỎ NẾU CHƯA CÓ
 ***********************************************/
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/***********************************************
 * 4. THÊM HOẶC CẬP NHẬT GIỎ
 ***********************************************/
$_SESSION['cart'][$id] = [
    "name"  => $name,
    "image" => $image,
    "price" => $price,
    "color" => $color,
    "size"  => $size,
    "qty"   => $qty
];

/***********************************************
 * 5. ĐI TỚI TRANG GIỎ HÀNG
 ***********************************************/
header("Location: giohang.php");
exit;
