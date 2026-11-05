<?php
require_once __DIR__ . '/include/session.php';
Session::init();

require_once __DIR__ . '/include/database.php';
require_once __DIR__ . '/partials/header.php';

$db = new Database();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql = "SELECT * FROM tbl_order WHERE order_id = $id LIMIT 1";
$rs  = $db->select($sql);
$order = $rs ? $rs->fetch_assoc() : null;

if (!$order) {
    echo "<div class='container' style='padding: 60px 20px; text-align: center;'>";
    echo "<h2>❌ Không tìm thấy đơn hàng #{$id}!</h2>";
    echo "<p><a href='trangchu.php' style='color: #007bff; text-decoration: underline;'>Quay lại trang chủ</a></p>";
    echo "</div>";
    require_once __DIR__ . '/partials/footer.php';
    exit;
}

// Truy vấn sản phẩm từ tbl_order_detail liên kết tbl_product
$sql_items = "
    SELECT 
        od.*, 
        p.product_name, 
        p.product_img
    FROM tbl_order_detail od
    LEFT JOIN tbl_product p ON od.product_id = p.product_id
    WHERE od.order_id = $id
    ORDER BY od.detail_id ASC
";
$items = $db->select($sql_items);

// Xử lý đường dẫn ảnh sản phẩm
function getProductImg($path) {
    if (!$path) return 'images/logo.png';
    if (strpos($path, 'admin/uploads/') === 0) return $path;
    return 'admin/uploads/' . ltrim($path, '/');
}
?>

<style>
.success-page {
    padding: 60px 15px;
    background: #f8f9fa;
    min-height: 70vh;
}
.success-card {
    max-width: 680px;
    margin: 0 auto;
    background: #fff;
    border-radius: 16px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.06);
    text-align: center;
}
.success-icon {
    font-size: 56px;
    color: #2ed573;
    margin-bottom: 16px;
}
.success-card h1 {
    font-size: 26px;
    color: #2f3542;
    margin-bottom: 8px;
    font-weight: 700;
}
.success-card .subtitle {
    color: #747d8c;
    font-size: 15px;
    margin-bottom: 25px;
}
.order-info-box {
    background: #f1f2f6;
    border-radius: 12px;
    padding: 20px;
    text-align: left;
    margin-bottom: 25px;
}
.order-info-box h2 {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 12px;
    border-bottom: 1px dashed #ced6e0;
    padding-bottom: 8px;
}
.order-info-box p {
    font-size: 14px;
    color: #57606f;
    margin: 6px 0;
    line-height: 1.5;
}
.order-info-box strong {
    color: #2f3542;
}
.success-products {
    margin-top: 20px;
    border-top: 1px solid #e4e7eb;
    padding-top: 20px;
    text-align: left;
}
.success-products h3 {
    font-size: 16px;
    font-weight: 700;
    color: #333;
    margin-bottom: 15px;
}
.success-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 12px 0;
    border-bottom: 1px solid #f1f2f6;
    gap: 15px;
}
.success-item-thumb {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    background: #eee;
}
.success-item-info {
    flex: 1;
}
.success-item-info .name {
    font-size: 14px;
    font-weight: 600;
    color: #2f3542;
}
.success-item-info .meta {
    font-size: 12px;
    color: #747d8c;
}
.success-item-price {
    font-size: 14px;
    font-weight: 700;
    color: #ff4757;
}
.success-actions {
    margin-top: 30px;
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}
.btn-home, .btn-orders {
    display: inline-block;
    padding: 12px 24px;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.2s;
}
.btn-home {
    background: #2f3542;
    color: #fff !important;
}
.btn-home:hover {
    background: #000;
}
.btn-orders {
    background: #f1f2f6;
    color: #2f3542 !important;
}
.btn-orders:hover {
    background: #e4e7eb;
}
</style>

<section class="success-page">
    <div class="success-card">
        <div class="success-icon">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1>ĐẶT HÀNG THÀNH CÔNG!</h1>
        <p class="subtitle">Cảm ơn bạn đã tin tưởng và mua sắm tại <strong>5Themen Store</strong>.</p>

        <div class="order-info-box">
            <h2>Mã đơn hàng: #<?= (int)$id ?></h2>
            <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['fullname']) ?></p>
            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
            <p><strong>Địa chỉ nhận hàng:</strong> <?= htmlspecialchars($order['address']) ?></p>
            <p><strong>Phương thức thanh toán:</strong> <?= strtoupper(htmlspecialchars($order['payment_method'])) ?></p>
            <p><strong>Tổng giá trị đơn:</strong> <span style="color:#ff4757; font-weight:700; font-size:15px;"><?= number_format($order['total_price'] ?? 0) ?>đ</span></p>
        </div>

        <div class="success-products">
            <h3>Chi tiết sản phẩm đã đặt:</h3>
            <?php if ($items && $items->num_rows > 0): ?>
                <?php while ($i = $items->fetch_assoc()): ?>
                    <div class="success-item">
                        <img src="<?= htmlspecialchars(getProductImg($i['product_img'] ?? '')) ?>" class="success-item-thumb" alt="">
                        <div class="success-item-info">
                            <div class="name"><?= htmlspecialchars($i['product_name'] ?? 'Sản phẩm') ?></div>
                            <div class="meta">
                                Size: <?= htmlspecialchars($i['size'] ?? 'L') ?> · Số lượng: <?= (int)$i['qty'] ?>
                            </div>
                        </div>
                        <div class="success-item-price">
                            <?= number_format((float)$i['price'] * (int)$i['qty']) ?>đ
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="font-size: 13px; color: #888;">Chi tiết đơn hàng đang được xử lý.</p>
            <?php endif; ?>
        </div>

        <div class="success-actions">
            <a class="btn-orders" href="account.php?view=orders">Xem lịch sử đơn hàng</a>
            <a class="btn-home" href="trangchu.php">Tiếp tục mua sắm</a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/partials/footer.php'; ?>
