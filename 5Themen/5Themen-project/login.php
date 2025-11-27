<?php
require_once __DIR__ . '/include/session.php';
Session::init(); 

// Tạo CSRF token nếu chưa tồn tại
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errorMessage = $_SESSION['error'] ?? '';
$oldEmail     = $_SESSION['old']['email'] ?? '';

// Xóa thông báo lỗi và dữ liệu cũ sau khi hiển thị
unset($_SESSION['error'], $_SESSION['old']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <style>
        /* CSS Tích hợp: Đồng bộ với phong cách Admin Dashboard (Glassmorphism) */
        
        /* Background Blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: -1;
            opacity: 0.6;
            animation: float 10s infinite alternate;
        }
        .blob-1 { width: 400px; height: 400px; background: #ff9a9e; top: 50px; left: -50px; }
        .blob-2 { width: 350px; height: 350px; background: #a18cd1; bottom: 50px; right: -50px; }

        @keyframes float { 0% { transform: translate(0, 0); } 100% { transform: translate(20px, 40px); } }

        .auth {
            position: relative;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 80vh;
            background: linear-gradient(135deg, #f0f0f5, #e0e0e0);
            padding: 40px 20px;
            overflow: hidden;
        }
        .auth-title {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            font-size: 2.5rem;
            font-weight: 700;
        }

        /* CARD STYLE - GLASSMORPHISM */
        .auth-card-login {
            position: relative;
            z-index: 10;
            padding: 20px; 
            max-width: 800px; 
            width: 100%;
            
            /* Glassmorphism Styles */
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            
            /* DÙNG FLEXBOX CHO 2 CỘT */
            display: flex; 
            flex-direction: row; 
            gap: 20px;
        }
        
        .auth-col, .auth-col-right {
            padding: 20px;
            flex: 1; 
        }

        .auth-col {
            border-right: 1px solid rgba(255, 255, 255, 0.5); 
        }
        
        .auth-error {
            background-color: rgba(248, 215, 218, 0.8);
            color: #721c24;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 12px;
            text-align: center;
            font-weight: 500;
        }
        .auth-field { margin-bottom: 15px; }
        .auth-field label { display: block; margin-bottom: 5px; font-weight: 600; color: #333; }
        
        .auth-field input[type="text"],
        .auth-field input[type="password"] {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid #000;
            border-radius: 10px;
            box-sizing: border-box;
            color: #333;
            transition: border-color 0.3s;
        }
        .auth-field input:focus {
            border-color: #4b7bec;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(75, 123, 236, 0.25);
        }
        
        .btn-primary {
            background: linear-gradient(90deg, #4b7bec, #3867d6); 
            color: white;
            padding: 14px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(75, 123, 236, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #3867d6, #4b7bec);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(75, 123, 236, 0.4);
        }
        .btn-full { width: 100%; text-align: center; text-decoration: none; display: inline-block; }
        
        .auth-col-right {
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center; 
            align-items: center;
        }
        .auth-col-right p {
            margin-bottom: 25px;
            color: #495057;
            font-size: 1.1rem;
            line-height: 1.5;
        }
        .auth-col-right a {
            background: linear-gradient(90deg, #20bf6b, #0fb9b1); 
            color: white;
            padding: 14px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(32, 191, 107, 0.3);
            max-width: 250px;
            width: 100%;
        }
        .auth-col-right a:hover {
            background: linear-gradient(90deg, #0fb9b1, #20bf6b);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(32, 191, 107, 0.4);
        }

        /* Responsive cho mobile */
        @media (max-width: 768px) {
            .auth-card-login {
                flex-direction: column;
            }
            .auth-col {
                border-right: none;
                border-bottom: 1px solid rgba(255, 255, 255, 0.5);
            }
            .auth-col-right {
                padding-top: 30px;
            }
        }
    </style>
</head>
<body>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<?php require_once __DIR__ . "/partials/header.php"; ?>

<section class="auth">
    <div class="container">
        <h1 class="auth-title">Chào mừng trở lại</h1>

        <div class="auth-card auth-card-login">

            <div class="auth-col">
                <h2 style="text-align: center; margin-bottom: 20px; color: #333;">Đăng nhập</h2>

                <?php if ($errorMessage): ?>
                <div class="auth-error"><?= htmlspecialchars($errorMessage) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="login_process.php"> 

                    <input type="hidden" name="csrf_token"
                           value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                    <div class="auth-field">
                        <label>Email / Số điện thoại *</label>
                        <input type="text" name="email"
                               value="<?= htmlspecialchars($oldEmail) ?>" required>
                    </div>

                    <div class="auth-field">
                        <label>Mật khẩu *</label>
                        <input type="password" name="password" required>
                    </div>

                    <button type="submit" class="btn-primary btn-full">Đăng nhập</button>

                </form>
            </div>

            <div class="auth-col-right">
                <h2 style="color: #333; margin-bottom: 20px;">Bạn là khách hàng mới?</h2>
                <p>Hãy đăng ký tài khoản để quản lý đơn hàng, nhận ưu đãi độc quyền và nhiều tiện ích khác!</p>
                <a href="register.php" class="btn-full">Đăng ký tài khoản</a>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . "/partials/footer.php"; ?>

</body>
</html>