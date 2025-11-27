<?php
require_once "../include/session.php";
require_once "../include/database.php";

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/brand_class.php";

$b = new Brand();

/* Phân trang */
$records_per_page = 8;
$current_page     = (isset($_GET['page']) && (int)$_GET['page'] > 0)
                    ? (int)$_GET['page']
                    : 1;

$total_records = $b->count_all_brands();
$total_pages   = ceil($total_records / $records_per_page);

if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
} elseif ($total_pages == 0) {
    $current_page = 1;
}

$start_limit = ($current_page - 1) * $records_per_page;
$list        = $b->show_brand($start_limit, $records_per_page);
?>

<style>
    .admin-content-right {
        margin-left: 230px;
        flex: 1;
        padding: 40px;
        position: relative;
    }

    .list-container {
        padding: 30px;
        border-radius: 20px;
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255,255,255,0.6);
        box-shadow: 0 8px 32px rgba(31,38,135,0.15);
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .list-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        text-align: center;
        margin-bottom: 30px;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    table th {
        background: linear-gradient(90deg, #fa8231, #fc5c65);
        color: white;
        padding: 15px;
        font-weight: 600;
        text-transform: uppercase;
        white-space: nowrap;
    }

    table tr:first-child th:first-child { border-top-left-radius: 10px; }
    table tr:first-child th:last-child  { border-top-right-radius: 10px; }

    table td {
        padding: 15px;
        background: rgba(255,255,255,0.7);
        border-bottom: 1px solid #eee;
        font-weight: 500;
        color: #333;
    }

    table tbody tr {
        transition: 0.3s;
    }
    table tbody tr:hover {
        background: rgba(255,255,255,0.9);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    .action-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 6px;
        font-weight: 600;
        font-size: 13px;
        margin-right: 5px;
        transition: 0.2s;
        white-space: nowrap;
    }

    .edit-btn   { background: #ffeaa7; color: #d63031; }
    .edit-btn:hover { background: #fed330; }

    .delete-btn { background: #ff7675; color: white; }
    .delete-btn:hover { background: #e17055; }

    .blob-decor-list {
        position: absolute;
        width: 350px;
        height: 350px;
        background: linear-gradient(180deg, #f093fb 0%, #f5576c 100%);
        border-radius: 50%;
        filter: blur(100px);
        opacity: 0.3;
        z-index: -1;
        top: 10%;
        right: 5%;
        animation: float 10s infinite alternate;
    }

    @keyframes float {
        0%   { transform: translate(0, 0); }
        100% { transform: translate(20px, 40px); }
    }

    .pagination {
        display: flex;
        justify-content: center;
        padding: 20px 0 10px;
        margin-top: 15px;
        border-top: 1px solid rgba(255,255,255,0.5);
    }

    .pagination a,
    .pagination span {
        padding: 8px 15px;
        margin: 0 4px;
        border: 1px solid #ddd;
        border-radius: 6px;
        font-weight: 600;
        background: #fff;
        text-decoration: none;
        min-width: 40px;
        text-align: center;
        color: #fa8231;
        transition: 0.2s;
    }

    .pagination a:hover {
        background: #fa8231;
        color: white;
        border-color: #fa8231;
    }

    .current-page {
        background: #fc5c65;
        color: white !important;
        border-color: #fc5c65;
    }
</style>

<div class="admin-content-right">

    <div class="blob-decor-list"></div>

    <div class="list-container">
        <h1 class="list-title"><i class="fa-solid fa-list-ul"></i> Danh sách loại sản phẩm</h1>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>ID</th>
                    <th>Tên loại</th>
                    <th>Thuộc danh mục</th>
                    <th>Tùy chỉnh</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($list && $list->num_rows > 0): ?>
                    <?php $i = $start_limit + 1; ?>
                    <?php while ($r = $list->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $r['brand_id'] ?></td>
                            <td><?= htmlspecialchars($r['brand_name']) ?></td>
                            <td><?= htmlspecialchars($r['category_name']) ?></td>

                            <td>
                                <a href="brandedit.php?brand_id=<?= $r['brand_id'] ?>" class="action-link edit-btn">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>

                                <a href="brandelete.php?brand_id=<?= $r['brand_id'] ?>"
                                   onclick="return confirm('Bạn có chắc muốn xóa loại sản phẩm ID: <?= $r['brand_id'] ?>?')"
                                   class="action-link delete-btn">
                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center;color:#777;font-style:italic;">
                            Chưa có loại sản phẩm nào được thêm.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages >= 1): ?>
            <div class="pagination">

                <?php if ($current_page > 1): ?>
                    <a href="?page=<?= $current_page - 1 ?>">Trước</a>
                <?php endif; ?>

                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                    <?php if ($p == $current_page): ?>
                        <span class="current-page"><?= $p ?></span>
                    <?php else: ?>
                        <a href="?page=<?= $p ?>"><?= $p ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($current_page < $total_pages): ?>
                    <a href="?page=<?= $current_page + 1 ?>">Sau</a>
                <?php endif; ?>

            </div>
        <?php endif; ?>

    </div>
</div>

</section>
</body>
</html>
