<style>
    .admin-content-left {
        /* Kích thước và vị trí */
        width: 260px;
        /* Chiều cao bằng 100% màn hình trừ đi chiều cao header (70px) */
        min-height: calc(100vh - 70px); 
        padding: 20px;
        
        /* Hiệu ứng Kính mờ (Glassmorphism) */
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        /* Viền mờ bên phải */
        border-right: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 5px 0 15px rgba(0,0,0,0.05);
        
        display: flex;
        flex-direction: column;
        gap: 20px;
        /* Để sidebar đứng yên nếu nội dung bên phải dài và cần cuộn */
        position: sticky; 
        top: 70px; 
    }

    /* Style cho nút "Dashboard" */
    .btn-back {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 15px;
        background: linear-gradient(135deg, #6c5ce7, #a29bfe);
        color: white !important;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        box-shadow: 0 4px 10px rgba(108, 92, 231, 0.3);
        transition: transform 0.2s ease;
        margin-bottom: 10px;
    }
    
    .btn-back:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(108, 92, 231, 0.4);
    }

    /* Style cho từng nhóm menu */
    .menu-group {
        margin-bottom: 15px;
    }

    .menu-title {
        font-size: 12px;
        font-weight: 700;
        color: #666;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 10px;
        padding-left: 10px;
        opacity: 0.8;
    }

    .admin-content-left ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .admin-content-left ul li {
        margin-bottom: 8px;
    }

    /* Style cho link menu */
    .admin-content-left ul li a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 15px;
        color: #333;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        border-radius: 10px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.1); 
    }

    /* Icon width cố định để text thẳng hàng */
    .admin-content-left ul li a i {
        width: 20px;
        text-align: center;
        color: #555;
        transition: 0.3s;
    }

    /* Hiệu ứng Hover */
    .admin-content-left ul li a:hover {
        background: rgba(255, 255, 255, 0.6);
        color: #0984e3;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        transform: translateX(5px); 
    }

    .admin-content-left ul li a:hover i {
        color: #0984e3; 
        transform: scale(1.1);
    }
</style>

<section class="admin-content"> 
    <div class="admin-content-left">
        
        <a href="admin_home.php" class="btn-back">
            <i class="fa-solid fa-house"></i> Dashboard
        </a>

        <div class="menu-group">
            <div class="menu-title">Quản lý Sản phẩm</div>
            <ul>
                <li>
                    <a href="categorylist.php">
                        <i class="fa-solid fa-folder-tree"></i> Danh mục
                    </a>
                </li>
                <li>
                    <a href="categoryadd.php">
                        <i class="fa-solid fa-folder-plus"></i> Thêm danh mục
                    </a>
                </li>

                <li>
                    <a href="brandlist.php">
                        <i class="fa-solid fa-tags"></i> Thương hiệu
                    </a>
                </li>
                <li>
                    <a href="brandadd.php">
                        <i class="fa-solid fa-tag"></i> Thêm thương hiệu
                    </a>
                </li>

                <li>
                    <a href="productlist.php">
                        <i class="fa-solid fa-box-open"></i> Tất cả sản phẩm
                    </a>
                </li>
                <li>
                    <a href="productadd.php">
                        <i class="fa-solid fa-square-plus"></i> Thêm sản phẩm
                    </a>
                </li>
            </ul>
        </div>

        <div class="menu-group">
            <div class="menu-title">Nội dung & Cấu hình</div>
            <ul>
                <li>
                    <a href="category_content_list.php">
                        <i class="fa-solid fa-newspaper"></i> Bài viết nội dung
                    </a>
                </li>
                <li>
                    <a href="category_content_add.php">
                        <i class="fa-solid fa-pen-to-square"></i> Thêm bài viết
                    </a>
                </li>
            </ul>
        </div>
    </div>
    