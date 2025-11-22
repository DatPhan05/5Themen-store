<?php
// =======================================================
// XỬ LÝ XÓA DANH MỤC (categorydelete.php)
// =======================================================

// 1. Đảm bảo các file/class cần thiết được include
// Cần session và database (hoặc cấu hình) cho Class hoạt động
include "../include/session.php"; 
include "../include/database.php"; 
require_once __DIR__ . "/Class/category_class.php";

$redirect_url = "categorylist.php";

// 2. Kiểm tra phương thức request (Tùy chọn, nên sử dụng POST cho hành động thay đổi dữ liệu)
// Tuy nhiên, đối với link xóa đơn giản, thường sử dụng GET. Ta kiểm tra request có chứa ID không.
if (!isset($_REQUEST['category_id'])) {
    // Nếu không có ID, chuyển hướng và thoát ngay lập tức
    header("Location: " . $redirect_url);
    exit;
}

// 3. Lấy ID và chuyển sang số nguyên
$id = (int)($_REQUEST['category_id']);

if ($id > 0) {
    $cg = new Category();
    
    // Lưu ý về bảo mật: Việc xóa qua link GET dễ bị tấn công CSRF. 
    // Nên sử dụng Token CSRF hoặc chuyển sang phương thức POST.
    
    // Thực hiện xóa danh mục
    $result = $cg->delete_category($id);
    
    // Tùy chọn: Thêm thông báo vào session (ví dụ, để hiển thị trên trang danh sách)
    if ($result) {
        // $_SESSION['delete_msg'] = "✔ Xóa danh mục ID $id thành công.";
    } else {
        // $_SESSION['delete_msg'] = "❌ Lỗi: Không thể xóa danh mục ID $id (có thể do ràng buộc khóa ngoại).";
    }
} 

// 4. Chuyển hướng người dùng về trang danh sách danh mục
header("Location: " . $redirect_url);
exit;
?>