<?php
include "../include/session.php";
include "../include/database.php";
include "header.php"; 


$db = new Database();

// Thống kê danh mục
$cat_count = $db->select("SELECT COUNT(*) AS total FROM tbl_category")->fetch_assoc()['total'];

// Thống kê thương hiệu
$brand_count = $db->select("SELECT COUNT(*) AS total FROM tbl_brand")->fetch_assoc()['total'];

// Thống kê sản phẩm
$product_count = $db->select("SELECT COUNT(*) AS total FROM tbl_product")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Trang quản trị Admin</title>
    <style>
        .dashboard-container {
            padding: 20px;
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 25px;
        }

        .dashboard-box {
            display: flex;
            gap: 20px;
            margin-bottom: 40px;
        }

        .box-item {
            width: 30%;
            padding: 20px;
            border-radius: 10px;
            color: white;
            text-align: center;
        }

        .box-category { background: #2284d1; }
        .box-brand { background: #dc972f; }
        .box-product { background: #1cb35c; }

        .box-item h2 {
            font-size: 40px;
        }

        .quick-links {
            margin-top: 30px;
        }

        .quick-links h3 {
            font-size: 20px;
            margin-bottom: 15px;
        }

        .quick-links ul li {
            margin-bottom: 10px;
        }

        .quick-links a {
            text-decoration: none;
            font-weight: bold;
            color: #8b0000;
        }

        .quick-links a:hover {
            color: red;
        }
    </style>
</head>

<body>

<div class="dashboard-container">

    <div class="dashboard-title">🛠 Trang điều khiển quản trị website</div>

    <!-- Ô THỐNG KÊ -->
    <div class="dashboard-box">
        <div class="box-item box-category">
            <h2><?php echo $cat_count; ?></h2>
            <p>Số lượng Danh mục</p>
        </div>

        <div class="box-item box-brand">
            <h2><?php echo $brand_count; ?></h2>
            <p>Số lượng Thương hiệu</p>
        </div>

        <div class="box-item box-product">
            <h2><?php echo $product_count; ?></h2>
            <p>Số lượng Sản phẩm</p>
        </div>
    </div>

    <!-- LIÊN KẾT NHANH -->
    <div class="quick-links">
        <h3> Chức năng nhanh</h3>
        <ul>
            <li><a href="#">Danh mục</a>
               <ul>
               <li><a href="categoryadd.php">➕ Thêm danh mục mới</a></li>
               <li><a href="categorylist.php">📂 Danh sách danh mục</a></li>
               </ul>
            </li>
            <li><a href="#">Loại sản phẩm</a>
               <ul>
               <li><a href="brandadd.php">➕ Thêm thương hiệu mới</a></li>
               <li><a href="brandlist.php">🏷 Danh sách thương hiệu</a></li>
               </ul>
            </li>

             <li><a href="#">Sản phẩm</a>
               <ul>
               <li><a href="productadd.php">➕ Thêm sản phẩm mới</a></li>
               <li><a href="productlist.php">🛒 Danh sách sản phẩm</a></li>
               </ul>
               </ul>
            </li>
    

            

            
    </div>

</div>

</body>
</html>
