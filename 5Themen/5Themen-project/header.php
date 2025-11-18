<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/admin/class/category_class.php';
require_once __DIR__ . '/admin/class/brand_class.php';

$categoryModel = new Category();
$brandModel    = new Brand();

// Lấy tất cả ROOT categories (parent_id = NULL)
$rootCategories = $categoryModel->get_parent_categories();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>5Themen - Fashion Store</title>
    <link rel="stylesheet" href="CSS/style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

<!-- ============================================================
     HEADER ICONDENIM STYLE – FIXED + FULL WIDTH
     ============================================================= -->
<div class="header-main">
    <div class="header-wrap">
        
        <!-- Logo -->
        <div class="header-logo">
            <a href="trangchu.php">
                <img src="images/logo.png" alt="5Themen Logo">
            </a>
        </div>

        <!-- Menu Navigation -->
        <nav class="header-menu">
            <ul id="main-menu">
                <?php 
                if ($rootCategories && is_object($rootCategories) && $rootCategories->num_rows > 0):
                    while ($root = $rootCategories->fetch_assoc()):
                        $rootId   = $root['category_id'];
                        $rootName = $root['category_name'];
                        
                        // Lấy child categories (Áo Nam, Quần Nam, Giày...)
                        $childCategories = $categoryModel->get_children($rootId);

                        // =========================
                        // CHỈNH Ở ĐÂY:
                        // Nếu root là "Sản phẩm" -> href = category.php (tất cả sản phẩm)
                        // Các root khác -> href = category.php?cat=<id>
                        // =========================
                        if (function_exists('mb_strtolower')) {
                            $rootNameLower = mb_strtolower($rootName, 'UTF-8');
                        } else {
                            $rootNameLower = strtolower($rootName);
                        }

                        if ($rootNameLower === 'sản phẩm') {
                            // Click vào "Sản phẩm" -> trang TẤT CẢ SẢN PHẨM
                            $rootHref = 'category.php';
                        } else {
                            // Các root khác vẫn filter theo danh mục
                            $rootHref = 'category.php?cat=' . $rootId;
                        }
                ?>
                    <li class="menu-item">
                        <!-- ROOT CATEGORY LINK (Sản phẩm, Sản phẩm mới, Bộ sưu tập, Thông tin) -->
                        <a href="<?= htmlspecialchars($rootHref) ?>">
                            <?= htmlspecialchars($rootName) ?>
                        </a>
                        
                        <!-- MEGA MENU - Chỉ hiện nếu có child categories -->
                        <?php if ($childCategories && is_object($childCategories) && $childCategories->num_rows > 0): ?>
                        <div class="mega-menu">
                            <div class="mega-content">
                                <div class="mega-column">
                                    <a href="category.php">Tất cả sản phẩm</a>
                                    <a href="category.php?filter=new">Sản phẩm mới</a>
                                    <a href="category.php?cat=13">Bộ sưu tập</a>
                                    <a href="category.php?cat=14">Thông tin</a>
                                </div>
                                <?php 
                                $colCount = 0;
                                while ($child = $childCategories->fetch_assoc()): 
                                    $childId   = $child['category_id'];
                                    $childName = $child['category_name'];
                                    
                                    // Lấy brands thuộc child này
                                    $brands = $brandModel->get_brand_by_category($childId);
                                    $colCount++;
                                ?>
                                    <div class="mega-column">
                                        <!-- CHILD CATEGORY TITLE (Áo Nam, Quần Nam...) -->
                                        <h4>
                                            <a href="category.php?cat=<?= $childId ?>" class="mega-title">
                                                <?= htmlspecialchars($childName) ?>
                                            </a>
                                        </h4>
                                        
                                        <?php if ($brands && is_object($brands) && $brands->num_rows > 0): ?>
                                            <?php while ($brand = $brands->fetch_assoc()): ?>
                                            <a href="category.php?cat=<?= $childId ?>&brand=<?= $brand['brand_id'] ?>">
                                                <?= htmlspecialchars($brand['brand_name']) ?>
                                            </a>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </div>
                                <?php 
                                    // Thêm banner nếu đạt 4 cột
                                    if ($colCount == 4): 
                                ?>
                                    <div class="mega-column banner">
                                        <img src="images/mega-banner.jpg" alt="Banner"
                                             onerror="this.style.display='none'">
                                    </div>
                                <?php 
                                        break; // Chỉ hiện tối đa 4 cột + 1 banner
                                    endif;
                                endwhile; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                    </li>
                <?php 
                    endwhile;
                endif; 
                ?>
            </ul>
        </nav>

        <!-- Icons (Search, User, Cart) -->
        <div class="header-icons">
            <!-- Search Box -->
            <div class="search-box">
                <input type="text" placeholder="Tìm kiếm..." name="search">
                <i class="fa fa-search"></i>
            </div>
            
            <!-- User Icon with Dropdown -->
            <div class="user-menu">
                <?php if (isset($_SESSION['customer_id'])): ?>
                    <a href="#" id="user-toggle">
                        <i class="fa fa-user"></i>
                    </a>
                    <ul class="dropdown-user" id="dropdown-user">
                        <li><a href="account.php">Tài khoản</a></li>
                        <li><a href="order_history.php">Đơn hàng</a></li>
                        <li><a href="logout.php">Đăng xuất</a></li>
                    </ul>
                <?php else: ?>
                    <a href="login.php"><i class="fa fa-user"></i></a>
                <?php endif; ?>
            </div>
            
            <!-- Cart Icon -->
            <a href="giohang.php">
                <i class="fa fa-shopping-cart"></i>
                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                <span class="cart-count"
                      style="background:red;color:#fff;border-radius:50%;padding:2px 6px;font-size:10px;position:absolute;top:-5px;right:-8px;">
                    <?= count($_SESSION['cart']) ?>
                </span>
                <?php endif; ?>
            </a>
        </div>

    </div>
</div>

<!-- Include Mega Menu JS -->
<script src="megamenu.js"></script>

<!-- User Dropdown Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userToggle   = document.getElementById('user-toggle');
    const dropdownUser = document.getElementById('dropdown-user');
    
    if (userToggle && dropdownUser) {
        userToggle.addEventListener('click', function(e) {
            e.preventDefault();
            dropdownUser.style.display =
                (dropdownUser.style.display === 'block') ? 'none' : 'block';
        });
        
        // Click outside to close
        document.addEventListener('click', function(e) {
            if (!userToggle.contains(e.target) && !dropdownUser.contains(e.target)) {
                dropdownUser.style.display = 'none';
            }
        });
    }
});
</script>

<!-- BREADCRUMB FIX - FORCE CONTAINER WIDTH -->
<script>
(function() {
    'use strict';
    
    function forceFixBreadcrumb() {
        const breadcrumb = document.querySelector('.breadcrumb-section');
        
        if (!breadcrumb) {
            return;
        }
        
        // FORCE breadcrumb styles
        breadcrumb.removeAttribute('style');
        breadcrumb.setAttribute(
            'style',
            'margin: 90px 0 0 0 !important; padding: 14px 0 !important; width: 100% !important; ' +
            'background: #f8f8f8 !important; border-bottom: 1px solid #e0e0e0 !important; ' +
            'display: block !important; box-sizing: border-box !important;'
        );
        
        // FORCE container - ĐẢM BẢO PADDING GIỐNG NHAU
        const container = breadcrumb.querySelector('.container');
        if (container) {
            container.removeAttribute('style');
            container.setAttribute(
                'style',
                'max-width: 100% !important; width: 100% !important; margin: 0 !important; ' +
                'padding: 0 10px !important; box-sizing: border-box !important;'
            );
        }
        
        // FORCE breadcrumb inner
        const breadcrumbInner = breadcrumb.querySelector('.breadcrumb');
        if (breadcrumbInner) {
            breadcrumbInner.removeAttribute('style');
            breadcrumbInner.setAttribute(
                'style',
                'display: flex !important; align-items: center !important; ' +
                'justify-content: flex-start !important; gap: 8px !important; margin: 0 !important; ' +
                'padding: 0 !important; list-style: none !important;'
            );
        }
        
        console.log('✅ Breadcrumb forced: 90px margin, container padding 10px');
    }
    
    // Chạy nhiều lần để đảm bảo
    forceFixBreadcrumb();
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', forceFixBreadcrumb);
    }
    
    window.addEventListener('load', forceFixBreadcrumb);
    
    setTimeout(forceFixBreadcrumb, 100);
    setTimeout(forceFixBreadcrumb, 300);
    setTimeout(forceFixBreadcrumb, 500);
})();
</script>
