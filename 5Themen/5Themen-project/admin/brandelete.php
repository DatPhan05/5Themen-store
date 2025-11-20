<?php
// =======================================================
// XỬ LÝ XÓA LOẠI SẢN PHẨM/THƯƠNG HIỆU (brand_delete.php)
// =======================================================

// 1. Đảm bảo các file/class cần thiết và database được include
include "../include/session.php"; 
include "../include/database.php"; 
require_once __DIR__ . "/Class/brand_class.php";

$redirect_url = "brandlist.php";

// 2. Kiểm tra tính hợp lệ của ID
// Sử dụng $_REQUEST để linh hoạt (GET hoặc POST)
$id = (int)($_REQUEST['brand_id'] ?? 0); 

if ($id > 0) {
    $brand = new Brand();
    
    // Thực hiện xóa
    $result = $brand->delete_brand($id);
    
    // Tùy chọn: Thêm thông báo vào session (ví dụ, để hiển thị trên trang danh sách)
    if ($result) {
        // $_SESSION['delete_brand_msg'] = "✔ Xóa loại sản phẩm ID $id thành công.";
    } else {
        // $_SESSION['delete_brand_msg'] = "❌ Lỗi: Không thể xóa loại sản phẩm ID $id. 
        // Vui lòng kiểm tra ràng buộc khóa ngoại (sản phẩm có thể đang sử dụng loại này).";
    }
} 

// 3. Chuyển hướng người dùng về trang danh sách
header("Location: " . $redirect_url);
exit;
?>