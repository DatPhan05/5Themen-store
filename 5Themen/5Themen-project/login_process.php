<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

// Khởi tạo Session và Database
Session::init();

$db   = new Database();
$conn = $db->link;

// =========================
// 1. KIỂM TRA CSRF TOKEN (Giữ nguyên, rất tốt)
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

// =========================================================================
// 3. TÌM USER THEO EMAIL / PHONE (ĐÃ SỬA VỚI PREPARED STATEMENT để bảo mật)
// =========================================================================
$sql = "
    SELECT user_id, fullname, email, password
    FROM tbl_user
    WHERE email = ? OR phone = ?
    LIMIT 1
";

// Chuẩn bị statement
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Lỗi hệ thống: không thể chuẩn bị truy vấn
    $_SESSION['error'] = "Lỗi hệ thống: Không thể xử lý truy vấn!";
    header("Location: login.php");
    exit;
}

// Gắn tham số (bind parameters) - 'ss' nghĩa là 2 chuỗi (string)
$stmt->bind_param("ss", $email, $email);

// Thực thi truy vấn
$stmt->execute();

// Lấy kết quả
$result = $stmt->get_result();

if ($result->num_rows < 1) {
    $stmt->close();
    $_SESSION['error'] = "Tài khoản không tồn tại!";
    header("Location: login.php");
    exit;
}

$user = $result->fetch_assoc();

// Đóng statement
$stmt->close();

// =========================
// 4. KIỂM TRA MẬT KHẨU HASH (Giữ nguyên, đây là phương pháp đúng)
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

// Không cần CSRF token cũ nữa (Đúng, chống Replay Attack)
unset($_SESSION['csrf_token']);

header("Location: account.php");
exit;
?>