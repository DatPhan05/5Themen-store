<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/class/category_content_class.php";
require_once __DIR__ . "/class/category_class.php";

$cg = new Category();
$ct = new CategoryContent();

$categories = $cg->show_category();

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $catId = $_POST['category_id'];
    $title = $_POST['title'];
    $content = $_POST['content'];

    if ($ct->insert($catId, $title, $content)) {
        $msg = "✔ Thêm nội dung thành công!";
    } else {
        $msg = "❌ Lỗi, vui lòng thử lại.";
    }
}
?>

<div class="admin-content-right">
    <h1>Thêm nội dung cho Danh mục</h1>

    <p style="color: green;"><?= $msg ?></p>

    <form action="" method="POST">
        <label>Chọn danh mục</label>
        <select name="category_id">
            <?php while ($r = $categories->fetch_assoc()): ?>
                <option value="<?= $r['category_id'] ?>">
                    <?= $r['category_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>Tiêu đề</label>
        <input type="text" name="title" required>

        <label>Nội dung</label>
        <textarea name="content" id="editor"></textarea>

        <button type="submit">Thêm nội dung</button>
    </form>
</div>

<!-- CKEditor -->
<script src="../ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace("editor");
</script>
