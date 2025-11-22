<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/post_class.php";

$postModel = new Post();
$id = (int)($_GET['id'] ?? 0);
$post = $postModel->get_post($id);

if (!$post) {
    die("Bài viết không tồn tại");
}

$msg = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title'] ?? '');
    $slug     = trim($_POST['slug'] ?? '');
    $summary  = trim($_POST['summary'] ?? '');
    $content  = $_POST['content'] ?? '';
    $category = $_POST['category'] ?? 'Tin tức';
    $status   = isset($_POST['status']) ? 1 : 0;

    $thumbnail = $post['thumbnail'];
    if (!empty($_FILES['thumbnail']['name'])) {
        $uploadDir = "uploads/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $fileName   = time() . "_" . basename($_FILES['thumbnail']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $targetPath)) {
            $thumbnail = $fileName;
        }
    }

    if ($title !== "" && $content !== "") {
        $result = $postModel->update_post($id, $title, $slug, $thumbnail, $summary, $content, $category, $status);
        if ($result) {
            $msg = "✔ Cập nhật bài viết thành công!";
            $msg_type = "success";
            $post = $postModel->get_post($id);
        } else {
            $msg = "❌ Cập nhật thất bại!";
            $msg_type = "error";
        }
    } else {
        $msg = "⚠️ Vui lòng nhập đầy đủ tiêu đề và nội dung.";
        $msg_type = "warning";
    }
}
?>

<section class="admin-page">
    <div class="main-content-wrapper">
        <div class="admin-content-left">
            <?php include __DIR__ . '/slider.php'; ?>
        </div>

        <div class="admin-content-right">
            <h2>Sửa bài viết</h2>

            <?php if ($msg): ?>
                <div class="alert alert-<?= $msg_type; ?>">
                    <?= $msg; ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" enctype="multipart/form-data" class="admin-form">

                <div class="form-group">
                    <label class="form-label">Tiêu đề bài viết</label>
                    <input type="text" name="title" class="form-input" value="<?= htmlspecialchars($post['title']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" class="form-input" value="<?= htmlspecialchars($post['slug']); ?>">
                </div>

                <div class="form-group">
                    <label class="form-label">Chuyên mục</label>
                    <select name="category" class="form-input">
                        <option value="Tin tức"        <?= $post['category']=='Tin tức' ? 'selected' : '' ?>>Tin tức</option>
                        <option value="Tin thời trang" <?= $post['category']=='Tin thời trang' ? 'selected' : '' ?>>Tin thời trang</option>
                        <option value="Kinh nghiệm hay" <?= $post['category']=='Kinh nghiệm hay' ? 'selected' : '' ?>>Kinh nghiệm hay</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Ảnh thumbnail hiện tại</label><br>
                    <?php if ($post['thumbnail']): ?>
                        <img src="uploads/<?= $post['thumbnail']; ?>" alt="" style="max-width:150px; border-radius:8px; margin-bottom:10px;">
                    <?php else: ?>
                        <em>Chưa có ảnh</em>
                    <?php endif; ?>
                    <input type="file" name="thumbnail" class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Tóm tắt</label>
                    <textarea name="summary" rows="3" class="form-input"><?= htmlspecialchars($post['summary']); ?></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Nội dung</label>
                    <textarea name="content" id="editor" class="form-input"><?= htmlspecialchars($post['content']); ?></textarea>
                </div>

                <div class="form-group">
                    <label>
                        <input type="checkbox" name="status" <?= $post['status'] ? 'checked' : ''; ?>>
                        Hiển thị
                    </label>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                </button>

            </form>
        </div>
    </div>
</section>

<script src="ckeditor/ckeditor.js"></script>
<script>
    CKEDITOR.replace("editor");
</script>
</body>
</html>
