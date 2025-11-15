<?php 
include "header.php";

require_once __DIR__ . "/admin/class/product_class.php";
require_once __DIR__ . "/admin/class/category_class.php";

$productModel  = new Product();
$categoryModel = new Category();

/* =============================
    1. Mapping type → category_id
============================= */
$categoryMap = [
    'nam'        => 7,
    'nu'         => 8,
    'tre-em'     => 9,
    'sale'       => 10,
    'bo-suu-tap' => 11
];

/* =============================
    2. Mapping type → tiêu đề
============================= */
$titleMap = [
    'nam'        => 'Thời trang Nam',
    'nu'         => 'Thời trang Nữ',
    'tre-em'     => 'Thời trang Trẻ Em',
    'sale'       => 'Khuyến mãi',
    'bo-suu-tap' => 'Bộ sưu tập'
];

$type = $_GET['type'] ?? 'all';

$categoryId = $categoryMap[$type] ?? null;
$title      = $titleMap[$type] ?? "Tất cả sản phẩm";

/* =============================
    3. Lấy sản phẩm từ Database
============================= */
if ($categoryId) {
    $productList = $productModel->get_product_by_category($categoryId);
} else {
    $productList = $productModel->get_all_products();
}
?>

<section class="category-page">
    <div class="container">

        <!-- BREADCRUMB -->
        <div class="breadcrumb">
            <a href="trangchu.php">Trang chủ</a> /
            <?php echo $title; ?>
        </div>

        <div class="category-products" style="width:100%;">


            

            <!-- PRODUCT LIST -->
            <div class="category-products">

                <h2 class="category-title"><?php echo $title; ?></h2>

                <div class="product-grid">

                    <?php 
                    if ($productList && $productList->num_rows > 0):
                        while ($row = $productList->fetch_assoc()):
                    ?>
                        <div class="product-item">

                            <!-- Ảnh sản phẩm -->
                            <img src="<?php echo $row['product_img']; ?>" alt="" />

                            <!-- Tên -->
                            <h3 class="product-name">
                                <?php echo htmlspecialchars($row['product_name']); ?>
                            </h3>

                            <!-- Giá -->
                            <p class="price">
                                <?php echo number_format($row['product_price'], 0, ',', '.'); ?>đ
                            </p>

                            <!-- Nút thêm giỏ -->
                            <a href="cart.php?action=add&id=<?php echo $row['product_id']; ?>" class="btn-buy">
                                Thêm vào giỏ
                            </a>

                        </div>
                    <?php 
                        endwhile;
                    else:
                        echo "<p>Chưa có sản phẩm nào</p>";
                    endif;
                    ?>

                </div>

            </div>
        </div>
    </div>
</section>

<?php include "footer.php"; ?>
