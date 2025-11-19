<?php
// Sử dụng hệ thống session chung
require_once __DIR__ . '/include/session.php';

// Xoá tất cả session
session_unset();
session_destroy();

// Chuyển hướng sau khi đăng xuất
header('Location: trangchu.php'); 
exit;
