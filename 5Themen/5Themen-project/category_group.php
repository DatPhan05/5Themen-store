<?php
/***********************************************
 * 1. IMPORT SESSION + DB + CLASS
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
 * 3. LẤY ID NHÓM
 ***********************************************/
$parentId = isset($_GET['parent']) ? (int)$_GET['parent'] : 0;

/***********************************************
 * 4. LẤY THÔNG TIN NHÓM
 ***********************************************/
$groupInfo = null;
$groupName = "Tất cả sản phẩm";
$bannerImg = "images/default-banner.jpg";

if ($parentId > 0) {
    $groupInfo = $categoryModel->get_category($parentId);
    $groupName = $groupInfo['category_name'] ?? "Nhóm sản phẩm";
    $bannerImg = $groupInfo['banner_image'] ?? "images/default-banner.jpg";
}

/***********************************************
 * 5. LẤY BRAND THUỘC NHÓM
 ***********************************************/
$brandList = ($parentId > 0) 
    ? $brandModel->get_brand_by_category($parentId)
    : null;

/***********************************************
 * 6. LẤY SẢN PHẨM THEO NHÓM
 ***********************************************/
if ($parentId > 0) {
    $productList = $productModel->get_product_by_category($parentId);

    if (!$productList || (is_object($productList) && $productList->num_rows == 0)) {
        $productList = $productModel->get_all_products();
    }
} else {
    $productList = $productModel->get_all_products();
}

/***********************************************
 * 7. BREADCRUMB DÙNG PARTIAL
 ***********************************************/
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
    ['text' => $groupName]
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($groupName) ?> - 5Themen</title>
    <link rel="stylesheet" href="CSS/style.css">
</head>

<body>

<?php require_once __DIR__ . "/partials/header.php"; ?>
<?php require_once __DIR__ . "/partials/breadcrumb.php"; ?>

<!-- Category Group Page -->
<div class="category-page">
    <div class="container">

        <!-- Page Title -->
        <h1 class="category-title" style="font-size: 32px; font-weight: 600; margin-bottom: 20px;">
            <?= htmlspecialchars($groupName) ?>
        </h1>

        <!-- Brand Filter -->
        <?php if ($brandList && $brandList->num_rows > 0): ?>
        <div class="brand-filter" style="margin-bottom: 30px;">
            
            <a href="category_group.php?parent=<?= $parentId ?>" 
               class="brand-btn"
               style="display: inline-block; padding: 8px 16px; margin-right: 10px;
                      border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333;">
                Tất cả
            </a>

            <?php while ($b = $brandList->fetch_assoc()): ?>
                <a href="category.php?cat=<?= $parentId ?>&brand=<?= $b['brand_id'] ?>"
                   class="brand-btn"
                   style="display: inline-block; padding: 8px 16px; margin-right: 10px;
                          border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333;">
                    <?= htmlspecialchars($b['brand_name']) ?>
                </a>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <!-- Product Grid -->
        <div class="row">
            <?php 
            $hasProducts = false;

            if ($productList && $productList->num_rows > 0):
                $hasProducts = true;

                while ($p = $productList->fetch_assoc()):
            ?>
                <div class="col-3" style="width: 25%; padding: 10px; box-sizing: border-box;">
                    <div class="product-card"
                         style="border: 1px solid #eee; padding: 15px; text-align: center; border-radius: 8px;">

                        <a href="product_detail.php?id=<?= $p['product_id'] ?>"
                           style="text-decoration: none; color: inherit;">

                            <img src="<?= htmlspecialchars($p['product_image']) ?>"
                                 alt="<?= htmlspecialchars($p['product_name']) ?>"
                                 style="width: 100%; height: 300px; object-fit: cover; margin-bottom: 15px; border-radius: 4px;">

                            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 10px; min-height: 40px;">
                                <?= htmlspecialchars($p['product_name']) ?>
                            </h3>

                            <p class="price"
                               style="font-size: 18px; font-weight: 600; color: #000; margin-bottom: 15px;">
                                <?= number_format($p['product_price'], 0, ',', '.') ?>đ
                            </p>
                        </a>

                        <button class="add-to-cart-btn"
                                style="width: 100%; padding: 12px; background: #000; color: #fff;
                                       border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;"
                                onmouseover="this.style.background='#333'" 
                                onmouseout="this.style.background='#000'">
                            THÊM VÀO GIỎ
                        </button>

                    </div>
                </div>
            <?php 
                endwhile;
            endif;

            if (!$hasProducts):
            ?>
                <div class="no-products"
                     style="width: 100%; text-align: center; padding: 60px 20px; color: #999; font-size: 18px;">
                    <p>Không có sản phẩm nào trong nhóm này.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Responsive Inline CSS -->
<style>
@media (max-width: 1024px) { .col-3 { width: 33.33% !important; } }
@media (max-width: 768px)  { .col-3 { width: 50% !important; } }
@media (max-width: 480px)  { .col-3 { width: 100% !important; } }

.brand-btn:hover { background: #f5f5f5; border-color: #000 !important; }
</style>

<?php require_once __DIR__ . "/partials/footer.php"; ?>

</body>
</html>
