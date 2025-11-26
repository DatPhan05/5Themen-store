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
$page    = isset($_GET['page'])  ? (int)$_GET['page']  : 1; // Trang hiện tại

if ($catId == 14) {
    require_once __DIR__ . "/news.php";
    exit;
}

// 1. Cài đặt Phân Trang
$limit = 8; // Số lượng sản phẩm trên mỗi trang
$offset = ($page - 1) * $limit; // Vị trí bắt đầu lấy dữ liệu

$categoryInfo = ($catId > 0) ? $categoryModel->get_category($catId) : null;
$brandInfo    = ($brandId > 0) ? $brandModel->get_brand($brandId)  : null;

$pageTitle    = $categoryInfo['category_name'] ?? "Tất cả sản phẩm";

$brandList = ($catId > 0) 
    ? $brandModel->get_brand_by_category($catId)
    : null;

// 2. Tính Tổng số Sản phẩm (Total Records)
$totalRecords = 0;
if ($catId > 0 && $brandId > 0 && $brandInfo) {
    // Giả sử có hàm để đếm tổng số sản phẩm theo danh mục và thương hiệu
    $totalRecords = $productModel->count_product_by_category_brand($catId, $brandId); 
} elseif ($catId > 0) {
    // Giả sử có hàm để đếm tổng số sản phẩm theo danh mục
    $totalRecords = $productModel->count_product_by_category($catId);
} else {
    // Giả sử có hàm để đếm tổng số tất cả sản phẩm
    $totalRecords = $productModel->count_all_products(); 
}

// 3. Tính Tổng số Trang (Total Pages)
$totalPages = ceil($totalRecords / $limit);

// Đảm bảo trang hiện tại không nhỏ hơn 1 và không lớn hơn tổng số trang
if ($page < 1) $page = 1;
if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// 5. Lấy danh sách Sản phẩm cho trang hiện tại (có LIMIT và OFFSET)
if ($catId > 0 && $brandId > 0 && $brandInfo) {
    // Cần sửa đổi hàm get_product_by_category_brand để chấp nhận $limit và $offset
    $productList = $productModel->get_product_by_category_brand($catId, $brandId, $limit, $offset);
} elseif ($catId > 0) {
    // Cần sửa đổi hàm get_product_by_category để chấp nhận $limit và $offset
    $productList = $productModel->get_product_by_category($catId, $limit, $offset);
} else {
    // Cần sửa đổi hàm get_all_products để chấp nhận $limit và $offset
    $productList = $productModel->get_all_products($limit, $offset);
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
                <?php while ($b = $brandList->fetch_assoc()): 
                    // Thêm $page=1 để reset trang khi đổi brand
                    $brandUrl = "category.php?cat={$catId}&brand={$b['brand_id']}&page=1";
                ?>
                    <a class="filter-pill <?= ($brandId == $b['brand_id']) ? 'active' : '' ?>"
                       href="<?= $brandUrl ?>">
                       <?= htmlspecialchars($b['brand_name']) ?>
                    </a>
                <?php endwhile; ?>
                <?php if ($brandId > 0): ?>
                    <?php 
                        // Link quay lại All Brand
                        $allBrandUrl = "category.php?cat={$catId}&page=1";
                    ?>
                    <a class="filter-pill" href="<?= $allBrandUrl ?>">
                       Tất cả Thương hiệu
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($productList && $productList->num_rows > 0): ?>
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

                        <?php
                        $price     = (float)$row['product_price'];
                        $salePrice = (float)$row['product_sale'];

                        $hasSale = ($salePrice > 0 && $salePrice < $price);
                        ?>

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
            <p style="text-align: center; margin-top: 50px;">
                Không tìm thấy sản phẩm nào trong danh mục này.
            </p>
        <?php endif; ?>


        <?php if ($totalPages > 1): ?>
            <div class="pagination-area">
                <nav aria-label="Phân trang">
                    <ul class="pagination">
                        
                        <?php 
                            $prevPage = $page - 1; 
                            $prevUrl = "category.php?cat={$catId}" 
                                . ($brandId > 0 ? "&brand={$brandId}" : "") 
                                . "&page={$prevPage}";
                        ?>
                        <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page > 1) ? $prevUrl : '#' ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>

                        <?php for ($i = 1; $i <= $totalPages; $i++): 
                            $pageUrl = "category.php?cat={$catId}" 
                                . ($brandId > 0 ? "&brand={$brandId}" : "") 
                                . "&page={$i}";
                        ?>
                            <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                                <a class="page-link" href="<?= $pageUrl ?>"><?= $i ?></a>
                            </li>
                        <?php endfor; ?>

                        <?php 
                            $nextPage = $page + 1;
                            $nextUrl = "category.php?cat={$catId}" 
                                . ($brandId > 0 ? "&brand={$brandId}" : "") 
                                . "&page={$nextPage}";
                        ?>
                        <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                            <a class="page-link" href="<?= ($page < $totalPages) ? $nextUrl : '#' ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        <?php endif; ?>

    </div>
</section>

<?php require_once __DIR__ . "/partials/footer.php"; ?>