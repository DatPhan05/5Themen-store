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
</head>

<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>

<section class="auth">
    <div class="container">
        <h1 class="auth-title">Đăng ký tài khoản</h1>

        <div class="auth-card auth-card-register">
            <form method="POST" class="auth-form">

                <div class="auth-row">
                    <div class="auth-field">
                        <label>Họ tên</label>
                        <input type="text" name="fullname" required value="<?= htmlspecialchars($fullname ?? '') ?>">
                    </div>

                    <div class="auth-field">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone" value="<?= htmlspecialchars($phone ?? '') ?>">
                    </div>
                </div>

                <div class="auth-row">
                    <div class="auth-field">
                        <label>Email</label>
                        <input type="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
                    </div>

                    <div class="auth-field">
                        <label>Mật khẩu</label>
                        <input type="password" name="password" required>
                    </div>
                </div>

                <div class="auth-field">
                    <label>Địa chỉ</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($address ?? '') ?>">
                </div>

                <button type="submit" class="btn-primary btn-full">Đăng ký</button>

                <p style="margin-top:10px;">
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