<?php
require_once __DIR__.'/include/session.php';
require_once __DIR__.'/include/database.php';

$pageTitle = "Thông tin cửa hàng";

// Breadcrumb
$breadcrumbs = [
    ['text' => 'Trang chủ', 'url' => 'trangchu.php'],
    ['text' => $pageTitle]
];

require __DIR__ . "/partials/header.php";
require __DIR__ . "/partials/breadcrumb.php";
?>

<style>
/* ============================
   ABOUT PAGE – 5THEMEN
============================ */
/* Fix bị đè bởi header fixed */

.about-section {
    padding: 60px 0;
    background: #f8f8f8;
    font-family: 'Poppins', sans-serif;
}

.about-container {
    width: 125%
    max-width: 1200px;
    margin: auto;
}

.about-hero {
    text-align: center;
    margin-bottom: 40px;
}

.about-hero h1 {
    font-size: 38px;
    font-weight: 700;
    color: #111;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.about-hero h1 span {
    color: #6a5af9; /* tím hiện đại */
}

.about-hero p {
    font-size: 18px;
    color: #444;
}

.about-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
}

.about-block {
    background: #fff;
    padding: 30px;
    border-radius: 14px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.05);
    transition: 0.3s;
}

.about-block:hover {
    transform: translateY(-6px);
    box-shadow: 0 12px 32px rgba(0,0,0,0.08);
}

.about-block h2 {
    font-size: 24px;
    margin-bottom: 12px;
    color: #333;
    display: flex;
    align-items: center;
    gap: 10px;
}

.about-block h2 i {
    color: #6a5af9;
}

.about-block p {
    font-size: 16px;
    line-height: 1.6;
    color: #555;
}

/* Responsive */
@media (max-width: 768px) {
    .about-hero h1 { font-size: 30px; }
}
</style>

<section class="about-section">
    <div class="about-container">

        <div class="about-hero">
            <h1>Giới thiệu về <span>5Themen</span></h1>
            <p>Thương hiệu thời trang nam hiện đại – tối giản – dẫn đầu xu hướng.</p>
        </div>

        <div class="about-content">

            <div class="about-block">
                <h2><i class="fa-solid fa-bullseye"></i> Sứ mệnh</h2>
                <p>
                    Mang đến những sản phẩm chất lượng, giá thành hợp lý cùng dịch vụ tận tâm.
                </p>
            </div>

            <div class="about-block">
                <h2><i class="fa-solid fa-eye"></i> Tầm nhìn</h2>
                <p>
                    Trở thành thương hiệu thời trang nam Việt Nam được yêu thích nhất.
                </p>
            </div>

            <div class="about-block">
                <h2><i class="fa-solid fa-phone"></i> Liên hệ</h2>
                <p>📞 0876 419 291</p>
                <p>📧 contact@5themen.com</p>
            </div>

        </div>

    </div>
</section>

<?php require __DIR__ . "/partials/footer.php"; ?>
