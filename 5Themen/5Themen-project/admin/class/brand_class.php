<?php
require_once __DIR__ . '/../database.php';

class Brand {
    private $db;
    public function __construct(){
        $this->db = new Database();
    }

    // Thêm loại sản phẩm
    public function insert_brand($category_id, $brand_name){
        $cid = (int)$category_id;
        $name = $this->db->escape($brand_name);
        $q = "INSERT INTO tbl_brand (brand_name, category_id) VALUES ('$name', $cid)";
        return $this->db->insert($q);
    }

    // Hiển thị danh sách loại sản phẩm + tên danh mục
    public function show_brand(){
    $query = "
        SELECT 
            b.brand_id, 
            b.brand_name, 
            c.category_name
        FROM tbl_brand AS b
        LEFT JOIN tbl_category AS c 
            ON b.category_id = c.category_id
        ORDER BY b.brand_id DESC
    ";
    return $this->db->select($query);
}




    public function get_brand($id){
        $id = (int)$id;
        $q = "SELECT brand_id, brand_name, category_id FROM tbl_brand WHERE brand_id = $id";
        $rs = $this->db->select($q);
        return $rs ? $rs->fetch_assoc() : null;
    }

    public function update_brand($id, $category_id, $brand_name){
        $id = (int)$id;
        $cid = (int)$category_id;
        $name = $this->db->escape($brand_name);
        $q = "UPDATE tbl_brand 
              SET brand_name = '$name', category_id = $cid 
              WHERE brand_id = $id";
        return $this->db->update($q);
    }

    public function delete_brand($id){
        $id = (int)$id;
        $q = "DELETE FROM tbl_brand WHERE brand_id = $id";
        return $this->db->delete($q);
    }
}
?>
