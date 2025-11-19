<?php
/***********************************************
 * 1. IMPORT FILE HỆ THỐNG
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
 * 3. LẤY GIÁ TRỊ TỪ URL
 ***********************************************/
$catId   = isset($_GET['cat'])   ? (int)$_GET['cat']   : 0;
$brandId = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;

/***********************************************
 * 4. LẤY INFO DANH MỤC / BRAND
 ***********************************************/
$categoryInfo = ($catId > 0) ? $categoryModel->get_category($catId) : null;
$brandInfo    = ($brandId > 0) ? $brandModel->get_brand($brandId) : null;

$pageTitle    = "Tất cả sản phẩm";
$categoryName = $categoryInfo['category_name'] ?? "";

/***********************************************
 * 5. LẤY LIST BRAND THEO DANH MỤC
 ***********************************************/
$brandList = ($catId > 0) 
    ? $brandModel->get_brand_by_category($catId)
    : null;

/***********************************************
 * 6. LẤY LIST SẢN PHẨM
 ***********************************************/
if ($catId > 0 && $brandId > 0 && $brandInfo) {
    $productList = $productModel->get_product_by_category_brand($catId, $brandId);
    $pageTitle   = $categoryName . " – " . $brandInfo['brand_name'];
} elseif ($catId > 0) {
    $productList = $productModel->get_product_by_category($catId);
    $pageTitle   = $categoryName ?: $pageTitle;
} else {
    $productList = $productModel->get_all_products();
}

/***********************************************
 * 7. BREADCRUMB
 ***********************************************/
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
];

if ($catId && $categoryName) {
    if ($brandId && $brandInfo) {
        $breadcrumbs[] = [
            'text' => $categoryName,
            'url'  => 'category.php?cat=' . $catId
        ];
        $breadcrumbs[] = ['text' => $brandInfo['brand_name']];
    } else {
        $breadcrumbs[] = ['text' => $categoryName];
    }
} else {
    $breadcrumbs[] = ['text' => $pageTitle];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>

<section class="category-page">
    <div class="container">

        <!-- BREADCRUMB -->
        <?php require __DIR__ . '/partials/breadcrumb.php'; ?>

        <div class="category-layout">

            <!-- KHỐI SẢN PHẨM -->
            <div class="category-products">

                <h1 class="category-title">
                    <?= htmlspecialchars($pageTitle) ?>
                </h1>

                <!-- FILTER BRAND -->
                <?php if ($brandList && $brandList->num_rows > 0 && $catId): ?>
                    <div class="category-filter">
                        <?php while ($b = $brandList->fetch_assoc()): ?>
                            <a class="filter-pill <?= ($brandId == $b['brand_id']) ? 'active' : '' ?>"
                               href="category.php?cat=<?= $catId ?>&brand=<?= (int)$b['brand_id'] ?>">
                                <?= htmlspecialchars($b['brand_name']) ?>
                            </a>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>

                <!-- GRID SẢN PHẨM -->
                <div class="product-grid">

                    <?php if ($productList && $productList->num_rows > 0): ?>
                        <?php while ($row = $productList->fetch_assoc()): ?>
                            <div class="product-item">

                                <!-- MEDIA -->
                                <div class="product-media">
                                    <a href="product_detail.php?id=<?= (int)$row['product_id'] ?>"
                                       class="product-thumb">
                                        <img src="<?= htmlspecialchars($row['product_img']) ?>"
                                             alt="<?= htmlspecialchars($row['product_name']) ?>">
                                    </a>

                                    <div class="product-hover-actions">
                                        <div class="product-hover-actions-inner">
                                            <a href="them_giohang.php?action=add&id=<?= (int)$row['product_id'] ?>"
                                               class="hover-btn"
                                               title="Thêm vào giỏ">
                                                <i class="fa-solid fa-cart-shopping"></i>
                                            </a>

                                            <a href="product_detail.php?id=<?= (int)$row['product_id'] ?>"
                                               class="hover-btn"
                                               title="Xem chi tiết">
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- THÔNG TIN -->
                                <div class="product-info">
                                    <h3 class="product-name">
                                        <a href="product_detail.php?id=<?= (int)$row['product_id'] ?>">
                                            <?= htmlspecialchars($row['product_name']) ?>
                                        </a>
                                    </h3>

                                    <p class="product-price">
                                        <?= number_format($row['product_price'], 0, ',', '.') ?>đ
                                    </p>
                                </div>

                                <!-- MÀU SẮC DEMO -->
                                <div class="product-extra">
                                    <div class="product-color-list">
                                        <div class="product-color">
                                            <img src="<?= htmlspecialchars($row['product_img']) ?>"
                                                 alt="<?= htmlspecialchars($row['product_name']) ?>">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        <?php endwhile; ?>

                    <?php else: ?>
                        <p>Không có sản phẩm nào.</p>
                    <?php endif; ?>

                </div>
                <!-- ./product-grid -->

            </div>
        </div>

    </div>
</section>

<?php require_once __DIR__ . "/partials/footer.php"; ?>

<script src="js/megamenu.js"></script>
<script src="js/main.js"></script>

</body>
</html>
