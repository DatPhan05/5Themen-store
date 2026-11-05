<?php
/**
 * Xử lý đăng xuất tài khoản Quản trị viên
 */
require_once __DIR__ . '/../include/session.php';
Session::init();

Session::remove('admin_login');
Session::remove('admin_name');
Session::remove('admin_id');

header("Location: login_admin.php");
exit();

