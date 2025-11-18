<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/brand_class.php";
require_once __DIR__ . "/Class/category_class.php";

$cg = new Category();
$brands = new Brand();

$msg = "";

$categories = $cg->show_category(); // tất cả, lọc category con khi hiển thị

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $cid  = (int)$_POST['category_id'];
    $name = trim($_POST['brand_name']);

    if ($cid && $name !== "") {
        $brands->insert_brand($cid, $name);
        $msg = "Đã thêm loại sản phẩm.";
    } else {
        $msg = "Vui lòng chọn danh mục con và nhập tên loại.";
    }
}
?>

<div class="admin-content-right">
    <div class="admin-content-right-category_add">
        
        <h1>Thêm Loại sản phẩm</h1>

        <?php if ($msg): ?>
            <p style="color:#333"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form action="" method="POST">

            <label>Chọn danh mục (Category Con)</label>
            <select name="category_id" required>
                <option value="">-- Chọn danh mục --</option>

                <?php if ($categories): ?>
                    <?php while ($c = $categories->fetch_assoc()): ?>
                        <?php if ($c['parent_id'] != NULL): ?> 
                            <option value="<?= $c['category_id'] ?>">
                                <?= $c['category_name'] ?>
                            </option>
                        <?php endif; ?>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <label>Tên loại</label>
            <input type="text" name="brand_name" placeholder="Nhập tên loại sản phẩm" required>

            <button type="submit">Thêm</button>
        </form>

    </div>
</div>

</section>
</body>
</html>
