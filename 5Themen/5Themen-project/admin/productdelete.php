<?php
// =======================================================
// XỬ LÝ XÓA SẢN PHẨM (product_delete.php)
// =======================================================

// Include session và database để class hoạt động
include "../include/session.php";
include "../include/database.php";
require_once __DIR__ . "/Class/product_class.php";

$redirect_url = "productlist.php";

/* =======================================================
   Chỉ xử lý khi request là GET hoặc POST
======================================================= */
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: " . $redirect_url);
    exit;
}

/* =======================================================
   Lấy ID sản phẩm cần xóa
======================================================= */
$id = (int)($_REQUEST['id'] ?? 0);

if ($id > 0) {
    $pd     = new Product();
    $result = $pd->delete_product($id);

    // Có thể lưu session thông báo nếu muốn
}

/* =======================================================
   Chuyển hướng về danh sách sản phẩm
======================================================= */
header("Location: " . $redirect_url);
exit;
?>
