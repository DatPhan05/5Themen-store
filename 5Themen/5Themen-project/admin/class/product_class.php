<?php
$filepath = realpath(dirname(__FILE__));

require_once $filepath . '/../../include/database.php';
require_once $filepath . '/../../include/helpers.php';

class Product {
    private $db;
    public function __construct() { 
        $this->db = new Database(); 
    }

    // =======================
    // Thêm sản phẩm
    // =======================
    public function insert_product($name, $category_id, $brand_id, $price, $sale_price, $desc, $thumb){
        $name  = $this->db->escape($name);
        $desc  = $this->db->escape($desc);
        $thumb = $this->db->escape($thumb);

        $cid = (int)$category_id;
        $bid = (int)$brand_id;
        $price = (float)$price;
        $sale  = (float)$sale_price;

        $query = "INSERT INTO tbl_product 
                (product_name, category_id, brand_id, product_price, product_sale, product_desc, product_img)
                VALUES 
                ('$name', $cid, $bid, $price, $sale, '$desc', '$thumb')";
        return $this->db->insert($query);
    }

    // =======================
    // Lấy tất cả sản phẩm
    // =======================
    public function get_all_products() {
        $query = "SELECT p.*, c.category_name, b.brand_name
                  FROM tbl_product AS p
                  JOIN tbl_category AS c ON p.category_id = c.category_id
                  LEFT JOIN tbl_brand AS b ON p.brand_id = b.brand_id
                  ORDER BY p.product_id DESC";
        return $this->db->select($query);
    }

    // =======================
    // Lấy sản phẩm theo category
    // =======================
    public function get_product_by_category($category_id) {
        $cid = (int)$category_id;
        $query = "
            SELECT p.*, c.category_name, b.brand_name
            FROM tbl_product p
            JOIN tbl_category c ON p.category_id = c.category_id
            LEFT JOIN tbl_brand b ON p.brand_id = b.brand_id
            WHERE p.category_id = $cid
            ORDER BY p.product_id DESC";
        return $this->db->select($query);
    }

    // =======================
    // Lấy sản phẩm theo category + brand
    // =======================
    public function get_product_by_category_brand($category_id, $brand_id) {
        $cid = (int)$category_id;
        $bid = (int)$brand_id;

        $query = "
            SELECT p.*, c.category_name, b.brand_name
            FROM tbl_product p
            JOIN tbl_category c ON p.category_id = c.category_id
            LEFT JOIN tbl_brand b ON p.brand_id = b.brand_id
            WHERE p.category_id = $cid AND p.brand_id = $bid
            ORDER BY p.product_id DESC";

        return $this->db->select($query);
    }

    // =======================
    // Lấy sản phẩm theo id
    // =======================
    public function get_product($id){
        $id = (int)$id;
        $query = "SELECT * FROM tbl_product WHERE product_id = $id";
        $rs = $this->db->select($query);
        return $rs ? $rs->fetch_assoc() : null;
    }

    // =======================
    // Xóa
    // =======================
    public function delete_product($id){
        $id = (int)$id;
        $query = "DELETE FROM tbl_product WHERE product_id = $id";
        return $this->db->delete($query);
    }
    public function get_product_by_parent($parent_id){
    $parent_id = (int)$parent_id;

    $sql = "
        SELECT p.* FROM tbl_product p
        INNER JOIN tbl_category c ON p.category_id = c.category_id
        WHERE c.parent_id = $parent_id
        ORDER BY p.product_id DESC
    ";

    return $this->db->select($sql);
}
public function show_product() {
    return $this->get_all_products();
}

}
?>
