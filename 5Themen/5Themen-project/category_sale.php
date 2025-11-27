<?php 
require_once __DIR__.'/include/session.php';
require_once __DIR__.'/include/database.php';
require_once __DIR__.'/admin/class/product_class.php';

$productModel = new Product();

// Lấy ID sản phẩm mới nhất (dùng để hiển thị badge NEW)
$maxProductId = $productModel->get_last_id();

$pageTitle = "Khuyến mãi hot";

// --- PHÂN TRANG ---
$limit = 8;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

// Tổng số sp giảm giá
$totalRecords = $productModel->count_sale_products();
$totalPages   = ceil($totalRecords / $limit);

// Lấy danh sách sản phẩm theo trang
$saleProducts = $productModel->get_hot_sale_products($limit, $offset);

// Breadcrumb
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
    ['text' => $pageTitle]
];

require __DIR__."/partials/header.php";
require __DIR__."/partials/breadcrumb.php";
?>

<section class="category-page" style="margin-top:10px;">
<div class="container">

<h1 class="category-title"><?= htmlspecialchars($pageTitle) ?></h1>

<?php if ($saleProducts && $saleProducts->num_rows > 0): ?>
    <div class="product-grid">

        <?php while ($p = $saleProducts->fetch_assoc()): ?>
            <?php
            $price = (float)$p['product_price'];
            $sale  = (float)$p['product_sale'];

            // Sản phẩm đang giảm giá?
            $hasSale = ($sale > 0 && $sale < $price);

            // Sản phẩm mới? (ví dụ: thuộc top 20 id mới nhất)
            $isNew = ($p['product_id'] >= $maxProductId - 20);
            ?>
            <div class="product-item">

                <div class="product-media">
                    <!-- Badge HOT SALE (góc trái) -->
                    <?php if ($hasSale): ?>
                        <span class="product-badge hot">HOT SALE</span>
                    <?php endif; ?>

                    <!-- Badge NEW (góc phải) -->
                    <?php if ($isNew): ?>
                        <span class="product-badge new">NEW</span>
                    <?php endif; ?>
                    
                    <a href="product_detail.php?id=<?= $p['product_id'] ?>" class="product-thumb">
                        <img src="<?= htmlspecialchars($p['product_img']) ?>" alt="">
                    </a>

                    <div class="product-hover-actions">
                        <div class="product-hover-actions-inner">
                            <a href="them_giohang.php?action=add&id=<?= $p['product_id'] ?>" class="hover-btn">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                            <a href="product_detail.php?id=<?= $p['product_id'] ?>" class="hover-btn">
                                <i class="fa-regular fa-eye"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <h3 class="product-name">
                    <a href="product_detail.php?id=<?= $p['product_id'] ?>">
                        <?= htmlspecialchars($p['product_name']) ?>
                    </a>
                </h3>

                <div class="product-price">
                    <span class="price-current"><?= number_format($sale,0,',','.') ?>đ</span>
                    <span class="price-old"><?= number_format($price,0,',','.') ?>đ</span>
                    <span class="price-sale-badge">
                        -<?= round((($price - $sale) / $price) * 100) ?>%
                    </span>
                </div>

                <div class="product-color-list">
                    <div class="product-color">
                        <img src="<?= htmlspecialchars($p['product_img']) ?>" alt="">
                    </div>
                </div>

            </div>
        <?php endwhile; ?>

    </div>

    <!-- PHÂN TRANG -->
    <div class="pagination-area">
        <nav>
            <ul class="pagination">

                <li class="page-item <?= ($page<=1?'disabled':'') ?>">
                    <a class="page-link" href="<?= $page>1?'category_sale.php?page='.($page-1):'#' ?>">&laquo;</a>
                </li>

                <?php for($i=1;$i<=$totalPages;$i++): ?>
                    <li class="page-item <?= ($i==$page?'active':'') ?>">
                        <a class="page-link" href="category_sale.php?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= ($page>=$totalPages?'disabled':'') ?>">
                    <a class="page-link" href="<?= $page<$totalPages?'category_sale.php?page='.($page+1):'#' ?>">&raquo;</a>
                </li>

            </ul>
        </nav>
    </div>

<?php else: ?>
    <p style="text-align:center;margin-top:40px;">Không có sản phẩm khuyến mãi.</p>
<?php endif; ?>

</div>
</section>

<?php require __DIR__."/partials/footer.php"; ?>
