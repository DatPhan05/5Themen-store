<?php
session_start();
include __DIR__ . "/../include/database.php";   // <-- Đúng 100%

$db = new Database();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['username'];
    $pass = md5($_POST['password']);

    $query = "SELECT * FROM tbl_admin 
              WHERE admin_user='$user' AND admin_pass='$pass' LIMIT 1";

    $result = $db->select($query);

    if ($result && $result->num_rows > 0) {
        $_SESSION['admin_login'] = true;
        header("Location: admin_home.php");
        exit();
    } else {
        $error = "Sai tài khoản hoặc mật khẩu!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Login</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<div class="admin-login-box">
    <h2>Đăng nhập Admin</h2>

    

    <form method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" required>
        <input type="password" name="password" placeholder="Mật khẩu" required>

        <button type="submit" class="admin-btn">ĐĂNG NHẬP</button>
    </form>
</div>

</body>
</html>