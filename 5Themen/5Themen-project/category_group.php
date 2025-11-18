<?php
include 'header.php';
require_once __DIR__ . '/admin/class/product_class.php';
require_once __DIR__ . '/admin/class/category_class.php';
require_once __DIR__ . '/admin/class/brand_class.php';

$productModel = new Product();
$categoryModel = new Category();
$brandModel = new Brand();

/* =============================
   1. Lấy ID nhóm từ URL
============================= */
$parentId = isset($_GET['parent']) ? (int)$_GET['parent'] : 0;

/* =============================
   2. Lấy thông tin nhóm
============================= */
$groupInfo = null;
$groupName = "Tất cả sản phẩm";
$bannerImg = "images/default-banner.jpg";

if ($parentId > 0) {
    $groupInfo = $categoryModel->get_category($parentId);
    $groupName = $groupInfo ? $groupInfo['category_name'] : "Nhóm sản phẩm";
    $bannerImg = $groupInfo && isset($groupInfo['banner_image']) ? $groupInfo['banner_image'] : "images/default-banner.jpg";
}

/* =============================
   3. Lấy brand thuộc nhóm
============================= */
$brandList = ($parentId > 0) ? $brandModel->get_brand_by_category($parentId) : null;

/* =============================
   4. Lấy toàn bộ sản phẩm trong nhóm
============================= */
$productList = null;

if ($parentId > 0) {
    // Thử lấy sản phẩm theo parent
    $productList = $productModel->get_product_by_category($parentId);
    
    // Nếu không có, thử lấy tất cả sản phẩm
    if (!$productList || (is_object($productList) && $productList->num_rows == 0)) {
        $productList = $productModel->get_all_products();
    }
} else {
    $productList = $productModel->get_all_products();
}

/* =============================
   5. Breadcrumb
============================= */
$breadcrumbHtml = '<a href="trangchu.php">Trang chủ</a>';
if ($groupName) {
    $breadcrumbHtml .= ' / <span>' . htmlspecialchars($groupName) . '</span>';
}
?>

<!-- Category Group Page -->
<div class="category-page">
    <div class="container">
        
        <!-- Breadcrumb -->
        <div class="breadcrumb" style="margin: 20px 0; font-size: 14px; color: #666;">
            <?= $breadcrumbHtml ?>
        </div>

        <!-- Page Title -->
        <h1 class="category-title" style="font-size: 32px; font-weight: 600; margin-bottom: 20px;">
            <?= htmlspecialchars($groupName) ?>
        </h1>

        <!-- Brand Filter (nếu có) -->
        <?php if ($brandList && is_object($brandList) && $brandList->num_rows > 0): ?>
        <div class="brand-filter" style="margin-bottom: 30px;">
            <a href="category_group.php?parent=<?= $parentId ?>" 
               class="brand-btn" style="display: inline-block; padding: 8px 16px; margin-right: 10px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333;">
                Tất cả
            </a>
            <?php while ($b = $brandList->fetch_assoc()): ?>
                <a href="category.php?cat=<?= $parentId ?>&brand=<?= $b['brand_id'] ?>" 
                   class="brand-btn" style="display: inline-block; padding: 8px 16px; margin-right: 10px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333;">
                    <?= htmlspecialchars($b['brand_name']) ?>
                </a>
            <?php endwhile; ?>
        </div>
        <?php endif; ?>

        <!-- Product Grid -->
        <div class="row">
            <?php 
            $hasProducts = false;
            
            if ($productList && is_object($productList) && $productList->num_rows > 0):
                $hasProducts = true;
                while ($p = $productList->fetch_assoc()):
            ?>
                <div class="col-3" style="width: 25%; padding: 10px; box-sizing: border-box;">
                    <div class="product-card" style="border: 1px solid #eee; padding: 15px; text-align: center; border-radius: 8px;">
                        <a href="product_detail.php?id=<?= $p['product_id'] ?>" style="text-decoration: none; color: inherit;">
                            <img src="<?= htmlspecialchars($p['product_image']) ?>" 
                                 alt="<?= htmlspecialchars($p['product_name']) ?>" 
                                 style="width: 100%; height: 300px; object-fit: cover; margin-bottom: 15px; border-radius: 4px;">
                            <h3 style="font-size: 16px; font-weight: 500; margin-bottom: 10px; min-height: 40px;">
                                <?= htmlspecialchars($p['product_name']) ?>
                            </h3>
                            <p class="price" style="font-size: 18px; font-weight: 600; color: #000; margin-bottom: 15px;">
                                <?= number_format($p['product_price'], 0, ',', '.') ?>đ
                            </p>
                        </a>
                        <button class="add-to-cart-btn" 
                                style="width: 100%; padding: 12px; background: #000; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; font-weight: 500;"
                                onmouseover="this.style.background='#333'" 
                                onmouseout="this.style.background='#000'">
                            THÊM VÀO GIỎ
                        </button>
                    </div>
                </div>
            <?php 
                endwhile;
            endif;
            
            // Hiển thị thông báo nếu không có sản phẩm
            if (!$hasProducts):
            ?>
                <div class="no-products" style="width: 100%; text-align: center; padding: 60px 20px; color: #999; font-size: 18px;">
                    <p>Không có sản phẩm nào trong nhóm này.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<!-- Inline CSS for responsive -->
<style>
@media (max-width: 1024px) {
    .col-3 { width: 33.33% !important; }
}

@media (max-width: 768px) {
    .col-3 { width: 50% !important; }
}

@media (max-width: 480px) {
    .col-3 { width: 100% !important; }
}

.brand-btn:hover {
    background: #f5f5f5;
    border-color: #000 !important;
}
</style>

<?php include 'footer.php'; ?>
