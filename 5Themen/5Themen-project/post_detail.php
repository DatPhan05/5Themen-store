<?php
require_once __DIR__ . "/admin/class/post_class.php";

$postModel = new Post();

// Lấy ID bài viết
$postId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($postId <= 0) {
    echo "<div style='padding:30px;font-size:22px;'>Bài viết không tồn tại.</div>";
    exit;
}

// Lấy bài
$post = $postModel->get_post($postId);
if (!$post) {
    echo "<div style='padding:30px;font-size:22px;'>Không tìm thấy bài viết.</div>";
    exit;
}

// Lấy bài viết liên quan
$related = $postModel->get_all_posts_by_category($post['category']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?= htmlspecialchars($post['title']) ?></title>

<!-- =============== CSS FULL TÍCH HỢP =============== -->
<style>
body {
    margin:0;
    font-family:Arial, sans-serif;
    background:#fff;
}

.article-container {
    max-width:1180px;
    margin:40px auto;
}

.article-breadcrumb {
    font-size:14px;
    margin-bottom:20px;
    color:#777;
}

.article-breadcrumb a {
    color:#000;
    text-decoration:none;
}

.article-layout {
    display:flex;
    gap:40px;
}

/* Content */
.article-main {
    width:70%;
}

.article-title {
    font-size:36px;
    font-weight:700;
    margin-bottom:10px;
}

.article-meta {
    color:#888;
    margin-bottom:20px;
}

.article-thumb img {
    width:100%;
    border-radius:6px;
    margin-bottom:20px;
}

.article-content {
    font-size:18px;
    line-height:1.7;
}

.article-content img {
    max-width:100%;
    border-radius:6px;
    margin:15px 0;
}

/* Share */
.article-share {
    margin-top:25px;
    font-size:20px;
}

.article-share i {
    margin-right:12px;
    cursor:pointer;
}

/* Sidebar */
.article-sidebar {
    width:30%;
}

.article-sidebar h3 {
    font-size:20px;
    margin-bottom:15px;
}

.sidebar-item {
    display:flex;
    margin-bottom:15px;
    gap:10px;
}

.sidebar-thumb img {
    width:90px;
    height:70px;
    object-fit:cover;
    border-radius:6px;
}

.sidebar-title {
    font-size:15px;
    color:#000;
    text-decoration:none;
    line-height:1.3;
}

.sidebar-date {
    font-size:11px;
    color:#777;
}

/* Mobile */
@media(max-width:900px){
    .article-layout{flex-direction:column;}
    .article-main, .article-sidebar {width:100%;}
}
</style>

<!-- FontAwesome -->
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

</head>

<body>

<div class="article-container">

    <!-- Breadcrumb -->
    <div class="article-breadcrumb">
        <a href="trangchu.php">Trang chủ</a> › 
        <a href="news_all.php?type=<?= slugify($post['category']) ?>">
            <?= $post['category'] ?>
        </a> › 
        <span><?= $post['title'] ?></span>
    </div>

    <div class="article-layout">

        <!-- ================== CONTENT ================== -->
        <article class="article-main">

            <h1 class="article-title"><?= $post['title'] ?></h1>

            <div class="article-meta">
                <i class="fa-regular fa-clock"></i>
                <?= date("d/m/Y", strtotime($post['created_at'])) ?>
            </div>

            <div class="article-thumb">
                <img src="admin/uploads/<?= $post['thumbnail'] ?>" alt="">
            </div>

            <div class="article-content">
                <?= $post['content'] ?>
            </div>

            <!-- SHARE -->
            <div class="article-share">
                <span>Chia sẻ:</span>

                <a target="_blank"
                   href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>">
                    <i class="fa-brands fa-facebook"></i>
                </a>

                <a target="_blank"
                   href="https://www.messenger.com/share/?link=<?= urlencode("http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']) ?>">
                    <i class="fa-brands fa-facebook-messenger"></i>
                </a>

                <i class="fa-solid fa-copy" onclick="copyLink()"></i>
            </div>

        </article>

        <!-- ================== SIDEBAR ================== -->
        <aside class="article-sidebar">

            <h3>Bài viết liên quan</h3>

            <?php 
            if ($related && $related->num_rows > 1):
                while ($row = $related->fetch_assoc()):
                    if ($row['post_id'] == $postId) continue;
            ?>
            <div class="sidebar-item">
                <a href="post_detail.php?id=<?= $row['post_id'] ?>" class="sidebar-thumb">
                    <img src="admin/uploads/<?= $row['thumbnail'] ?>" alt="">
                </a>

                <div>
                    <a href="post_detail.php?id=<?= $row['post_id'] ?>" class="sidebar-title">
                        <?= $row['title'] ?>
                    </a>
                    <div class="sidebar-date">
                        <?= date("d/m/Y", strtotime($row['created_at'])) ?>
                    </div>
                </div>
            </div>
            <?php
                endwhile;
            else:
                echo "<p>Không có bài viết liên quan.</p>";
            endif;
            ?>

        </aside>

    </div>
</div>

<!-- === JS COPY LINK === -->
<script>
function copyLink() {
    navigator.clipboard.writeText(window.location.href);
    alert("Đã sao chép liên kết bài viết!");
}
</script>

</body>
</html>
