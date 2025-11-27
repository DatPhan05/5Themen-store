<?php
/***********************************************
 * 1. SESSION
 ***********************************************/
require_once __DIR__ . '/include/session.php';
Session::init();

/***********************************************
 * 2. XỬ LÝ ĐĂNG XUẤT
 ***********************************************/

// Xóa các biến session liên quan đến trạng thái đăng nhập
unset($_SESSION['is_logged_in']);
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);

// Tùy chọn: Xóa toàn bộ dữ liệu session và hủy session
// session_unset();
// session_destroy();

// Chuyển hướng người dùng về trang chủ hoặc trang đăng nhập
header("Location: trangchu.php"); 
exit;
?>