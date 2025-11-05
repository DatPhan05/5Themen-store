<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/brand_class.php";
require_once __DIR__ . "/Class/category_class.php";

$msg="";
$cg = new category();
$cates = $cg->show_category();
if($_SERVER['REQUEST_METHOD']==='POST'){
    $cid = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['brand_name'] ?? '');
    if($cid && $name!==""){
        $b = new Brand();
        $b->insert_brand($cid, $name);
        $msg="Đã thêm loại sản phẩm.";
    } else {
        $msg="Chọn danh mục và nhập tên loại.";
    }
}
?>
<div class="admin-content-right">
  <div class="admin-content-right-category_add">
    <h1>Thêm Loại sản phẩm</h1>
    <?php if($msg){ echo "<p>$msg</p>"; }?>
    <form action="" method="POST">
      <label>Chọn danh mục</label>
      <select name="category_id">
        <option value="">--Chọn--</option>
        <?php if($cates){ while($c=$cates->fetch_assoc()){ ?>
          <option value="<?= $c['category_id'] ?>"><?= htmlspecialchars($c['category_name']) ?></option>
        <?php }} ?>
      </select>
      <label>Tên loại</label>
      <input name="brand_name" type="text" placeholder="Nhập tên loại">
      <button type="submit">Thêm</button>
    </form>
  </div>
</div>
</section></body></html>
