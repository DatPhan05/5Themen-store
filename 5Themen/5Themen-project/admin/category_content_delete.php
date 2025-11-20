<?php
// =======================================================
// XỬ LÝ XÓA NỘI DUNG DANH MỤC (category_content_delete.php)
// =======================================================

// 1. Đảm bảo các file/class cần thiết và database được include
include "../include/session.php"; 
include "../include/database.php"; 
require_once __DIR__ . "/class/category_content_class.php";

$redirect_url = "category_content_list.php";

// 2. Kiểm tra tính hợp lệ của ID
// Sử dụng $_REQUEST để linh hoạt hơn (GET hoặc POST)
$id = (int)($_REQUEST['id'] ?? 0); 

if ($id > 0) {
    $ct = new CategoryContent();
    
    // Thực hiện xóa nội dung
    $result = $ct->delete($id);
    
    // Tùy chọn: Thêm thông báo vào session để hiển thị trên trang danh sách
    if ($result) {
        // $_SESSION['delete_content_msg'] = "✔ Xóa nội dung ID $id thành công.";
    } else {
        // $_SESSION['delete_content_msg'] = "❌ Lỗi: Không thể xóa nội dung ID $id.";
    }
} 

// 3. Chuyển hướng người dùng về trang danh sách
header("Location: " . $redirect_url);
exit;
?>