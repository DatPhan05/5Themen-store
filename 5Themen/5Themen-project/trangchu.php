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

// LẤY DỮ LIỆU SẢN PHẨM MỚI + KHUYẾN MÃI
$newProducts  = $productModel->get_new_products(8);
$saleProducts = $productModel->get_hot_sale_products(8);

// Lấy ID sản phẩm mới nhất (dùng để hiển thị badge NEW)
$maxProductId = $productModel->get_last_id()
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
<section class="home-section">
    <div class="container">
        <h2 class="category-title">Sản phẩm mới</h2>

        <?php if ($newProducts && $newProducts->num_rows > 0): ?>
            <div class="product-grid">
                
            <?php while ($p = $newProducts->fetch_assoc()): ?>

    <?php
        $price = (float)$p['product_price'];
        $sale  = (float)$p['product_sale'];
        $hasSale = ($sale > 0 && $sale < $price);
        $isNew  = true; // vì đang nằm trong danh sách NEW
    ?>

    <div class="product-item">

        <div class="product-media">

            <!-- Badge NEW (góc phải) -->
            <?php if ($isNew): ?>
                <span class="product-badge new">NEW</span>
            <?php endif; ?>

            <!-- Badge HOT SALE (nếu sản phẩm mới nhưng vẫn giảm giá) -->
            <?php if ($hasSale): ?>
                <span class="product-badge hot">HOT</span>
            <?php endif; ?>

            <a href="product_detail.php?id=<?= $p['product_id'] ?>" class="product-thumb">
                <img src="<?= htmlspecialchars($p['product_img']) ?>" alt="">
            </a>

            <div class="product-hover-actions">
                <div class="product-hover-actions-inner">
                    <a href="them_giohang.php?action=add&id=<?= $p['product_id'] ?>" class="hover-btn">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </a>
                    <a href="product_detail.php?id=<?= $p['product_id'] ?>" class="hover-btn">
                        <i class="fa-regular fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>

        <h3 class="product-name">
            <a href="product_detail.php?id=<?= $p['product_id'] ?>">
                <?= htmlspecialchars($p['product_name']) ?>
            </a>
        </h3>

        <div class="product-price">
            <?php if ($hasSale): ?>
                <span class="price-current"><?= number_format($sale, 0, ',', '.') ?>đ</span>
                <span class="price-old"><?= number_format($price, 0, ',', '.') ?>đ</span>
                <span class="price-sale-badge">
                    -<?= round((($price - $sale) / $price) * 100) ?>%
                </span>
            <?php else: ?>
                <span class="price-current"><?= number_format($price, 0, ',', '.') ?>đ</span>
            <?php endif; ?>
        </div>

        <div class="product-color-list">
            <div class="product-color">
                <img src="<?= htmlspecialchars($p['product_img']) ?>" alt="">
            </div>
        </div>

    </div>

<?php endwhile; ?>

            </div>
        <?php else: ?>
            <p style="margin-top: 20px;">Chưa có sản phẩm mới.</p>
        <?php endif; ?>

    </div>
</section>


<!---------------------------------- KHUYẾN MÃI HOT --------------------->
<section class="home-section">
    <div class="container">
        <h2 class="category-title">Khuyến mãi hot</h2>

        <?php if ($saleProducts && $saleProducts->num_rows > 0): ?>
            <div class="product-grid">
                <?php while ($p = $saleProducts->fetch_assoc()): ?>

    <?php
        $price = (float)$p['product_price'];
        $sale  = (float)$p['product_sale'];
        $hasSale = ($sale > 0 && $sale < $price);
        
        // kiểm tra sản phẩm mới
        $isNew = ($p['product_id'] >= $maxProductId - 20);
    ?>

    <div class="product-item">

        <div class="product-media">

            <!-- HOT (góc trái) -->
            <?php if ($hasSale): ?>
            <span class="product-badge hot">HOT</span>
            <?php endif; ?>

            <!-- NEW (góc phải) -->
            <?php if ($isNew): ?>
            <span class="product-badge new">NEW</span>
            <?php endif; ?>

            <a href="product_detail.php?id=<?= $p['product_id'] ?>" class="product-thumb">
                <img src="<?= htmlspecialchars($p['product_img']) ?>" alt="">
            </a>

            <div class="product-hover-actions">
                <div class="product-hover-actions-inner">
                    <a href="them_giohang.php?action=add&id=<?= $p['product_id'] ?>" class="hover-btn">
                        <i class="fa-solid fa-cart-shopping"></i>
                    </a>
                    <a href="product_detail.php?id=<?= $p['product_id'] ?>" class="hover-btn">
                        <i class="fa-regular fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>

        <h3 class="product-name"><?= htmlspecialchars($p['product_name']) ?></h3>

        <div class="product-price">
            <span class="price-current"><?= number_format($sale, 0, ',', '.') ?>đ</span>
            <span class="price-old"><?= number_format($price, 0, ',', '.') ?>đ</span>
            <span class="price-sale-badge">
                -<?= round((($price - $sale) / $price) * 100) ?>%
            </span>
        </div>

    </div>

<?php endwhile; ?>

            </div>
        <?php else: ?>
            <p style="margin-top: 20px;">Chưa có sản phẩm khuyến mãi.</p>
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
