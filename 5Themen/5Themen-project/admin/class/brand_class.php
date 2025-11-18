<?php
require_once __DIR__ . '/../database.php';

class Brand {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function insert_brand($category_id, $brand_name){
        $cid  = (int)$category_id;
        $name = $this->db->escape($brand_name);

        $sql = "INSERT INTO tbl_brand (brand_name, category_id) 
                VALUES ('$name', $cid)";
        return $this->db->insert($sql);
    }

    public function show_brand(){
        $sql = "
            SELECT b.brand_id, b.brand_name, c.category_name
            FROM tbl_brand b
            LEFT JOIN tbl_category c ON b.category_id = c.category_id
            ORDER BY b.brand_id ASC
        ";
        return $this->db->select($sql);
    }

    public function get_brand($id){
        $id = (int)$id;
        $sql = "SELECT * FROM tbl_brand WHERE brand_id = $id LIMIT 1";

        $rs = $this->db->select($sql);
        return $rs ? $rs->fetch_assoc() : null;
    }

    public function get_brand_by_category($cid){
        $cid = (int)$cid;
        $sql = "SELECT * FROM tbl_brand WHERE category_id = $cid ORDER BY brand_name ASC";
        return $this->db->select($sql);
    }

    public function update_brand($id, $category_id, $brand_name){
        $id   = (int)$id;
        $cid  = (int)$category_id;
        $name = $this->db->escape($brand_name);

        $sql = "UPDATE tbl_brand 
                SET brand_name = '$name',
                    category_id = $cid
                WHERE brand_id = $id";

        return $this->db->update($sql);
    }

    public function delete_brand($id){
        $id = (int)$id;
        $sql = "DELETE FROM tbl_brand WHERE brand_id = $id";
        return $this->db->delete($sql);
    }
}
?>
