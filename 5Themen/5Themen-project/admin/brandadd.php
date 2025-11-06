<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/brand_class.php";
require_once __DIR__ . "/Class/category_class.php";

$msg = "";
$cg = new category();
$cates = $cg->show_category();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cid  = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['brand_name'] ?? '');

    if ($cid && $name !== "") {
        $b = new Brand();
        $b->insert_brand($cid, $name);
        $msg = "Đã thêm loại sản phẩm.";
    } else {
        $msg = "Vui lòng chọn danh mục và nhập tên loại sản phẩm.";
    }
}
?>

<div class="admin-content-right">
    <div class="admin-content-right-category_add">
        <h1>Thêm Loại sản phẩm</h1>

        <?php if ($msg) : ?>
            <p style="color: #333; margin: 10px 0;"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <label for="category_id">Chọn danh mục</label>
            <select name="category_id" id="category_id">
                <option value="">-- Chọn danh mục --</option>
                <?php if ($cates) : ?>
                    <?php while ($c = $cates->fetch_assoc()) : ?>
                        <option value="<?= $c['category_id'] ?>">
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
                placeholder="Nhập tên loại sản phẩm"
                required
            >

            <button type="submit">Thêm</button>
        </form>
    </div>
</div>

</section>
</body>
</html>
