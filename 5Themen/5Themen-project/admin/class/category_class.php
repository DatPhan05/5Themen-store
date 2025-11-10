<?php
require_once __DIR__ . '/../database.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Thêm danh mục
    public function insert_category($name){
        $name = $this->db->escape($name);
        $query = "INSERT INTO tbl_category (category_name) VALUES ('$name')";
        return $this->db->insert($query);
    }

    // Hiển thị tất cả danh mục
    public function show_category(){
        $query = "SELECT * FROM tbl_category ORDER BY category_id ASC";
        return $this->db->select($query);
    }

    // Lấy 1 danh mục theo ID
    public function get_category($id){
        $id = (int)$id;
        $query = "SELECT * FROM tbl_category WHERE category_id = $id";
        $result = $this->db->select($query);
        return $result ? $result->fetch_assoc() : null;
    }

    // Cập nhật danh mục
    public function update_category($id, $name){
        $id = (int)$id;
        $name = $this->db->escape($name);
        $query = "UPDATE tbl_category SET category_name = '$name' WHERE category_id = $id";
        return $this->db->update($query);
    }

    // Xóa danh mục
    public function delete_category($id){
        $id = (int)$id;
        $query = "DELETE FROM tbl_category WHERE category_id = $id";
        return $this->db->delete($query);
    }
}
?>
