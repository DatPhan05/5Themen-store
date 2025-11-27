<?php
require_once __DIR__ . "/../include/session.php";
require_once __DIR__ . "/../include/database.php";

Session::init();

// Nếu đã đăng nhập → chuyển về admin_home
if (Session::get('admin_login') === true) {
    header("Location: admin_home.php");
    exit;
}

$db = new Database();
$error = "";

// Xử lý đăng nhập
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    if ($user === '' || $pass === '') {
        $error = "Vui lòng nhập đầy đủ thông tin!";
    } else {

        
         // Kiểm tra tài khoản + mật khẩu bcrypt
          $user_safe = $db->escape($user);
          $sql = "
           SELECT * 
           FROM tbl_admin 
           WHERE admin_user = '$user_safe'
           LIMIT 1";

          $result = $db->select($sql);

          if ($result && $result->num_rows > 0) {

          $admin = $result->fetch_assoc();

          // KIỂM TRA MẬT KHẨU BCRYPT
          if (password_verify($pass, $admin['admin_pass'])) {

          Session::set('admin_login', true);
          Session::set('admin_name', $admin['admin_user']);
          Session::set('admin_id', $admin['admin_id'] ?? 0);

          header("Location: admin_home.php");
          exit;

    } else {
        $error = "Sai tài khoản hoặc mật khẩu!";
    }

} else {
    $error = "Sai tài khoản hoặc mật khẩu!";
}

    }
}
?>


<!DOCTYPE html>
<html lang="vi">
<head>
    <title>Đăng nhập Admin</title>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        :root {
            --glass-bg: rgba(255, 255, 255, 0.15);
            --border-color: rgba(255, 255, 255, 0.4);
            --shadow: rgba(0, 0, 0, 0.1);
            --main: #007bff;
            --error: #dc3545;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0; padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #a1c4fd, #c2e9fb);
        }

        .admin-login-box {
            position: relative;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 20px;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px var(--shadow);
            text-align: center;
        }

        .admin-login-box::before,
        .admin-login-box::after {
            content: '';
            position: absolute;
            width: 250px; height: 250px;
            border-radius: 50%;
            filter: blur(50px);
            z-index: -1;
        }

        .admin-login-box::before {
            top: -50px; left: -50px;
            background: linear-gradient(135deg, #00c6ff, #0072ff);
        }

        .admin-login-box::after {
            bottom: -50px; right: -50px;
            background: linear-gradient(135deg, #ff7f50, #ffcba4);
        }

        h2 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            text-transform: uppercase;
        }

        .admin-login-box form input {
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.6);
            border-radius: 10px;
            background: rgba(255,255,255,0.7);
            font-size: 16px;
            outline: none;
            transition: 0.3s;
        }

        .admin-login-box form input:focus {
            background: white;
            border-color: var(--main);
            box-shadow: 0 0 0 3px rgba(0,123,255,0.2);
        }

        .admin-btn {
            width: 100%;
            padding: 15px;
            margin-top: 10px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(90deg, #007bff, #00b4d8);
            color: white;
            font-size: 17px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s;
        }

        .admin-btn:hover {
            filter: brightness(1.1);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0,123,255,0.4);
        }

        .error-message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
            background-color: rgba(220,53,69,0.1);
            color: var(--error);
            border: 1px solid rgba(220,53,69,0.3);
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
            justify-content: center;
        }
    </style>
</head>

<body>

<div class="admin-login-box">
    <h2><i class="fas fa-user-lock"></i> Đăng nhập Admin</h2>

    <?php if (!empty($error)) : ?>
        <div class="error-message">
            <i class="fas fa-exclamation-triangle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Tên đăng nhập" required autocomplete="off">
        <input type="password" name="password" placeholder="Mật khẩu" required autocomplete="current-password">
        <button type="submit" class="admin-btn">Đăng nhập</button>
    </form>
</div>

</body>
</html>
