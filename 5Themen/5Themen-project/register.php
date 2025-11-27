<?php
/***********************************************
 * 1. SESSION + DATABASE
 ***********************************************/
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

$db   = new Database();
$conn = $db->link;

/***************************************************************************
 * 2. XỬ LÝ ĐĂNG KÝ (ĐÃ SỬA VỚI PREPARED STATEMENTS để bảo mật và ổn định)
 ***************************************************************************/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $fullname = trim($_POST['fullname'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $address  = trim($_POST['address'] ?? '');

    // Kiểm tra đầu vào tối thiểu
    if (empty($fullname) || empty($email) || empty($password)) {
        echo "<script>alert('Vui lòng nhập đủ Họ tên, Email và Mật khẩu!');</script>";
    } else {
        
        // --- 1. KIỂM TRA TRÙNG EMAIL (Dùng Prepared Statement) ---
        $check_sql = "SELECT email FROM tbl_user WHERE email = ? LIMIT 1";
        $check_stmt = $conn->prepare($check_sql);
        
        if (!$check_stmt) {
            echo "<script>alert('Lỗi hệ thống khi chuẩn bị kiểm tra email!');</script>";
        } else {
            $check_stmt->bind_param("s", $email);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();
            $check_stmt->close();

            if ($check_result && $check_result->num_rows > 0) {
                echo "<script>alert('Email đã tồn tại. Vui lòng dùng email khác!');</script>";
            } else {
                // --- 2. HASH MẬT KHẨU VÀ CHÈN DỮ LIỆU ---
                $passHash = password_hash($password, PASSWORD_DEFAULT); // HASH mật khẩu tiêu chuẩn PHP

                $insert_sql = "
                    INSERT INTO tbl_user(fullname, email, password, phone, address)
                    VALUES (?, ?, ?, ?, ?)
                ";

                $insert_stmt = $conn->prepare($insert_sql);
                
                if (!$insert_stmt) {
                    echo "<script>alert('Lỗi hệ thống khi chuẩn bị chèn dữ liệu!');</script>";
                } else {
                    // Gắn tham số (5 strings: sssss)
                    $insert_stmt->bind_param("sssss", $fullname, $email, $passHash, $phone, $address);

                    if ($insert_stmt->execute()) {
                        header("Location: login.php?registered=1");
                        exit();
                    } else {
                        echo "<script>alert('Đăng ký thất bại. Vui lòng thử lại: " . $conn->error . "');</script>";
                    }

                    $insert_stmt->close();
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký - 5Themen</title>
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
            min-height: 90vh;
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
        .auth-card-register {
            position: relative;
            z-index: 10;
            padding: 40px;
            max-width: 700px;
            width: 100%;
            
            /* Glassmorphism Styles */
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
        }

        .auth-row {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
        }
        .auth-field {
            flex-grow: 1;
        }
        .auth-field label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .auth-field input[type="text"],
        .auth-field input[type="email"],
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
            border-color: #20bf6b;
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(32, 191, 107, 0.25);
        }
        
        /* BUTTON STYLE */
        .btn-primary {
            background: linear-gradient(90deg, #20bf6b, #0fb9b1); 
            color: white;
            padding: 14px;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 700;
            transition: all 0.3s;
            margin-top: 10px;
            box-shadow: 0 4px 10px rgba(32, 191, 107, 0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #0fb9b1, #20bf6b);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(32, 191, 107, 0.4);
        }
        .btn-full { width: 100%; }
        
        .auth-card-register p {
            text-align: center;
            margin-top: 20px;
            color: #6c757d;
        }
        .auth-card-register p a {
            color: #4b7bec;
            text-decoration: none;
            font-weight: 600;
        }
        .auth-card-register p a:hover {
            text-decoration: underline;
        }
        
        /* Media query cho mobile */
        @media (max-width: 600px) {
            .auth-row {
                flex-direction: column;
                gap: 0;
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
        <h1 class="auth-title">Đăng ký tài khoản</h1>

        <div class="auth-card auth-card-register">
            <form method="POST" class="auth-form">

                <div class="auth-row">
                    <div class="auth-field">
                        <label>Họ tên *</label>
                        <input type="text" name="fullname" required value="<?= htmlspecialchars($fullname ?? '') ?>">
                    </div>

                    <div class="auth-field">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>">
                    </div>
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label>Email *</label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
                    </div>

                    <div class="auth-field">
                        <label>Mật khẩu *</label>
                        <input type="password" name="password" required>
                    </div>
                </div>

                <div class="auth-field">
                    <label>Địa chỉ</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($address ?? '') ?>">
                </div>

                <button type="submit" class="btn-primary btn-full">Đăng ký</button>

                <p>
                    Đã có tài khoản?
                    <a href="login.php">Đăng nhập</a>
                </p>

            </form>
        </div>
    </div>
</section>

<?php require_once __DIR__ . "/partials/footer.php"; ?>

</body>
</html>