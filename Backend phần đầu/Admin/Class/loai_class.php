<?php
require_once __DIR__ . '/../csdl.php';

class LoaiSanPham {
    private $db;

    public function __construct() {
        $this->db = new CoSoDuLieu();
    }

    // Thêm loại
    public function them($id_danh_muc, $ten_loai) {
        $id_danh_muc = intval($id_danh_muc);
        $ten_loai = $this->db->anToan($ten_loai);
        $sql = "INSERT INTO dm_loai_san_pham (ten_loai, id_danh_muc) VALUES ('$ten_loai', $id_danh_muc)";
        return $this->db->thucThi($sql);
    }

    // Sửa loại
    public function sua($id, $id_danh_muc, $ten_loai) {
        $id = intval($id);
        $id_danh_muc = intval($id_danh_muc);
        $ten_loai = $this->db->anToan($ten_loai);
        $sql = "UPDATE dm_loai_san_pham 
                SET ten_loai = '$ten_loai', id_danh_muc = $id_danh_muc
                WHERE id_loai = $id";
        return $this->db->thucThi($sql);
    }

    // Xóa loại
    public function xoa($id) {
        $id = intval($id);
        $sql = "DELETE FROM dm_loai_san_pham WHERE id_loai = $id";
        return $this->db->thucThi($sql);
    }

    // Lấy danh sách loại sản phẩm
    public function danhsach() {
        $sql = "SELECT l.id_loai, l.ten_loai, d.ten_danh_muc
                FROM dm_loai_san_pham l
                JOIN dm_danh_muc d ON l.id_danh_muc = d.id_danh_muc
                ORDER BY l.id_loai DESC";
        return $this->db->chon($sql);
    }

    // Lấy chi tiết
    public function chitiet($id) {
        $id = intval($id);
        return $this->db->chon("SELECT * FROM dm_loai_san_pham WHERE id_loai = $id")->fetch_assoc();
    }
}
?>
