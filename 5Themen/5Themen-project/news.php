<?php
require_once __DIR__ . "/partials/header.php";
require_once __DIR__ . "/admin/Class/post_class.php";

$postModel = new Post();

// Lấy bài viết theo từng nhóm
$tinTuc        = $postModel->get_posts_by_category("Tin tức", 4);
$tinThoiTrang  = $postModel->get_posts_by_category("Tin thời trang", 4);
$kinhNghiem    = $postModel->get_posts_by_category("Kinh nghiệm hay", 4);

function printNewsBlock($title, $posts, $typeSlug)
{
    if (!$posts || $posts->num_rows == 0) return;

    $first = $posts->fetch_assoc();
    ?>

    <section class="news-section">
        <div class="news-title">
            <h2><?= $title ?></h2>
        </div>

        <div class="news-grid">
            
            <!-- Bài viết nổi bật -->
            <div class="news-feature">
                <a class="news-feature-img" href="post_detail.php?id=<?= $first['post_id'] ?>">
                    <img src="admin/uploads/<?= $first['thumbnail'] ?>" alt="">
                </a>

                <div class="news-feature-content">
                    <h3><a href="post_detail.php?id=<?= $first['post_id'] ?>"><?= $first['title'] ?></a></h3>
                    <p><?= $first['summary'] ?></p>
                </div>
            </div>

            <!-- Bài viết nhỏ -->
            <div class="news-list">
                <?php while ($row = $posts->fetch_assoc()): ?>
                    <div class="news-item">
                        <div class="news-item-image">
                            <a href="post_detail.php?id=<?= $row['post_id'] ?>">
                                <img src="admin/uploads/<?= $row['thumbnail'] ?>" alt="">
                            </a>
                        </div>
                        <div class="news-item-info">
                            <a class="news-item-title" href="post_detail.php?id=<?= $row['post_id'] ?>">
                                <?= $row['title'] ?>
                            </a>
                            <span class="news-date"><?= date("d/m/Y", strtotime($row['created_at'])) ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

        </div>

        <div class="news-more">
            <a href="news_all.php?type=<?= $typeSlug ?>">Xem tất cả ›</a>
        </div>
    </section>

    <?php
}
?>

<link rel="stylesheet" href="CSS/news.css">

<div class="container">
    <?php printNewsBlock("Tin tức", $tinTuc, "tin-tuc"); ?>
    <?php printNewsBlock("Tin thời trang", $tinThoiTrang, "tin-thoi-trang"); ?>
    <?php printNewsBlock("Kinh nghiệm hay", $kinhNghiem, "kinh-nghiem-hay"); ?>
</div>

<?php require_once __DIR__ . "/partials/footer.php"; ?>
