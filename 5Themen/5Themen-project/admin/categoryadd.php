<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/category_class.php";

$cg = new Category();
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $name = trim($_POST['category_name']);
    $parent_id = $_POST['parent_id'] !== "" ? (int)$_POST['parent_id'] : null;

    if ($name !== "") {
        $cg->insert_category($name, $parent_id);
        $msg = "Đã thêm danh mục thành công.";
    } else {
        $msg = "Vui lòng nhập tên danh mục.";
    }
}

$parents = $cg->get_parent_categories();
?>

<div class="admin-content-right">
    <div class="admin-content-right-category_add">

        <h1>Thêm Danh Mục</h1>

        <?php if (!empty($msg)) : ?>
            <p style="color:#333;"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form action="" method="POST">

            <label>Tên danh mục</label>
            <input type="text" name="category_name" placeholder="Nhập tên danh mục" required>

            <label>Danh mục cha</label>
            <select name="parent_id">
                <option value="">-- Không chọn (Danh mục cha) --</option>

                <?php if ($parents): ?>
                    <?php while ($p = $parents->fetch_assoc()): ?>
                        <option value="<?= $p['category_id'] ?>">
                            <?= $p['category_name'] ?>
                        </option>
                    <?php endwhile; ?>
                <?php endif; ?>
            </select>

            <button type="submit">Thêm</button>
        </form>

    </div>
</div>

</section>
</body>
</html>
