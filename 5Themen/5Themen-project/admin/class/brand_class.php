<?php
// Từ /admin/class → lùi 2 cấp về /5Themen-project
$rootPath = dirname(__DIR__, 2);

// Include DB & helpers
require_once $rootPath . '/include/database.php';
require_once $rootPath . '/include/helpers.php';

class Brand {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /* =====================================================
       1. THÊM LOẠI SẢN PHẨM (brand)
       ===================================================== */
    public function insert_brand($category_id, $brand_name) {
        $category_id = (int)$category_id;
        $brand_name  = $this->db->escape($brand_name);

        if ($category_id <= 0 || $brand_name == "") return false;

        // Chống trùng tên theo danh mục
        $check = $this->db->select("
            SELECT * FROM tbl_brand 
            WHERE brand_name = '$brand_name' AND category_id = $category_id
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
       2. LẤY TẤT CẢ LOẠI SẢN PHẨM
       ===================================================== */
    public function show_brand() {
        $sql = "
            SELECT b.*, c.category_name
            FROM tbl_brand b
            LEFT JOIN tbl_category c ON b.category_id = c.category_id
            ORDER BY b.brand_id DESC
        ";
        return $this->db->select($sql);
    }

    /* =====================================================
       3. LẤY 1 LOẠI SẢN PHẨM THEO ID
       ===================================================== */
    public function get_brand($id) {
        $id = (int)$id;
        $sql = "SELECT * FROM tbl_brand WHERE brand_id = $id LIMIT 1";
        $rs  = $this->db->select($sql);
        return $rs ? $rs->fetch_assoc() : null;
    }

    /* =====================================================
       4. CẬP NHẬT LOẠI SẢN PHẨM
       ===================================================== */
    public function update_brand($brand_id, $category_id, $brand_name) {
        $brand_id    = (int)$brand_id;
        $category_id = (int)$category_id;
        $brand_name  = $this->db->escape($brand_name);

        if ($brand_id <= 0 || $category_id <= 0 || $brand_name == "") return false;

        // Check trùng tên
        $check = $this->db->select("
            SELECT * FROM tbl_brand 
            WHERE brand_name = '$brand_name' 
            AND category_id = $category_id
            AND brand_id != $brand_id
            LIMIT 1
        ");
        if ($check && $check->num_rows > 0) return false;

        $sql = "
            UPDATE tbl_brand 
            SET category_id = $category_id, brand_name = '$brand_name'
            WHERE brand_id = $brand_id
        ";
        return $this->db->update($sql);
    }

    /* =====================================================
       5. XÓA LOẠI SẢN PHẨM (CÓ CHỐNG LỖI FK)
       ===================================================== */
    public function delete_brand($id) {
        $id = (int)$id;

        if ($id <= 0) return false;

        // Check FK – xem loại này có sản phẩm đang dùng không
        $checkFK = $this->db->select("
            SELECT product_id 
            FROM tbl_product 
            WHERE brand_id = $id 
            LIMIT 1
        ");

        if ($checkFK && $checkFK->num_rows > 0) {
            // Không được xóa do còn sản phẩm sử dụng brand này
            return false;
        }

        // Xóa
        $sql = "DELETE FROM tbl_brand WHERE brand_id = $id";
        return $this->db->delete($sql);
    }
        /* =====================================================
       6. LẤY DANH SÁCH BRAND THEO CATEGORY
       ===================================================== */
    public function get_brand_by_category($category_id)
    {
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
