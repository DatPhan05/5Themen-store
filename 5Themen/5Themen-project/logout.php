<?php
session_start();
session_unset();   // Xoá toàn bộ biến session
session_destroy(); // Hủy session

header("Location: trangchu.php"); // Chuyển về trang chủ
exit();
?>
