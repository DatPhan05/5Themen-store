<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

$db   = new Database();
$conn = $db->link;

// =======================
// 1. BẮT BUỘC ĐĂNG NHẬP
// =======================
if (empty($_SESSION['is_logged_in']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = (int)$_SESSION['user_id'];

// =======================
// 2. LẤY THÔNG TIN USER
// =======================
$sqlUser = "SELECT * FROM tbl_user WHERE user_id = '$user_id' LIMIT 1";
$result  = $conn->query($sqlUser);

if (!$result || $result->num_rows === 0) {
    header("Location: logout.php");
    exit;
}

$userData = $result->fetch_assoc();
$msg      = "";

// =======================
// HÀM XỬ LÝ ĐƯỜNG DẪN ẢNH SP
// =======================
function buildProductImgPath($img)
{
    $img = (string)$img;
    if ($img === '') return '';
    if (strpos($img, 'admin/uploads/') === 0) return $img;
    return 'admin/uploads/' . ltrim($img, '/');
}

// =======================
// 3. XỬ LÝ TAB HIỂN THỊ
// =======================
$view = $_GET['view'] ?? 'profile';
$view = in_array($view, ['profile', 'orders', 'password'], true) ? $view : 'profile';

$allowedStatus = ['all', 'pending', 'processing', 'shipping', 'completed', 'cancelled', 'returned'];
$statusFilter  = $_GET['status'] ?? 'all';
$statusFilter  = in_array($statusFilter, $allowedStatus, true) ? $statusFilter : 'all';

// =======================
// 4. CẬP NHẬT PROFILE
// =======================
if ($view === 'profile' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $conn->real_escape_string(trim($_POST['fullname'] ?? ''));
    $phone    = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    $address  = $conn->real_escape_string(trim($_POST['address'] ?? ''));

    $update = "
        UPDATE tbl_user
        SET fullname = '$fullname',
            phone    = '$phone',
            address  = '$address'
        WHERE user_id = '$user_id'
        LIMIT 1
    ";

    if ($conn->query($update)) {
        $userData['fullname'] = $fullname;
        $userData['phone']    = $phone;
        $userData['address']  = $address;
        $msg = "Cập nhật thành công!";
    } else {
        $msg = "Có lỗi xảy ra, vui lòng thử lại.";
    }
}

// =======================
// 4b. ĐỔI MẬT KHẨU
// =======================
if ($view === 'password' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $oldPass = trim($_POST['old_password'] ?? '');
    $newPass = trim($_POST['new_password'] ?? '');
    $rePass  = trim($_POST['re_password'] ?? '');

    if ($newPass === '' || $oldPass === '' || $rePass === '') {
        $msg = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($newPass !== $rePass) {
        $msg = "Xác nhận mật khẩu mới không khớp.";
    } else {
        $oldEsc = $conn->real_escape_string($oldPass);
        $sqlCheck = "SELECT password FROM tbl_user WHERE user_id = '$user_id' LIMIT 1";
        $rsCheck  = $conn->query($sqlCheck);

        if ($rsCheck && $rsCheck->num_rows > 0) {
            $row = $rsCheck->fetch_assoc();
            if ($row['password'] !== $oldEsc) {
                $msg = "Mật khẩu cũ không đúng.";
            } else {
                $newEsc = $conn->real_escape_string($newPass);
                $sqlUpdatePass = "UPDATE tbl_user SET password = '$newEsc' WHERE user_id = '$user_id' LIMIT 1";
                if ($conn->query($sqlUpdatePass)) {
                    $msg = "Đổi mật khẩu thành công!";
                } else {
                    $msg = "Không thể đổi mật khẩu. Vui lòng thử lại.";
                }
            }
        } else {
            $msg = "Tài khoản không tồn tại.";
        }
    }
}

// =======================
// 5. LẤY DANH SÁCH ĐƠN HÀNG
// =======================
$orders = [];

if ($view === 'orders') {
    $userPhone = trim($userData['phone'] ?? '');
    $where = "1=1";

    if ($userPhone !== '') {
        $phoneEsc = $conn->real_escape_string($userPhone);
        $where = "o.phone = '$phoneEsc'";
    }

    if ($statusFilter !== 'all') {
        $statusEsc = $conn->real_escape_string($statusFilter);
        $where .= " AND o.status = '$statusEsc'";
    }

    $sqlOrders = "
        SELECT 
            o.order_id,
            o.fullname,
            o.phone,
            o.address,
            o.payment_method,
            o.total_price,
            o.status,
            o.created_at,
            od.detail_id,
            od.product_id,
            od.qty,
            od.price      AS detail_price,
            od.size,
            p.product_name,
            p.product_img
        FROM tbl_order o
        LEFT JOIN tbl_order_detail od ON o.order_id = od.order_id
        LEFT JOIN tbl_product p       ON od.product_id = p.product_id
        WHERE $where
        ORDER BY o.order_id DESC, od.detail_id ASC
    ";

    $rs = $conn->query($sqlOrders);
    if ($rs) {
        while ($row = $rs->fetch_assoc()) {
            $oid = (int)$row['order_id'];
            if (!isset($orders[$oid])) {
                $orders[$oid] = [
                    'info'  => [
                        'order_id'       => $oid,
                        'fullname'       => $row['fullname'],
                        'phone'          => $row['phone'],
                        'address'        => $row['address'],
                        'payment_method' => $row['payment_method'],
                        'total_price'    => $row['total_price'],
                        'status'         => $row['status'],
                        'created_at'     => $row['created_at'],
                    ],
                    'items' => [],
                ];
            }

            if (!empty($row['product_id'])) {
                $img = buildProductImgPath($row['product_img'] ?? '');
                $orders[$oid]['items'][] = [
                    'product_id'   => $row['product_id'],
                    'product_name' => $row['product_name'],
                    'product_img'  => $img,
                    'qty'          => $row['qty'],
                    'size'         => $row['size'],
                    'price'        => $row['detail_price'],
                ];
            }
        }
    }
}

// =======================
// LABEL STATUS
// =======================
function renderStatusLabel($status)
{
    $status = strtolower((string)$status);
    switch ($status) {
        case 'pending':
            return ['Chờ xác nhận', 'badge-pending'];
        case 'processing':
            return ['Đang xử lý', 'badge-processing'];
        case 'shipping':
            return ['Đang giao hàng', 'badge-shipping'];
        case 'completed':
            return ['Đã giao hàng', 'badge-completed'];
        case 'cancelled':
            return ['Đã hủy', 'badge-cancelled'];
        case 'returned':
            return ['Trả lại', 'badge-returned'];
        default:
            return ['Đang xử lý', 'badge-processing'];
    }
}

// Breadcrumb đơn giản cho account
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
    ['text' => 'Tài khoản'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tài khoản của tôi - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
    <style>
        /* (giữ nguyên toàn bộ CSS bạn đã có – mình không sửa phần này) */
        .account-container {
            padding: 40px 0 60px;
            background: #f5f5f7;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }
        .account-wrapper {
            width: 100%;
            margin: 0 auto;
            padding: 0 20px;
            display: grid;
            grid-template-columns: 280px 1fr;
            gap: 28px;
        }
        .account-sidebar {
            background: #fff;
            border-radius: 16px;
            padding: 20px 18px;
            box-shadow: 0 10px 30px rgba(15, 23, 42, 0.06);
        }
        .account-sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 18px;
        }
        .account-avatar {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg,#4f46e5,#6366f1);
            display: flex;
            align-items: center;
            justify-content: center;
            color:#fff;
            font-weight: 600;
            font-size: 22px;
            margin-right: 12px;
        }
        .account-sidebar-header .name {
            font-weight: 600;
            font-size: 15px;
            color:#111827;
        }
        .account-sidebar-header .email {
            font-size: 13px;
            color:#6b7280;
        }
        .account-menu {
            list-style:none;
            padding:0;
            margin: 10px 0 0;
        }
        .account-menu li { margin-bottom: 4px; }
        .account-menu a {
            display:flex;
            align-items:center;
            justify-content:space-between;
            padding:9px 10px;
            border-radius:10px;
            font-size:14px;
            color:#374151;
            text-decoration:none;
            transition: all .2s ease;
        }
        .account-menu a span.label {
            display:flex;
            align-items:center;
            gap:8px;
        }
        .account-menu a .icon {
            width:20px;
            text-align:center;
        }
        .account-menu a:hover {
            background:#f3f4ff;
            color:#111827;
        }
        .account-menu a.active {
            background:#111827;
            color:#f9fafb;
        }
        .account-menu a.logout { color:#b91c1c; }
        .account-menu a.logout:hover { background:#fee2e2; }

        .account-content {
            background:#fff;
            border-radius:16px;
            padding:22px 24px 26px;
            box-shadow:0 10px 30px rgba(15,23,42,.06);
            min-height:340px;
        }
        .account-content h2 {
            font-size:20px;
            margin-bottom:16px;
            color:#111827;
        }
        .account-intro {
            font-size:13px;
            color:#6b7280;
            margin-bottom:20px;
        }
        .account-form {
            max-width:520px;
        }
        .account-form .form-group {
            margin-bottom:14px;
        }
        .account-form label {
            display:block;
            font-size:13px;
            font-weight:500;
            margin-bottom:4px;
            color:#374151;
        }
        .account-form input,
        .account-form textarea,
        .account-form select {
            width:100%;
            padding:9px 10px;
            border-radius:10px;
            border:1px solid #e5e7eb;
            font-size:14px;
            outline:none;
            transition:border-color .15s ease, box-shadow .15s ease;
            background:#f9fafb;
        }
        .account-form input:focus,
        .account-form textarea:focus,
        .account-form select:focus {
            border-color:#4f46e5;
            box-shadow:0 0 0 1px rgba(79,70,229,.3);
            background:#fff;
        }
        .account-form textarea {
            min-height:80px;
            resize:vertical;
        }
        .account-actions { margin-top:10px; }
        .btn-primary {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:9px 18px;
            border-radius:999px;
            border:none;
            background:#111827;
            color:#f9fafb;
            font-size:14px;
            font-weight:500;
            cursor:pointer;
            transition: background .15s ease, transform .1s ease;
        }
        .btn-primary:hover { background:#4b5563; }
        .btn-primary:active { transform:translateY(1px); }
        .msg {
            padding:8px 12px;
            border-radius:10px;
            background:#ecfdf5;
            color:#166534;
            font-size:13px;
            margin-bottom:10px;
            display:inline-block;
        }
        .msg-error {
            background:#fef2f2;
            color:#b91c1c;
        }

        .account-orders-tabs {
            display:flex;
            flex-wrap:wrap;
            gap:8px;
            margin:6px 0 18px;
        }
        .account-orders-tabs a {
            padding:6px 12px;
            border-radius:999px;
            font-size:13px;
            text-decoration:none;
            border:1px solid #e5e7eb;
            color:#4b5563;
            background:#fff;
            transition:all .15s ease;
        }
        .account-orders-tabs a.active,
        .account-orders-tabs a:hover {
            background:#111827;
            color:#f9fafb;
            border-color:#111827;
        }
        .orders-empty {
            font-size:14px;
            color:#6b7280;
        }

        .order-list {
            display:flex;
            flex-direction:column;
            gap:14px;
        }
        .order-card {
            border-radius:14px;
            border:1px solid #e5e7eb;
            padding:12px 14px 10px;
            background:#f9fafb;
        }
        .order-card-header {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:8px;
        }
        .order-meta {
            font-size:12px;
            color:#6b7280;
        }
        .order-meta strong { color:#111827; }
        .order-status-badge {
            font-size:11px;
            padding:4px 10px;
            border-radius:999px;
            font-weight:500;
        }
        .badge-processing { background:#fef3c7; color:#92400e;}
        .badge-pending { background:#e5e7eb; color:#374151;}
        .badge-shipping { background:#dbeafe; color:#1d4ed8;}
        .badge-completed { background:#dcfce7; color:#166534;}
        .badge-cancelled { background:#fee2e2; color:#b91c1c;}
        .badge-returned { background:#f3e8ff; color:#6d28d9;}

        .order-products {
            border-radius:10px;
            background:#fff;
            padding:8px 10px;
            margin-bottom:8px;
        }
        .order-product-item {
            display:flex;
            gap:10px;
            padding:6px 0;
            border-bottom:1px solid #f3f4f6;
        }
        .order-product-item:last-child {
            border-bottom:none;
        }
        .order-product-thumb img {
            width:52px;
            height:52px;
            object-fit:cover;
            border-radius:8px;
            border:1px solid #e5e7eb;
        }
        .order-product-info { flex:1; }
        .order-product-name {
            font-size:14px;
            color:#111827;
            margin-bottom:2px;
        }
        .order-product-meta {
            font-size:12px;
            color:#6b7280;
        }
        .order-product-price {
            font-size:13px;
            font-weight:500;
            color:#111827;
        }
        .order-card-footer {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-top:4px;
            font-size:13px;
        }
        .order-total strong { font-size:14px; }
        .btn-order-again {
            font-size:13px;
            text-decoration:none;
            padding:6px 12px;
            border-radius:999px;
            border:1px solid #e5e7eb;
            color:#374151;
            background:#fff;
        }
        .btn-order-again:hover { background:#f3f4ff; }

        .password-form { max-width:420px; }

        @media (max-width: 900px) {
            .account-wrapper { grid-template-columns: 1fr; }
        }
        @media (max-width: 600px) {
            .account-content { padding:18px 16px 22px; }
            .account-sidebar { padding:16px 14px; }
        }
    </style>
</head>
<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>

<section class="account-container">
    <div class="account-wrapper">

        <!-- SIDEBAR -->
        <aside class="account-sidebar">
            <div class="account-sidebar-header">
                <div class="account-avatar">
                    <?php
                    $name = trim($userData['fullname'] ?? 'User');
                    echo mb_strtoupper(mb_substr($name, 0, 1, 'UTF-8'), 'UTF-8');
                    ?>
                </div>
                <div>
                    <div class="name"><?= htmlspecialchars($userData['fullname'] ?? 'Khách hàng') ?></div>
                    <div class="email"><?= htmlspecialchars($userData['email'] ?? '') ?></div>
                </div>
            </div>

            <ul class="account-menu">
                <li>
                    <a href="account.php?view=profile" class="<?= $view === 'profile' ? 'active' : '' ?>">
                        <span class="label">
                            <span class="icon"><i class="fa-regular fa-user"></i></span>
                            Thông tin tài khoản
                        </span>
                    </a>
                </li>
                <li>
                    <a href="account.php?view=orders" class="<?= $view === 'orders' ? 'active' : '' ?>">
                        <span class="label">
                            <span class="icon"><i class="fa-regular fa-clipboard"></i></span>
                            Đơn hàng của tôi
                        </span>
                    </a>
                </li>
                <li>
                    <a href="account.php?view=password" class="<?= $view === 'password' ? 'active' : '' ?>">
                        <span class="label">
                            <span class="icon"><i class="fa-solid fa-key"></i></span>
                            Đổi mật khẩu
                        </span>
                    </a>
                </li>
                <li>
                    <a href="giohang.php">
                        <span class="label">
                            <span class="icon"><i class="fa-solid fa-cart-shopping"></i></span>
                            Quản lý giỏ hàng
                        </span>
                    </a>
                </li>
                <li>
                    <a href="logout.php" class="logout">
                        <span class="label">
                            <span class="icon"><i class="fa-solid fa-arrow-right-from-bracket"></i></span>
                            Đăng xuất
                        </span>
                    </a>
                </li>
            </ul>
        </aside>

        <!-- MAIN CONTENT -->
        <main class="account-content">

            <?php if (!empty($msg)): ?>
                <p class="msg"><?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>

            <?php if ($view === 'profile'): ?>

                <h2>Thông tin của tôi</h2>
                <p class="account-intro">Cập nhật thông tin liên hệ để 5Themen giao hàng nhanh và chính xác hơn.</p>

                <form method="post" class="account-form">
                    <div class="form-group">
                        <label>Họ tên</label>
                        <input type="text" name="fullname"
                               value="<?= htmlspecialchars($userData['fullname'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Email (không thể thay đổi)</label>
                        <input type="email" value="<?= htmlspecialchars($userData['email'] ?? '') ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" name="phone"
                               value="<?= htmlspecialchars($userData['phone'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ nhận hàng</label>
                        <textarea name="address"><?= htmlspecialchars($userData['address'] ?? '') ?></textarea>
                    </div>

                    <div class="account-actions">
                        <button type="submit" class="btn-primary">Lưu thay đổi</button>
                    </div>
                </form>

            <?php elseif ($view === 'password'): ?>

                <h2>Đổi mật khẩu</h2>
                <p class="account-intro">Vui lòng nhập chính xác mật khẩu hiện tại để bảo vệ tài khoản.</p>

                <form method="post" class="account-form password-form">
                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" name="old_password" required>
                    </div>

                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" name="new_password" required>
                    </div>

                    <div class="form-group">
                        <label>Nhập lại mật khẩu mới</label>
                        <input type="password" name="re_password" required>
                    </div>

                    <div class="account-actions">
                        <button type="submit" class="btn-primary">Đổi mật khẩu</button>
                    </div>
                </form>

            <?php elseif ($view === 'orders'): ?>

                <h2>Đơn hàng của tôi</h2>
                <p class="account-intro">
                    Xem lịch sử mua hàng và theo dõi trạng thái đơn hàng. Hệ thống đang lọc theo số điện thoại:
                    <strong><?= htmlspecialchars($userData['phone'] ?? 'Chưa cập nhật') ?></strong>
                </p>

                <!-- Thanh filter trạng thái -->
                <div class="account-orders-tabs">
                    <?php
                    $statusLinks = [
                        'all'        => 'Tất cả đơn hàng',
                        'processing' => 'Đang xử lý',
                        'shipping'   => 'Đang giao hàng',
                        'completed'  => 'Đã giao',
                        'cancelled'  => 'Đã hủy',
                        'returned'   => 'Trả lại',
                    ];
                    foreach ($statusLinks as $key => $label):
                        $active = ($statusFilter === $key) ? 'active' : '';
                        ?>
                        <a href="account.php?view=orders&status=<?= $key ?>" class="<?= $active ?>">
                            <?= $label ?>
                        </a>
                    <?php endforeach; ?>
                </div>

                <?php if (empty($orders)) : ?>

                    <p class="orders-empty">
                        Bạn chưa có đơn hàng nào hoặc chưa cập nhật số điện thoại trong tài khoản.
                    </p>

                <?php else: ?>

                    <div class="order-list">
                        <?php foreach ($orders as $order): ?>
                            <?php
                            $info   = $order['info'];
                            $items  = $order['items'];
                            [$statusText, $statusClass] = renderStatusLabel($info['status']);
                            ?>
                            <article class="order-card">
                                <div class="order-card-header">
                                    <div class="order-meta">
                                        <div>Mã đơn: <strong>#<?= (int)$info['order_id'] ?></strong></div>
                                        <div>Ngày đặt: <?= htmlspecialchars($info['created_at']) ?></div>
                                    </div>
                                    <span class="order-status-badge <?= $statusClass ?>">
                                        <?= $statusText ?>
                                    </span>
                                </div>

                                <div class="order-products">
                                    <?php if (!empty($items)) : ?>
                                        <?php foreach ($items as $prod): ?>
                                            <div class="order-product-item">
                                                <div class="order-product-thumb">
                                                    <?php if (!empty($prod['product_img'])): ?>
                                                        <img src="<?= htmlspecialchars($prod['product_img']) ?>"
                                                             alt="<?= htmlspecialchars($prod['product_name']) ?>">
                                                    <?php endif; ?>
                                                </div>
                                                <div class="order-product-info">
                                                    <div class="order-product-name">
                                                        <?= htmlspecialchars($prod['product_name']) ?>
                                                    </div>
                                                    <div class="order-product-meta">
                                                        Size: <?= htmlspecialchars($prod['size']) ?> |
                                                        SL: <?= (int)$prod['qty'] ?>
                                                    </div>
                                                </div>
                                                <div class="order-product-price">
                                                    <?= number_format($prod['price'], 0, ',', '.') ?>đ
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="order-product-item">
                                            <div class="order-product-info">
                                                Không có chi tiết sản phẩm.
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="order-card-footer">
                                    <div class="order-total">
                                        Tổng cộng:
                                        <strong><?= number_format($info['total_price'], 0, ',', '.') ?>đ</strong>
                                    </div>
                                    <div class="order-actions">
                                        <a href="order_detail.php?id=<?= (int)$info['order_id'] ?>" class="btn-order-again">
                                            Xem chi tiết                
                                        </a>
                                        <a href="reorder.php?id=<?= $info['order_id'] ?>" class="btn-order-again">
                                            Mua lại
                                        </a>
                                    </div>
                                </div>
                            </article>
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>

            <?php endif; ?>

        </main>

    </div>
</section>

<?php require __DIR__ . "/partials/footer.php"; ?>

</body>
</html>
