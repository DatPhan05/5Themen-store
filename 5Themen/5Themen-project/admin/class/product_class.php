<?php
$filepath = realpath(dirname(__FILE__));
require_once $filepath . '/../../include/database.php';

class Product {
    private $db;
    public function __construct() { 
        $this->db = new Database(); 
    }

    // ============================
    // Thêm sản phẩm
    // ============================
    public function insert_product($name, $category_id, $brand_id, $price, $sale_price, $desc, $thumb)
{
    // Escape để tránh SQL Injection
    $name  = $this->db->escape($name);
    $desc  = $this->db->escape($desc);
    $thumb = $this->db->escape($thumb);

    $cid   = (int)$category_id;
    $bid   = (int)$brand_id;
    $price = (float)$price;
    $sale  = (float)$sale_price;

    // LƯU NGUYÊN GIÁ TRỊ $thumb TRUYỀN VÀO
    $query = "
        INSERT INTO tbl_product
            (product_name, category_id, brand_id, product_price, product_sale, product_desc, product_img)
        VALUES
            ('$name', $cid, $bid, $price, $sale, '$desc', '$thumb')
    ";

    return $this->db->insert($query);
}


    // ============================
    // Lấy tất cả sản phẩm
    // ============================
    public function get_all_products() {
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
        ";

        return $this->db->select($query);
    }

    // ============================
    // Lấy sản phẩm theo category
    // ============================
    public function get_product_by_category($category_id) {
        $cid = (int)$category_id;

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
        ";

        return $this->db->select($query);
    }

    // ============================
    // Lấy sản phẩm theo category + brand
    // ============================
    public function get_product_by_category_brand($category_id, $brand_id) {
        $cid = (int)$category_id;
        $bid = (int)$brand_id;

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
        ";

        return $this->db->select($query);
    }

    // ============================
    // Lấy sản phẩm theo ID
    // ============================
    public function get_product($id){
        $id = (int)$id;
        $query = "SELECT * FROM tbl_product WHERE product_id = $id";
        $rs = $this->db->select($query);
        return $rs ? $rs->fetch_assoc() : null;
    }

    // ============================
    // Xóa sản phẩm
    // ============================
    public function delete_product($id){
        $id = (int)$id;
        $query = "DELETE FROM tbl_product WHERE product_id = $id";
        return $this->db->delete($query);
    }
    
    // ============================
    // Lấy 1 sản phẩm theo ID  ← mới thêm vào
    // ============================
    public function getOne($id){
        $id = (int)$id;
        $query = "SELECT * FROM tbl_product WHERE product_id = $id LIMIT 1";
        $rs = $this->db->select($query);
        return $rs ? $rs->fetch_assoc() : null;
    }
        // ============================
    // ĐẾM SẢN PHẨM THEO BỘ LỌC
    // ============================
    public function count_filtered_products($category_id = 0, $brand_id = 0, $min_price = 0, $max_price = 0) {
        $cid = (int)$category_id;
        $bid = (int)$brand_id;
        $min = (float)$min_price;
        $max = (float)$max_price;

        // Giá sử dụng: nếu có sale thì lấy sale, không thì lấy price
        $priceField = "CASE WHEN p.product_sale > 0 THEN p.product_sale ELSE p.product_price END";

        $where = "1=1";

        if ($cid > 0) {
            $where .= " AND p.category_id = $cid";
        }

        if ($bid > 0) {
            $where .= " AND p.brand_id = $bid";
        }

        if ($min > 0) {
            $where .= " AND $priceField >= $min";
        }

        if ($max > 0) {
            $where .= " AND $priceField <= $max";
        }

        $query = "
            SELECT COUNT(*) AS total
            FROM tbl_product p
            WHERE $where
        ";

        $rs = $this->db->select($query);
        if ($rs) {
            $row = $rs->fetch_assoc();
            return (int)$row['total'];
        }
        return 0;
    }

    // ============================
    // LẤY DS SẢN PHẨM THEO FILTER + SORT + PHÂN TRANG
    // ============================
    public function get_filtered_products(
        $category_id = 0,
        $brand_id    = 0,
        $min_price   = 0,
        $max_price   = 0,
        $sort        = 'newest',
        $limit       = 12,
        $offset      = 0
    ) {
        $cid   = (int)$category_id;
        $bid   = (int)$brand_id;
        $min   = (float)$min_price;
        $max   = (float)$max_price;
        $limit = (int)$limit;
        $offset= (int)$offset;

        if ($limit <= 0)  $limit  = 12;
        if ($offset < 0)  $offset = 0;

        $priceField = "CASE WHEN p.product_sale > 0 THEN p.product_sale ELSE p.product_price END";

        $where = "1=1";

        if ($cid > 0) {
            $where .= " AND p.category_id = $cid";
        }

        if ($bid > 0) {
            $where .= " AND p.brand_id = $bid";
        }

        if ($min > 0) {
            $where .= " AND $priceField >= $min";
        }

        if ($max > 0) {
            $where .= " AND $priceField <= $max";
        }

        // Sắp xếp
        switch ($sort) {
            case 'price_asc':
                $orderBy = "$priceField ASC";
                break;
            case 'price_desc':
                $orderBy = "$priceField ASC";
                break;
            case 'name_asc':
                $orderBy = "p.product_name ASC";
                break;
            case 'name_desc':
                $orderBy = "p.product_name ASC";
                break;
            case 'newest':
            default:
                $orderBy = "p.product_id ASC";
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
            LEFT JOIN tbl_brand b    ON p.brand_id    = b.brand_id
            WHERE $where
            ORDER BY $orderBy
            LIMIT $limit OFFSET $offset
        ";

        return $this->db->select($query);
    }

}
?>

