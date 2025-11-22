<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

Session::init();

$db   = new Database();
$conn = $db->link;

// =========================
// 1. KIỂM TRA CSRF TOKEN
// =========================
if (
    !isset($_POST['csrf_token']) ||
    !isset($_SESSION['csrf_token']) ||
    $_POST['csrf_token'] !== $_SESSION['csrf_token']
) {
    $_SESSION['error'] = "Phiên đăng nhập không hợp lệ!";
    header("Location: login.php");
    exit;
}

// =========================
// 2. LẤY DỮ LIỆU FORM
// =========================
$email    = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

$_SESSION['old']['email'] = $email;

if ($email === '' || $password === '') {
    $_SESSION['error'] = "Vui lòng nhập đủ thông tin!";
    header("Location: login.php");
    exit;
}

// =========================
// 3. TÌM USER THEO EMAIL / PHONE
// =========================
$emailEscaped = $conn->real_escape_string($email);

$sql = "
    SELECT *
    FROM tbl_user
    WHERE email = '$emailEscaped'
       OR phone = '$emailEscaped'
    LIMIT 1
";

$result = $conn->query($sql);

if (!$result || $result->num_rows < 1) {
    $_SESSION['error'] = "Tài khoản không tồn tại!";
    header("Location: login.php");
    exit;
}

$user = $result->fetch_assoc();

// =========================
// 4. KIỂM TRA MẬT KHẨU HASH
// =========================
if (!password_verify($password, $user['password'])) {
    $_SESSION['error'] = "Mật khẩu không đúng!";
    header("Location: login.php");
    exit;
}

// =========================
// 5. LƯU SESSION ĐĂNG NHẬP
// =========================
$_SESSION['is_logged_in'] = true;
$_SESSION['user_id']      = $user['user_id'];
$_SESSION['user_name']    = $user['fullname'] ?? $user['email'];

// Không cần CSRF token cũ nữa
unset($_SESSION['csrf_token']);

header("Location: account.php");
exit;
