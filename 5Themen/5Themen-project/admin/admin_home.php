<?php
include "../include/session.php";
include "../include/database.php";
// GỌI FILE HEADER VỪA SỬA
include "header.php"; 

$db = new Database();
$query = $db->select("
    SELECT 
        (SELECT COUNT(*) FROM tbl_category) AS cat_total,
        (SELECT COUNT(*) FROM tbl_brand) AS brand_total,
        (SELECT COUNT(*) FROM tbl_product) AS product_total
");
$data = $query->fetch_assoc();
?>

<style>
    /* Background Blobs trang trí riêng cho Dashboard */
    .blob {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        z-index: -1;
        opacity: 0.6;
        animation: float 10s infinite alternate;
    }
    .blob-1 { width: 400px; height: 400px; background: #ff9a9e; top: 50px; left: -50px; }
    .blob-2 { width: 350px; height: 350px; background: #a18cd1; bottom: 50px; right: -50px; }

    @keyframes float { 0% { transform: translate(0, 0); } 100% { transform: translate(20px, 40px); } }

    .dashboard-container {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto; /* Căn giữa container */
        padding: 40px 20px;
        text-align: center;
    }

    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #333;
        margin-bottom: 60px;
    }

    .dashboard-box {
        display: flex;
        justify-content: center;
        align-items: flex-start;
        gap: 50px;
        flex-wrap: wrap;
    }

    /* ... (Giữ nguyên phần CSS Card Item và Animation nút bấm ở bài trước) ... */
    .box-item {
        width: 300px;
        padding: 40px 30px;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.15);
        text-align: center;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        overflow: hidden;
    }
    .box-item:hover { transform: translateY(-10px); background: rgba(255, 255, 255, 0.5); }
    
    /* Icon Style */
    .icon-wrapper { width: 80px; height: 80px; margin: 0 auto 20px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 32px; color: white; }
    .box-category .icon-wrapper { background: linear-gradient(135deg, #4b7bec, #3867d6); }
    .box-brand .icon-wrapper    { background: linear-gradient(135deg, #fa8231, #fc5c65); }
    .box-product .icon-wrapper  { background: linear-gradient(135deg, #20bf6b, #0fb9b1); }

    /* Text Style */
    .box-item h2 { font-size: 3.2rem; margin: 0; font-weight: 700; color: #333; }
    .box-item p { font-size: 1rem; color: #666; margin-top: 5px; font-weight: 600; text-transform: uppercase; }

    /* Menu Expand */
    .action-container { max-height: 0; opacity: 0; overflow: hidden; transition: all 0.5s ease-in-out; }
    .box-item:hover .action-container { max-height: 200px; opacity: 1; margin-top: 25px; padding-top: 20px; border-top: 1px solid rgba(0,0,0,0.06); }
    
    .btn-action { display: flex; align-items: center; justify-content: center; gap: 10px; text-decoration: none; padding: 12px 20px; margin-bottom: 10px; border-radius: 50px; font-weight: 600; font-size: 13px; color: white; transition: 0.3s; }
    .box-category .btn-action { background: linear-gradient(90deg, #4b7bec, #3867d6); }
    .box-brand .btn-action    { background: linear-gradient(90deg, #fa8231, #fc5c65); }
    .box-product .btn-action  { background: linear-gradient(90deg, #20bf6b, #0fb9b1); }
    .btn-action:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.2); }
</style>

<div class="blob blob-1"></div>
<div class="blob blob-2"></div>

<div class="dashboard-container">
    <div class="dashboard-title">Tổng quan hệ thống</div>

    <div class="dashboard-box">
        <div class="box-item box-category">
            <div class="icon-wrapper"><i class="fa-solid fa-folder-tree"></i></div>
            <h2><?= $data['cat_total'] ?></h2>
            <p>Danh mục</p>
            <div class="action-container">
                <a href="categoryadd.php" class="btn-action"><i class="fa-solid fa-plus"></i> Thêm mới</a>
                <a href="categorylist.php" class="btn-action"><i class="fa-solid fa-list"></i> Danh sách</a>
            </div>
        </div>

        <div class="box-item box-brand">
            <div class="icon-wrapper"><i class="fa-solid fa-award"></i></div>
            <h2><?= $data['brand_total'] ?></h2>
            <p>Thương hiệu</p>
            <div class="action-container">
                <a href="brandadd.php" class="btn-action"><i class="fa-solid fa-plus"></i> Thêm mới</a>
                <a href="brandlist.php" class="btn-action"><i class="fa-solid fa-list"></i> Danh sách</a>
            </div>
        </div>

        <div class="box-item box-product">
            <div class="icon-wrapper"><i class="fa-solid fa-box-open"></i></div>
            <h2><?= $data['product_total'] ?></h2>
            <p>Sản phẩm</p>
            <div class="action-container">
                <a href="productadd.php" class="btn-action"><i class="fa-solid fa-plus"></i> Thêm mới</a>
                <a href="productlist.php" class="btn-action"><i class="fa-solid fa-list"></i> Danh sách</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>