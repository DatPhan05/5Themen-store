<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/product_class.php";
require_once __DIR__ . "/Class/category_class.php";
require_once __DIR__ . "/Class/brand_class.php";

$id  = (int)($_GET['id'] ?? 0);
$pd  = new Product();
$row = $pd->get_product($id);

if (!$row) { die(" Không tìm thấy sản phẩm."); }

$cg = new Category();
$bd = new Brand();
$cates  = $cg->show_category();
$brands = $bd->show_brand();

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $cid   = (int)($_POST['category_id'] ?? 0);
    $bid   = (int)($_POST['brand_id'] ?? 0);
    $price = (int)($_POST['price'] ?? 0);
    $sale  = (int)($_POST['sale_price'] ?? 0);
    $desc  = trim($_POST['description'] ?? '');
    $thumb = trim($_POST['thumb'] ?? '');

    if ($name && $cid && $bid) {
        $thumbVal = $thumb !== "" ? $thumb : null;
        $pd->update_product($id, $name, $cid, $bid, $price, $sale, $desc, $thumbVal);
        $msg = " Đã lưu thay đổi sản phẩm.";
        $row = $pd->get_product($id);
    } else {
        $msg = " Vui lòng nhập đủ thông tin bắt buộc.";
    }
}
?>

<div class="admin-content-right">
    <div class="admin-content-right-product_add">
        <h1>Sửa sản phẩm</h1>
        <?php if ($msg) : ?>
            <p style="color:#333; margin:10px 0;"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label>Tên sản phẩm *</label>
            <input type="text" name="name" value="<?= htmlspecialchars($row['ten_san_pham']) ?>" required>

            <label>Danh mục *</label>
            <select name="category_id" required>
                <option value="">-- Chọn danh mục --</option>
                <?php if ($cates) : while ($c = $cates->fetch_assoc()) : ?>
                    <option value="<?= $c['category_id'] ?>" <?= $row['id_danh_muc'] == $c['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                <?php endwhile; endif; ?>
            </select>

            <label>Loại sản phẩm *</label>
            <select name="brand_id" required>
                <option value="">-- Chọn loại sản phẩm --</option>
                <?php if ($brands) : while ($b = $brands->fetch_assoc()) : ?>
                    <option value="<?= $b['brand_id'] ?>" <?= $row['id_loai'] == $b['brand_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($b['brand_name']) ?>
                    </option>
                <?php endwhile; endif; ?>
            </select>

            <label>Giá sản phẩm</label>
            <input type="number" name="price" value="<?= (int)$row['gia'] ?>">

            <label>Giá khuyến mãi</label>
            <input type="number" name="sale_price" value="<?= (int)$row['gia_khuyen_mai'] ?>">

            <label>Mô tả</label>
            <textarea name="description" rows="5"><?= htmlspecialchars($row['mo_ta'] ?? '') ?></textarea>

            <label>Ảnh (đường dẫn)</label>
            <input type="text" name="thumb" value="<?= htmlspecialchars($row['anh'] ?? '') ?>">

            <button type="submit">Lưu thay đổi</button>
        </form>
    </div>
</div>

</section>
</body>
</html>
