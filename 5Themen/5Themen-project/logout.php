<?php
require_once __DIR__ . '/include/session.php';

// Khởi tạo session đúng quy trình
Session::init();

// Xóa toàn bộ session
$_SESSION = [];
session_unset();
session_destroy();

// Xóa cookie PHPSESSID nếu có
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}

// Chuyển hướng về trang đăng nhập hoặc trang chủ
header("Location: login.php");
exit;
?>
