<?php
require_once __DIR__ . "/admin/class/post_class.php";

$postModel = new Post();

// Lấy loại bài từ URL
$typeSlug = $_GET['type'] ?? "";
$typeMap = [
    "tin-tuc"        => "Tin tức",
    "tin-thoi-trang" => "Tin thời trang",
    "kinh-nghiem-hay" => "Kinh nghiệm hay"
];

$category = $typeMap[$typeSlug] ?? "Tin tức";

// Lấy toàn bộ bài viết theo category
$posts = $postModel->get_all_posts_by_category($category);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<title><?= $category ?> - 5TheMen</title>

<!-- ========== CSS FULL TÍCH HỢP ========== -->
<style>
body {
    margin:0;
    font-family:Arial, sans-serif;
    background:#fff;
}

.news-container {
    max-width:1180px;
    margin:40px auto;
}

.news-title-main {
    font-size:32px;
    font-weight:700;
    margin-bottom:25px;
}

.news-list {
    display:flex;
    flex-direction:column;
    gap:25px;
}

.news-item {
    display:flex;
    gap:20px;
    padding-bottom:20px;
    border-bottom:1px solid #eee;
}

.news-thumb img {
    width:260px;
    height:170px;
    object-fit:cover;
    border-radius:6px;
}

.news-info {
    flex:1;
}

.news-info h2 {
    font-size:20px;
    margin:0 0 10px;
}

.news-info h2 a {
    color:#000;
    text-decoration:none;
}

.news-summary {
    font-size:15px;
    line-height:1.6;
    color:#333;
}

.news-date {
    margin-top:10px;
    font-size:13px;
    color:#777;
}

/* Pagination */
.pagination {
    margin-top:30px;
    text-align:center;
}

.pagination a {
    display:inline-block;
    padding:8px 14px;
    margin:0 4px;
    border:1px solid #ddd;
    color:#000;
    text-decoration:none;
    border-radius:4px;
}

.pagination a.active {
    background:#000;
    color:#fff;
    border-color:#000;
}

.pagination a:hover {
    background:#f0f0f0;
}

/* Mobile */
@media(max-width:768px){
    .news-item {
        flex-direction:column;
    }
    
    .news-thumb img {
        width:100%;
        height:200px;
    }
}
</style>

<link rel="stylesheet"
 href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>

<div class="news-container">

    <h1 class="news-title-main"><?= $category ?></h1>

    <div class="news-list">

        <?php 
        if ($posts && $posts->num_rows > 0):
            while ($row = $posts->fetch_assoc()):
        ?>

        <div class="news-item">
            <a href="post_detail.php?id=<?= $row['post_id'] ?>" class="news-thumb">
                <img src="admin/uploads/<?= $row['thumbnail'] ?>" alt="">
            </a>

            <div class="news-info">
                <h2>
                    <a href="post_detail.php?id=<?= $row['post_id'] ?>">
                        <?= $row['title'] ?>
                    </a>
                </h2>

                <p class="news-summary">
                    <?= $row['summary'] ?>
                </p>

                <div class="news-date">
                    <i class="fa-regular fa-clock"></i>
                    <?= date("d/m/Y", strtotime($row['created_at'])) ?>
                </div>
            </div>
        </div>

        <?php
            endwhile;
        else:
            echo "<p>Không có bài viết nào.</p>";
        endif;
        ?>

    </div>

</div>

</body>
</html>
