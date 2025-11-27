<?php

require_once __DIR__ . '/../../include/database.php';

class Order
{
    private $db;
    private $conn;

    public function __construct()
    {
        $this->db   = new Database();
        $this->conn = $this->db->link;
    }

    /* =====================================================
       1. Lấy danh sách đơn hàng (có thể lọc theo trạng thái)
       ===================================================== */
    public function getAll($status = null)
    {
        $sql = "SELECT * FROM tbl_order";

        if ($status !== null && $status !== '') {
            $statusEsc = $this->conn->real_escape_string($status);
            $sql .= " WHERE status = '$statusEsc'";
        }

        $sql .= " ORDER BY order_id ASC";
        return $this->conn->query($sql);
    }

    /* =====================================================
       2. Lấy thông tin một đơn hàng theo ID
       ===================================================== */
    public function getById($orderId)
    {
        $orderId = (int)$orderId;

        $sql = "
            SELECT *
            FROM tbl_order
            WHERE order_id = $orderId
            LIMIT 1
        ";

        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }

    /* =====================================================
       3. Lấy danh sách item trong đơn hàng
       ===================================================== */
    public function getItems($orderId)
    {
        $orderId = (int)$orderId;

        $sql = "
            SELECT 
                d.*, 
                p.product_name
            FROM tbl_order_detail AS d
            LEFT JOIN tbl_product AS p 
                   ON d.product_id = p.product_id
            WHERE d.order_id = $orderId
            ORDER BY d.detail_id ASC
        ";

        return $this->conn->query($sql);
    }

    /* =====================================================
       4. Lấy đơn hàng + danh sách items + tổng tiền tính lại
       ===================================================== */
    public function getOrderWithTotal($orderId)
    {
        $order = $this->getById($orderId);
        if (!$order) {
            return [null, null];
        }

        $orderId = (int)$orderId;

        // Lấy danh sách sản phẩm trong đơn
        $items = $this->getItems($orderId);

        // Tính tổng từ chi tiết đơn hàng
        $sumSql = "
            SELECT SUM(price * qty) AS calc_total 
            FROM tbl_order_detail 
            WHERE order_id = $orderId
        ";

        $sumRes = $this->conn->query($sumSql);
        $calcTotal = 0;

        if ($sumRes && $row = $sumRes->fetch_assoc()) {
            $calcTotal = (float)$row['calc_total'];
        }

        $order['calc_total'] = $calcTotal;

        return [$order, $items];
    }

    /* =====================================================
       5. Cập nhật trạng thái đơn hàng
       ===================================================== */
    public function updateStatus($orderId, $status)
    {
        $orderId   = (int)$orderId;
        $statusEsc = $this->conn->real_escape_string($status);

        $sql = "
            UPDATE tbl_order
            SET status = '$statusEsc'
            WHERE order_id = $orderId
        ";

        return $this->conn->query($sql);
    }

    /* =====================================================
       6. Xóa đơn hàng (bao gồm chi tiết đơn)
       ===================================================== */
    public function delete($orderId)
    {
        $orderId = (int)$orderId;

        // Xóa chi tiết đơn hàng
        $this->conn->query("
            DELETE FROM tbl_order_detail 
            WHERE order_id = $orderId
        ");

        // Xóa đơn
        return $this->conn->query("
            DELETE FROM tbl_order 
            WHERE order_id = $orderId
        ");
    }
}
?>
