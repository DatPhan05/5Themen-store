<?php
require_once __DIR__ . '/../database.php';

class Product {
    private $db;
    public function __construct() { 
        $this->db = new Database(); 
    }

    // Thêm sản phẩm
    public function insert_product($name, $category_id, $brand_id, $price, $sale_price, $desc, $thumb, $gallery = []){
        $name = $this->db->escape($name);
        $desc = $this->db->escape($desc);
        $thumb = $this->db->escape($thumb);
        $cid = (int)$category_id;
        $bid = (int)$brand_id;
        $price = (float)$price;
        $sale = (float)$sale_price;

        $query = "INSERT INTO tbl_product 
                    (product_name, category_id, brand_id, product_price, product_sale, product_desc, product_img)
                  VALUES 
                    ('$name', $cid, $bid, $price, $sale, '$desc', '$thumb')";
        return $this->db->insert($query);
    }

    // Hiển thị sản phẩm
    public function show_product(){
        $query = "SELECT p.product_id, p.product_name, p.product_price, p.product_sale, p.product_img,
                         c.category_name, b.brand_name
                  FROM tbl_product p
                  JOIN tbl_category c ON p.category_id = c.category_id
                  JOIN tbl_brand b ON p.brand_id = b.brand_id
                  ORDER BY p.product_id DESC";
        return $this->db->select($query);
    }

    // Lấy sản phẩm theo id
    public function get_product($id){
        $id = (int)$id;
        $query = "SELECT * FROM tbl_product WHERE product_id = $id";
        $rs = $this->db->select($query);
        return $rs ? $rs->fetch_assoc() : null;
    }

    // Cập nhật sản phẩm
    public function update_product($id, $name, $category_id, $brand_id, $price, $sale_price, $desc, $thumb = null){
        $id = (int)$id;
        $cid = (int)$category_id;
        $bid = (int)$brand_id;
        $price = (float)$price;
        $sale = (float)$sale_price;
        $name = $this->db->escape($name);
        $desc = $this->db->escape($desc);

        $setThumb = $thumb ? ", product_img = '".$this->db->escape($thumb)."'" : "";

        $query = "UPDATE tbl_product 
                  SET product_name = '$name', category_id = $cid, brand_id = $bid, 
                      product_price = $price, product_sale = $sale, product_desc = '$desc' $setThumb
                  WHERE product_id = $id";
        return $this->db->update($query);
    }

    // Xóa sản phẩm
    public function delete_product($id){
        $id = (int)$id;
        $query = "DELETE FROM tbl_product WHERE product_id = $id";
        return $this->db->delete($query);
    }
}
?>
