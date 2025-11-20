<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/class/category_content_class.php";

$ct = new CategoryContent();
$id = $_GET['id'];
$data = $ct->getById($id)->fetch_assoc();

$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    if ($ct->update($id, $title, $content)) {
        $msg = "✔ Cập nhật thành công!";
    } else {
        $msg = "❌ Lỗi, vui lòng thử lại.";
    }
}
?>

<div class="admin-content-right">
    <h1>Sửa nội dung danh mục</h1>
    <p style="color: green;"><?= $msg ?></p>

    <form method="POST">
        <label>Tiêu đề</label>
        <input type="text" name="title" value="<?= $data['title'] ?>">

        <label>Nội dung</label>
        <textarea name="content" id="editor"><?= $data['content'] ?></textarea>

        <button type="submit">Cập nhật</button>
    </form>
</div>

<script src="../ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace("editor");
</script>
