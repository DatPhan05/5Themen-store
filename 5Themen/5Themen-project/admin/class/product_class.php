<?php
$filepath = realpath(dirname(__FILE__));

// Đảm bảo đường dẫn include chính xác
require_once $filepath . '/../../include/database.php';
require_once $filepath . '/../../include/helpers.php';

class Product {
    private $db;
    public function __construct() { 
        // Khởi tạo đối tượng Database
        $this->db = new Database(); 
    }

    // =======================
    // Thêm sản phẩm
    // Sử dụng tên cột theo cấu trúc tbl_product đã thống nhất
    // =======================
    public function insert_product($name, $category_id, $brand_id, $price, $sale_price, $desc, $thumb){
        // Sử dụng escape để tránh SQL Injection
        $name = $this->db->escape($name);
        $desc = $this->db->escape($desc);
        $thumb = $this->db->escape($thumb);

        // Chuyển đổi kiểu dữ liệu an toàn
        $cid = (int)$category_id;
        $bid = (int)$brand_id;
        $price = (float)$price;
        $sale = (float)$sale_price;

        $query = "INSERT INTO tbl_product 
                (ten_san_pham, id_danh_muc, id_loai, gia, gia_khuyen_mai, mo_ta, anh)
                VALUES 
                ('$name', $cid, $bid, $price, $sale, '$desc', '$thumb')";
                
        return $this->db->insert($query);
    }

    // =======================
    // Lấy tất cả sản phẩm
    // Sử dụng cột khóa chính 'id' và các tên cột chuẩn hóa
    // =======================
    public function get_all_products() {
        $query = "SELECT p.id, p.ten_san_pham, p.gia, p.gia_khuyen_mai, p.anh,
                         c.category_name, b.brand_name
                  FROM tbl_product AS p
                  -- Liên kết dựa trên id_danh_muc và id_loai
                  JOIN tbl_category AS c ON p.id_danh_muc = c.category_id
                  LEFT JOIN tbl_brand AS b ON p.id_loai = b.brand_id
                  ORDER BY p.id DESC"; // Đã sửa product_id thành id
        return $this->db->select($query);
    }

    // =======================
    // Lấy sản phẩm theo category
    // =======================
    public function get_product_by_category($category_id) {
        $cid = (int)$category_id;
        $query = "
            SELECT p.id, p.ten_san_pham, p.gia, p.gia_khuyen_mai, p.anh,
                   c.category_name, b.brand_name
            FROM tbl_product p
            JOIN tbl_category c ON p.id_danh_muc = c.category_id
            LEFT JOIN tbl_brand b ON p.id_loai = b.brand_id
            WHERE p.id_danh_muc = $cid
            ORDER BY p.id DESC"; // Đã sửa product_id thành id
        return $this->db->select($query);
    }

    // =======================
    // Lấy sản phẩm theo category + brand
    // =======================
    public function get_product_by_category_brand($category_id, $brand_id) {
        $cid = (int)$category_id;
        $bid = (int)$brand_id;

        $query = "
            SELECT p.id, p.ten_san_pham, p.gia, p.gia_khuyen_mai, p.anh,
                   c.category_name, b.brand_name
            FROM tbl_product p
            JOIN tbl_category c ON p.id_danh_muc = c.category_id
            LEFT JOIN tbl_brand b ON p.id_loai = b.brand_id
            WHERE p.id_danh_muc = $cid AND p.id_loai = $bid
            ORDER BY p.id DESC"; // Đã sửa product_id thành id

        return $this->db->select($query);
    }

    // =======================
    // Lấy sản phẩm theo id (sửa lỗi Unknown column 'product_id')
    // =======================
    public function get_product($id){
        $id = (int)$id;
        // Sử dụng khóa chính 'id'
        $query = "SELECT * FROM tbl_product WHERE id = $id"; 
        $rs = $this->db->select($query);
        // Trả về dữ liệu fetch_assoc hoặc null
        return $rs ? $rs->fetch_assoc() : null;
    }

    // =======================
    // Xóa (sửa lỗi Unknown column 'product_id')
    // =======================
    public function delete_product($id){
        $id = (int)$id;
        // Sử dụng khóa chính 'id'
        $query = "DELETE FROM tbl_product WHERE id = $id";
        return $this->db->delete($query);
    }
    
    // =======================
    // Lấy sản phẩm theo ID Danh mục cha
    // =======================
    public function get_product_by_parent($parent_id){
        $parent_id = (int)$parent_id;

        $sql = "
            SELECT p.* FROM tbl_product p
            -- Liên kết với id_danh_muc (product_table) và category_id (category_table)
            INNER JOIN tbl_category c ON p.id_danh_muc = c.category_id
            WHERE c.parent_id = $parent_id
            ORDER BY p.id DESC
        "; // Đã sửa product_id thành id

        return $this->db->select($sql);
    }
    
    // =======================
    // Hàm show_product (Alias)
    // =======================
    public function show_product() {
        return $this->get_all_products();
    }
}
?>