<?php
/***********************************************
 * 1. IMPORT SESSION + DATABASE + CLASS
 ***********************************************/
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

require_once __DIR__ . '/admin/class/product_class.php';
require_once __DIR__ . '/admin/class/category_class.php';
require_once __DIR__ . '/admin/class/brand_class.php';

/***********************************************
 * 2. KHỞI TẠO MODEL
 ***********************************************/
$productModel  = new Product();
$categoryModel = new Category();
$brandModel    = new Brand();

/***********************************************
 * 3. LẤY product_id TỪ URL
 ***********************************************/
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header("Location: trangchu.php");
    exit;
}

/***********************************************
 * 4. LẤY THÔNG TIN SẢN PHẨM 
 ***********************************************/
$product = $productModel->get_product($productId);

if (!$product) {
    header("Location: trangchu.php");
    exit;
}

/***********************************************
 * 5. LẤY THÔNG TIN CATEGORY + BRAND
 ***********************************************/
$category = $categoryModel->get_category($product['category_id']);
$brand    = $product['brand_id'] ? $brandModel->get_brand($product['brand_id']) : null;

/***********************************************
 * 6. SẢN PHẨM LIÊN QUAN (cùng category)
 ***********************************************/
$relatedProducts = $productModel->get_product_by_category($product['category_id']);

$pageTitle = $product['product_name'];

/***********************************************
 * 7. BREADCRUMB
 ***********************************************/
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
];

if ($category) {
    $breadcrumbs[] = [
        'text' => $category['category_name'],
        'url'  => 'category.php?cat=' . (int)$product['category_id'],
    ];
}

$breadcrumbs[] = ['text' => $product['product_name']];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?> - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>

<?php require_once __DIR__ . "/partials/breadcrumb.php"; ?>

<!-- Product Detail Section -->
<div class="product-detail-page">
    <div class="container-wide">
        <div class="product-detail-grid">

            <!-- Left: Product Image -->
            <div class="product-gallery">
                <div class="main-image">
                    <img src="<?= htmlspecialchars($product['product_img']) ?>"
                         alt="<?= htmlspecialchars($product['product_name']) ?>">
                </div>
            </div>

            <!-- Right: Product Info -->
            <div class="product-info">
                <h1 class="product-title">
                    <?= htmlspecialchars($product['product_name']) ?>
                </h1>

                <div class="product-meta">
                    <span class="product-sku">
                        Mã SP:
                        <strong>SP<?= str_pad($product['product_id'], 6, '0', STR_PAD_LEFT) ?></strong>
                    </span>
                </div>

                <?php
                $price     = (float)$product['product_price'];
                $salePrice = (float)$product['product_sale'];
                ?>
                <div class="product-price">
                    <?php if ($salePrice > 0 && $salePrice < $price): ?>
                        <span class="price-sale">
                            <?= number_format($salePrice, 0, ',', '.') ?>đ
                        </span>
                        <span class="price-original">
                            <?= number_format($price, 0, ',', '.') ?>đ
                        </span>
                        <?php $discount = round((($price - $salePrice) / $price) * 100); ?>
                        <span class="discount-badge">-<?= $discount ?>%</span>
                    <?php else: ?>
                        <span class="price-current">
                            <?= number_format($price, 0, ',', '.') ?>đ
                        </span>
                    <?php endif; ?>
                </div>

                <!-- Khuyến mãi -->
                <div class="promotion-box">
                    <h4>🎁 KHUYẾN MÃI - ƯU ĐÃI</h4>
                    <ul>
                        <li>Nhập mã <strong>NOV15</strong> giảm 15K đơn từ 299K</li>
                        <li>Nhập mã <strong>NOV40</strong> giảm 40K đơn từ 599K</li>
                        <li>Nhập mã <strong>NOV70</strong> giảm 70K đơn từ 999K</li>
                        <li>Freeship đơn từ 399K</li>
                    </ul>
                </div>

                <!-- Form thêm vào giỏ -->
                <form id="addToCartForm" method="POST" action="them_giohang.php">
                    <input type="hidden" name="product_id" value="<?= $productId ?>">

                    <!-- SIZE PICKER -->
                    <?= /** giữ nguyên đoạn size bạn gửi — KHÔNG ĐỘNG */ "" ?>
                    <div id="variant-swatch-1" class="swatch clearfix" data-option="option2" data-option-index="1">
                        <div class="header-container">
                            <div class="header">Kích thước:
                                <span class="size-current">S</span>
                            </div>
                        </div>

                        <div class="select-swap">
                            <div data-value="S" class="n-sd swatch-element s is-size">
                                <input class="variant-1 size-radio" id="swatch-1-s" type="radio"
                                       name="option2" value="S" checked>
                                <label data-size="1-s" for="swatch-1-s" class="size-label active"><span>S</span></label>
                            </div>

                            <div data-value="M" class="n-sd swatch-element m is-size">
                                <input class="variant-1 size-radio" id="swatch-1-m" type="radio"
                                       name="option2" value="M">
                                <label data-size="1-m" for="swatch-1-m" class="size-label"><span>M</span></label>
                            </div>

                            <div data-value="L" class="n-sd swatch-element l is-size">
                                <input class="variant-1 size-radio" id="swatch-1-l" type="radio"
                                       name="option2" value="L">
                                <label data-size="1-l" for="swatch-1-l" class="size-label"><span>L</span></label>
                            </div>

                            <div data-value="XL" class="n-sd swatch-element xl is-size">
                                <input class="variant-1 size-radio" id="swatch-1-xl" type="radio"
                                       name="option2" value="XL">
                                <label data-size="1-xl" for="swatch-1-xl" class="size-label"><span>XL</span></label>
                            </div>
                        </div>
                    </div>

                    <!-- Số lượng -->
                    <div class="product-option">
                        <label>Số lượng:</label>
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn" onclick="decreaseQty()">−</button>
                            <input type="number" name="quantity" id="quantity"
                                   value="1" min="1" max="99">
                            <button type="button" class="qty-btn" onclick="increaseQty()">+</button>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="product-actions">
                        <button type="submit" class="btn btn-add-cart">🛒 THÊM VÀO GIỎ</button>
                        <button type="submit" class="btn btn-buy-now">MUA NGAY</button>
                    </div>
                </form>

                <!-- Features -->
                <div class="product-features">
                    <div class="feature-item">
                        <span class="feature-icon">🚚</span>
                        <div><strong>Freeship đơn từ 399K</strong><small>Giao hàng toàn quốc</small></div>
                    </div>

                    <div class="feature-item">
                        <span class="feature-icon">💳</span>
                        <div><strong>Thanh toán COD</strong><small>Thanh toán khi nhận hàng</small></div>
                    </div>

                    <div class="feature-item">
                        <span class="feature-icon">↩️</span>
                        <div><strong>Đổi trả trong 15 ngày</strong><small>Hoàn tiền 100%</small></div>
                    </div>
                </div>

            </div>
        </div>

        <!-- MÔ TẢ -->
        <div class="product-description-section">
            <h2>MÔ TẢ SẢN PHẨM</h2>
            <div class="description-content">
                <?= nl2br(htmlspecialchars($product['product_desc'])) ?>
            </div>
        </div>

        <!-- RELATED -->
        <?php if ($relatedProducts && $relatedProducts->num_rows > 1): ?>
        <div class="related-products-section">
            <h2>Sản phẩm cùng loại</h2>
            <div class="product-grid">
                <?php
                $count = 0;
                while ($rp = $relatedProducts->fetch_assoc()):
                    if ($rp['product_id'] != $productId && $count < 5):
                        $count++;
                ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?= (int)$rp['product_id'] ?>">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($rp['product_img']) ?>"
                                 alt="<?= htmlspecialchars($rp['product_name']) ?>">
                        </div>
                        <h3><?= htmlspecialchars($rp['product_name']) ?></h3>
                        <p class="price">
                            <?php
                            $rpPrice     = (float)$rp['product_price'];
                            $rpSalePrice = (float)$rp['product_sale'];
                            if ($rpSalePrice > 0 && $rpSalePrice < $rpPrice):
                            ?>
                                <span class="price-sale"><?= number_format($rpSalePrice, 0, ',', '.') ?>đ</span>
                                <span class="price-old"><?= number_format($rpPrice, 0, ',', '.') ?>đ</span>
                            <?php else: ?>
                                <?= number_format($rpPrice, 0, ',', '.') ?>đ
                            <?php endif; ?>
                        </p>
                    </a>
                </div>
                <?php
                    endif;
                endwhile;
                ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . "/partials/footer.php"; ?>

<script>
// Quantity buttons
function increaseQty(){
    const qty = document.getElementById('quantity');
    qty.value = Math.min(parseInt(qty.value || 1) + 1, 99);
}

function decreaseQty(){
    const qty = document.getElementById('quantity');
    qty.value = Math.max(parseInt(qty.value || 1) - 1, 1);
}

// Size highlight
document.addEventListener('DOMContentLoaded', function(){
    const sizeCurrent = document.querySelector('.size-current');
    const radios = document.querySelectorAll('.size-radio');
    const labels = document.querySelectorAll('.size-label');

    radios.forEach(r => {
        r.addEventListener('change', function(){
            sizeCurrent.textContent = this.value;

            labels.forEach(lb => lb.classList.remove('active'));
            document.querySelector(`label[for="${this.id}"]`).classList.add('active');
        });
    });
});
</script>

<script src="js/megamenu.js"></script>
<script src="js/main.js"></script>

</body>
</html>
