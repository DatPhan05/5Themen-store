<?php

$rootPath = dirname(__DIR__, 2); 

require_once $rootPath . '/include/database.php';
require_once $rootPath . '/include/helpers.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /* ===================== INSERT ===================== */
    public function insert_category($name, $parent_id = 0) {
        $name   = $this->db->escape($name);
        $parent = (int)$parent_id;   // 0 = không có cha

        $sql = "
            INSERT INTO tbl_category (category_name, parent_id)
            VALUES ('$name', $parent)
        ";
        return $this->db->insert($sql);
    }

    /* ===================== SHOW LIST (HỖ TRỢ PHÂN TRANG) ===================== */
    public function show_category($offset = null, $limit = null) {
        $sql = "
            SELECT 
                c.category_id,
                c.category_name,
                c.parent_id,
                p.category_name AS parent_name
            FROM tbl_category c
            LEFT JOIN tbl_category p
                   ON c.parent_id = p.category_id
            ORDER BY c.parent_id ASC, c.category_id ASC
        ";
        
        // Thêm LIMIT và OFFSET cho phân trang
        if ($offset !== null && $limit !== null) {
            $offset = (int)$offset;
            $limit  = (int)$limit;
            $sql .= " LIMIT $offset, $limit";
        }
        
        return $this->db->select($sql);
    }
    
    /* ===================== NEW: ĐẾM TỔNG SỐ BẢN GHI ===================== */
    public function count_all_categories() {
        $sql = "SELECT COUNT(*) AS total FROM tbl_category";
        $result = $this->db->select($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }
    /* =================================================================== */

    /* ===================== GET ONLY TOP PARENTS ===================== */
    public function get_parent_categories() {
        $sql = "
            SELECT * FROM tbl_category
            WHERE parent_id = 0 OR parent_id IS NULL
            ORDER BY category_id ASC
        ";
        return $this->db->select($sql);
    }

    /* ===================== GET CHILDREN ===================== */
    public function get_children($parent_id) {
        $id = (int)$parent_id;
        $sql = "
            SELECT * FROM tbl_category 
            WHERE parent_id = $id 
            ORDER BY category_id ASC
        ";
        return $this->db->select($sql);
    }

    /* ===================== GET ONE ===================== */
    public function get_category($id) {
        $id  = (int)$id;
        $sql = "SELECT * FROM tbl_category WHERE category_id = $id LIMIT 1";
        $rs  = $this->db->select($sql);
        return $rs ? $rs->fetch_assoc() : null;
    }

    /* ===================== UPDATE ===================== */
    public function update_category($id, $name, $parent_id = null) {
        $id   = (int)$id;
        $name = $this->db->escape($name);

        // Nếu không truyền parent_id -> chỉ cập nhật tên (giữ nguyên cha)
        if ($parent_id === null) {
            $sql = "
                UPDATE tbl_category 
                SET category_name = '$name'
                WHERE category_id = $id
            ";
        } else {
            $parent = (int)$parent_id;
            $sql = "
                UPDATE tbl_category 
                SET category_name = '$name', parent_id = $parent
                WHERE category_id = $id
            ";
        }

        return $this->db->update($sql);
    }

    /* ===================== DELETE (Safe delete) ===================== */
    public function delete_category($id) {
        $id = (int)$id;

        // 1. Không cho xóa danh mục cha nếu còn danh mục con
        $check = $this->db->select("
            SELECT * FROM tbl_category WHERE parent_id = $id LIMIT 1
        ");

        if ($check && $check->num_rows > 0) {
            return false; // Có danh mục con → không xóa
        }
        
        // 2. Kiểm tra xem có sản phẩm nào thuộc danh mục này không (Nếu cần)
        // Đây là một ràng buộc FK phổ biến, nhưng không có trong code gốc, nên tôi không thêm.

        // 3. Thực hiện xóa
        $sql = "DELETE FROM tbl_category WHERE category_id = $id";
        return $this->db->delete($sql);
    }
}
?>