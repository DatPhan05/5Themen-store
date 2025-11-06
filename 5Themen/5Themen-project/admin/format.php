<?php
/**
 * Format Class – Xử lý định dạng và bảo vệ dữ liệu đầu vào
 */
class Format {
    public function formatDate($date) {
        return date('d/m/Y H:i', strtotime($date));
    }

    public function textShorten($text, $limit = 100) {
        $text = substr($text, 0, $limit);
        $text = substr($text, 0, strrpos($text, ' '));
        return $text . '...';
    }

    public function validation($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    public function title() {
        $path  = $_SERVER['SCRIPT_FILENAME'];
        $title = basename($path, '.php');
        if ($title == 'index') $title = 'Trang chủ';
        return ucfirst($title);
    }
}
?>
