<?php
require_once __DIR__ . '/include/session.php';
Session::init(); 

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

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
</head>
<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>

<section class="auth">
    <div class="container">
        <h1 class="auth-title">Đăng nhập</h1>

        <div class="auth-card auth-card-login">

            <div class="auth-col">

                <?php if ($errorMessage): ?>
                <div class="auth-error"><?= htmlspecialchars($errorMessage) ?></div>
                <?php endif; ?>

                <form method="POST" action="/5Themen/5Themen-project/login_process.php">

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

            <div class="auth-col auth-col-right">
                <a href="register.php" class="btn-primary btn-full">Đăng ký</a>
            </div>

        </div>
    </div>
</section>

<?php require_once __DIR__ . "/partials/footer.php"; ?>

</body>
</html>
