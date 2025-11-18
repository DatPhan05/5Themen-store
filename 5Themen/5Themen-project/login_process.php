<?php
session_start();
require_once __DIR__ . "/admin/database.php";

$db = new Database();   // tạo object Database
$conn = $db->link;      // lấy kết nối mysqli

// 1. Kiểm tra CSRF token
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    $_SESSION['error'] = "Yêu cầu không hợp lệ!";
    header("Location: login.php");
    exit;
}

// 2. Lấy dữ liệu
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$_SESSION['old']['email'] = $email;

if ($email === '' || $password === '') {
    $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
    header("Location: login.php");
    exit;
}

// Escape input để tránh SQL Injection
$emailEscaped = $conn->real_escape_string($email);

// 4. Query kiểm tra user
$sql = "SELECT * FROM tbl_user WHERE email = '$emailEscaped' OR phone = '$emailEscaped' LIMIT 1";

$result = $conn->query($sql);

if ($result->num_rows === 0) {
    $_SESSION['error'] = "Tài khoản không tồn tại!";
    header("Location: login.php");
    exit;
}

$user = $result->fetch_assoc();

// Kiểm tra mật khẩu (password_hash chuẩn)
if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Mật khẩu không đúng!";
    header("Location: login.php");
    exit;
}


// 6. Tạo session đăng nhập - ĐỒNG BỘ VỚI tbl_user
$_SESSION['user_id']   = $user['user_id'];   // cột khóa chính trong bảng tbl_user
$_SESSION['user_name'] = $user['fullname'];  // tên hiển thị

$_SESSION['is_logged_in'] = true;

// (tuỳ chọn) Xoá CSRF token cũ sau khi login
unset($_SESSION['csrf_token']);

// Redirect sang trang tài khoản
header("Location: account.php");
exit;

