<?php
$filepath = realpath(dirname(__FILE__));
require_once $filepath . '/../../include/database.php';

class Product {
    private $db;

    public function __construct() { 
        $this->db = new Database(); 
    }

    /* =====================================================
       1. Thêm sản phẩm
       ===================================================== */
    public function insert_product($name, $category_id, $brand_id, $price, $sale_price, $desc, $thumb)
    {
        $name  = $this->db->escape($name);
        $desc  = $this->db->escape($desc);
        $thumb = $this->db->escape($thumb);

        $cid   = (int)$category_id;
        $bid   = (int)$brand_id;
        $price = (float)$price;
        $sale  = (float)$sale_price;

        $query = "
            INSERT INTO tbl_product
                (product_name, category_id, brand_id, product_price, product_sale, product_desc, product_img)
            VALUES
                ('$name', $cid, $bid, $price, $sale, '$desc', '$thumb')
        ";
        return $this->db->insert($query);
    }

    /* =====================================================
       2. Lấy tất cả sản phẩm (phân trang)
       ===================================================== */
    public function get_all_products($limit = 8, $offset = 0) {
        $limit  = (int)$limit;
        $offset = (int)$offset;

        $query = "
            SELECT 
                p.product_id,
                p.product_name,
                c.category_name,
                b.brand_name,
                p.product_price,
                p.product_sale,
                p.product_img
            FROM tbl_product p
            LEFT JOIN tbl_category c ON p.category_id = c.category_id
            LEFT JOIN tbl_brand b ON p.brand_id = b.brand_id
            ORDER BY p.product_id ASC
            LIMIT $limit OFFSET $offset
        ";

        return $this->db->select($query);
    }

    /* =====================================================
       3. Lấy sản phẩm theo danh mục (phân trang)
       ===================================================== */
    public function get_product_by_category($category_id, $limit = 8, $offset = 0) {
        $cid    = (int)$category_id;
        $limit  = (int)$limit;
        $offset = (int)$offset;

        $query = "
            SELECT 
                p.product_id,
                p.product_name,
                p.product_price,
                p.product_sale,
                p.product_img,
                c.category_name,
                b.brand_name
            FROM tbl_product p
            JOIN tbl_category c ON p.category_id = c.category_id
            LEFT JOIN tbl_brand b ON p.brand_id = b.brand_id
            WHERE p.category_id = $cid
            ORDER BY p.product_id ASC
            LIMIT $limit OFFSET $offset
        ";

        return $this->db->select($query);
    }

    /* =====================================================
       4. Lấy sản phẩm theo danh mục + thương hiệu (phân trang)
       ===================================================== */
    public function get_product_by_category_brand($category_id, $brand_id, $limit = 8, $offset = 0) {
        $cid    = (int)$category_id;
        $bid    = (int)$brand_id;
        $limit  = (int)$limit;
        $offset = (int)$offset;

        $query = "
            SELECT 
                p.product_id,
                p.product_name,
                p.product_price,
                p.product_sale,
                p.product_img,
                c.category_name,
                b.brand_name
            FROM tbl_product p
            JOIN tbl_category c ON p.category_id = c.category_id
            LEFT JOIN tbl_brand b ON p.brand_id = b.brand_id
            WHERE p.category_id = $cid AND p.brand_id = $bid
            ORDER BY p.product_id ASC
            LIMIT $limit OFFSET $offset
        ";

        return $this->db->select($query);
    }

    /* =====================================================
       5. Đếm tổng số sản phẩm
       ===================================================== */
    public function count_all_products() {
        $query = "SELECT COUNT(*) AS total FROM tbl_product";
        $result = $this->db->select($query);
        return $result ? (int)$result->fetch_assoc()['total'] : 0;
    }

    /* =====================================================
       6. Đếm sản phẩm theo danh mục
       ===================================================== */
    public function count_product_by_category($category_id) {
        $cid = (int)$category_id;
        $query = "SELECT COUNT(*) AS total FROM tbl_product WHERE category_id = $cid";
        $result = $this->db->select($query);
        return $result ? (int)$result->fetch_assoc()['total'] : 0;
    }

    /* =====================================================
       7. Đếm sản phẩm theo danh mục + brand
       ===================================================== */
    public function count_product_by_category_brand($category_id, $brand_id) {
        $cid = (int)$category_id;
        $bid = (int)$brand_id;

        $query = "
            SELECT COUNT(*) AS total 
            FROM tbl_product 
            WHERE category_id = $cid AND brand_id = $bid
        ";
        $result = $this->db->select($query);
        return $result ? (int)$result->fetch_assoc()['total'] : 0;
    }

    /* =====================================================
       8. Lấy 1 sản phẩm theo ID
       ===================================================== */
    public function get_product($id) {
        $id = (int)$id;

        $query = "
            SELECT 
                product_id,
                product_name,
                product_price,
                product_sale,
                product_desc,
                product_img,
                category_id,
                brand_id
            FROM tbl_product
            WHERE product_id = $id
            LIMIT 1
        ";

        $rs = $this->db->select($query);
        return $rs ? $rs->fetch_assoc() : null;
    }

    /* =====================================================
       9. Xóa sản phẩm
       ===================================================== */
    public function delete_product($id){
        $id = (int)$id;
        $query = "DELETE FROM tbl_product WHERE product_id = $id";
        return $this->db->delete($query);
    }

    /* =====================================================
       10. Lấy 1 sản phẩm (full cột)
       ===================================================== */
    public function getOne($id){
        $id = (int)$id;

        $query = "
            SELECT *
            FROM tbl_product
            WHERE product_id = $id
            LIMIT 1
        ";

        $rs = $this->db->select($query);
        return $rs ? $rs->fetch_assoc() : null;
    }

    /* =====================================================
       11. Đếm sản phẩm theo filter
       ===================================================== */
    public function count_filtered_products($category_id = 0, $brand_id = 0, $min_price = 0, $max_price = 0) {
        $cid = (int)$category_id;
        $bid = (int)$brand_id;
        $min = (float)$min_price;
        $max = (float)$max_price;

        $priceField = "CASE WHEN p.product_sale > 0 THEN p.product_sale ELSE p.product_price END";
        $where = "1=1";

        if ($cid > 0) $where .= " AND p.category_id = $cid";
        if ($bid > 0) $where .= " AND p.brand_id = $bid";
        if ($min > 0) $where .= " AND $priceField >= $min";
        if ($max > 0) $where .= " AND $priceField <= $max";

        $query = "
            SELECT COUNT(*) AS total
            FROM tbl_product p
            WHERE $where
        ";

        $rs = $this->db->select($query);
        return $rs ? (int)$rs->fetch_assoc()['total'] : 0;
    }

    /* =====================================================
       12. Lấy danh sách theo filter + sort + phân trang
       ===================================================== */
    public function get_filtered_products(
        $category_id = 0, $brand_id = 0,
        $min_price = 0, $max_price = 0,
        $sort = 'newest', $limit = 8, $offset = 0
    ) {
        $cid = (int)$category_id;
        $bid = (int)$brand_id;
        $min = (float)$min_price;
        $max = (float)$max_price;
        $limit = (int)$limit;
        $offset = (int)$offset;

        $priceField = "CASE WHEN p.product_sale > 0 THEN p.product_sale ELSE p.product_price END";
        $where = "1=1";

        if ($cid > 0) $where .= " AND p.category_id = $cid";
        if ($bid > 0) $where .= " AND p.brand_id = $bid";
        if ($min > 0) $where .= " AND $priceField >= $min";
        if ($max > 0) $where .= " AND $priceField <= $max";

        switch ($sort) {
            case 'price_asc':  $orderBy = "$priceField ASC"; break;
            case 'price_desc': $orderBy = "$priceField DESC"; break;
            case 'name_asc':   $orderBy = "p.product_name ASC"; break;
            case 'name_desc':  $orderBy = "p.product_name DESC"; break;
            case 'newest':
            default:           $orderBy = "p.product_id DESC";
        }

        $query = "
            SELECT 
                p.product_id,
                p.product_name,
                p.product_price,
                p.product_sale,
                p.product_img,
                c.category_name,
                b.brand_name
            FROM tbl_product p
            LEFT JOIN tbl_category c ON p.category_id = c.category_id
            LEFT JOIN tbl_brand b ON p.brand_id = b.brand_id
            WHERE $where
            ORDER BY $orderBy
            LIMIT $limit OFFSET $offset
        ";

        return $this->db->select($query);
    }

    /* =====================================================
       13. Cập nhật sản phẩm
       ===================================================== */
    public function update_product($id, $name, $category_id, $brand_id, $price, $sale_price, $desc, $thumb = null)
    {
        $id    = (int)$id;
        $cid   = (int)$category_id;
        $bid   = (int)$brand_id;
        $price = (float)$price;
        $sale  = (float)$sale_price;

        $name  = $this->db->escape($name);
        $desc  = $this->db->escape($desc);

        $sql_thumb = "";
        if ($thumb !== null && $thumb !== "") {
            $thumb = $this->db->escape($thumb);
            $sql_thumb = ", product_img = '$thumb'";
        }

        $query = "
            UPDATE tbl_product
            SET 
                product_name  = '$name',
                category_id   = $cid,
                brand_id      = $bid,
                product_price = $price,
                product_sale  = $sale,
                product_desc  = '$desc'
                $sql_thumb
            WHERE product_id = $id
        ";

        return $this->db->update($query);
    }

    /* =====================================================
       14. Tìm kiếm sản phẩm theo từ khóa
       ===================================================== */
    public function search_products($keyword) {
        $kw = $this->db->escape(mb_strtolower($keyword, 'UTF-8'));

        $sql = "
            SELECT *
            FROM tbl_product
            WHERE 
                LOWER(product_name) LIKE '$kw %'
                OR LOWER(product_name) LIKE '% $kw %'
                OR LOWER(product_name) LIKE '% $kw'
            ORDER BY product_id DESC
        ";

        return $this->db->select($sql);
    }

    public function count_search_products($keyword) {
        $kw = $this->db->escape(mb_strtolower($keyword, 'UTF-8'));

        $sql = "
            SELECT COUNT(*) AS total
            FROM tbl_product
            WHERE 
                LOWER(product_name) LIKE '$kw %'
                OR LOWER(product_name) LIKE '% $kw %'
                OR LOWER(product_name) LIKE '% $kw'
        ";

        $rs = $this->db->select($sql);
        return $rs ? (int)$rs->fetch_assoc()['total'] : 0;
    }

    public function search_products_paging($keyword, $limit, $offset) {
        $kw = $this->db->escape(mb_strtolower($keyword, 'UTF-8'));
        $limit  = (int)$limit;
        $offset = (int)$offset;

        $sql = "
            SELECT *
            FROM tbl_product
            WHERE 
                LOWER(product_name) LIKE '$kw %'
                OR LOWER(product_name) LIKE '% $kw %'
                OR LOWER(product_name) LIKE '% $kw'
            ORDER BY product_id DESC
            LIMIT $limit OFFSET $offset
        ";

        return $this->db->select($sql);
    }

    /* =====================================================
       15. Sản phẩm mới nhất
       ===================================================== */
    public function get_new_products($limit = 8) {
        $limit = (int)$limit;

        $sql = "
            SELECT *
            FROM tbl_product
            ORDER BY product_id DESC
            LIMIT $limit
        ";
        return $this->db->select($sql);
    }

    /* =====================================================
       16. Sản phẩm hot sale
       ===================================================== */
    public function get_hot_sale_products($limit = 8) {
        $limit = (int)$limit;

        $sql = "
            SELECT *
            FROM tbl_product
            WHERE product_sale > 0 AND product_sale < product_price
            ORDER BY (product_price - product_sale) DESC
            LIMIT $limit
        ";
        return $this->db->select($sql);
    }

    public function count_sale_products() {
        $sql = "
            SELECT COUNT(*) AS total 
            FROM tbl_product 
            WHERE product_sale > 0 AND product_sale < product_price
        ";
        $rs = $this->db->select($sql);
        return $rs ? (int)$rs->fetch_assoc()['total'] : 0;
    }

    /* =====================================================
       17. Lấy ID sản phẩm cuối cùng
       ===================================================== */
    public function get_last_id() {
        $sql = "SELECT MAX(product_id) AS max_id FROM tbl_product";
        $rs = $this->db->select($sql);
        return $rs ? (int)$rs->fetch_assoc()['max_id'] : 0;
    }
}
?>
