<?php

require_once __DIR__ . '/include/session.php';
Session::init();
// Xóa các biến session liên quan đến trạng thái đăng nhập
unset($_SESSION['is_logged_in']);
unset($_SESSION['user_id']);
unset($_SESSION['user_name']);

header("Location: trangchu.php"); 
exit;
?>