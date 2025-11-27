<?php
require_once "../include/session.php";
require_once "../include/database.php";
require_once __DIR__ . "/class/brand_class.php";

$redirect_url = "brandlist.php";

// Lấy ID brand cần xóa
$id = (int)($_REQUEST['brand_id'] ?? 0);

if ($id > 0) {
    $brand = new Brand();
    $brand->delete_brand($id);
}

// Chuyển hướng về danh sách
header("Location: $redirect_url");
exit;
?>
