<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: thanhtoan.php");
    exit;
}

$db = new Database();

// ----------------------------
// 1. LẤY DATA TỪ FORM
// ----------------------------
$fullname = trim($_POST['fullname'] ?? '');
$email    = trim($_POST['email'] ?? '');
$phone    = trim($_POST['phone'] ?? '');
$address  = trim($_POST['address'] ?? '');
$method   = trim($_POST['method'] ?? 'cod');

// Kiểm tra đơn giản
if ($fullname == '' || $email == '' || $phone == '' || $address == '') {
    die("Thiếu thông tin giao hàng!");
}

if (empty($_SESSION['cart'])) {
    die("Giỏ hàng rỗng!");
}

// ----------------------------
// 2. TÍNH TỔNG TIỀN
// ----------------------------
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}


// ----------------------------
// 3. TẠO ĐƠN HÀNG
// ----------------------------
$uid = $_SESSION['user_id'] ?? "NULL";

$sql_order = "
INSERT INTO tbl_order (user_id, fullname, email, phone, address, payment_method, total_amount)
VALUES ($uid, 
        '{$db->escape($fullname)}',
        '{$db->escape($email)}',
        '{$db->escape($phone)}',
        '{$db->escape($address)}',
        '{$db->escape($method)}',
        $total)
";

$db->insert($sql_order);

// Lấy ID đơn hàng vừa tạo
$order_id = $db->link->insert_id;


// ----------------------------
// 4. LƯU SẢN PHẨM TRONG ĐƠN
// ----------------------------
foreach ($_SESSION['cart'] as $pid => $item) {

    $pname = $db->escape($item['name']);
    $price = (int)$item['price'];
    $qty   = (int)$item['qty'];
    $size  = $db->escape($item['size']);
    $color = $db->escape($item['color']);

    $sql_item = "
    INSERT INTO tbl_order_items(order_id, product_id, product_name, price, qty, size, color)
    VALUES ($order_id, $pid, '$pname', $price, $qty, '$size', '$color')
    ";

    $db->insert($sql_item);
}

// ----------------------------
// 5. XOÁ GIỎ HÀNG & CHUYỂN TRANG
// ----------------------------
unset($_SESSION['cart']);

header("Location: order_success.php?id=" . $order_id);
exit;
