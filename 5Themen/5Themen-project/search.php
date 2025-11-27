<?php
require_once __DIR__ . '/include/session.php';
require_once __DIR__ . '/include/database.php';

require_once __DIR__ . '/admin/class/product_class.php';
require_once __DIR__ . '/admin/class/category_class.php';
require_once __DIR__ . '/admin/class/brand_class.php';

$productModel = new Product();

// Lấy từ khóa
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
 

if ($keyword === "") {
    header("Location: trangchu.php");
    exit;
}

// --- PHÂN TRANG ---
$limit = 8;
$page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

$totalRecords = $productModel->count_search_products($keyword);
$totalPages   = ceil($totalRecords / $limit);

if ($page > $totalPages && $totalPages > 0) $page = $totalPages;

// Lấy danh sách sản phẩm theo trang
$products = $productModel->search_products_paging($keyword, $limit, $offset);


// BREADCRUMB
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
    ['text' => 'Kết quả tìm kiếm'],
];

require __DIR__ . "/partials/header.php";
require __DIR__ . "/partials/breadcrumb.php";
// Lấy ID sản phẩm mới nhất (dùng để hiển thị badge NEW)
$maxProductId = $productModel->get_last_id()  
?>

<section class="category-page">
    <div class="container">

        <h1 class="category-title">
            Kết quả tìm kiếm cho: <strong><?= htmlspecialchars($keyword) ?></strong>
        </h1>

        <?php if ($products && $products->num_rows > 0): ?>
        <div class="product-grid">

            <?php while ($row = $products->fetch_assoc()): ?>

    <?php
        $price     = (float)$row['product_price'];
        $salePrice = (float)$row['product_sale'];
        $hasSale   = ($salePrice > 0 && $salePrice < $price);

        // sản phẩm mới = ID trong top 20 sản phẩm gần nhất
        $isNew = ($row['product_id'] >= $productModel->get_last_id() - 20);

        $img = htmlspecialchars($row['product_img']);
    ?>

    <div class="product-item">

        <div class="product-media">

            <!-- HOT SALE góc trái -->
            <?php if ($hasSale): ?>
                <span class="product-badge hot">HOT</span>
            <?php endif; ?>

            <!-- NEW góc phải -->
            <?php if ($isNew): ?>
                <span class="product-badge new">NEW</span>
            <?php endif; ?>

            <a href="product_detail.php?id=<?= $row['product_id'] ?>" class="product-thumb">
                <img src="<?= $img ?>" alt="">
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
                <span class="price-current"><?= number_format($salePrice, 0, ',', '.') ?>đ</span>
                <span class="price-old"><?= number_format($price, 0, ',', '.') ?>đ</span>
                <span class="price-sale-badge">
                    -<?= round((($price - $salePrice)/$price) * 100) ?>%
                </span>
            <?php else: ?>
                <span class="price-current"><?= number_format($price, 0, ',', '.') ?>đ</span>
            <?php endif; ?>
        </div>

        <div class="product-color-list">
            <div class="product-color">
                <img src="<?= $img ?>" alt="">
            </div>
        </div>

    </div>

<?php endwhile; ?>


        </div>

        <!-- PHÂN TRANG -->
        <div class="pagination-area">
            <nav>
                <ul class="pagination">

                    <!-- Prev -->
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= ($page > 1) 
                                ? "search.php?keyword=".urlencode($keyword)."&page=".($page-1)
                                : "#" ?>">
                            &laquo;
                        </a>
                    </li>

                    <!-- Number -->
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                            <a class="page-link"
                               href="search.php?keyword=<?= urlencode($keyword) ?>&page=<?= $i ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Next -->
                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link"
                           href="<?= ($page < $totalPages)
                                ? "search.php?keyword=".urlencode($keyword)."&page=".($page+1)
                                : "#" ?>">
                            &raquo;
                        </a>
                    </li>

                </ul>
            </nav>
        </div>

        <?php else: ?>

        <p style="text-align:center; margin-top:40px;">
            Không tìm thấy sản phẩm nào.
        </p>

        <?php endif; ?>

    </div>
</section>

<?php require __DIR__ . "/partials/footer.php"; ?>
