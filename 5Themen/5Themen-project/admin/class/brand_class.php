<?php
// Load Database & Helpers
$rootPath = dirname(__DIR__, 2);
require_once $rootPath . '/include/database.php';
require_once $rootPath . '/include/helpers.php';

class Brand {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /* =====================================================
       1. Thêm loại sản phẩm (Brand)
       ===================================================== */
    public function insert_brand($category_id, $brand_name) {
        $category_id = (int)$category_id;
        $brand_name  = $this->db->escape($brand_name);

        if ($category_id <= 0 || $brand_name == "") return false;

        // Kiểm tra trùng tên trong cùng danh mục
        $check = $this->db->select("
            SELECT *
            FROM tbl_brand 
            WHERE brand_name = '$brand_name'
              AND category_id = $category_id
            LIMIT 1
        ");
        if ($check && $check->num_rows > 0) return false;

        $sql = "
            INSERT INTO tbl_brand (category_id, brand_name)
            VALUES ($category_id, '$brand_name')
        ";
        return $this->db->insert($sql);
    }

    /* =====================================================
       2. Lấy danh sách brand (hỗ trợ phân trang)
       ===================================================== */
    public function show_brand($offset = null, $limit = null) {
        $sql = "
            SELECT b.*, c.category_name
            FROM tbl_brand b
            LEFT JOIN tbl_category c 
                   ON b.category_id = c.category_id
            ORDER BY b.brand_id DESC
        ";

        if ($offset !== null && $limit !== null) {
            $offset = (int)$offset;
            $limit  = (int)$limit;
            $sql .= " LIMIT $offset, $limit";
        }

        return $this->db->select($sql);
    }

    /* =====================================================
       2.1. Đếm tổng số brand (phục vụ phân trang)
       ===================================================== */
    public function count_all_brands() {
        $sql = "SELECT COUNT(*) AS total FROM tbl_brand";
        $result = $this->db->select($sql);
        $row = $result->fetch_assoc();
        return (int)$row['total'];
    }

    /* =====================================================
       3. Lấy 1 brand theo ID
       ===================================================== */
    public function get_brand($id) {
        $id = (int)$id;

        $sql = "
            SELECT *
            FROM tbl_brand
            WHERE brand_id = $id
            LIMIT 1
        ";

        $rs = $this->db->select($sql);
        return $rs ? $rs->fetch_assoc() : null;
    }

    /* =====================================================
       4. Cập nhật brand
       ===================================================== */
    public function update_brand($brand_id, $category_id, $brand_name) {
        $brand_id    = (int)$brand_id;
        $category_id = (int)$category_id;
        $brand_name  = $this->db->escape($brand_name);

        if ($brand_id <= 0 || $category_id <= 0 || $brand_name == "") return false;

        // Kiểm tra trùng tên
        $check = $this->db->select("
            SELECT *
            FROM tbl_brand
            WHERE brand_name = '$brand_name'
              AND category_id = $category_id
              AND brand_id != $brand_id
            LIMIT 1
        ");
        if ($check && $check->num_rows > 0) return false;

        $sql = "
            UPDATE tbl_brand
            SET category_id = $category_id,
                brand_name  = '$brand_name'
            WHERE brand_id = $brand_id
        ";

        return $this->db->update($sql);
    }

    /* =====================================================
       5. Xóa brand (kiểm tra khóa ngoại)
       ===================================================== */
    public function delete_brand($id) {
        $id = (int)$id;
        if ($id <= 0) return false;

        // Kiểm tra xem brand có đang được dùng bởi sản phẩm nào không
        $checkFK = $this->db->select("
            SELECT product_id
            FROM tbl_product
            WHERE brand_id = $id
            LIMIT 1
        ");

        if ($checkFK && $checkFK->num_rows > 0) {
            return false; // Có sản phẩm đang dùng → không được xóa
        }

        $sql = "DELETE FROM tbl_brand WHERE brand_id = $id";
        return $this->db->delete($sql);
    }

    /* =====================================================
       6. Lấy brand theo category (dùng trong lọc sản phẩm)
       ===================================================== */
    public function get_brand_by_category($category_id) {
        $cid = (int)$category_id;

        $sql = "
            SELECT DISTINCT 
                b.brand_id,
                b.brand_name
            FROM tbl_brand b
            INNER JOIN tbl_product p 
                    ON p.brand_id = b.brand_id
            WHERE p.category_id = $cid
            ORDER BY b.brand_id ASC
        ";

        return $this->db->select($sql);
    }
}
?>
