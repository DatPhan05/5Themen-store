<?php
// Từ /admin/class → lùi 2 cấp về /5Themen-project
$rootPath = dirname(__DIR__, 2); 
// __DIR__ = .../admin/class
// dirname(__DIR__, 2) = .../5Themen-project

// Include DB & helpers từ thư mục include/
require_once $rootPath . '/include/database.php';
require_once $rootPath . '/include/helpers.php';

class Category {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function insert_category($name, $parent_id = null) {
        $name   = $this->db->escape($name);
        $parent = $parent_id !== null ? (int)$parent_id : "NULL";

        $sql = "INSERT INTO tbl_category (category_name, parent_id) 
                VALUES ('$name', $parent)";
        return $this->db->insert($sql);
    }

    public function show_category() {
        $sql = "SELECT * FROM tbl_category ORDER BY parent_id ASC, category_id ASC";
        return $this->db->select($sql);
    }

    public function get_parent_categories() {
        $sql = "SELECT * FROM tbl_category 
                WHERE parent_id IS NULL 
                ORDER BY category_id ASC";
        return $this->db->select($sql);
    }

    public function get_children($parent_id) {
        $id  = (int)$parent_id;
        $sql = "SELECT * FROM tbl_category WHERE parent_id = $id ORDER BY category_name ASC";
        return $this->db->select($sql);
    }

    public function get_category($id) {
        $id  = (int)$id;
        $sql = "SELECT * FROM tbl_category WHERE category_id = $id LIMIT 1";
        $rs  = $this->db->select($sql);
        return $rs ? $rs->fetch_assoc() : null;
    }

    public function update_category($id, $name, $parent_id = null) {
        $id     = (int)$id;
        $name   = $this->db->escape($name);
        $parent = $parent_id !== null ? (int)$parent_id : "NULL";

        $sql = "UPDATE tbl_category 
                SET category_name = '$name', parent_id = $parent 
                WHERE category_id = $id";
        return $this->db->update($sql);
    }

    public function delete_category($id) {
        $id  = (int)$id;
        $sql = "DELETE FROM tbl_category WHERE category_id = $id";
        return $this->db->delete($sql);
    }
}
?>
