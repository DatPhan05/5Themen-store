<?php
require_once __DIR__ . '/../../include/database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /* =====================================================
       1. Lấy thông tin người dùng theo ID
       ===================================================== */
    public function getUserById($userId) {
        $userId = (int)$userId;

        $query = "
            SELECT *
            FROM tbl_user
            WHERE user_id = $userId
            LIMIT 1
        ";

        return $this->db->select($query);
    }

    /* =====================================================
       2. Cập nhật hồ sơ cá nhân
       ===================================================== */
    public function updateProfile($userId, $fullname, $email, $phone, $address) {
        $userId   = (int)$userId;
        $fullname = $this->db->escape($fullname);
        $email    = $this->db->escape($email);
        $phone    = $this->db->escape($phone);
        $address  = $this->db->escape($address);

        $query = "
            UPDATE tbl_user
            SET 
                fullname = '$fullname',
                email    = '$email',
                phone    = '$phone',
                address  = '$address'
            WHERE user_id = $userId
        ";

        return $this->db->update($query);
    }

    /* =====================================================
       3. Đổi mật khẩu
       ===================================================== */
    public function changePassword($userId, $oldPass, $newPass) {
        $userId  = (int)$userId;
        $oldPass = $this->db->escape($oldPass);
        $newPass = $this->db->escape($newPass);

        // Lấy mật khẩu hiện tại
        $sql    = "SELECT password FROM tbl_user WHERE user_id = $userId";
        $result = $this->db->select($sql);

        if (!$result) return false;

        $row = $result->fetch_assoc();

        // Kiểm tra mật khẩu cũ
        if ($row['password'] !== $oldPass) {
            return "wrong_old_pass";
        }

        // Cập nhật mật khẩu mới
        $query = "
            UPDATE tbl_user
            SET password = '$newPass'
            WHERE user_id = $userId
        ";

        return $this->db->update($query);
    }
}
?>
