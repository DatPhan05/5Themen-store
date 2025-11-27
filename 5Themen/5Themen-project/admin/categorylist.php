<?php
include "../include/session.php";
include "../include/database.php";

require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/class/category_class.php";

$cg = new Category();

$records_per_page = 6;
$current_page     = isset($_GET['page']) && (int)$_GET['page'] > 0 ? (int)$_GET['page'] : 1;

$total_records = $cg->count_all_categories();
$total_pages   = ceil($total_records / $records_per_page);

if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
} elseif ($total_pages == 0) {
    $current_page = 1;
}

$start_limit = ($current_page - 1) * $records_per_page;
$list        = $cg->show_category($start_limit, $records_per_page);

$msg      = "";
$msg_type = "";

if (isset($_GET['msg'])) {
    if ($_GET['msg'] === 'deleted') {
        $msg      = "✔ Đã xóa danh mục thành công.";
        $msg_type = "success";
    } elseif ($_GET['msg'] === 'has_child') {
        $msg      = "⚠ Không thể xóa danh mục vì vẫn còn danh mục con.";
        $msg_type = "error";
    }
}
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
        padding: 30px;
        border-radius: 20px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(15px);
        border: 1px solid rgba(255, 255, 255, 0.6);
        box-shadow: 0 8px 32px rgba(31, 38, 135, 0.15);
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .list-title {
        font-size: 28px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 20px;
        text-transform: uppercase;
        color: #333;
        letter-spacing: 1px;
    }

    .alert {
        padding: 12px 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .alert-success {
        background: rgba(32,191,107,0.15);
        border: 1px solid rgba(32,191,107,0.3);
        color: #20bf6b;
    }

    .alert-error {
        background: rgba(252,92,101,0.15);
        border: 1px solid rgba(252,92,101,0.3);
        color: #fc5c65;
    }

    table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 14px;
    }

    table th {
        background: linear-gradient(90deg, #4b7bec, #3867d6);
        color: white;
        padding: 15px;
        text-transform: uppercase;
    }

    table tr:first-child th:first-child { border-top-left-radius: 10px; }
    table tr:first-child th:last-child  { border-top-right-radius: 10px; }

    table td {
        padding: 15px;
        border-bottom: 1px solid #eee;
        background: rgba(255,255,255,0.7);
        font-weight: 500;
    }

    table tbody tr:hover {
        background: rgba(255,255,255,0.9);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }

    table tbody tr:last-child td { border-bottom: none; }

    .action-link {
        padding: 6px 12px;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 600;
        margin-right: 5px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        font-size: 13px;
    }

    .edit-btn {
        background: #ffeaa7;
        color: #d63031;
    }

    .edit-btn:hover { background: #fed330; }

    .delete-btn {
        background: #ff7675;
        color: white;
    }

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
        100% { transform: translate(-20px, 20px); }
    }

    .pagination {
        display: flex;
        justify-content: center;
        padding: 20px 0 10px;
        margin-top: 15px;
        border-top: 1px solid rgba(255,255,255,0.5);
    }

    .pagination a, .pagination span {
        padding: 8px 15px;
        margin: 0 4px;
        border-radius: 6px;
        border: 1px solid #ddd;
        font-weight: 600;
        background: #fff;
        color: #4b7bec;
        transition: 0.2s;
        text-align: center;
        min-width: 40px;
    }

    .pagination a:hover {
        background: #4b7bec;
        color: white;
        border-color: #4b7bec;
    }

    .pagination .current-page {
        background: #3867d6;
        color: white;
        border-color: #3867d6;
    }
</style>

<div class="admin-content-right">
    <div class="blob-decor-list"></div>

    <div class="list-container">
        <h1 class="list-title"><i class="fa-solid fa-list-check"></i> Danh sách Danh mục</h1>

        <?php if (!empty($msg)): ?>
            <div class="alert <?= $msg_type === 'success' ? 'alert-success' : 'alert-error' ?>">
                <i class="<?= $msg_type === 'success' ? 'fa-solid fa-check-circle' : 'fa-solid fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>STT</th>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Danh mục cha</th>
                    <th>Tùy chỉnh</th>
                </tr>
            </thead>

            <tbody>
                <?php if ($list && $list->num_rows > 0): ?>
                    <?php $i = $start_limit + 1; ?>
                    <?php while ($r = $list->fetch_assoc()): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $r['category_id'] ?></td>
                            <td><?= htmlspecialchars($r['category_name']) ?></td>
                            <td><?= $r['parent_id'] == 0 ? "—" : htmlspecialchars($r['parent_name'] ?? '—') ?></td>

                            <td>
                                <a href="categoryedit.php?category_id=<?= $r['category_id'] ?>" class="action-link edit-btn">
                                    <i class="fa-solid fa-pen-to-square"></i> Sửa
                                </a>

                                <a href="categorydelete.php?category_id=<?= $r['category_id'] ?>"
                                   onclick="return confirm('Bạn có chắc muốn xóa danh mục ID: <?= $r['category_id'] ?>?')"
                                   class="action-link delete-btn">
                                    <i class="fa-solid fa-trash-can"></i> Xóa
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align:center; color:#777; font-style:italic;">
                            Chưa có danh mục nào được thêm.
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

</body>
</html>
