<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

require_once __DIR__ . '/admin/class/product_class.php';
require_once __DIR__ . '/admin/class/category_class.php';
require_once __DIR__ . '/admin/class/brand_class.php';

$productModel  = new Product();
$categoryModel = new Category();
$brandModel    = new Brand();

$catId   = isset($_GET['cat'])   ? (int)$_GET['cat']   : 0;
$brandId = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;

if ($catId == 14) {
    require_once __DIR__ . "/news.php";
    exit;
}

$categoryInfo = ($catId > 0) ? $categoryModel->get_category($catId) : null;
$brandInfo    = ($brandId > 0) ? $brandModel->get_brand($brandId)  : null;

$pageTitle    = $categoryInfo['category_name'] ?? "Tất cả sản phẩm";

$brandList = ($catId > 0) 
    ? $brandModel->get_brand_by_category($catId)
    : null;

if ($catId > 0 && $brandId > 0 && $brandInfo) {
    $productList = $productModel->get_product_by_category_brand($catId, $brandId);
} elseif ($catId > 0) {
    $productList = $productModel->get_product_by_category($catId);
} else {
    $productList = $productModel->get_all_products();
}

$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
];
$breadcrumbs[] = ['text' => $pageTitle];
?>

<?php require_once __DIR__ . "/partials/header.php"; ?>

<section class="category-page">
    <div class="container">

        <?php require __DIR__ . "/partials/breadcrumb.php"; ?>

        <h1 class="category-title"><?= htmlspecialchars($pageTitle) ?></h1>

        <?php if ($brandList && $brandList->num_rows > 0): ?>
            <div class="category-filter">
                <?php while ($b = $brandList->fetch_assoc()): ?>
                    <a class="filter-pill <?= ($brandId == $b['brand_id']) ? 'active' : '' ?>"
                       href="category.php?cat=<?= $catId ?>&brand=<?= $b['brand_id'] ?>">
                       <?= htmlspecialchars($b['brand_name']) ?>
                    </a>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

        <div class="product-grid">
            <?php while ($row = $productList->fetch_assoc()): ?>
                <div class="product-item">

                    <div class="product-media">
                        <a href="product_detail.php?id=<?= $row['product_id'] ?>" class="product-thumb">
                            <img src="<?= htmlspecialchars($row['product_img']) ?>" alt="">

                        </a>

                        <div class="product-hover-actions">
                            <div class="product-hover-actions-inner">
                                <a href="them_giohang.php?action=add&id=<?= $row['product_id'] ?>" class="hover-btn">
                                    <i class="fa-solid fa-cart-shopping"></i>
                                </a>
                                <a href="product_detail.php?id=<?= $row['product_id'] ?>" class="hover-btn">
                                    <i class="fa-regular fa-eye"></i>
                                </a>
                            </div>
                        </div>
                    </div>

                    <h3 class="product-name">
                        <a href="product_detail.php?id=<?= $row['product_id'] ?>">
                            <?= htmlspecialchars($row['product_name']) ?>
                        </a>
                    </h3>

                    <p class="product-price"><?= number_format($row['product_price']) ?>đ</p>

                    <div class="product-color-list">
                        <div class="product-color">
                            <img src="<?= htmlspecialchars($row['product_img']) ?>" alt="">

                        </div>
                    </div>

                </div>
            <?php endwhile; ?>
        </div>

    </div>
</section>

<?php require_once __DIR__ . "/partials/footer.php"; ?>
