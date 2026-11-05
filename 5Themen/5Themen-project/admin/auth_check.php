<?php
/**
 * Admin Authentication Guard / Middleware
 * Kiểm tra trạng thái đăng nhập của Admin trước khi truy cập bất kỳ trang quản trị nào
 */

require_once __DIR__ . '/../include/session.php';
Session::init();

if (Session::get('admin_login') !== true) {
    header("Location: login_admin.php");
    exit();
}

