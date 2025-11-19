<?php
// Dùng hệ thống session chung
require_once __DIR__ . '/include/session.php';

// Lấy ID sản phẩm cần xóa
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Nếu tồn tại trong giỏ thì xóa
if ($id > 0 && isset($_SESSION['cart'][$id])) {
    unset($_SESSION['cart'][$id]);
}

// Quay về lại trang giỏ hàng
header("Location: giohang.php");
exit;
