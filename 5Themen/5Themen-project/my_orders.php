<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/include/session.php";
require_once __DIR__ . "/include/database.php";

// Nếu chưa login thì bắt đăng nhập
if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = (int)$_SESSION['user_id'];

$db   = new Database();
$conn = $db->link;

// Lấy danh sách đơn hàng của user
$sql = "
    SELECT order_id, created_at, total_price, payment_method, status
    FROM tbl_order
    WHERE user_id = $userId
    ORDER BY order_id DESC
";
$result = $conn->query($sql);

require __DIR__ . "/partials/header.php";
?>

<section class="account-orders">
    <div class="container">
        <h1 class="account-title">Đơn hàng của tôi</h1>

        <?php if ($result && $result->num_rows > 0): ?>
            <div class="order-list">
                <?php while ($row = $result->fetch_assoc()): 
                    $status = $row['status'];
                    // Map status sang text & class
                    switch ($status) {
                        case 'pending':
                            $statusText  = 'Chờ xử lý';
                            $statusClass = 'badge-pending';
                            break;
                        case 'processing':
                            $statusText  = 'Đang chuẩn bị hàng';
                            $statusClass = 'badge-processing';
                            break;
                        case 'shipping':
                            $statusText  = 'Đang giao hàng';
                            $statusClass = 'badge-shipping';
                            break;
                        case 'completed':
                            $statusText  = 'Đã giao';
                            $statusClass = 'badge-completed';
                            break;
                        case 'cancelled':
                            $statusText  = 'Đã hủy';
                            $statusClass = 'badge-cancelled';
                            break;
                        default:
                            $statusText  = $status;
                            $statusClass = 'badge-pending';
                            break;
                    }
                ?>
                    <div class="order-card">
                        <div class="order-card-top">
                            <div>
                                <div class="order-code">Mã đơn: #<?= (int)$row['order_id'] ?></div>
                                <div class="order-date">Ngày đặt: <?= htmlspecialchars($row['created_at']) ?></div>
                            </div>
                            <div class="order-status <?= $statusClass ?>">
                                <?= $statusText ?>
                            </div>
                        </div>

                        <div class="order-card-middle">
                            <div class="order-payment">
                                <span>Thanh toán:</span>
                                <strong><?= strtoupper($row['payment_method']) ?></strong>
                            </div>
                            <div class="order-total">
                                <span>Tổng tiền:</span>
                                <strong><?= number_format($row['total_price'], 0, ',', '.') ?>đ</strong>
                            </div>
                        </div>

                        <div class="order-card-bottom">
                            <a href="order_detail.php?id=<?= (int)$row['order_id'] ?>" class="btn-outline">Xem chi tiết</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>

            <p>Bạn chưa có đơn hàng nào.</p>

        <?php endif; ?>
    </div>
</section>

<?php require __DIR__ . "/partials/footer.php"; ?>
