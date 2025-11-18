<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/category_class.php";

$cg = new Category();
$list = $cg->show_category();
?>

<div class="admin-content-right">
    <div class="admin-content-right-category_list">

        <h1>Danh sách Danh mục</h1>

        <table>
            <tr>
                <th>STT</th>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Danh mục cha</th>
                <th>Tùy chỉnh</th>
            </tr>

            <?php if ($list): ?>
                <?php 
                    $i = 1; 
                    while ($r = $list->fetch_assoc()): 
                ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td><?= $r['category_id'] ?></td>
                        <td><?= htmlspecialchars($r['category_name']) ?></td>
                        <td>
                            <?= $r['parent_id'] ? " ".$r['parent_id'] : "—" ?>
                        </td>
                        <td>
                            <a href="categoryedit.php?category_id=<?= $r['category_id'] ?>">Sửa</a> |
                            <a onclick="return confirm('Bạn có chắc muốn xóa?')" 
                               href="categorydelete.php?category_id=<?= $r['category_id'] ?>">Xóa</a>
                        </td>
                    </tr>

                <?php endwhile; ?>
            <?php endif; ?>

        </table>

    </div>
</div>

</section>
</body>
</html>
