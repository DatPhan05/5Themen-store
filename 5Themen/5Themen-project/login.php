<?php
/***********************************************
 * 1. IMPORT SESSION
 ***********************************************/
require_once __DIR__ . '/include/session.php';

/***********************************************
 * 2. TẠO CSRF TOKEN
 ***********************************************/
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

/***********************************************
 * 3. LẤY FLASH DATA
 ***********************************************/
$errorMessage = $_SESSION['error'] ?? '';
$oldEmail     = $_SESSION['old']['email'] ?? '';

unset($_SESSION['error'], $_SESSION['old']);
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

<?php require_once __DIR__ . "/partials/header.php"; ?>

<section class="auth">
    <div class="container">
        <h1 class="auth-title">Đăng nhập</h1>

        <div class="auth-card auth-card-login">

            <div class="auth-col">
                <h2>Bạn đã có tài khoản 5Themen</h2>

                <?php if ($errorMessage): ?>
                    <div class="auth-error" role="alert">
                        <?= htmlspecialchars($errorMessage) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login_process.php" class="auth-form" novalidate>

                    <!-- CSRF token -->
                    <input type="hidden" name="csrf_token"
                           value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                    <div class="auth-field">
                        <label for="email">Email / Số điện thoại *</label>
                        <input id="email"
                               type="text"
                               name="email"
                               value="<?= htmlspecialchars($oldEmail) ?>"
                               required
                               autocomplete="username"
                               placeholder="Email hoặc số điện thoại">
                    </div>

                    <div class="auth-field">
                        <label for="password">Mật khẩu *</label>
                        <input id="password"
                               type="password"
                               name="password"
                               required
                               autocomplete="current-password">
                    </div>

                    <div class="auth-remember">
                        <input id="remember" type="checkbox" name="remember" value="1">
                        <label for="remember">Ghi nhớ đăng nhập</label>
                    </div>

                    <button type="submit" class="btn-primary btn-full">Đăng nhập</button>
                </form>
            </div>

            <div class="auth-col auth-col-right">
                <h2>Khách hàng mới</h2>
                <a href="register.php" class="btn-primary btn-full">Đăng ký</a>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . "/partials/footer.php"; ?>

</body>
</html>
