<?php
include "../include/session.php";
include "../include/database.php";

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/product_class.php";

$db = new Database();

/* ================================
   PHÂN TRANG
================================ */
$products_per_page = 8;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) $current_page = 1;

$count_query    = $db->select("SELECT COUNT(*) AS total_products FROM tbl_product");
$total_products = $count_query->fetch_assoc()['total_products'];
$total_pages    = ceil($total_products / $products_per_page);

$offset = ($current_page - 1) * $products_per_page;

if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
    $offset = ($current_page - 1) * $products_per_page;
}

/* ================================
   LẤY DANH SÁCH SẢN PHẨM
================================ */
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

    table {
        width: 100%;
        min-width: 900px;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 14px;
        margin-bottom: 25px;
    }

    table th {
        background: linear-gradient(90deg, #10ac84, #00d2d3);
        color: #fff;
        padding: 14px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
    }

    table tr:first-child th:first-child { border-top-left-radius: 10px; }
    table tr:first-child th:last-child  { border-top-right-radius: 10px; }

    table td {
        padding: 14px;
        border-bottom: 1px solid #eee;
        background: rgba(255,255,255,0.85);
        color: #333;
        font-weight: 500;
        vertical-align: middle;
    }

    table tbody tr:hover {
        background: #ffffff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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

    .pagination {
        display: flex;
        justify-content: center;
        margin-top: 20px;
    }

    .pagination a, .pagination span {
        text-decoration: none;
        color: #333;
        padding: 8px 15px;
        margin: 0 4px;
        border-radius: 8px;
        font-weight: 600;
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid #ddd;
        min-width: 45px;
        text-align: center;
        transition: all 0.3s;
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
        pointer-events: none;
    }

    .pagination .disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

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
            <i class="fa-solid fa-boxes-stacked"></i> 
            Danh sách Sản phẩm (Trang <?= $current_page ?>/<?= $total_pages ?>)
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
                        $i = $offset + 1;
                        while ($r = $list->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td style="max-width:250px;"><?= htmlspecialchars($r['product_name']) ?></td>
                        <td><?= htmlspecialchars($r['category_name']) ?></td>
                        <td><?= htmlspecialchars($r['brand_name']) ?></td>

                        <td class="price-col">
                            <?= number_format($r['product_price'], 0, ',', '.') ?> đ
                        </td>

                        <td>
                            <?php if (!empty($r['product_img'])): ?>
                                <img src="../<?= htmlspecialchars($r['product_img']) ?>" alt="">
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

                <a href="?page=<?= $current_page - 1 ?>" 
                   class="<?= ($current_page <= 1) ? 'disabled' : '' ?>">&laquo; Trước</a>

                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <a href="?page=<?= $p ?>" class="<?= ($p == $current_page) ? 'active' : '' ?>">
                        <?= $p ?>
                    </a>
                <?php endfor; ?>

                <a href="?page=<?= $current_page + 1 ?>" 
                   class="<?= ($current_page >= $total_pages) ? 'disabled' : '' ?>">Sau &raquo;</a>

            </div>
        <?php endif; ?>

    </div>
</div>

</section>
</div>
</body>
</html>
