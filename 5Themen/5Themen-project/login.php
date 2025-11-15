<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
</head>
<body>

<?php include __DIR__ . "/header.php"; ?>

<section class="auth">
    <div class="container">
        <h1 class="auth-title">Đăng nhập</h1>

        <div class="auth-card auth-card-login">

            <div class="auth-col">
                <h2>Bạn đã có tài khoản 5Themen</h2>

                <form method="POST" action="login_process.php" class="auth-form">
                    <div class="auth-field">
                        <label>Email / Số điện thoại *</label>
                        <input type="text" name="email" required>
                    </div>

                    <div class="auth-field">
                        <label>Mật khẩu *</label>
                        <input type="password" name="password" required>
                    </div>

                    <div class="auth-remember">
                        <input type="checkbox"> Ghi nhớ đăng nhập
                    </div>

                    <button class="btn-primary btn-full">Đăng nhập</button>
                </form>
            </div>

            <div class="auth-col auth-col-right">
                <h2>Khách hàng mới</h2>
                <a href="register.php" class="btn-primary btn-full">Đăng ký</a>
            </div>

        </div>
    </div>
</section>

<?php include __DIR__ . "/footer.php"; ?>

</body>
</html>
