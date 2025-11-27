<?php
// Bổ sung các file cần thiết
include "../include/session.php";
include "../include/database.php";

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/product_class.php";

$db = new Database();

// ===================================
// 1. CẤU HÌNH PHÂN TRANG
// ===================================
$products_per_page = 8; // Số sản phẩm trên mỗi trang

// Lấy trang hiện tại từ URL, mặc định là trang 1
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

// ===================================
// 2. TÍNH TOÁN TỔNG SỐ TRANG
// ===================================

// Lấy tổng số lượng sản phẩm từ bảng tbl_product
$count_query = $db->select("SELECT COUNT(*) AS total_products FROM tbl_product");
$total_products = $count_query->fetch_assoc()['total_products'];

// Tính toán tổng số trang
$total_pages = ceil($total_products / $products_per_page);

// Xác định OFFSET (vị trí bắt đầu lấy dữ liệu)
$offset = ($current_page - 1) * $products_per_page;

// Kiểm tra và điều chỉnh trang hiện tại nếu vượt quá tổng số trang
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
    $offset = ($current_page - 1) * $products_per_page;
}

// ===================================
// 3. TRUY VẤN DỮ LIỆU CÓ PHÂN TRANG
// ===================================

// Câu truy vấn đã được chỉnh sửa để JOIN lấy tên Danh mục và Loại SP, 
// đồng thời thêm LIMIT và OFFSET.
$sql = "
    SELECT 
        p.*, 
        c.category_name, 
        b.brand_name 
    FROM tbl_product p
    JOIN tbl_category c ON p.category_id = c.category_id
    JOIN tbl_brand b ON p.brand_id = b.brand_id
    LIMIT $products_per_page OFFSET $offset
";
$list = $db->select($sql); 
?>

<style>
    /* ========== LAYOUT ========== */
    .admin-content-right {
        margin-left: 230px;
        flex: 1;
        padding: 40px;
        position: relative;
    }

    .list-container {
        width: 100%;
        margin: 0 auto;
        padding: 30px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        overflow-x: auto;
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .list-title {
        font-size: 26px;
        font-weight: 700;
        color: #333;
        margin-bottom: 25px;
    }

    .list-title i {
        margin-right: 8px;
    }

    /* ========== TABLE ========== */
    table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 14px;
        margin-bottom: 25px; /* Thêm khoảng cách dưới table */
    }

    table th {
        background: linear-gradient(90deg, #10ac84, #00d2d3);
        color: #fff;
        padding: 14px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
    }

    table tr:first-child th:first-child {
        border-top-left-radius: 10px;
    }
    table tr:first-child th:last-child {
        border-top-right-radius: 10px;
    }

    table td {
        padding: 14px;
        border-bottom: 1px solid #eee;
        background: rgba(255,255,255,0.85);
        color: #333;
        font-weight: 500;
        vertical-align: middle;
    }

    table tbody tr {
        transition: background 0.25s ease, box-shadow 0.25s ease;
    }

    table tbody tr:hover {
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    table tbody tr:last-child td {
        border-bottom: none;
    }

    .price-col {
        white-space: nowrap;
        font-weight: 700;
        color: #333;
    }

    table td img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #ddd;
    }

    /* ========== BUTTONS ========== */
    .action-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-weight: 600;
        font-size: 13px;
        margin-right: 4px;
    }

    .edit-btn {
        background: #ffeaa7;
        color: #d63031;
    }
    .edit-btn:hover {
        background: #ffd86b;
    }

    .delete-btn {
        background: #ff7675;
        color: #fff;
    }
    .delete-btn:hover {
        background: #e17055;
    }

    /* ========== PHÂN TRANG STYLE ========== */
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 20px;
    }

    .pagination a, .pagination span {
        text-decoration: none;
        color: #333;
        padding: 8px 15px;
        margin: 0 4px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid #ddd;
        min-width: 45px;
        text-align: center;
    }

    .pagination a:hover {
        background: #00d2d3;
        color: white;
        border-color: #00d2d3;
    }

    .pagination .active {
        background: #10ac84;
        color: white;
        border-color: #10ac84;
        pointer-events: none; /* Vô hiệu hóa click vào trang hiện tại */
    }

    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* ========== BLOB TRANG TRÍ ========== */
    .blob-decor-list {
        position: absolute;
        width: 350px;
        height: 350px;
        background: linear-gradient(180deg, #a1c4fd 0%, #c2e9fb 100%);
        border-radius: 50%;
        filter: blur(100px);
        opacity: 0.3;
        z-index: -1;
        top: 10%;
        right: 5%;
    }
</style>

<div class="admin-content-right">
    <div class="blob-decor-list"></div>

    <div class="list-container">
        <h1 class="list-title">
            <i class="fa-solid fa-boxes-stacked"></i> Danh sách Sản phẩm (Trang <?= $current_page ?>/<?= $total_pages ?>)
        </h1>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Loại</th>
                    <th>Giá</th>
                    <th>Ảnh</th>
                    <th>Tùy chỉnh</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($list && $list->num_rows > 0): ?>
                    <?php 
                    // STT bắt đầu từ vị trí OFFSET + 1
                    $i = $offset + 1; 
                    while ($r = $list->fetch_assoc()): 
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td style="max-width: 250px;"><?= htmlspecialchars($r['product_name']) ?></td>
                            <td><?= htmlspecialchars($r['category_name']) ?></td>
                            <td><?= htmlspecialchars($r['brand_name']) ?></td>

                            <td class="price-col">
                                <?= number_format($r['product_price'], 0, ',', '.') ?> đ
                            </td>

                            <td>
                                <?php if (!empty($r['product_img'])): ?>
                                    <img src="../<?= htmlspecialchars($r['product_img']) ?>" alt="Ảnh sản phẩm">
                                <?php else: ?>
                                    <span>N/A</span>
                                <?php endif; ?>
                            </td>

                            <td>
                                <a href="productedit.php?id=<?= $r['product_id'] ?>" class="action-link edit-btn">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>
                                <a href="productdelete.php?id=<?= $r['product_id'] ?>"
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm ID: <?= $r['product_id'] ?>?')"
                                   class="action-link delete-btn">
                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align:center; color:#777; font-style:italic;">
                            Chưa có sản phẩm nào được thêm.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <?php $prev_page = $current_page - 1; ?>
                <a href="?page=<?= $prev_page ?>" class="<?= ($current_page <= 1) ? 'disabled' : '' ?>">
                    &laquo; Trước
                </a>

                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <a href="?page=<?= $p ?>" class="<?= ($p == $current_page) ? 'active' : '' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <?php $next_page = $current_page + 1; ?>
                <a href="?page=<?= $next_page ?>" class="<?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">
                    Sau &raquo;
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

</section>
</div> </body>
</html>