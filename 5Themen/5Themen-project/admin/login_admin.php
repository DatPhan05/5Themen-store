<?php
// =======================================================
// PHP LOGIC - ADMIN LOGIN
// =======================================================
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa, nếu rồi thì chuyển hướng
if (isset($_SESSION['admin_login']) && $_SESSION['admin_login'] === true) {
    header("Location: admin_home.php");
    exit();
}

// Giả định: file database.php chứa class Database và kết nối
include __DIR__ . "/../include/database.php"; 

$db = new Database(); // Khởi tạo Database

$error = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Lấy dữ liệu và làm sạch (trim)
    $user = trim($_POST['username'] ?? '');
    $pass = $_POST['password'] ?? '';

    // Kiểm tra đầu vào
    if (empty($user) || empty($pass)) {
        $error = "Vui lòng nhập đủ tên đăng nhập và mật khẩu.";
    } else {
        
        // CẢNH BÁO BẢO MẬT QUAN TRỌNG: 
        // md5() là phương pháp hashing lỗi thời và không an toàn.
        // Bạn nên sử dụng PHP password_hash() và password_verify().
        // ********************************************************************
        // Nếu không thể thay đổi DB ngay, tạm thời vẫn dùng md5 cho việc TEST:
        $hashed_pass = md5($pass);
        // ********************************************************************
        
        // Sử dụng Prepared Statements để chống SQL Injection
        // Giả định class Database của bạn có hàm prepare() hoặc tương đương.
        // Nếu không có, bạn phải đảm bảo hàm select() xử lý an toàn.
        
        // Để giữ tính tương thích với hàm select() cũ:
        // Đảm bảo hàm escape() trong class Database được gọi cho $user
        $user_safe = $db->escape($user); 
        
        $query = "SELECT * FROM tbl_admin 
                  WHERE admin_user='$user_safe' AND admin_pass='$hashed_pass' LIMIT 1";

        $result = $db->select($query);

        if ($result && $result->num_rows > 0) {
            // Lấy thông tin admin
            // $admin_info = $result->fetch_assoc(); 
            
            $_SESSION['admin_login'] = true;
            // Lưu thêm thông tin nếu cần, ví dụ: $_SESSION['admin_id'] = $admin_info['id'];
            
            header("Location: admin_home.php");
            exit();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* ======================================================= */
        /* CSS CHO GIAO DIỆN GLASSMORPHISM/MINIMALISM */
        /* ======================================================= */
        
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap');

        :root {
            --bg-color: #f0f2f5;
            --glass-bg: rgba(255, 255, 255, 0.15);
            --border-color: rgba(255, 255, 255, 0.4);
            --shadow-color: rgba(0, 0, 0, 0.1);
            --main-color: #007bff; /* Màu xanh dương */
            --error-color: #dc3545;
        }

        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #a1c4fd, #c2e9fb); /* Gradient nền nhẹ nhàng */
            overflow: hidden;
        }

        /* Hiệu ứng trang trí background */
        .admin-login-box::before,
        .admin-login-box::after {
            content: '';
            position: absolute;
            width: 250px;
            height: 250px;
            background: rgba(255, 255, 255, 0.4);
            border-radius: 50%;
            filter: blur(50px);
            z-index: -1;
        }

        .admin-login-box::before {
            top: -50px;
            left: -50px;
            background: linear-gradient(135deg, #00c6ff, #0072ff);
        }

        .admin-login-box::after {
            bottom: -50px;
            right: -50px;
            background: linear-gradient(135deg, #ff7f50, #ffcba4);
        }
        
        /* Box chính Glassmorphism */
        .admin-login-box {
            position: relative;
            width: 100%;
            max-width: 400px;
            padding: 40px;
            border-radius: 20px;
            background: var(--glass-bg); /* Nền kính mờ */
            backdrop-filter: blur(10px);
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 30px var(--shadow-color);
            text-align: center;
            z-index: 10;
        }

        .admin-login-box h2 {
            font-size: 24px;
            font-weight: 600;
            color: #333;
            margin-bottom: 30px;
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        /* Trường nhập liệu */
        .admin-login-box form input {
            width: 100%;
            padding: 15px 20px;
            margin-bottom: 20px;
            border: 1px solid rgba(255, 255, 255, 0.6);
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.7);
            color: #333;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
            box-sizing: border-box; 
        }

        .admin-login-box form input:focus {
            background: rgba(255, 255, 255, 1);
            border-color: var(--main-color);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.2);
        }
        
        .admin-login-box form input::placeholder {
            color: #777;
        }

        /* Nút Đăng nhập */
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
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .admin-btn:hover {
            filter: brightness(1.1);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(0, 123, 255, 0.4);
        }

        /* Thông báo lỗi */
        .error-message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 8px;
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--error-color);
            font-size: 14px;
            font-weight: 500;
            border: 1px solid rgba(220, 53, 69, 0.3);
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

        <button type="submit" class="admin-btn">ĐĂNG NHẬP</button>
    </form>
</div>

</body>
</html>