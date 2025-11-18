<?php
include "header.php";

require_once __DIR__ . "/admin/class/product_class.php";
require_once __DIR__ . "/admin/class/category_class.php";
require_once __DIR__ . "/admin/class/brand_class.php";

$productModel  = new Product();
$categoryModel = new Category();
$brandModel    = new Brand();

/* =============================
   1. Lấy ID từ URL
============================= */
$catId   = isset($_GET['cat'])   ? (int)$_GET['cat']   : 0;
$brandId = isset($_GET['brand']) ? (int)$_GET['brand'] : 0;

/* =============================
   2. Lấy thông tin danh mục / brand
============================= */
$categoryInfo = null;
$brandInfo    = null;
$pageTitle    = "Tất cả sản phẩm";

if ($catId > 0) {
    $categoryInfo = $categoryModel->get_category($catId);
}

if ($brandId > 0) {
    $brandInfo = $brandModel->get_brand($brandId);
}

$categoryName = $categoryInfo ? $categoryInfo['category_name'] : "";

/* =============================
   3. Lấy danh sách brand trong danh mục
============================= */
$brandList = ($catId > 0) ? $brandModel->get_brand_by_category($catId) : null;

/* =============================
   4. Lấy sản phẩm theo điều kiện
============================= */
if ($catId > 0 && $brandId > 0 && $brandInfo) {
    // Lọc theo danh mục + loại sản phẩm
    $productList = $productModel->get_product_by_category_brand($catId, $brandId);
    $pageTitle   = $categoryName . " – " . $brandInfo['brand_name'];
} elseif ($catId > 0) {
    // Lọc theo danh mục
    $productList = $productModel->get_product_by_category($catId);
    $pageTitle   = $categoryName ?: $pageTitle;
} else {
    // Xem tất cả sản phẩm
    $productList = $productModel->get_all_products();
}

/* =============================
   5. Breadcrumb dùng component chung
============================= */
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
];

if ($catId && $categoryName) {
    if ($brandId && $brandInfo) {
        // Trang chủ / Áo Nam / Áo thun
        $breadcrumbs[] = [
            'text' => $categoryName,
            'url'  => 'category.php?cat=' . $catId,
        ];
        $breadcrumbs[] = ['text' => $brandInfo['brand_name']];
    } else {
        // Trang chủ / Áo Nam
        $breadcrumbs[] = ['text' => $categoryName];
    }
} else {
    // Trang chủ / Tất cả sản phẩm
    $breadcrumbs[] = ['text' => $pageTitle];
}
?>

<section class="category-page">
    <div class="container">

        <!-- BREADCRUMB COMPONENT -->
        <?php include 'breadcrumb.php'; ?>

        <div class="category-layout">
            <!-- KHỐI SẢN PHẨM -->
            <div class="category-products">

                <h1 class="category-title">
                    <?= htmlspecialchars($pageTitle) ?>
                </h1>

                <!-- HÀNG FILTER LOẠI SẢN PHẨM THEO BRAND -->
                <?php if ($brandList && $brandList->num_rows > 0 && $catId): ?>
                    <div class="category-filter">
                        <?php while ($b = $brandList->fetch_assoc()): ?>
                            <a
                                class="filter-pill <?= ($brandId == $b['brand_id']) ? 'active' : '' ?>"
                                href="category.php?cat=<?= $catId ?>&brand=<?= (int)$b['brand_id'] ?>"
                            >
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

                                <!-- Khối media: ảnh + nút hover -->
                                <div class="product-media">
                                    <!-- Ảnh sản phẩm (khung vuông cố định) -->
                                    <a href="product_detail.php?id=<?= (int)$row['product_id'] ?>" class="product-thumb">
                                        <img
                                            src="<?= htmlspecialchars($row['product_img']) ?>"
                                            alt="<?= htmlspecialchars($row['product_name']) ?>"
                                        >
                                    </a>

                                    <!-- Nút hover: Thêm giỏ + Xem nhanh -->
                                    <div class="product-hover-actions">
                                        <div class="product-hover-actions-inner">
                                            <a
                                                href="cart.php?action=add&id=<?= (int)$row['product_id'] ?>"
                                                class="hover-btn"
                                                title="Thêm vào giỏ"
                                            >
                                                <i class="fa-solid fa-cart-shopping"></i>
                                            </a>
                                            <a
                                                href="product_detail.php?id=<?= (int)$row['product_id'] ?>"
                                                class="hover-btn"
                                                title="Xem chi tiết"
                                            >
                                                <i class="fa-regular fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Thông tin: tên + giá -->
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

                                <!-- Ô màu / biến thể (demo: 1 ô dùng lại ảnh chính) -->
                                <div class="product-extra">
                                    <div class="product-color-list">
                                        <div class="product-color">
                                            <img
                                                src="<?= htmlspecialchars($row['product_img']) ?>"
                                                alt="<?= htmlspecialchars($row['product_name']) ?>"
                                            >
                                        </div>
                                        <!-- Sau này nếu có nhiều màu, bạn lặp thêm nhiều .product-color ở đây -->
                                    </div>
                                </div>

                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>Không có sản phẩm nào.</p>
                    <?php endif; ?>
                </div>

            </div>
        </div>

    </div>
</section>

<?php include "footer.php"; ?>
