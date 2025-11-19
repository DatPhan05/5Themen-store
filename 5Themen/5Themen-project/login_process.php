<?php
/***********************************************
 * 1. IMPORT SESSION + DATABASE
 ***********************************************/
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

$db   = new Database();
$conn = $db->link;

/***********************************************
 * 2. KIỂM TRA CSRF TOKEN
 ***********************************************/
if (
    !isset($_POST['csrf_token']) 
    || !isset($_SESSION['csrf_token']) 
    || $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    $_SESSION['error'] = "Yêu cầu không hợp lệ!";
    header("Location: login.php");
    exit;
}

/***********************************************
 * 3. LẤY DỮ LIỆU FORM
 ***********************************************/
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$_SESSION['old']['email'] = $email;

if ($email === '' || $password === '') {
    $_SESSION['error'] = "Vui lòng nhập đầy đủ thông tin!";
    header("Location: login.php");
    exit;
}

/***********************************************
 * 4. Escape input chống SQL Injection
 ***********************************************/
$emailEscaped = $conn->real_escape_string($email);

/***********************************************
 * 5. QUERY KIỂM TRA USER
 ***********************************************/
$sql = "
    SELECT *
    FROM tbl_user
    WHERE email = '$emailEscaped'
       OR phone = '$emailEscaped'
    LIMIT 1
";

$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    $_SESSION['error'] = "Tài khoản không tồn tại!";
    header("Location: login.php");
    exit;
}

$user = $result->fetch_assoc();

/***********************************************
 * 6. KIỂM TRA MẬT KHẨU HASH
 ***********************************************/
if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Mật khẩu không đúng!";
    header("Location: login.php");
    exit;
}

/***********************************************
 * 7. TẠO SESSION ĐĂNG NHẬP
 ***********************************************/
$_SESSION['user_id']      = $user['user_id'];
$_SESSION['user_name']    = $user['fullname'];
$_SESSION['is_logged_in'] = true;

// Xoá CSRF token sau khi login
unset($_SESSION['csrf_token']);

/***********************************************
 * 8. TRẢ VỀ TRANG TÀI KHOẢN
 ***********************************************/
header("Location: account.php");
exit;
