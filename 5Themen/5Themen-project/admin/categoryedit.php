<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/category_class.php";

$cg  = new category();
$id  = (int)($_GET['category_id'] ?? 0);
$row = $id ? $cg->get_category($id) : null;

if (!$row) {
    die(" Không tìm thấy danh mục.");
}

$msg = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['category_name'] ?? '');

    if ($name !== "") {
        $cg->update_category($id, $name);
        $msg = " Đã lưu thay đổi danh mục.";
        $row = $cg->get_category($id);
    } else {
        $msg = " Vui lòng nhập tên danh mục.";
    }
}
?>

<div class="admin-content-right">
    <div class="admin-content-right-category_add">
        <h1>Sửa Danh mục</h1>

        <?php if ($msg) : ?>
            <p style="color:#333; margin:10px 0;"><?= htmlspecialchars($msg) ?></p>
        <?php endif; ?>

        <form action="" method="POST">
            <input 
                type="text" 
                name="category_name" 
                value="<?= htmlspecialchars($row['category_name']) ?>" 
                required
            >
            <button type="submit">Lưu</button>
        </form>
    </div>
</div>

</section>
</body>
</html>
