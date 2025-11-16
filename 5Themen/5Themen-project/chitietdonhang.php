   <?php
session_start();
include "header.php";
?>

<section class="order-detail">
    <div class="container">
        <h2>Chi tiết đơn hàng của bạn</h2>
        <h3>Thông tin khách hàng</h3>
        <ul>
            <li>Họ tên: <?php echo htmlspecialchars($_POST['fullname'] ?? ""); ?></li>
            <li>Điện thoại: <?php echo htmlspecialchars($_POST['phone'] ?? ""); ?></li>
            <li>Địa chỉ: <?php echo htmlspecialchars($_POST['address'] ?? ""); ?></li>
            <!-- Có thể thêm tỉnh/thành, quận/huyện nếu bạn truyền -->
        </ul>
        <h3>Danh sách sản phẩm</h3>
        <table border="1" cellpadding="6" cellspacing="0" style="width:100%;text-align:center;">
            <tr>
                <th>Tên sản phẩm</th>
                <th>Màu</th>
                <th>Size</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
            </tr>
            <?php
            $tong = 0;
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $item) {
                    $thanhtien = $item['price'] * $item['qty'];
                    $tong += $thanhtien;
                    echo "<tr>";
                    echo "<td>{$item['name']}</td>";
                    echo "<td>{$item['color']}</td>";
                    echo "<td>{$item['size']}</td>";
                    echo "<td>{$item['qty']}</td>";
                    echo "<td>" . number_format($thanhtien) . "đ</td>";
                    echo "</tr>";
                }
            }
            ?>
            <tr>
                <td colspan="4" style="font-weight:bold;">Tổng tiền hàng</td>
                <td style="font-weight:bold;"><?= number_format($tong) ?>đ</td>
            </tr>
        </table>
        <div style="margin-top:20px;">
            <h3>Cảm ơn bạn đã đặt hàng! Đơn hàng sẽ được xử lý và giao tận nơi cho bạn trong thời gian sớm nhất.</h3>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>
<script src="js/script.js"></script>
<script src="js/slider.js"></script>
 