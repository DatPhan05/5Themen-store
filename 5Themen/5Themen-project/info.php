<?php
require_once __DIR__.'/include/session.php';
require_once __DIR__.'/include/database.php';

$pageTitle = "Thông tin cửa hàng";


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
    color: #6a5af9;
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

        <!-- HERO -->
        <div class="about-hero">
            <h1>Giới thiệu về <span>5THEMEN</span></h1>
            <p>Thời trang nam hiện đại – tối giản – thoải mái – dẫn đầu xu hướng Việt Nam.</p>
        </div>

        <!-- 3 KHỐI -->
        <div class="about-content">

            <div class="about-block">
                <h2><i class="fa-solid fa-bullseye"></i> Sứ mệnh</h2>
                <p>
                    Mang đến sản phẩm chất lượng cao – giá hợp lý – trải nghiệm mua sắm tin cậy,
                    giúp nam giới tự tin thể hiện phong cách cá nhân.
                </p>
            </div>

            <div class="about-block">
                <h2><i class="fa-solid fa-eye"></i> Tầm nhìn</h2>
                <p>
                    Trở thành thương hiệu thời trang nam được yêu thích nhất Việt Nam,
                    tiên phong trong phong cách tối giản và bền vững.
                </p>
            </div>

            <div class="about-block">
                <h2><i class="fa-solid fa-gem"></i> Giá trị cốt lõi</h2>
                <p>
                    Chất lượng – Tận tâm – Minh bạch – Không ngừng đổi mới – Tôn trọng khách hàng.
                </p>
            </div>

        </div>

        <!-- CÂU CHUYỆN -->
        <div class="about-block" style="margin-top:40px;">
            <h2><i class="fa-solid fa-book-open"></i> Câu chuyện thương hiệu</h2>
            <p>
                5Themen được thành lập với mong muốn tạo ra những sản phẩm thời trang
                phù hợp với phong cách sống của đàn ông Việt: đơn giản, mạnh mẽ và tinh tế.  
                Từng đường may – chất liệu – form dáng đều được nghiên cứu để mang lại sự thoải mái
                khi mặc và giúp khách hàng tự tin trong mọi khoảnh khắc.
            </p>
        </div>

        <!-- TẠI SAO CHỌN -->
        <div class="about-block" style="margin-top:25px;">
            <h2><i class="fa-solid fa-check-circle"></i> Tại sao chọn 5THEMEN?</h2>
            <ul style="margin:0; padding-left:18px; line-height:1.8; color:#555; font-size:16px;">
                <li>Chất liệu cao cấp – co giãn – thấm hút – thoải mái cả ngày.</li>
                <li>Thiết kế tối giản, dễ phối – phù hợp đi làm, đi học, đi chơi.</li>
                <li>Giá hợp lý – chính sách đổi trả rõ ràng.</li>
                <li>Luôn cập nhật xu hướng mới.</li>
                <li>Đội ngũ chăm sóc khách hàng tận tâm.</li>
            </ul>
        </div>

        <!-- CAM KẾT -->
        <div class="about-block" style="margin-top:25px;">
            <h2><i class="fa-solid fa-shield-halved"></i> Cam kết chất lượng</h2>
            <p>
                ✔ 100% sản phẩm kiểm định kỹ trước khi giao  
                ✔ Nói không với hàng kém chất lượng  
                ✔ Minh bạch về giá – chất liệu  
                ✔ Bảo hành đường may  
            </p>
        </div>

        <!-- QUY TRÌNH -->
        <div class="about-block" style="margin-top:25px;">
            <h2><i class="fa-solid fa-industry"></i> Quy trình sản xuất</h2>
            <p>
                Sản phẩm 5Themen được sản xuất theo quy trình tiêu chuẩn:
            </p>
            <ul style="margin:0; padding-left:18px; line-height:1.8; color:#555; font-size:16px;">
                <li>Chọn lọc chất liệu từ nhà cung cấp uy tín.</li>
                <li>Kiểm tra form dáng – tỉ lệ cơ thể nam Việt.</li>
                <li>Gia công tỉ mỉ – từng đường may chắc chắn.</li>
                <li>Kiểm tra chất lượng 3 bước trước khi giao hàng.</li>
            </ul>
        </div>

        <!-- HỆ THỐNG CỬA HÀNG -->
        <div class="about-block" style="margin-top:25px;">
            <h2><i class="fa-solid fa-store"></i> Hệ thống cửa hàng</h2>
            <p>Hiện tại 5Themen có mặt tại:</p>
            <ul style="margin:0; padding-left:18px; line-height:1.8; color:#555; font-size:16px;">
                <li>TP. Hồ Chí Minh – 70 Tô Ký, Q.12, TP.HCM</li>
                <li>Tokyo - Seoul - Hồng Kông</li>
                <li>Đang mở rộng thêm nhiều chi nhánh mới</li>
            </ul>
        </div>

        <!-- LIÊN HỆ -->
        <div class="about-block" style="margin-top:25px;">
            <h2><i class="fa-solid fa-phone"></i> Liên hệ</h2>
            <p>📞 Hotline: 0876 419 291</p>
            <p>📧 Email: contact@5themen.com</p>
            <p>🌐 Website: www.5themen.com</p>
        </div>

    </div>
</section>


<?php require __DIR__ . "/partials/footer.php"; ?>
