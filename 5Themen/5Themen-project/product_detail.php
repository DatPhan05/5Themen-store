<?php
/***********************************************
 * 1. IMPORT SESSION + DATABASE + CLASS
 ***********************************************/
require_once __DIR__ . '/include/session.php';
Session::init();
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

/***********************************************
 * 8. HÀM XỬ LÝ ẢNH SẢN PHẨM (PATH AN TOÀN)
 ***********************************************/
function buildProductImgPath($img)
{
    $img = (string)$img;
    if ($img === '') return '';
    // Nếu đã có admin/uploads/ thì giữ nguyên
    if (strpos($img, 'admin/uploads/') === 0) {
        return $img;
    }
    return 'admin/uploads/' . ltrim($img, '/');
}

$mainImg = buildProductImgPath($product['product_img'] ?? '');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?> - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- ================== PRODUCT DETAIL – STYLE A (Routine/Yody) ================== -->
    <style>
        :root {
            --pd-bg: #f5f5f7;
            --pd-white: #ffffff;
            --pd-dark: #111827;
            --pd-gray: #6b7280;
            --pd-light-gray: #e5e7eb;
            --pd-accent: #facc15; /* vàng nhạt */
            --pd-accent-soft: #fef9c3;
            --pd-radius-lg: 18px;
            --pd-shadow-soft: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        body {
            background: var(--pd-bg);
        }

        .pd-page {
            padding: 40px 0 60px;
        }
        .pd-container {
            max-width: 1180px;
            margin: 0 auto;
            padding: 0 16px;
        }

        /* GRID CHÍNH */
        .pd-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(0, 1fr);
            gap: 34px;
            align-items:  stretch;;
        }

        /* CARD CHUNG */
        .pd-card {
            background: var(--pd-white);
            border-radius: var(--pd-radius-lg);
            box-shadow: var(--pd-shadow-soft);
            padding: 18px 18px 22px;
        }

        /* ========================== */
/*   LEFT GALLERY CÂN ĐỐI     */
/* ========================== */

.pd-gallery {
    display: grid;
    grid-template-columns: 110px 1fr;  /* giữ thumbnail gọn, không quá to */
    gap: 10px;
    align-items: start;
}

/* MAIN IMAGE – vừa đẹp, không lố */
.pd-main-img {
    border-radius: 18px;
    overflow: hidden;
    background: #f3f4f6;
    border: 1px solid #e5e7eb;
}

.pd-main-img img {
    width: 100%;
    max-height: 600px;     /* giảm xuống để không đè form bên phải */
    object-fit: cover;
    display: block;
}

/* THUMBNAIL COLUMN – chỉ 3 ảnh */
.pd-thumb-col {
    display: flex;
    flex-direction: column;
    gap: 12px;
    max-height: 600px;     /* giới hạn để căn bằng ảnh chính */
    overflow: hidden;      /* ẩn bớt nếu dư */
}

/* CHỈ HIỆN 3 ẢNH */
.pd-thumb-col .pd-thumb-item:nth-child(n+5) {
    display: none !important;
}

/* THUMB ITEM – nhỏ lại cho đẹp */
.pd-thumb-item {
    width: 100%;
    height: 130px;
    border-radius: 14px;
    overflow: hidden;
    cursor: pointer;
    background: #f3f4f6;
    border: 2px solid transparent;
    transition: all .15s ease;
}

.pd-thumb-item img {
    width: 100%;
    height: 115px;
    object-fit: cover;
}

.pd-thumb-item.active {
    border-color: #facc15;
    box-shadow: 0 0 0 2px rgba(250, 204, 21, 0.5);
}


/* MOBILE */
@media (max-width: 768px) {
    .pd-gallery {
        grid-template-columns: 1fr;
    }

    .pd-thumb-col {
        flex-direction: row;
        height: auto;
        max-height: none;
        overflow-x: auto;
    }

    .pd-thumb-item {
        min-width: 90px;
        height: 90px;
    }
}

        /* INFO BLOCK */
        .pd-info {
            display: flex;
            flex-direction: column;
            gap: 14px;
        }
        .pd-brand {
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: var(--pd-gray);
        }
        .pd-brand span {
            padding: 2px 10px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .pd-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--pd-dark);
            margin: 0;
        }
        .pd-meta-line {
            display: flex;
            flex-wrap: wrap;
            gap: 10px 16px;
            font-size: 13px;
            color: var(--pd-gray);
        }
        .pd-meta-line span {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .pd-meta-line i {
            font-size: 12px;
        }

        /* GIÁ */
        .pd-price-block {
            padding: 12px 14px;
            border-radius: 16px;
            background: #0f172a;
            color: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }
        .pd-price-main {
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .pd-price-current {
            font-size: 22px;
            font-weight: 600;
        }
        .pd-price-original {
            font-size: 13px;
            text-decoration: line-through;
            opacity: 0.7;
        }
        .pd-discount-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 6px 12px;
            border-radius: 999px;
            background: var(--pd-accent-soft);
            color: #854d0e;
            font-size: 12px;
            font-weight: 600;
            border: 1px solid var(--pd-accent);
        }
        .pd-price-note {
            font-size: 11px;
            opacity: 0.8;
        }

        /* KHUYẾN MÃI */
        .pd-promo {
            border-radius: 16px;
            background: #fef9c3;
            border: 1px dashed #eab308;
            padding: 10px 12px;
            font-size: 13px;
            color: #713f12;
        }
        .pd-promo h4 {
            margin: 0 0 4px;
            font-size: 13px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .pd-promo ul {
            margin: 0;
            padding-left: 18px;
        }
        .pd-promo li {
            margin-bottom: 2px;
        }

        /* LABEL + SELECT */
        .pd-form-section {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: 4px;
        }
        .pd-label-row {
            display: flex;
            justify-content: space-between;
            align-items: baseline;
            font-size: 13px;
        }
        .pd-label-row .main {
            color: var(--pd-dark);
            font-weight: 500;
        }
        .pd-label-row .sub {
            color: var(--pd-gray);
            font-size: 12px;
        }

        /* SIZE SWATCH – lấy từ code cũ nhưng style lại */
        .pd-size-wrap {
            margin-top: 4px;
        }
        #variant-swatch-1 {
            margin: 0;
        }
        #variant-swatch-1 .header-container .header {
            font-size: 13px;
            font-weight: 500;
            color: var(--pd-dark);
            margin-bottom: 6px;
        }
        #variant-swatch-1 .header-container .size-current {
            font-weight: 600;
            color: #b45309;
        }
        #variant-swatch-1 .select-swap {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .swatch-element.is-size {
            position: relative;
        }
        .size-radio {
            display: none;
        }
        .size-label {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 42px;
            padding: 7px 10px;
            font-size: 13px;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            cursor: pointer;
            transition: all .15s ease;
        }
        .size-label span {
            display: inline-block;
        }
        .size-label:hover {
            border-color: var(--pd-accent);
            background: #fffbeb;
        }
        .size-label.active {
            background: #111827;
            color: #f9fafb;
            border-color: #111827;
        }

        /* QUANTITY */
        .pd-qty-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .pd-qty-box {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
            background: #f9fafb;
        }
        .pd-qty-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: transparent;
            cursor: pointer;
            font-size: 17px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #4b5563;
            transition: background .15s ease, color .15s ease;
        }
        .pd-qty-btn:hover {
            background: #e5e7eb;
            color: #111827;
        }
        .pd-qty-input {
            width: 46px;
            height: 32px;
            border: none;
            text-align: center;
            font-size: 14px;
            background: transparent;
            outline: none;
        }

        /* BUTTONS */
        .pd-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 4px;
        }
        .btn-pd {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            padding: 10px 18px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all .15s ease;
            white-space: nowrap;
        }
        .btn-add-cart {
            background: #111827;
            color: #f9fafb;
        }
        .btn-add-cart:hover {
            background: #374151;
        }
        .btn-buy-now {
            background: var(--pd-accent);
            color: #78350f;
        }
        .btn-buy-now:hover {
            background: #eab308;
        }

        .pd-safe {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            font-size: 12px;
            color: var(--pd-gray);
            margin-top: 2px;
        }
        .pd-safe-item {
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .pd-safe-item i {
            font-size: 13px;
            color: #16a34a;
        }

        /* FEATURES ICONS DƯỚI */
        .pd-features {
            margin-top: 8px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            display: grid;
            grid-template-columns: repeat(3, minmax(0,1fr));
            gap: 10px;
            font-size: 12px;
        }
        .pd-feature-item {
            display: flex;
            gap: 8px;
            align-items: flex-start;
        }
        .pd-feature-icon {
            width: 24px;
            height: 24px;
            border-radius: 999px;
            background: #f3f4f6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
        }
        .pd-feature-text strong {
            display: block;
            font-size: 12px;
            color: var(--pd-dark);
        }
        .pd-feature-text small {
            display: block;
            font-size: 11px;
            color: var(--pd-gray);
        }

        /* MÔ TẢ SẢN PHẨM */
        .pd-desc-section {
            margin-top: 28px;
        }
        .pd-desc-card {
            background: var(--pd-white);
            border-radius: var(--pd-radius-lg);
            box-shadow: var(--pd-shadow-soft);
            padding: 18px 18px 20px;
        }
        .pd-desc-title {
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 10px;
            color: var(--pd-dark);
        }
        .pd-desc-content {
            font-size: 14px;
            color: #374151;
            line-height: 1.6;
            white-space: pre-line;
        }

        /* RELATED PRODUCTS */
        .pd-related-section {
            margin-top: 26px;
        }
        .pd-related-title {
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 14px;
            color: var(--pd-dark);
        }
        .pd-related-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0,1fr));
            gap: 14px;
        }
        .pd-related-card {
            background: #ffffff;
            border-radius: 14px;
            overflow: hidden;
            border: 1px solid #e5e7eb;
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
        }
        .pd-related-card a {
            display: block;
            color: inherit;
            text-decoration: none;
        }
        .pd-related-thumb {
            position: relative;
            padding-top: 120%;
            background: #f3f4f6;
        }
        .pd-related-thumb img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .pd-related-body {
            padding: 8px 10px 10px;
        }
        .pd-related-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--pd-dark);
            margin-bottom: 4px;
            min-height: 34px;
            overflow: hidden;
        }
        .pd-related-price {
            font-size: 13px;
            color: #111827;
            font-weight: 600;
        }
        .pd-related-price-old {
            font-size: 11px;
            text-decoration: line-through;
            color: #9ca3af;
            margin-left: 6px;
        }
        .pd-related-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--pd-shadow-soft);
            border-color: var(--pd-accent);
        }

        /* RESPONSIVE */
        @media (max-width: 992px) {
            .pd-grid {
                grid-template-columns: 1fr;
            }
        }
        @media (max-width: 768px) {
            .pd-card {
                padding: 14px 14px 18px;
            }
            .pd-title {
                font-size: 20px;
            }
            .pd-related-grid {
                grid-template-columns: repeat(2, minmax(0,1fr));
            }
        }
        @media (max-width: 480px) {
            .pd-related-grid {
                grid-template-columns: repeat(2, minmax(0,1fr));
                gap: 10px;
            }
            .pd-features {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>
<?php require_once __DIR__ . "/partials/breadcrumb.php"; ?>

<div class="pd-page">
    <div class="pd-container">

        <div class="pd-grid">

            <!-- LEFT – GALLERY -->
            <div class="pd-card pd-gallery">
    
    <!-- Thumbnail bên trái -->
    <div class="pd-thumb-col">
        <div class="pd-thumb-item active" data-src="<?= htmlspecialchars($mainImg) ?>">
            <img src="<?= htmlspecialchars($mainImg) ?>">
        </div>

        <div class="pd-thumb-item" data-src="<?= htmlspecialchars($mainImg) ?>">
            <img src="<?= htmlspecialchars($mainImg) ?>">
        </div>

        <div class="pd-thumb-item" data-src="<?= htmlspecialchars($mainImg) ?>">
            <img src="<?= htmlspecialchars($mainImg) ?>">
        </div>

        <div class="pd-thumb-item" data-src="<?= htmlspecialchars($mainImg) ?>">
            <img src="<?= htmlspecialchars($mainImg) ?>">
        </div>
    </div>

    <!-- Ảnh lớn bên phải -->
    <div class="pd-main-img">
        <img id="pd-main-img"
            src="<?= htmlspecialchars($mainImg) ?>"
            alt="<?= htmlspecialchars($product['product_name']) ?>">
    </div>

</div>


            <!-- RIGHT – INFO -->
            <div class="pd-card pd-info">

                <?php if (!empty($brand['brand_name'])): ?>
                    <div class="pd-brand">
                        <span><?= htmlspecialchars($brand['brand_name']) ?></span>
                    </div>
                <?php endif; ?>

                <h1 class="pd-title">
                    <?= htmlspecialchars($product['product_name']) ?>
                </h1>

                <div class="pd-meta-line">
                    <span>
                        <i class="fa-regular fa-circle-check"></i>
                        Mã SP:
                        <strong>SP<?= str_pad($product['product_id'], 6, '0', STR_PAD_LEFT) ?></strong>
                    </span>

                    <?php if ($category): ?>
                    <span>
                        <i class="fa-regular fa-folder"></i>
                        <?= htmlspecialchars($category['category_name']) ?>
                    </span>
                    <?php endif; ?>
                </div>

                <?php
                $price     = (float)$product['product_price'];
                $salePrice = (float)$product['product_sale'];
                $hasSale   = ($salePrice > 0 && $salePrice < $price);
                ?>
                <div class="pd-price-block">
                    <div class="pd-price-main">
                        <?php if ($hasSale): ?>
                            <div class="pd-price-current">
                                <?= number_format($salePrice, 0, ',', '.') ?>đ
                            </div>
                            <div class="pd-price-original">
                                <?= number_format($price, 0, ',', '.') ?>đ
                            </div>
                        <?php else: ?>
                            <div class="pd-price-current">
                                <?= number_format($price, 0, ',', '.') ?>đ
                            </div>
                        <?php endif; ?>

                        <div class="pd-price-note">
                            Giá đã bao gồm VAT, chưa gồm phí vận chuyển.
                        </div>
                    </div>

                    <?php if ($hasSale && $price > 0): ?>
                        <?php $discount = round((($price - $salePrice) / $price) * 100); ?>
                        <div class="pd-discount-badge">
                            Giảm <?= $discount ?>%
                        </div>
                    <?php endif; ?>
                </div>

                <!-- KHUYẾN MÃI -->
                <div class="pd-promo">
                    <h4>🎁 Ưu đãi tháng này</h4>
                    <ul>
                        <li>Nhập mã <strong>NOV15</strong> giảm 15K cho đơn từ 299K</li>
                        <li>Nhập mã <strong>NOV40</strong> giảm 40K cho đơn từ 599K</li>
                        <li>Nhập mã <strong>NOV70</strong> giảm 70K cho đơn từ 999K</li>
                        <li>Freeship đơn từ 399K (áp dụng nội thành)</li>
                    </ul>
                </div>

                <!-- FORM THÊM GIỎ -->
                <form id="addToCartForm" method="POST" action="them_giohang.php" class="pd-form-section">
                    <input type="hidden" name="product_id" value="<?= $productId ?>">

                    <!-- SIZE -->
                    <div class="pd-label-row">
                        <div class="main">Kích thước</div>
                        <div class="sub">Đang chọn: <span class="size-current">S</span></div>
                    </div>
                    <div class="pd-size-wrap">
                        <div id="variant-swatch-1" class="swatch clearfix" data-option="option2" data-option-index="1">
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
                    </div>

                    <!-- SỐ LƯỢNG -->
                    <div class="pd-label-row" style="margin-top: 4px;">
                        <div class="main">Số lượng</div>
                        <div class="sub">Tối đa 99 sản phẩm / lượt</div>
                    </div>
                    <div class="pd-qty-wrap">
                        <div class="pd-qty-box">
                            <button type="button" class="pd-qty-btn" onclick="decreaseQty()">−</button>
                            <input type="number" name="quantity" id="quantity"
                                   class="pd-qty-input" value="1" min="1" max="99">
                            <button type="button" class="pd-qty-btn" onclick="increaseQty()">+</button>
                        </div>
                    </div>

                    <!-- BUTTONS -->
                    <div class="pd-actions">
                        <button type="submit" name="add_cart" class="btn-pd btn-add-cart">
                            <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                        </button>

                        <button type="submit" name="buy_now" class="btn-pd btn-buy-now">
                            Mua ngay
                        </button>
                    </div>

                    <div class="pd-safe">
                        <div class="pd-safe-item">
                            <i class="fa-solid fa-shield-heart"></i>
                            <span>Thanh toán an toàn, bảo mật</span>
                        </div>
                        <div class="pd-safe-item">
                            <i class="fa-solid fa-check"></i>
                            <span>Hàng chính hãng 100%</span>
                        </div>
                    </div>
                </form>

                <!-- FEATURES -->
                <div class="pd-features">
                    <div class="pd-feature-item">
                        <div class="pd-feature-icon">🚚</div>
                        <div class="pd-feature-text">
                            <strong>Giao hàng nhanh</strong>
                            <small>Toàn quốc 2–5 ngày làm việc</small>
                        </div>
                    </div>
                    <div class="pd-feature-item">
                        <div class="pd-feature-icon">💳</div>
                        <div class="pd-feature-text">
                            <strong>Thanh toán linh hoạt</strong>
                            <small>COD / VNPay / MoMo</small>
                        </div>
                    </div>
                    <div class="pd-feature-item">
                        <div class="pd-feature-icon">↩️</div>
                        <div class="pd-feature-text">
                            <strong>Đổi trả 15 ngày</strong>
                            <small>Đổi size / mẫu miễn phí</small>
                        </div>
                    </div>
                </div>

            </div><!-- /RIGHT -->

        </div><!-- /GRID -->

        <!-- MÔ TẢ SẢN PHẨM -->
        <div class="pd-desc-section">
            <div class="pd-desc-card">
                <h2 class="pd-desc-title">Mô tả sản phẩm</h2>
                <div class="pd-desc-content">
                    <?= nl2br(htmlspecialchars($product['product_desc'])) ?>
                </div>
            </div>
        </div>

        <!-- SẢN PHẨM LIÊN QUAN -->
        <?php if ($relatedProducts && $relatedProducts->num_rows > 1): ?>
        <div class="pd-related-section">
            <div class="pd-related-title">Sản phẩm cùng danh mục</div>
            <div class="pd-related-grid">
                <?php
                $count = 0;
                while ($rp = $relatedProducts->fetch_assoc()):
                    if ($rp['product_id'] == $productId) continue;
                    if ($count >= 5) break;
                    $count++;
                    $rpImg = buildProductImgPath($rp['product_img'] ?? '');
                    $rpPrice     = (float)$rp['product_price'];
                    $rpSalePrice = (float)$rp['product_sale'];
                    $rpHasSale   = ($rpSalePrice > 0 && $rpSalePrice < $rpPrice);
                ?>
                <div class="pd-related-card">
                    <a href="product_detail.php?id=<?= (int)$rp['product_id'] ?>">
                        <div class="pd-related-thumb">
                            <img src="<?= htmlspecialchars($rpImg) ?>"
                                 alt="<?= htmlspecialchars($rp['product_name']) ?>">
                        </div>
                        <div class="pd-related-body">
                            <div class="pd-related-name">
                                <?= htmlspecialchars($rp['product_name']) ?>
                            </div>
                            <div class="pd-related-price">
                                <?php if ($rpHasSale): ?>
                                    <?= number_format($rpSalePrice, 0, ',', '.') ?>đ
                                    <span class="pd-related-price-old">
                                        <?= number_format($rpPrice, 0, ',', '.') ?>đ
                                    </span>
                                <?php else: ?>
                                    <?= number_format($rpPrice, 0, ',', '.') ?>đ
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
                <?php endwhile; ?>
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
            const lb = document.querySelector(`label[for="${this.id}"]`);
            if (lb) lb.classList.add('active');
        });
    });

    // Gallery thumb click
    const mainImg = document.getElementById('pd-main-img');
    const thumbs  = document.querySelectorAll('.pd-thumb-item');
    thumbs.forEach(tb => {
        tb.addEventListener('click', function(){
            thumbs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            const src = this.getAttribute('data-src');
            if (src && mainImg) {
                mainImg.src = src;
            }
        });
    });
});
</script>

<script src="js/megamenu.js"></script>
<script src="js/main.js"></script>

</body>
</html>
