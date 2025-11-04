<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/category_class.php";
$cg = new category();
$list = $cg->show_category();
?>
<div class="admin-content-right">
  <div class="admin-content-right-category_list">
    <h1>Danh sách danh mục</h1>
    <table>
      <tr><th>Stt</th><th>ID</th><th>Danh mục</th><th>Tùy biến</th></tr>
      <?php if($list && $list->num_rows>0){ $i=1; while($r=$list->fetch_assoc()){ ?>
        <tr>
          <td><?= $i++ ?></td>
          <td><?= $r['category_id'] ?></td>
          <td><?= htmlspecialchars($r['category_name']) ?></td>
          <td>
            <a href="categoryedit.php?category_id=<?= $r['category_id'] ?>">Sửa</a> |
            <a onclick="return confirm('Xóa danh mục này?')" href="categorydelete.php?category_id=<?= $r['category_id'] ?>">Xóa</a>
          </td>
        </tr>
      <?php } } else { ?>
        <tr><td colspan="4" style="text-align:center">Chưa có danh mục</td></tr>
      <?php } ?>
    </table>
  </div>
</div>
</section></body></html>
