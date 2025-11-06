<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/brand_class.php";
require_once __DIR__ . "/Class/category_class.php";

$id = (int)($_GET['brand_id'] ?? 0);

$brand = new Brand();
$row   = $brand->get_brand($id);
if (!$row) {
    die("❌ Không tìm thấy loại sản phẩm.");
}

$cg    = new category();
$cates = $cg->show_category();

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid  = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['brand_name'] ?? '');

    if ($cid && $name !== "") {
        $brand->update_brand($id, $cid, $name);
        $msg = " Đã lưu thay đổi loại sản phẩm.";
        $row = $brand->get_brand($id);
    } else {
        $msg = " Vui lòng chọn danh mục và nhập tên loại.";
    }
}
?>

<div class="admin-content-right">
    <div class="admin-content-right-category_add">
        <h1>Sửa Loại sản phẩm</h1>

        <?php if ($msg) : ?>
            <p style="color:#333; margin:10px 0;"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="category_id">Chọn danh mục</label>
            <select name="category_id" id="category_id">
                <option value="">-- Chọn danh mục --</option>
                <?php if ($cates) : ?>
                    <?php while ($c = $cates->fetch_assoc()) : ?>
                        <option 
                            value="<?= $c['category_id'] ?>" 
                            <?= ($row['category_id'] == $c['category_id']) ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($c['category_name']) ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <label for="brand_name">Tên loại</label>
            <input 
                type="text" 
                name="brand_name" 
                id="brand_name" 
                value="<?= htmlspecialchars($row['brand_name']) ?>" 
                required
            >

            <button type="submit">Lưu</button>
        </form>
    </div>
</div>

</section>
</body>
</html>
