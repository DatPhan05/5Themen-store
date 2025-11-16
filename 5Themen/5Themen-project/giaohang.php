<?php
session_start();
include "header.php";
?>
<!-- -------------------------------Delivery--------------------- -->
<section class="delivery">
    <div class="container">
        <div class="delivery-top-wrap">
            <div class="delivery-top">
                <div class="delivery-top-delivery delivery-top-item">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="delivery-top-adress delivery-top-item">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="delivery-top-payment delivery-top-item">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="delivery-content">
            <div class="delivery-content-left">
                <h2 style="margin-bottom:10px;">Vui lòng nhập đầy đủ địa chỉ giao hàng, số điện thoại để xác nhận đơn hàng!</h2>
                <p>Thông tin này sẽ được sử dụng để vận chuyển và liên hệ giao hàng.</p>

                <div class="delivery-content-left-dangnhap">
                    <i class="fas fa-sign-in-alt"></i>
                    <p>Đăng nhập (Nếu bạn đã có tài khoản của IVY)</p>
                </div>
                <div class="delivery-content-left-khachle">
                    <input checked name="loaikhach" type="radio">
                    <p><span style="font-weight: bold;">Khách lẻ</span> (Nếu bạn không muốn lưu lại thông tin)</p>
                </div>
                <div class="delivery-content-left-dangky row">
                    <input name="loaikhach" type="radio">
                    <p><span style="font-weight: bold;">Đăng ký</span> (Tạo mới tài khoản với thông tin bên dưới)</p>
                </div>

                <div class="delivery-content-left-input-top row">
                    <div class="delivery-content-left-input-top-item">
                        <label>Họ tên <span style="color: red;">*</span></label>
                        <input type="text">
                    </div>
                    <div class="delivery-content-left-input-top-item">
                        <label>Điện thoại <span style="color: red;">*</span></label>
                        <input type="text">
                    </div>
                    <div class="delivery-content-left-input-top-item">
                        <label>Tỉnh/Tp <span style="color: red;">*</span></label>
                        <input type="text">
                    </div>
                    <div class="delivery-content-left-input-top-item">
                        <label>Quận/Huyện <span style="color: red;">*</span></label>
                        <input type="text">
                    </div>
                </div>
                <div class="delivery-content-left-input-bottom">
                    <label>Địa chỉ <span style="color: red;">*</span></label>
                    <input type="text">
                </div>
                <button onclick="window.location='thanhtoan.php'" style="color: #ffrgba(0, 0, 0, 1)ont-weight:bold; margin-top:10px;">
                    XÁC NHẬN VÀ THANH TOÁN
                </button>
            </div>

            <!-- Cột bên phải: bảng sản phẩm -->
            <div class="delivery-content-right">
                <table>
                    <tr>
                        <th>Tên sản phẩm</th>
                        <th>Giảm giá</th>
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
                            echo "<td>0%</td>";
                            echo "<td>{$item['qty']}</td>";
                            echo "<td><p>" . number_format($thanhtien) . " <sup>đ</sup></p></td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                    <tr>
                        <td style="font-weight: bold;" colspan="3">Tổng</td>
                        <td style="font-weight: bold;"><p><?= number_format($tong) ?> <sup>đ</sup></p></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;" colspan="3">Thuế VAT</td>
                        <td style="font-weight: bold;"><p><?= number_format($tong*0.1) ?> <sup>đ</sup></p></td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;" colspan="3">Tổng tiền hàng</td>
                        <td style="font-weight: bold;"><p><?= number_format($tong*1.1) ?> <sup>đ</sup></p></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>
<script src="js/script.js"></script>
<script src="js/slider.js"></script>
