<?php
// =======================================================
// XỬ LÝ XÓA SẢN PHẨM (product_delete.php)
// =======================================================

// 1. Đảm bảo các file/class cần thiết được include
// Thường cần session và database để Class hoạt động.
include "../include/session.php"; 
include "../include/database.php"; 
require_once __DIR__ . "/Class/product_class.php";

$redirect_url = "productlist.php";

// 2. Chỉ thực hiện logic khi có ID và phương thức request hợp lệ (GET hoặc POST)
if ($_SERVER['REQUEST_METHOD'] !== 'GET' && $_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Nếu không phải GET hoặc POST, chuyển hướng và thoát
    header("Location: " . $redirect_url);
    exit;
}

// Lấy ID và chuyển sang số nguyên
$id = (int)($_REQUEST['id'] ?? 0);

if ($id > 0) {
    $pd = new Product();
    
    // Thực hiện xóa sản phẩm
    $result = $pd->delete_product($id);
    
    // Tùy chọn: Thêm thông báo vào session nếu xóa thành công/thất bại
    if ($result) {
        // $_SESSION['delete_msg'] = "✔ Xóa sản phẩm ID $id thành công.";
    } else {
        // $_SESSION['delete_msg'] = "❌ Lỗi: Không thể xóa sản phẩm ID $id.";
    }
} 

// 3. Chuyển hướng người dùng về trang danh sách sản phẩm
header("Location: " . $redirect_url);
exit;
?>