<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/include/session.php";
require_once __DIR__ . "/include/database.php";
require_once __DIR__ . "/admin/class/product_class.php";

$productModel = new Product();

/* ================== XỬ LÝ THÊM SẢN PHẨM VÀO GIỎ ================== */
if (isset($_GET['action']) && $_GET['action'] === "add") {

    $id = (int)$_GET['id'];

    // Lấy thông tin sản phẩm từ DB
    $product = $productModel->get_product($id);

    if ($product) {

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Nếu SP đã tồn tại thì tăng SL
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty']++;
        } else {
            // Nếu chưa có thì tạo mới
            $_SESSION['cart'][$id] = [
                "name"  => $product["product_name"],
                "price" => $product["product_price"],
                "qty"   => 1,
                "image" => $product["product_img"],   // ảnh từ DB
                "color" => "",                        // tạm
                "size"  => "L"                        // tạm
            ];
        }
    }

    header("Location: giohang.php");
    exit;
}

/* ================== XÓA SẢN PHẨM TRONG GIỎ ================== */
if (isset($_GET["delete"])) {
    $id = (int)$_GET["delete"];
    if (isset($_SESSION["cart"][$id])) {
        unset($_SESSION["cart"][$id]);
    }
    header("Location: giohang.php");
    exit;
}

/* ================== BREADCRUMB ================== */
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
    ['text' => 'Giỏ hàng']
];
?>

<?php require __DIR__ . "/partials/header.php"; ?>

<!-- BREADCRUMB -->
<?php require __DIR__ . "/partials/breadcrumb.php"; ?>

<section class="cart">
    <div class="container">

        <!-- Thanh bước -->
        <div class="cart-top-wrap">
            <div class="cart-top">
                <div class="cart-top-cart cart-top-item"><i class="fas fa-shopping-cart"></i></div>
                <div class="cart-top-adress cart-top-item"><i class="fas fa-map-marker-alt"></i></div>
                <div class="cart-top-payment cart-top-item"><i class="fas fa-money-check-alt"></i></div>
            </div>
        </div>

        <div class="cart-content row">

            <!---------------------- BÊN TRÁI ---------------------->
            <div class="cart-content-left">
                <table>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Tên</th>
                        <th>Màu</th>
                        <th>Size</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                        <th>Xóa</th>
                    </tr>

                    <?php 
                    if (!empty($_SESSION['cart'])):
                        foreach($_SESSION['cart'] as $key => $item):
                            $thanhtien = $item['price'] * $item['qty'];
                    ?>
                    <tr>
                        <td>
                            <img src="<?= htmlspecialchars($item['image']) ?>" width="100" alt="">
                        </td>
                        <td><?= htmlspecialchars($item['name']) ?></td>

                        <!-- màu tạm thời -->
                        <td>
                            <?php if ($item['color']): ?>
                                <?= htmlspecialchars($item['color']) ?>
                            <?php else: ?>
                                <div style="width:20px;height:20px;background:#000;border-radius:50%;margin:auto"></div>
                            <?php endif; ?>
                        </td>

                        <!-- size -->
                        <td><?= htmlspecialchars($item['size']) ?></td>

                        <!-- SL -->
                        <td><?= (int)$item['qty'] ?></td>

                        <!-- Thành tiền -->
                        <td><?= number_format($thanhtien, 0, ',', '.') ?>đ</td>

                        <!-- Xóa -->
                        <td><a href="giohang.php?delete=<?= (int)$key ?>">X</a></td>
                    </tr>

                    <?php 
                        endforeach;
                    else:
                    ?>
                    <tr>
                        <td colspan="7" style="text-align:center; padding: 20px 0;">
                            Giỏ hàng của bạn đang trống.
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>

            <!---------------------- BÊN PHẢI ---------------------->
            <div class="cart-content-right">
                <?php 
                $total = 0;
                if (!empty($_SESSION['cart'])) {
                    foreach ($_SESSION['cart'] as $item) {
                        $total += $item['price'] * $item['qty'];
                    }
                }
                ?>

                <table>
                    <tr>
                        <th colspan="2">TỔNG TIỀN GIỎ HÀNG</th>
                    </tr>
                    <tr>
                        <td>Tạm tính:</td>
                        <td><?= number_format(      $total, 0, ',', '.') ?>đ</td>
                    </tr>
                    <tr>
                        <td>Tổng cộng:</td>
                        <td><strong><?= number_format($total, 0, ',', '.') ?>đ</strong></td>
                    </tr>
                </table>

                <div class="cart-content-right-button">
                    <button onclick="window.location='trangchu.php'">TIẾP TỤC MUA SẮM</button>
                    <button onclick="window.location='thanhtoan.php'">THANH TOÁN</button>
                </div>
            </div>

        </div>                      
    </div>
</section>

<?php require __DIR__ . "/partials/footer.php"; ?>
