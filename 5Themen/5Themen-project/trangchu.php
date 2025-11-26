<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

// Nếu trang chủ có dùng Category/Product:
require_once __DIR__ . '/admin/class/category_class.php';
require_once __DIR__ . '/admin/class/product_class.php';

$db = new Database();
$conn = $db->link;

$categoryModel = new Category();
$productModel  = new Product();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/1147679ae7.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="CSS/style.css">
    <title>5Themen</title>

    

</head>
<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>


<!---------------------------------- SLIDER ------------------------------>
<section id="Sliders">
    <div class="aspect-ratio-169">
        <img src="images/Slide1.png">
        <img src="images/Slide2.png">
        <img src="images/Slide3.png">
        <img src="images/Slide4.png">
        <img src="images/Slide5.png">
    </div>

    <div class="dot-container">
        <div class="dot active"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
        <div class="dot"></div>
    </div>
</section>
<!---------------------------------- SẢN PHẨM MỚI ----------------------->
<section class="related-products-section home-section">
    <div class="container">
        <h2>Sản phẩm mới</h2>

        <?php if (!empty($newProducts)): ?>
            <div class="product-grid">
                <?php foreach ($newProducts as $p): ?>
                    <?php
                    $price = (float)$p['gia_goc'];
                    $sale  = (float)$p['gia_khuyen_mai'];
                    $hasSale = $sale > 0 && $sale < $price;
                    $thumb   = nh_image_url($p['anh_dai_dien']);
                    $percent = ($hasSale && $price > 0)
                        ? round(100 - ($sale / $price) * 100)
                        : 0;
                    ?>
                    <a href="product_detail.php?id=<?= (int)$p['ma_san_pham'] ?>" class="product-card">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($thumb) ?>"
                                 alt="<?= htmlspecialchars($p['ten_san_pham']) ?>">
                            <?php if ($percent > 0): ?>
                                <span class="badge-sale">-<?= $percent ?>%</span>
                            <?php endif; ?>
                        </div>
                        <h3><?= htmlspecialchars($p['ten_san_pham']) ?></h3>
                        <div class="price-row">
                            <?php if ($hasSale): ?>
                                <span class="price-new">
                                    <?= number_format($sale, 0, ',', '.') ?>₫
                                </span>
                                <span class="price-old">
                                    <?= number_format($price, 0, ',', '.') ?>₫
                                </span>
                            <?php else: ?>
                                <span class="price-new">
                                    <?= number_format($price, 0, ',', '.') ?>₫
                                </span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Chưa có sản phẩm mới.</p>
        <?php endif; ?>
    </div>
</section>

<!---------------------------------- KHUYẾN MÃI HOT --------------------->
<section class="related-products-section home-section">
    <div class="container">
        <h2>Khuyến mãi hot</h2>

        <?php if (!empty($saleProducts)): ?>
            <div class="product-grid">
                <?php foreach ($saleProducts as $p): ?>
                    <?php
                    $price = (float)$p['gia_goc'];
                    $sale  = (float)$p['gia_khuyen_mai'];
                    $thumb = nh_image_url($p['anh_dai_dien']);
                    $percent = ($sale > 0 && $price > 0)
                        ? round(100 - ($sale / $price) * 100)
                        : 0;
                    ?>
                    <a href="product_detail.php?id=<?= (int)$p['ma_san_pham'] ?>" class="product-card">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($thumb) ?>"
                                 alt="<?= htmlspecialchars($p['ten_san_pham']) ?>">
                            <?php if ($percent > 0): ?>
                                <span class="badge-sale">-<?= $percent ?>%</span>
                            <?php endif; ?>
                        </div>
                        <h3><?= htmlspecialchars($p['ten_san_pham']) ?></h3>
                        <div class="price-row">
                            <span class="price-new">
                                <?= number_format($sale, 0, ',', '.') ?>₫
                            </span>
                            <span class="price-old">
                                <?= number_format($price, 0, ',', '.') ?>₫
                            </span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p>Chưa có sản phẩm khuyến mãi.</p>
        <?php endif; ?>
    </div>
</section>

<!---------------------------------- FOOTER ------------------------------>
<?php require_once __DIR__ . "/partials/footer.php"; ?>



<script src="js/slider.js"></script>
<script src="js/megamenu.js"></script>
<script src="js/main.js"></script>

</body>
</html>
