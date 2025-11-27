<?php
/**
 * Session Helper – dùng chung cho toàn project
 */

class Session
{
    // KHỞI TẠO SESSION – chỉ gọi ở ĐẦU FILE PHP (trước khi xuất HTML)
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $val)
    {
        $_SESSION[$key] = $val;
    }

    public static function get($key, $default = false)
    {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public static function remove($key)
    {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    /**
     * Dùng cho logout: xóa session + quay về login
     */
    public static function destroy()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();

        header("Location: login.php");
        exit;
    }
}
