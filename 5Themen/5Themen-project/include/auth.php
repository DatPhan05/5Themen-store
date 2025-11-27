<?php
// require sau session.php trong các file khác là được
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kiểm tra user đã đăng nhập hay chưa
 */
function isLoggedIn(): bool
{
    return !empty($_SESSION['is_logged_in']) && !empty($_SESSION['user_id']);
}

/**
 * Bắt buộc đăng nhập.
 * - Nếu chưa đăng nhập → chuyển về login.php?redirect=URL_cũ
 */
function requireLogin(string $redirectTo = null): void
{
    if (isLoggedIn()) {
        return;
    }

    if ($redirectTo === null) {
        $redirectTo = $_SERVER['REQUEST_URI'] ?? 'trangchu.php';
    }

    $redirectTo = urlencode($redirectTo);
    header("Location: login.php?redirect={$redirectTo}");
    exit;
}

/**
 * Chỉ cho khách (chưa login) vào.
 * - Dùng cho login / register
 */
function requireGuest(string $redirectTo = 'account.php'): void
{
    if (isLoggedIn()) {
        header("Location: {$redirectTo}");
        exit;
    }
}
