<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/brand_class.php";

$b = new Brand();
$list = $b->show_brand();   
?>
<div class="admin-content-right">
  <div class="admin-content-right-category_list">
    <h1>Danh sách Loại sản phẩm</h1>
    <table>
      <tr>
        <th>Stt</th>
        <th>ID</th>
        <th>Tên loại</th>
        <th>Danh mục</th>
        <th>Tùy biến</th>
      </tr>
      <?php if($list && $list->num_rows>0){ $i=1; while($r=$list->fetch_assoc()){ ?>
      <tr>
         <td><?= $i++ ?></td>
         <td><?= $r['brand_id'] ?></td>
         <td><?= htmlspecialchars($r['brand_name']) ?></td>
         <td><?= htmlspecialchars($r['category_name'] ?? '—') ?></td>
         <td>
             <a href="brandedit.php?brand_id=<?= $r['brand_id'] ?>">Sửa</a> | 
             <a onclick="return confirm('Xóa loại sản phẩm này?')" 
                href="brandelete.php?brand_id=<?= $r['brand_id'] ?>">Xóa</a>
         </td>
      </tr>
      <?php } } else { ?>
      <tr><td colspan="5" style="text-align:center">Chưa có loại</td></tr>
      <?php } ?>
    </table>
  </div>
</div>
</section>
</body>
</html>
