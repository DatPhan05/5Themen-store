<?php
session_start();

// Xoá toàn bộ session
session_unset();
session_destroy();

// Chuyển về trang chủ hoặc trang đăng nhập
header('Location: trangchu.php'); // hoặc login.php tuỳ bạn
exit;
