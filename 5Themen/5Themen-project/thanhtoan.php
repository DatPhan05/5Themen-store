<?php
session_start();
include "header.php";
?>
<!---------------------------------------------Payment---------------------------------------------->
<section class="payment">
    <div class="container">
        <div class="payment-top-wrap">
            <div class="payment-top">
                <div class="payment-top-delivery payment-top-item">
                    <i class="fas fa-shopping-cart"></i>
                </div>
                <div class="payment-top-adress payment-top-item">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="payment-top-payment payment-top-item">
                    <i class="fas fa-money-check-alt"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <form id="form-payment">
            <div class="payment-content row">
                <div class="payment-content-left">
                    <div class="payment-content-left-method-payment">
                        <p style="font-weight: bold;">Phương thức giao hàng</p>
                        <div class="payment-content-left-method-payment-item">
                            <input checked type="radio" name="delivery">
                            <label for="">Giao hàng chuyển phát nhanh</label>
                        </div>
                    </div>
                    <div class="payment-content-left-method-payment">
                        <p style="font-weight: bold;">Phương thức thanh toán</p>
                        <p>Mọi giao dịch đều được bảo mật và mã hóa. Thông tin thẻ tín dụng sẽ không bao giờ được lưu lại.</p>
                        <div class="payment-content-left-method-payment-item">
                            <input name="method-payment" type="radio">
                            <label for="">Thanh toán bằng thẻ tín dụng (OnePay)</label>
                        </div>
                        <div class="payment-content-left-method-payment-item-img">
                            <img src="images/visa.png" alt="">
                        </div>
                        <div class="payment-content-left-method-payment-item">
                            <input checked name="method-payment" type="radio">
                            <label for="">Thanh toán bằng thẻ ATM (OnePay)</label>
                        </div>
                        <div class="payment-content-left-method-payment-item-img">
                            <img src="images/acb.png" alt="">
                            <img src="images/mb.png" alt="">
                            <img src="images/viettinbank.png" alt="">
                            <img src="images/techcombank.png" alt="">
                            <img src="images/vcb.png" alt="">
                            <img src="images/vib.png" alt="">
                        </div>
                        <div class="payment-content-left-method-payment-item">
                            <input name="method-payment" type="radio">
                            <label for="">Thanh toán Momo</label>
                        </div>
                        <div class="payment-content-left-method-payment-item-img">
                            <img src="images/momo.png" alt="">
                        </div>
                        <div class="payment-content-left-method-payment-item">
                            <input name="method-payment" type="radio">
                            <label for="">Thu tiền tận nơi</label>
                        </div>
                    </div>
                </div>

                <div class="payment-content-right">
                    <div class="payment-content-right-button">
                        <input type="text" placeholder="Mã giảm giá/Quà tặng">
                        <button type="button"><i class="fas fa-check"></i></button>
                    </div>
                    <div class="payment-content-right-button">
                        <input type="text" placeholder="Mã công tác viên">
                        <button type="button"><i class="fas fa-check"></i></button>
                    </div>
                    <div class="payment-content-right-mnv">
                        <select name="id">
                            <option value="">Chọn mã nhân viên thân thiết</option>
                            <option value="">D345</option>
                            <option value="">AD95</option>
                            <option value="">E365</option>
                            <option value="">I345</option>
                        </select>
                    </div>
                    <div class="payment-content-right-payment">
                        <button type="submit">TIẾP TỤC THANH TOÁN</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include "footer.php"; ?>
<script src="js/script.js"></script>
<script src="js/slider.js"></script>
<script>
document.getElementById("form-payment").addEventListener("submit", function(e) {
    e.preventDefault();
    alert("Thanh toán thành công!");
    window.location = "chitietdonhang.php";
});
</script>
