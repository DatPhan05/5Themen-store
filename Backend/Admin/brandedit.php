<?php
require_once __DIR__ . "/header.php";
require_once __DIR__ . "/slider.php";
require_once __DIR__ . "/Class/brand_class.php";
require_once __DIR__ . "/Class/category_class.php";

$id = (int)($_GET['brand_id'] ?? 0);
$brand = new Brand();
$row = $brand->get_brand($id);
if(!$row){ die("Không tìm thấy loại"); }
$cg = new category();
$cates = $cg->show_category();

$msg="";
if($_SERVER['REQUEST_METHOD']==='POST'){
    $cid = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['brand_name'] ?? '');
    if($cid && $name!==""){
        $brand->update_brand($id,$cid,$name);
        $msg="Đã lưu thay đổi.";
        $row = $brand->get_brand($id);
    } else {
        $msg="Chọn danh mục và nhập tên loại.";
    }
}
?>
<div class="admin-content-right">
  <div class="admin-content-right-category_add">
    <h1>Sửa Loại sản phẩm</h1>
    <?php if($msg){ echo "<p>$msg</p>"; }?>
    <form action="" method="POST">
      <label>Chọn danh mục</label>
      <select name="category_id">
        <option value="">--Chọn--</option>
        <?php if($cates){ while($c=$cates->fetch_assoc()){ ?>
          <option value="<?= $c['category_id'] ?>" <?= $row['category_id']==$c['category_id']?'selected':'' ?>>
            <?= htmlspecialchars($c['category_name']) ?>
          </option>
        <?php }} ?>
      </select>
      <label>Tên loại</label>
      <input name="brand_name" type="text" value="<?= htmlspecialchars($row['brand_name']) ?>">
      <button type="submit">Lưu</button>
    </form>
  </div>
</div>
</section></body></html>
