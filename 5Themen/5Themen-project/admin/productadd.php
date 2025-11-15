<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/product_class.php";
require_once __DIR__ . "/Class/category_class.php";
require_once __DIR__ . "/Class/brand_class.php";

$cg = new Category();
$bd = new Brand();
$cates  = $cg->show_category();
$brands = $bd->show_brand();

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['product_name'] ?? '');
    $cid   = (int)($_POST['category_id'] ?? 0);
    $bid   = (int)($_POST['brand_id'] ?? 0);
    $price = (int)($_POST['product_price'] ?? 0);
    $sale  = (int)($_POST['product_sale'] ?? 0);
    $desc  = trim($_POST['product_desc'] ?? '');
    $thumb = $_FILES['product_img']['name'] ?? '';

    if ($name && $cid && $bid) {
        $upload_dir = "/uploads/";
        if (!is_dir($upload_dir)) mkdir($upload_dir);
// Upload ảnh
        $target_file = "/uploads/" . basename($thumb);
        move_uploaded_file($_FILES['product_img']['tmp_name'], $target_file);

        (new Product())->insert_product($name, $cid, $bid, $price, $sale, $desc, $target_file);
        $msg = " Đã thêm sản phẩm thành công!";
    } else {
        $msg = " Vui lòng nhập đủ thông tin bắt buộc.";
    }
}
?>

<div class="admin-content-right">
    <div class="admin-content-right-product_add">
        <h1>Thêm sản phẩm</h1>

        <?php if ($msg): ?>
            <p style="color:red; font-weight:bold;"><?= $msg ?></p>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <label>Tên sản phẩm:</label><br>
            <input type="text" name="product_name" placeholder="Nhập tên sản phẩm"><br>

            <label>Danh mục:</label><br>
            <select name="category_id" required>
                <option value="">-- Chọn danh mục --</option>
                <?php if ($cates): ?>
                    <?php while ($c = $cates->fetch_assoc()): ?>
                        <option value="<?= $c['category_id'] ?>">
                            <?= htmlspecialchars($c['category_name']) ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select><br>

            <label>Loại sản phẩm:</label><br>
            <select name="brand_id" required>
                <option value="">-- Chọn loại sản phẩm --</option>
                <?php if ($brands): ?>
                    <?php while ($b = $brands->fetch_assoc()): ?>
                        <option value="<?= $b['brand_id'] ?>">
                            <?= htmlspecialchars($b['brand_name']) ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select><br>

            <label>Giá:</label><br>
            <input type="text" name="product_price" placeholder=""><br>

            <label>Khuyến mãi:</label><br>
            <input type="text" name="product_sale" placeholder=""><br>

            <label>Mô tả:</label><br>
            <textarea name="product_desc" rows="5" placeholder="Mô tả sản phẩm..."></textarea><br>

            <label>Ảnh:</label><br>
            <input type="file" name="product_img" accept="image/*"><br><br>

            <button type="submit">Thêm</button>
        </form>
    </div>
</div>

</section>
</body>
</html>
