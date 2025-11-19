<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

$db   = new Database();
$conn = $db->link;

// Nếu chưa đăng nhập thì chuyển về login
if (empty($_SESSION['is_logged_in']) || empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$msg      = '';
$userData = null;

// Lấy user_id từ session
$user_id = (int)$_SESSION['user_id'];

// Lấy thông tin user
$sql    = "SELECT * FROM tbl_user WHERE user_id = '$user_id' LIMIT 1";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    $userData = $result->fetch_assoc();
} else {
    header("Location: logout.php");
    exit;
}

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = $conn->real_escape_string(trim($_POST['fullname'] ?? ''));
    $phone    = $conn->real_escape_string(trim($_POST['phone'] ?? ''));
    $address  = $conn->real_escape_string(trim($_POST['address'] ?? ''));

    $update = "
        UPDATE tbl_user
        SET fullname = '$fullname',
            phone    = '$phone',
            address  = '$address'
        WHERE user_id = '$user_id'
    ";

    if ($conn->query($update)) {
        $_SESSION['user_name'] = $fullname;
        $msg = "Cập nhật thành công!";

        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            $userData = $result->fetch_assoc();
        }
    } else {
        $msg = "Cập nhật thất bại!";
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thông tin tài khoản - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>

<section class="account-container">
    <div class="account-wrapper">

        <aside class="account-sidebar">
            <h3>Trung tâm cá nhân</h3>
            <ul>
                <li><a href="account.php" class="active">Thông tin của tôi</a></li>
                <li><a href="giaohang.php">Trạng thái đơn hàng</a></li>
                <li><a href="giohang.php">Quản lý giỏ hàng</a></li>
                <li><a href="logout.php" class="logout">Đăng xuất</a></li>
            </ul>
        </aside>

        <main class="account-content">
            <h2>Thông tin của tôi</h2>

            <?php if (!empty($msg)): ?>
                <p class="msg"><?= htmlspecialchars($msg) ?></p>
            <?php endif; ?>

            <form method="POST" class="account-form">
                <label>Họ tên</label>
                <input type="text" name="fullname"
                       value="<?= htmlspecialchars($userData['fullname'] ?? '') ?>">

                <label>Email</label>
                <input type="text"
                       value="<?= htmlspecialchars($userData['email'] ?? '') ?>"
                       readonly>

                <label>Số điện thoại</label>
                <input type="text" name="phone"
                       value="<?= htmlspecialchars($userData['phone'] ?? '') ?>">

                <label>Địa chỉ</label>
                <input type="text" name="address"
                       value="<?= htmlspecialchars($userData['address'] ?? '') ?>">

                <button type="submit">LƯU</button>
            </form>
        </main>

    </div>
</section>

<?php require_once __DIR__ . "/partials/footer.php"; ?>

</body>
</html>
