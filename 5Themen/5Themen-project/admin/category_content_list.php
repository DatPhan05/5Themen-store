<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/class/category_content_class.php";

$ct = new CategoryContent();
$list = $ct->getAll();
?>

<div class="admin-content-right">
    <h1>Danh sách nội dung danh mục</h1>

    <table>
        <tr>
            <th>ID</th>
            <th>Danh mục</th>
            <th>Tiêu đề</th>
            <th>Tùy chỉnh</th>
        </tr>

        <?php while ($r = $list->fetch_assoc()): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= $r['category_name'] ?></td>
                <td><?= $r['title'] ?></td>
                <td>
                    <a href="category_content_edit.php?id=<?= $r['id'] ?>">Sửa</a> | 
                    <a href="category_content_delete.php?id=<?= $r['id'] ?>"
                       onclick="return confirm('Xóa nội dung này?');">Xóa</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>
</div>
