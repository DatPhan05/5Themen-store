<?php
class Session {
    public static function init() {
        if (session_status() === PHP_SESSION_NONE) session_start();
    }

    public static function set($key, $val) {
        $_SESSION[$key] = $val;
    }

    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
    }

    public static function destroy() {
        session_destroy();
        header("Location: login.php");
        exit;
    }
}
Session::init();
?>
