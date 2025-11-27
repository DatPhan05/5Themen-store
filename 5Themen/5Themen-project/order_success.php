<?php
require_once __DIR__ . '/include/database.php';
require_once __DIR__ . '/partials/header.php';

$db = new Database();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM tbl_order WHERE order_id = $id LIMIT 1";
$rs  = $db->select($sql);
$order = $rs ? $rs->fetch_assoc() : null;

if (!$order) {
    die("Không tìm thấy đơn hàng!");
}

$sql_items = "SELECT * FROM tbl_order_items WHERE order_id = $id";
$items = $db->select($sql_items);
?>

<section class="success container">
    <h1>🎉 ĐẶT HÀNG THÀNH CÔNG!</h1>
    <p>Cảm ơn bạn đã mua sắm tại 5Themen.</p>

    <div class="success-box">
        <h2>Mã đơn: #<?= $id ?></h2>
        <p><strong>Khách hàng:</strong> <?= $order['fullname'] ?></p>
        <p><strong>Điện thoại:</strong> <?= $order['phone'] ?></p>
        <p><strong>Địa chỉ:</strong> <?= $order['address'] ?></p>
        <p><strong>Phương thức thanh toán:</strong> <?= strtoupper($order['payment_method']) ?></p>
        <p><strong>Tổng tiền:</strong> <?= number_format($order['total_amount']) ?>đ</p>
    </div>

    <h3>Sản phẩm đã mua</h3>
    <div class="success-products">
        <?php while ($i = $items->fetch_assoc()): ?>
            <div class="success-item">
                <p><?= $i['product_name'] ?> (x<?= $i['qty'] ?>)</p>
                <span><?= number_format($i['price'] * $i['qty']) ?>đ</span>
            </div>
        <?php endwhile; ?>
    </div>

    <a class="btn-primary" href="trangchu.php">Tiếp tục mua sắm</a>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
