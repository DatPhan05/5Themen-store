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
        $userId = (int)$userId;

        // Lấy mật khẩu hiện tại
        $sql    = "SELECT password FROM tbl_user WHERE user_id = $userId LIMIT 1";
        $result = $this->db->select($sql);

        if (!$result || $result->num_rows === 0) return false;

        $row = $result->fetch_assoc();

        // Kiểm tra mật khẩu cũ (hỗ trợ hash và mật khẩu cũ plaintext)
        $isValid = password_verify($oldPass, $row['password']) || ($row['password'] === $oldPass);
        if (!$isValid) {
            return "wrong_old_pass";
        }

        // Băm mật khẩu mới bằng thuật toán an toàn
        $newHashed = password_hash($newPass, PASSWORD_DEFAULT);
        $newHashedEsc = $this->db->escape($newHashed);

        // Cập nhật mật khẩu mới
        $query = "
            UPDATE tbl_user
            SET password = '$newHashedEsc'
            WHERE user_id = $userId
        ";

        return $this->db->update($query);
    }
}
?>
