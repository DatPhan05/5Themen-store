<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/admin/class/product_class.php';
require_once __DIR__ . '/admin/class/category_class.php';
require_once __DIR__ . '/admin/class/brand_class.php';

$productModel  = new Product();
$categoryModel = new Category();
$brandModel    = new Brand();

/* =============================
   1. Lấy product_id từ URL
============================= */
$productId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($productId <= 0) {
    header("Location: trangchu.php");
    exit;
}

/* =============================
   2. Lấy thông tin sản phẩm
============================= */
$product = $productModel->get_product($productId);

if (!$product) {
    // Nếu không tìm thấy sản phẩm, quay về trang chủ
    header("Location: trangchu.php");
    exit;
}

/* =============================
   3. Lấy thông tin category và brand
============================= */
$category = $categoryModel->get_category($product['category_id']);
$brand    = $product['brand_id']
          ? $brandModel->get_brand($product['brand_id'])
          : null;

/* =============================
   4. Lấy sản phẩm cùng loại (cùng category)
============================= */
$relatedProducts = $productModel->get_product_by_category($product['category_id']);

$pageTitle = $product['product_name'];

/* =============================
   5. Chuẩn bị breadcrumb
============================= */
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<?php
// BREADCRUMB COMPONENT
include 'breadcrumb.php';
?>

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
                        <?php
                            $discount = round((($price - $salePrice) / $price) * 100);
                        ?>
                        <span class="discount-badge">
                            -<?= $discount ?>%
                        </span>
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

                    

                   <!-- KÍCH THƯỚC -->
                    <div id="variant-swatch-1"
     class="swatch clearfix type-"
     data-option="option2"
     data-option-index="1">
    <div class="header-container">
        <div class="header">
            Kích thước:
            <!-- span hiển thị size đang chọn -->
            <span class="size-current">S</span>
        </div>
    </div>
    <div class="select-swap">
    <div data-value="S" class="n-sd swatch-element s is-size">
        <input
            class="variant-1 size-radio"
            id="swatch-1-s"
            type="radio"
            name="option2"
            value="S"
            checked>
        <label data-size="1-s" data-title="S" for="swatch-1-s" class="size-label active">
            <span>S</span>
        </label>
    </div>

    <div data-value="M" class="n-sd swatch-element m is-size">
        <input
            class="variant-1 size-radio"
            id="swatch-1-m"
            type="radio"
            name="option2"
            value="M">
        <label data-size="1-m" data-title="M" for="swatch-1-m" class="size-label">
            <span>M</span>
        </label>
    </div>

    <div data-value="L" class="n-sd swatch-element l is-size">
        <input
            class="variant-1 size-radio"
            id="swatch-1-l"
            type="radio"
            name="option2"
            value="L">
        <label data-size="1-l" data-title="L" for="swatch-1-l" class="size-label">
            <span>L</span>
        </label>
    </div>

    <div data-value="XL" class="n-sd swatch-element xl is-size">
        <input
            class="variant-1 size-radio"
            id="swatch-1-xl"
            type="radio"
            name="option2"
            value="XL">
        <label data-size="1-xl" data-title="XL" for="swatch-1-xl" class="size-label">
            <span>XL</span>
        </label>
    </div>
</div>


   

    <div class="size-infomation"></div>
</div>



                    <!-- Số lượng -->
                    <div class="product-option">
                        <label>Số lượng:</label>
                        <div class="quantity-selector">
                            <button type="button" class="qty-btn" onclick="decreaseQty()">−</button>
                            <input type="number" name="quantity" id="quantity" value="1" min="1" max="99">
                            <button type="button" class="qty-btn" onclick="increaseQty()">+</button>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="product-actions">
                        <button type="submit" class="btn btn-add-cart">
                            🛒 THÊM VÀO GIỎ
                        </button>
                        <button type="submit" class="btn btn-buy-now">
                            MUA NGAY
                        </button>
                    </div>
                </form>

                <!-- Product Features -->
                <div class="product-features">
                    <div class="feature-item">
                        <span class="feature-icon">🚚</span>
                        <div>
                            <strong>Freeship đơn từ 399K</strong>
                            <small>Giao hàng toàn quốc</small>
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">💳</span>
                        <div>
                            <strong>Thanh toán COD</strong>
                            <small>Thanh toán khi nhận hàng</small>
                        </div>
                    </div>
                    <div class="feature-item">
                        <span class="feature-icon">↩️</span>
                        <div>
                            <strong>Đổi trả trong 15 ngày</strong>
                            <small>Hoàn tiền 100%</small>
                        </div>
                    </div>
                </div>
            </div> <!-- /.product-info -->
        </div> <!-- /.product-detail-grid -->

        <!-- Product Description -->
        <div class="product-description-section">
            <h2>MÔ TẢ SẢN PHẨM</h2>
            <div class="description-content">
                <?= nl2br(htmlspecialchars($product['product_desc'])) ?>
            </div>
        </div>

        <!-- Related Products -->
        <?php if ($relatedProducts && is_object($relatedProducts) && $relatedProducts->num_rows > 1): ?>
        <div class="related-products-section">
            <h2>Sản phẩm cùng loại</h2>
            <div class="product-grid">
                <?php
                $count = 0;
                while ($relatedProduct = $relatedProducts->fetch_assoc()):
                    if ((int)$relatedProduct['product_id'] !== $productId && $count < 5):
                        $count++;
                ?>
                <div class="product-card">
                    <a href="product_detail.php?id=<?= (int)$relatedProduct['product_id'] ?>">
                        <div class="product-image">
                            <img src="<?= htmlspecialchars($relatedProduct['product_img']) ?>"
                                 alt="<?= htmlspecialchars($relatedProduct['product_name']) ?>">
                        </div>
                        <h3><?= htmlspecialchars($relatedProduct['product_name']) ?></h3>
                        <p class="price">
                            <?php
                            $rpPrice     = (float)$relatedProduct['product_price'];
                            $rpSalePrice = (float)$relatedProduct['product_sale'];
                            if ($rpSalePrice > 0 && $rpSalePrice < $rpPrice):
                            ?>
                                <span class="price-sale">
                                    <?= number_format($rpSalePrice, 0, ',', '.') ?>đ
                                </span>
                                <span class="price-old">
                                    <?= number_format($rpPrice, 0, ',', '.') ?>đ
                                </span>
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
    </div> <!-- /.container-wide -->
</div> <!-- /.product-detail-page -->

<?php include 'footer.php'; ?>

<script>
// Quantity buttons
function increaseQty() {
    const qtyInput = document.getElementById('quantity');
    let currentQty = parseInt(qtyInput.value, 10) || 1;
    if (currentQty < 99) {
        qtyInput.value = currentQty + 1;
    }
}

function decreaseQty() {
    const qtyInput = document.getElementById('quantity');
    let currentQty = parseInt(qtyInput.value, 10) || 1;
    if (currentQty > 1) {
        qtyInput.value = currentQty - 1;
    }
}
// Cập nhật hiển thị size đang chọn
document.addEventListener('DOMContentLoaded', function () {
    const sizeCurrent = document.querySelector('.size-current');
    const sizeRadios  = document.querySelectorAll('.size-radio');
    const sizeLabels  = document.querySelectorAll('.size-label');

    function setSize(size) {
        if (sizeCurrent) {
            sizeCurrent.textContent = size;
        }
    }

    sizeRadios.forEach(function (radio) {
        radio.addEventListener('change', function () {
            const size = this.value;

            // Cập nhật chữ "Kích thước: X"
            setSize(size);

            // Đổi class active cho ô đang chọn
            sizeLabels.forEach(function (label) {
                label.classList.remove('active');
            });

            const label = document.querySelector('label[for="' + this.id + '"]');
            if (label) {
                label.classList.add('active');
            }
        });
    });

    // Khởi tạo theo size đang checked (mặc định S)
    const checked = document.querySelector('.size-radio:checked');
    if (checked) {
        setSize(checked.value);
    }
});
</script>

</body>
</html>
