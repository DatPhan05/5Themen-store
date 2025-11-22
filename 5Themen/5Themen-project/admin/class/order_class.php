<?php
// admin/class/order_class.php
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

    /**
     * Lấy danh sách tất cả đơn hàng
     * Có thể lọc theo status: pending / processing / shipping / success / cancelled
     */
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

    /**
     * Lấy thông tin 1 đơn hàng
     */
    public function getById($orderId)
    {
        $orderId = (int)$orderId;
        $sql = "SELECT * FROM tbl_order WHERE order_id = $orderId LIMIT 1";
        $result = $this->conn->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }

    /**
     * Lấy danh sách sản phẩm trong 1 đơn
     */
    public function getItems($orderId)
    {
        $orderId = (int)$orderId;
        $sql = "
            SELECT d.*, p.product_name 
            FROM tbl_order_detail AS d
            LEFT JOIN tbl_product AS p ON d.product_id = p.product_id
            WHERE d.order_id = $orderId
            ORDER BY d.detail_id ASC
        ";
        return $this->conn->query($sql);
    }

    /**
     * Lấy đơn hàng + items + tổng tiền tính lại (cho hóa đơn)
     * Trả về: [ $order, $itemsResult ]
     *  - $order['calc_total'] = tổng tiền tính từ chi tiết
     */
    public function getOrderWithTotal($orderId)
    {
        $order = $this->getById($orderId);
        if (!$order) {
            return [null, null];
        }

        $orderId = (int)$orderId;

        // Lấy items
        $items = $this->getItems($orderId);

        // Tính tổng lại từ bảng chi tiết
        $sumSql = "SELECT SUM(price * qty) AS calc_total FROM tbl_order_detail WHERE order_id = $orderId";
        $sumRes = $this->conn->query($sumSql);
        $calcTotal = 0;
        if ($sumRes && $row = $sumRes->fetch_assoc()) {
            $calcTotal = (float)$row['calc_total'];
        }
        $order['calc_total'] = $calcTotal;

        return [$order, $items];
    }

    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus($orderId, $status)
    {
        $orderId   = (int)$orderId;
        $statusEsc = $this->conn->real_escape_string($status);

        $sql = "UPDATE tbl_order SET status = '$statusEsc' WHERE order_id = $orderId";
        return $this->conn->query($sql);
    }

    /**
     * Xóa đơn hàng (xóa cả chi tiết đơn)
     */
    public function delete($orderId)
    {
        $orderId = (int)$orderId;

        // Xóa chi tiết
        $this->conn->query("DELETE FROM tbl_order_detail WHERE order_id = $orderId");
        // Xóa đơn
        return $this->conn->query("DELETE FROM tbl_order WHERE order_id = $orderId");
    }
}
