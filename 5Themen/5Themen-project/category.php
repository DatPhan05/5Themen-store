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
$page    = isset($_GET['page'])  ? (int)$_GET['page']  : 1;

// Lấy ID lớn nhất để xác định badge NEW
$maxProductId = $productModel->get_last_id();

// PHÂN TRANG

$limit  = 8;
$offset = ($page - 1) * $limit;

// Lấy thông tin danh mục / thương hiệu
$categoryInfo = ($catId > 0) ? $categoryModel->get_category($catId) : null;
$brandInfo    = ($brandId > 0) ? $brandModel->get_brand($brandId)   : null;

$pageTitle = $categoryInfo['category_name'] ?? "Tất cả sản phẩm";

$brandList = ($catId > 0)
    ? $brandModel->get_brand_by_category($catId)
    : null;


// ============================
// TỔNG SẢN PHẨM
// ============================
$totalRecords = 0;

if ($catId > 0 && $brandId > 0 && $brandInfo) {
    $totalRecords = $productModel->count_product_by_category_brand($catId, $brandId);
} elseif ($catId > 0) {
    $totalRecords = $productModel->count_product_by_category($catId);
} else {
    $totalRecords = $productModel->count_all_products();
}

// Tổng số trang
$totalPages = ceil($totalRecords / $limit);

// Giới hạn số trang
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;


// ============================
// TRUY VẤN SẢN PHẨM THEO TRANG
// ============================
if ($catId > 0 && $brandId > 0 && $brandInfo) {
    $productList = $productModel->get_product_by_category_brand($catId, $brandId, $limit, $offset);
} elseif ($catId > 0) {
    $productList = $productModel->get_product_by_category($catId, $limit, $offset);
} else {
    $productList = $productModel->get_all_products($limit, $offset);
}


// BREADCRUMB
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
    ['text' => $pageTitle]
];
?>

<?php require_once __DIR__ . "/partials/header.php"; ?>

<section class="category-page">
    <div class="container">

        <?php require __DIR__ . "/partials/breadcrumb.php"; ?>

        <h1 class="category-title"><?= htmlspecialchars($pageTitle) ?></h1>

        <?php if ($brandList && $brandList->num_rows > 0): ?>
            <div class="category-filter">
                <?php while ($b = $brandList->fetch_assoc()): 
                    $brandUrl = "category.php?cat={$catId}&brand={$b['brand_id']}&page=1";
                ?>
                    <a class="filter-pill <?= ($brandId == $b['brand_id']) ? 'active' : '' ?>"
                       href="<?= $brandUrl ?>">
                        <?= htmlspecialchars($b['brand_name']) ?>
                    </a>
                <?php endwhile; ?>

                <?php if ($brandId > 0): 
                    $allBrandUrl = "category.php?cat={$catId}&page=1";
                ?>
                    <a class="filter-pill" href="<?= $allBrandUrl ?>">Tất cả Thương hiệu</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>


        <?php if ($productList && $productList->num_rows > 0): ?>
            <div class="product-grid">

                <?php while ($row = $productList->fetch_assoc()): ?>
                    <?php
                        $price     = (float)$row['product_price'];
                        $salePrice = (float)$row['product_sale'];

                        $hasSale = ($salePrice > 0 && $salePrice < $price);
                        $isNew   = ($row['product_id'] >= $maxProductId - 20);
                    ?>

                    <div class="product-item">

                        <div class="product-media">

                            <?php if ($hasSale): ?>
                                <span class="product-badge hot">HOT</span>
                            <?php endif; ?>

                            <?php if ($isNew): ?>
                                <span class="product-badge new">NEW</span>
                            <?php endif; ?>

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

                        <div class="product-price">

                            <?php if ($hasSale): ?>
                                <span class="price-current">
                                    <?= number_format($salePrice, 0, ',', '.') ?>đ
                                </span>

                                <span class="price-old">
                                    <?= number_format($price, 0, ',', '.') ?>đ
                                </span>

                                <span class="price-sale-badge">
                                    -<?= round((($price - $salePrice) / $price) * 100) ?>%
                                </span>

                            <?php else: ?>
                                <span class="price-current">
                                    <?= number_format($price, 0, ',', '.') ?>đ
                                </span>
                            <?php endif; ?>

                        </div>

                        <div class="product-color-list">
                            <div class="product-color">
                                <img src="<?= htmlspecialchars($row['product_img']) ?>" alt="">
                            </div>
                        </div>

                    </div>
                <?php endwhile; ?>

            </div>
        <?php else: ?>
            <p style="text-align:center; margin-top:50px;">Không tìm thấy sản phẩm nào.</p>
        <?php endif; ?>


        <?php if ($totalPages > 1): ?>
            <div class="pagination-area">
                <nav aria-label="pagination">
                    <ul class="pagination">

                        <?php 
                            $prev = $page - 1;
                            $prevUrl = "category.php?cat={$catId}" 
                                . ($brandId ? "&brand={$brandId}" : "") 
                                . "&page={$prev}";
                        ?>

                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? $prevUrl : '#' ?>">&laquo;</a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): 
                            $pUrl = "category.php?cat={$catId}"
                                . ($brandId ? "&brand={$brandId}" : "")
                                . "&page={$i}";
                        ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $pUrl ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>


                        <?php 
                            $next = $page + 1;
                            $nextUrl = "category.php?cat={$catId}"
                                . ($brandId ? "&brand={$brandId}" : "")
                                . "&page={$next}";
                        ?>

                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $totalPages) ? $nextUrl : '#' ?>">&raquo;</a>
                        </li>

                    </ul>
                </nav>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . "/partials/footer.php"; ?>
